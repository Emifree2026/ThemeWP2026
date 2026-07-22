/**
 * Emifree Contact — per-section behaviors.
 *
 *  - Client-side validation on blur + submit. Mirrors the React's
 *    Zod schema: name ≥ 2, valid email regex, company ≥ 2,
 *    message ≥ 10. Inline error rendering via data-emifree-contact-error
 *    paragraphs + red border on the offending input.
 *  - Submit handler: fetch to admin-ajax.php?action=send_contact
 *    (URL provided by wp_localize_script as emifreeContact.ajaxUrl,
 *    already includes the action query string — see
 *    emifree_enqueue_contact_script() in functions.php). We POST
 *    FormData as-is. On success, show the success banner,
 *    clear the form. On error, show the error banner + surface any
 *    per-field errors that came back from the server.
 *  - Button state: idle (Send Message + send icon) ↔ loading
 *    (Sending... + spinner). Disabled while submitting. The two SVGs
 *    and the label span are toggled via data-emifree-contact-* markers.
 *
 * Loaded only when the Contact section is present on the page.
 * The localized data (ajaxUrl / nonce / messages) is registered via
 * emifree_enqueue_contact_script() in functions.php.
 */

(function () {
    'use strict';

    if (typeof window.emifreeContact === 'undefined') {
        return;
    }

    const emifreeForm = document.getElementById('emifree-contact-form');
    if (!emifreeForm) {
        return;
    }

    const emifreeResult = document.getElementById('emifree-contact-result');
    const emifreeSubmit = document.getElementById('emifree-contact-submit');
    const emifreeSubmitLabel = emifreeSubmit ? emifreeSubmit.querySelector('[data-emifree-contact-submit-label]') : null;
    const emifreeIconIdle = emifreeSubmit ? emifreeSubmit.querySelector('[data-emifree-contact-submit-icon-idle]') : null;
    const emifreeIconLoading = emifreeSubmit ? emifreeSubmit.querySelector('[data-emifree-contact-submit-icon-loading]') : null;

    const emifreeFields = emifreeForm.querySelectorAll('[data-emifree-contact-field]');

    // ----- Antispam Tier 1: timestamp anchor + honeypot pickup -----
    // emifree_ts is set to PHP-time at SSR as a fallback; we overwrite
    // it here so the server-measured elapsed time is "page opened to
    // submit click" rather than "PHP ran to submit click" (the latter
    // is artificially short on cached pages).
    //
    // We also keep references to both the timestamp + honeypot fields
    // so the success-path handler can re-anchor the timestamp after
    // form.reset() (which would otherwise restore the SSR value). The
    // honeypot itself is empty by default and stays empty through
    // reset(), so no re-empty action is needed.
    const emifreeTsInput = emifreeForm.querySelector('input[name="emifree_ts"]');
    const emifreeHoneypot = emifreeForm.querySelector('input[name="website_url"]');
    // Hidden product-of-interest inputs — populated by the
    // emifree:prefill-contact listener below when a product-section
    // "Request Quote" CTA is clicked. The server-side handler
    // (functions.php emifree_handle_contact_submit) whitelists the
    // slug and uses the label to build the email subject + body.
    const emifreeProductInput = emifreeForm.querySelector('input[name="product"]');
    const emifreeProductLabelInput = emifreeForm.querySelector('input[name="product_label"]');
    const emifreeMessageInput = emifreeForm.querySelector('[data-emifree-contact-field="message"]');
    if (emifreeTsInput) {
        emifreeTsInput.value = String(Math.floor(Date.now() / 1000));
    }

    // ----- emifree:prefill-contact listener -----
    //
    // products.js dispatches this event with detail = { slug, label, message }.
    // We populate the two hidden inputs (for the email subject + body
    // line in the recipient inbox) and inject a "I would like a quote
    // for X.\n\n" line into the message textarea so the visitor sees
    // confirmation of which product they selected and can edit the
    // draft before submitting.
    //
    // Switching products mid-flow: we stamp the inserted prefix's
    // length on the textarea itself (data-emifree-prefill-length) so
    // a subsequent CTA click can replace JUST the auto-fill portion
    // without clobbering any draft the user typed after it. Clicking
    // the SAME product CTA again is a no-op (the existing prefix is
    // untouched). If the user manually edits the auto-fill portion,
    // we still replace exactly the original character range — they
    // can edit AFTER the stamped boundary freely.
    //
    // Cursor lands at the end of the inserted prefix so the user
    // starts typing right after the auto-fill. On replacement, the
    // cursor lands at the same boundary position.
    window.addEventListener('emifree:prefill-contact', function (e) {
        const emifreeDetail = (e && e.detail) || {};
        const emifreeSlug = emifreeDetail.slug || '';
        const emifreeLabel = emifreeDetail.label || '';
        const emifreePrefix = emifreeDetail.message || '';
        if (emifreeProductInput && emifreeSlug) {
            emifreeProductInput.value = emifreeSlug;
        }
        if (emifreeProductLabelInput && emifreeLabel) {
            emifreeProductLabelInput.value = emifreeLabel;
        }
        if (!emifreeMessageInput || !emifreePrefix) {
            return;
        }
        const emifreeCurrent = emifreeMessageInput.value || '';
        const emifreePrevLen = parseInt(emifreeMessageInput.getAttribute('data-emifree-prefill-length') || '0', 10);
        const emifreePrevSlug = emifreeMessageInput.getAttribute('data-emifree-prefill-slug') || '';
        if (emifreePrevLen > 0 && emifreePrevSlug === emifreeSlug) {
            // Same product, same prefix — leave the user's textarea
            // alone (they may have edited it). No-op.
            return;
        }
        // Strip the previous auto-fill (if any) and prepend the new
        // one. Any text the user typed AFTER the previous auto-fill
        // boundary is preserved as-is.
        const emifreeTail = emifreePrevLen > 0 ? emifreeCurrent.substring(emifreePrevLen) : emifreeCurrent;
        emifreeMessageInput.value = emifreePrefix + emifreeTail;
        emifreeMessageInput.setAttribute('data-emifree-prefill-length', String(emifreePrefix.length));
        emifreeMessageInput.setAttribute('data-emifree-prefill-slug', emifreeSlug);
        emifreeMessageInput.focus();
        try {
            emifreeMessageInput.setSelectionRange(emifreePrefix.length, emifreePrefix.length);
        } catch (_) {
            // Some browsers (older Safari on certain input types) throw
            // when selection range is set on textarea — silently skip
            // the cursor placement rather than aborting the whole flow.
        }
    });

    // ----- Validation rules (mirror React's Zod schema) -----
    const emifreeValidators = {
        name:    (v) => v.trim().length >= 2,
        email:   (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()),
        company: (v) => v.trim().length >= 2,
        message: (v) => v.trim().length >= 10,
    };

    const emifreeErrorMessages = {
        name:    'Name must be at least 2 characters.',
        email:   'Please enter a valid email address.',
        company: 'Company name must be at least 2 characters.',
        message: 'Message must be at least 10 characters.',
    };

    // ----- Inline error rendering -----
    function emifreeShowFieldError(emifreeName, emifreeMessage) {
        const emifreeInput = emifreeForm.querySelector('[data-emifree-contact-field="' + emifreeName + '"]');
        const emifreeErrorEl = emifreeForm.querySelector('[data-emifree-contact-error="' + emifreeName + '"]');
        if (emifreeInput) {
            emifreeInput.classList.remove('border-slate-200', 'focus:border-blue-500');
            emifreeInput.classList.add('border-red-300', 'focus:border-red-500');
            emifreeInput.setAttribute('aria-invalid', 'true');
        }
        if (emifreeErrorEl) {
            emifreeErrorEl.textContent = emifreeMessage;
            emifreeErrorEl.classList.remove('hidden');
        }
    }

    function emifreeClearFieldError(emifreeName) {
        const emifreeInput = emifreeForm.querySelector('[data-emifree-contact-field="' + emifreeName + '"]');
        const emifreeErrorEl = emifreeForm.querySelector('[data-emifree-contact-error="' + emifreeName + '"]');
        if (emifreeInput) {
            emifreeInput.classList.remove('border-red-300', 'focus:border-red-500');
            emifreeInput.classList.add('border-slate-200', 'focus:border-blue-500');
            emifreeInput.removeAttribute('aria-invalid');
        }
        if (emifreeErrorEl) {
            emifreeErrorEl.textContent = '';
            emifreeErrorEl.classList.add('hidden');
        }
    }

    function emifreeValidateField(emifreeName) {
        const emifreeInput = emifreeForm.querySelector('[data-emifree-contact-field="' + emifreeName + '"]');
        if (!emifreeInput) {
            return true;
        }
        const emifreeValue = emifreeInput.value || '';
        if (!emifreeValidators[emifreeName](emifreeValue)) {
            emifreeShowFieldError(emifreeName, emifreeErrorMessages[emifreeName]);
            return false;
        }
        emifreeClearFieldError(emifreeName);
        return true;
    }

    function emifreeValidateAll() {
        let emifreeAllValid = true;
        emifreeFields.forEach((emifreeField) => {
            const emifreeName = emifreeField.getAttribute('data-emifree-contact-field');
            if (!emifreeValidateField(emifreeName)) {
                emifreeAllValid = false;
            }
        });
        return emifreeAllValid;
    }

    // ----- Result banner -----
    function emifreeShowResult(emifreeKind, emifreeMessage) {
        if (!emifreeResult) {
            return;
        }
        emifreeResult.classList.remove(
            'hidden',
            'bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200',
            'bg-red-50', 'text-red-800', 'border-red-200'
        );
        if (emifreeKind === 'success') {
            emifreeResult.classList.add('bg-emerald-50', 'text-emerald-800', 'border', 'border-emerald-200');
        } else {
            emifreeResult.classList.add('bg-red-50', 'text-red-800', 'border', 'border-red-200');
        }
        emifreeResult.textContent = emifreeMessage;
        emifreeResult.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function emifreeHideResult() {
        if (!emifreeResult) {
            return;
        }
        emifreeResult.classList.add('hidden');
        emifreeResult.textContent = '';
    }

    // ----- Submit button state -----
    function emifreeSetSubmitting(emifreeIsSubmitting) {
        if (!emifreeSubmit) {
            return;
        }
        emifreeSubmit.disabled = emifreeIsSubmitting;
        if (emifreeSubmitLabel) {
            emifreeSubmitLabel.textContent = emifreeIsSubmitting ? 'Sending...' : 'Send Message';
        }
        if (emifreeIconIdle) {
            emifreeIconIdle.classList.toggle('hidden', emifreeIsSubmitting);
        }
        if (emifreeIconLoading) {
            emifreeIconLoading.classList.toggle('hidden', !emifreeIsSubmitting);
        }
    }

    // ----- Blur validation per field -----
    emifreeFields.forEach((emifreeField) => {
        emifreeField.addEventListener('blur', () => {
            const emifreeName = emifreeField.getAttribute('data-emifree-contact-field');
            emifreeValidateField(emifreeName);
        });
    });

    // ----- Submit handler -----
    emifreeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        emifreeHideResult();
        if (!emifreeValidateAll()) {
            return;
        }

        emifreeSetSubmitting(true);
        try {
            const emifreeResponse = await fetch(window.emifreeContact.ajaxUrl, {
                method: 'POST',
                body: new FormData(emifreeForm),
            });
            let emifreeData = null;
            try {
                emifreeData = await emifreeResponse.json();
            } catch (emifreeParseErr) {
                emifreeData = null;
            }

            if (emifreeData && emifreeData.success) {
                const emifreeMsg = (emifreeData.data && emifreeData.data.message) || window.emifreeContact.successMsg;
                emifreeShowResult('success', emifreeMsg);
                emifreeForm.reset();
                // Re-anchor the timestamp after reset so a second submission
                // (after a successful first one, or after the user navigates
                // back) starts the elapsed-time measurement fresh. Without
                // this, form.reset() restores the original SSR value (or the
                // pre-DOMContentLoaded server time), which would inflate
                // elapsed time and risk hitting the EMIFREE_CONTACT_MAX
                // ceiling on long-running tabs.
                if (emifreeTsInput) {
                    emifreeTsInput.value = String(Math.floor(Date.now() / 1000));
                }
                emifreeFields.forEach((emifreeField) => {
                    emifreeClearFieldError(emifreeField.getAttribute('data-emifree-contact-field'));
                });
                // Auto-dismiss the success banner after 5 s (mirrors toast fade).
                setTimeout(() => {
                    emifreeHideResult();
                }, 5000);
            } else {
                const emifreeMsg = (emifreeData && emifreeData.data && emifreeData.data.message) || window.emifreeContact.errorMsg;
                emifreeShowResult('error', emifreeMsg);
                // Surface any per-field errors that the server returned.
                if (emifreeData && emifreeData.data && emifreeData.data.fields) {
                    Object.entries(emifreeData.data.fields).forEach(([emifreeFieldName, emifreeFieldMsg]) => {
                        emifreeShowFieldError(emifreeFieldName, emifreeFieldMsg);
                    });
                }
            }
        } catch (emifreeErr) {
            emifreeShowResult('error', window.emifreeContact.errorMsg);
        } finally {
            emifreeSetSubmitting(false);
        }
    });
})();