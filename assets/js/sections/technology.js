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
 *  - Auto-cycling carousel: when the user lands on the section, each
 *    process panel cycles through its step images on a 5 s timer
 *    (configurable via data-emifree-cycle-ms). Cycle pauses on hover,
 *    focus-within, manual click, or prefers-reduced-motion, and only
 *    runs while the panel is in the viewport (IntersectionObserver).
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

    // 767.98px aligns with Tailwind's md: breakpoint (min-width: 768px),
    // so a viewport right at 768px is treated the same by JS and CSS.
    const emifreeMobileQuery = window.matchMedia('(max-width: 767.98px)');

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

    // Wire step buttons inside each process. Clicks stop the auto-cycle
    // for a short cooldown so the user can read what they chose; the
    // timer resumes automatically.
    emifreeProcesses.forEach((emifreeProcess) => {
        emifreeProcess.querySelectorAll('[data-emifree-step]').forEach((emifreeBtn) => {
            emifreeBtn.addEventListener('click', () => {
                const emifreeStepIndex = emifreeBtn.getAttribute('data-emifree-step');
                emifreeSetActiveStep(emifreeProcess, emifreeStepIndex);
                if (emifreeProcess.__emifreeCarousel) {
                    emifreeProcess.__emifreeCarousel.userInteracted();
                }
            });
        });
    });

    /**
     * Auto-cycle a single process panel. Implements:
     *  - per-panel timer that advances to (current + 1) % stepCount
     *  - pause on mouseenter / focusin
     *  - resume on mouseleave / focusout after a small delay
     *  - pause while not in viewport (IntersectionObserver)
     *  - skip entirely if prefers-reduced-motion is set
     *  - skip if the panel has only 1 step
     */
    function emifreeMakeCarousel(emifreeProcess) {
        const emifreeStepCount = emifreeProcess.querySelectorAll('[data-emifree-image]').length;
        if (emifreeStepCount < 2) {
            return null;
        }

        // Respect the user's OS-level motion preference. Users who set
        // "reduce motion" don't get auto-advancing imagery — they keep
        // full manual control via the step buttons.
        const emifreeReducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (emifreeReducedMotionQuery.matches) {
            return null;
        }

        const emifreeInterval = parseInt(emifreeProcess.getAttribute('data-emifree-cycle-ms') || '5000', 10);
        let emifreeTimer = null;
        let emifreeInView = false;
        let emifreePaused = false;
        // After a manual click, hold the cycle for 2x the normal
        // interval so the user can read what they chose without the
        // carousel jumping away.
        let emifreeCooldownUntil = 0;

        function emifreeStart() {
            if (emifreeTimer) {
                return;
            }
            emifreeTimer = window.setInterval(() => {
                if (emifreePaused || !emifreeInView) {
                    return;
                }
                if (Date.now() < emifreeCooldownUntil) {
                    return;
                }
                const emifreeCurrent = parseInt(emifreeProcess.getAttribute('data-active-step') || '0', 10);
                const emifreeNext = (emifreeCurrent + 1) % emifreeStepCount;
                emifreeSetActiveStep(emifreeProcess, emifreeNext);
            }, emifreeInterval);
        }

        function emifreeStop() {
            if (emifreeTimer) {
                window.clearInterval(emifreeTimer);
                emifreeTimer = null;
            }
        }

        // Pause-on-hover / focus-within.
        emifreeProcess.addEventListener('mouseenter', () => { emifreePaused = true; });
        emifreeProcess.addEventListener('mouseleave', () => { emifreePaused = false; });
        emifreeProcess.addEventListener('focusin', () => { emifreePaused = true; });
        emifreeProcess.addEventListener('focusout', () => { emifreePaused = false; });

        // IntersectionObserver — only cycle when the panel is on screen.
        // threshold 0.35 means "at least 35% visible" — once the user
        // scrolls past, we stop the timer (saves cycles, prevents
        // off-screen step transitions the user never sees).
        if ('IntersectionObserver' in window) {
            const emifreeObserver = new IntersectionObserver(
                (emifreeEntries) => {
                    emifreeEntries.forEach((emifreeEntry) => {
                        emifreeInView = emifreeEntry.isIntersecting;
                    });
                },
                { threshold: 0.35 }
            );
            emifreeObserver.observe(emifreeProcess);
        } else {
            // Very old browser without IntersectionObserver — default
            // to always-cycling rather than never-cycling.
            emifreeInView = true;
        }

        emifreeStart();

        return {
            userInteracted: function () {
                emifreeCooldownUntil = Date.now() + (emifreeInterval * 2);
            },
            stop: emifreeStop,
        };
    }

    // Build a carousel for each process panel. Stored on the DOM node
    // itself (via a __emifreeCarousel property) so the click handler
    // above can call userInteracted() without re-querying.
    emifreeProcesses.forEach((emifreeProcess) => {
        emifreeProcess.__emifreeCarousel = emifreeMakeCarousel(emifreeProcess);
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

            // requestAnimationFrame survives iOS scroll where setTimeout
            // is throttled; we still check the modal exists before
            // falling back.
            requestAnimationFrame(() => {
                if (document.getElementById('emifree-inquiry-modal')) {
                    return;
                }
                const emifreeContact = document.getElementById('contact');
                if (emifreeContact) {
                    emifreeContact.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    });
})();