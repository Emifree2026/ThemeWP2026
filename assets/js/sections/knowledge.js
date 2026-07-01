/**
 * Emifree Knowledge — per-section behaviors.
 *
 *  - Tab switching inside the Knowledge section: clicking a tab swaps
 *    the visible panel. Each [data-emifree-tab] is associated with a
 *    [data-emifree-panel] of the same key, but ONLY inside the
 *    #knowledge section — products.js owns the same data-emifree-*
 *    attributes for its own section, so a click would otherwise toggle
 *    the wrong panel. We scope every read/write to elements inside
 *    #knowledge to keep the two sections independent.
 *
 * Loaded only when the Knowledge section is present on the page.
 * See functions.php for the enqueue helper.
 */

(function () {
    'use strict';

    const emifreeRoot = document.getElementById('knowledge');
    if (!emifreeRoot) {
        return;
    }

    const emifreeTabs = emifreeRoot.querySelectorAll('[data-emifree-tab]');
    const emifreePanels = emifreeRoot.querySelectorAll('[data-emifree-panel]');

    if (!emifreeTabs.length) {
        return;
    }

    emifreeTabs.forEach((emifreeTab) => {
        emifreeTab.addEventListener('click', () => {
            const emifreeTarget = emifreeTab.getAttribute('data-emifree-tab');

            emifreeTabs.forEach((emifreeOther) => {
                const emifreeIsActive = emifreeOther === emifreeTab;
                emifreeOther.setAttribute('aria-selected', emifreeIsActive ? 'true' : 'false');
                emifreeOther.classList.toggle('bg-blue-700', emifreeIsActive);
                emifreeOther.classList.toggle('text-white', emifreeIsActive);
                emifreeOther.classList.toggle('shadow-lg', emifreeIsActive);
                emifreeOther.classList.toggle('bg-white', !emifreeIsActive);
                emifreeOther.classList.toggle('text-slate-600', !emifreeIsActive);
                emifreeOther.classList.toggle('border', !emifreeIsActive);
                emifreeOther.classList.toggle('border-slate-200', !emifreeIsActive);
                emifreeOther.classList.toggle('hover:bg-slate-100', !emifreeIsActive);
                emifreeOther.classList.toggle('hover:text-blue-700', !emifreeIsActive);
            });

            emifreePanels.forEach((emifreePanel) => {
                emifreePanel.classList.toggle(
                    'hidden',
                    emifreePanel.getAttribute('data-emifree-panel') !== emifreeTarget
                );
            });
        });
    });
})();
