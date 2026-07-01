/**
 * Emifree Technology — per-section behaviors.
 *
 *  - Step switching inside each ProcessSection: clicking a step button
 *    swaps the active image and caption via .hidden toggling, and
 *    updates the active state styling on the button list. Two variant
 *    lists are rendered (mobile horizontal pills, desktop vertical
 *    rows); the JS keeps them in sync by routing all clicks through a
 *    shared "active step index" on the parent [data-emifree-process].
 *  - Mobile / desktop step-list visibility: the ProcessSection renders
 *    both variants in the DOM and toggles which one is visible based
 *    on a 767 px breakpoint (matching the React isMobile state).
 *  - In-section "Learn How It Works →" anchors smooth-scroll to their
 *    target ProcessSection with header-height + 8 px offset, mirroring
 *    the React scrollToElement() helper so the sticky header never
 *    occludes the section title.
 *  - "Get expert recommendation" CTA dispatches emifree:open-inquiry
 *    on window with { technologyType: 'technology' }, with a 50 ms
 *    fallback smooth-scroll to #contact if the modal hasn't shipped
 *    yet (Piece 10).
 *
 * Loaded only when the Technology section is present on the page.
 * See functions.php for the enqueue helper.
 */

(function () {
    'use strict';

    const emifreeProcesses = document.querySelectorAll('[data-emifree-process]');
    if (!emifreeProcesses.length) {
        return;
    }

    const emifreeMobileQuery = window.matchMedia('(max-width: 767px)');

    /**
     * Toggle which step-list variant is visible per process.
     * Desktop: `.flex`, mobile: `.flex` inside `.overflow-x-auto`.
     * We just toggle a `.hidden` utility on the inactive variant —
     * the active variant keeps its default display.
     */
    function emifreeSyncStepListVisibility() {
        emifreeProcesses.forEach((emifreeProcess) => {
            const emifreeDesktop = emifreeProcess.querySelector('[data-emifree-step-list="desktop"]');
            const emifreeMobile = emifreeProcess.querySelector('[data-emifree-step-list="mobile"]');
            if (emifreeMobileQuery.matches) {
                if (emifreeDesktop) {
                    emifreeDesktop.classList.add('hidden');
                }
                if (emifreeMobile) {
                    emifreeMobile.classList.remove('hidden');
                }
            } else {
                if (emifreeDesktop) {
                    emifreeDesktop.classList.remove('hidden');
                }
                if (emifreeMobile) {
                    emifreeMobile.classList.add('hidden');
                }
            }
        });
    }

    emifreeSyncStepListVisibility();

    // The matchMedia listener uses the modern addEventListener form when
    // available, falling back to the legacy addListener for older Safari.
    if (typeof emifreeMobileQuery.addEventListener === 'function') {
        emifreeMobileQuery.addEventListener('change', emifreeSyncStepListVisibility);
    } else if (typeof emifreeMobileQuery.addListener === 'function') {
        emifreeMobileQuery.addListener(emifreeSyncStepListVisibility);
    }

    /**
     * Set the active step inside one process: update parent data attr,
     * toggle .hidden on images + captions, swap active button styles.
     */
    function emifreeSetActiveStep(emifreeProcess, emifreeStepIndex) {
        emifreeProcess.setAttribute('data-active-step', String(emifreeStepIndex));

        emifreeProcess.querySelectorAll('[data-emifree-image]').forEach((emifreeImg) => {
            const emifreeMatch = emifreeImg.getAttribute('data-emifree-image') === String(emifreeStepIndex);
            emifreeImg.classList.toggle('hidden', !emifreeMatch);
        });

        emifreeProcess.querySelectorAll('[data-emifree-step-caption]').forEach((emifreeCaption) => {
            const emifreeMatch = emifreeCaption.getAttribute('data-emifree-step-caption') === String(emifreeStepIndex);
            emifreeCaption.classList.toggle('hidden', !emifreeMatch);
        });

        emifreeProcess.querySelectorAll('[data-emifree-step]').forEach((emifreeBtn) => {
            const emifreeMatch = emifreeBtn.getAttribute('data-emifree-step') === String(emifreeStepIndex);
            const emifreeVariant = emifreeBtn.getAttribute('data-emifree-step-variant');
            if (emifreeVariant === 'desktop') {
                emifreeBtn.classList.remove('bg-blue-50', 'font-semibold', 'text-blue-800', 'hover:bg-slate-50', 'text-slate-600');
                if (emifreeMatch) {
                    emifreeBtn.classList.add('bg-blue-50', 'font-semibold', 'text-blue-800');
                } else {
                    emifreeBtn.classList.add('hover:bg-slate-50', 'text-slate-600');
                }
            } else if (emifreeVariant === 'mobile') {
                emifreeBtn.classList.remove('bg-blue-600', 'text-white', 'bg-slate-100', 'text-slate-700', 'hover:bg-slate-200');
                if (emifreeMatch) {
                    emifreeBtn.classList.add('bg-blue-600', 'text-white');
                } else {
                    emifreeBtn.classList.add('bg-slate-100', 'text-slate-700', 'hover:bg-slate-200');
                }
            }
        });
    }

    // Wire step buttons inside each process.
    emifreeProcesses.forEach((emifreeProcess) => {
        emifreeProcess.querySelectorAll('[data-emifree-step]').forEach((emifreeBtn) => {
            emifreeBtn.addEventListener('click', () => {
                const emifreeStepIndex = emifreeBtn.getAttribute('data-emifree-step');
                emifreeSetActiveStep(emifreeProcess, emifreeStepIndex);
            });
        });
    });

    // Smooth-scroll for "Learn How It Works →" buttons. Mirrors the
    // React scrollToElement helper: offset = header height + 8 px.
    document.querySelectorAll('[data-emifree-tech-anchor]').forEach((emifreeAnchor) => {
        emifreeAnchor.addEventListener('click', () => {
            const emifreeTargetId = emifreeAnchor.getAttribute('data-emifree-tech-anchor');
            const emifreeTarget = document.getElementById(emifreeTargetId);
            if (!emifreeTarget) {
                return;
            }
            const emifreeHeader = document.querySelector('header');
            const emifreeHeaderHeight = emifreeHeader ? emifreeHeader.offsetHeight : 64;
            const emifreeOffset = emifreeHeaderHeight + 8;
            const emifreeTargetTop = emifreeTarget.getBoundingClientRect().top + window.pageYOffset - emifreeOffset;
            window.scrollTo({ top: emifreeTargetTop, behavior: 'smooth' });
        });
    });

    // Inquiry CTA — opens the modal from Piece 10, or scrolls to
    // #contact as a graceful fallback if the modal hasn't shipped yet.
    document.querySelectorAll('[data-emifree-inquiry="technology"]').forEach((emifreeCta) => {
        emifreeCta.addEventListener('click', () => {
            const emifreeEvent = new CustomEvent('emifree:open-inquiry', {
                detail: { technologyType: 'technology' },
            });
            window.dispatchEvent(emifreeEvent);

            setTimeout(() => {
                if (!document.getElementById('emifree-inquiry-modal')) {
                    const emifreeContact = document.getElementById('contact');
                    if (emifreeContact) {
                        emifreeContact.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }, 50);
        });
    });
})();
