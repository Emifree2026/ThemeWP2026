/**
 * Emifree Contact — per-section behaviors.
 *
 *  - Client-side validation on blur + submit. Mirrors the React's
 *    Zod schema: name ≥ 2, valid email regex, company ≥ 2,
 *    message ≥ 10. Inline error rendering via data-emifree-contact-error
 *    paragraphs + red border on the offending input.
 *  - Submit handler: fetch to admin-ajax.php (URL provided by
 *    wp_localize_script as emifreeContact.ajaxUrl). The form's action
 *    attribute already has the ?action=send_contact query string, so
 *    we POST the FormData as-is. On success, show the success banner,
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