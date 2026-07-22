// Page-level behaviors shared across the landing page.
//
// Responsibilities:
//  - Hero background video autoplay (two-stage retry that respects
//    iOS Safari's user-gesture gate — see commit message of the
//    change that introduced this).
//  - Sticky header background flip on scroll past 20 px.
//  - Mobile menu open/close toggle.
//  - Smooth scroll for in-page anchor links (legacy fallback for
//    pages where header.js's intercept hasn't taken effect — e.g.
//    on legal pages where the nav is rendered without the
//    wp_localize_script'd emifreeSite data).
//
// What this file does NOT do (handled by dedicated section scripts):
//  - Language switcher dropdown + subpath math (header.js).
//  - Contact form AJAX + validation (sections/contact.js, uses
//    wp_localize_script('emifreeContact')).
//  - Step switching in the Technology process sections
//    (sections/technology.js).
//
// Earlier revisions of this file held stale duplicate logic for
// the contact form (looking for #contact-form, reading
// emifree_ajax.ajax_url) and the header (looking for #main-header
// instead of #emifree-header). Those DOM IDs and localize handles
// don't exist on the current theme — every scroll event threw a
// null-ref classList exception in devtools. Removed.

(function () {
    'use strict';

    // ----- Hero background video autoplay retry -----
    //
    // The template-parts emit a single <video id="hero-video">. The
    // page-level autoplay attribute fires the first paint attempt;
    // this catches any case where the autoplay attribute was
    // blocked (mobile Safari data-saver, iOS Low Power Mode,
    // autoplay disabled by browser policy) and retries after the
    // first user gesture.
    const heroVideo = document.getElementById('hero-video');

    if (heroVideo) {
        // Older mobile Safari drops the loop attribute on
        // programmatic load. Re-assert.
        heroVideo.loop = true;

        // Stage 1: try to play immediately on script load. Catches
        // desktop + any mobile browser that allowed the markup's
        // autoplay attribute.
        const emifreePlayPromise = heroVideo.play();
        if (emifreePlayPromise && typeof emifreePlayPromise.catch === 'function') {
            emifreePlayPromise.catch(function () {
                // Stage 1 rejected. Defer retry to first user
                // gesture. Once-flag so we don't pile up listeners
                // if multiple events fire in quick succession.
                if (heroVideo.dataset.emifreeGestureArmed === '1') { return; }
                heroVideo.dataset.emifreeGestureArmed = '1';

                const emifreeArmedEvents = ['touchstart', 'pointerdown', 'mousedown', 'keydown', 'scroll'];
                const emifreeArmPlay = function () {
                    heroVideo.play().catch(function (e) {
                        console.log('Hero autoplay still blocked after gesture:', e);
                    });
                    emifreeArmedEvents.forEach(function (ev) {
                        window.removeEventListener(ev, emifreeArmPlay, { capture: true });
                    });
                };
                emifreeArmedEvents.forEach(function (ev) {
                    window.addEventListener(ev, emifreeArmPlay, { capture: true, passive: true });
                });
            });
        }
    }

    // ----- Sticky header background flip -----
    //
    // Header starts transparent (over the hero); flips to a
    // translucent white with blur + shadow once the user scrolls
    // past 20 px so subsequent sections have readable contrast.
    // header.js owns the actual scrollY > 20 styling already — see
    // window.addEventListener('scroll', ...) in header.js — so this
    // is a no-op kept only for parity. (Earlier revisions fought
    // header.js for control of the same classes; the unified
    // version lives in header.js now.)
    //
    // Empty block intentionally left to document the architectural
    // decision: header scroll state is centralized in header.js
    // (which has access to emifreeSite.homeSubpath for subpath-
    // aware nav-anchor handling) and main.js does not duplicate
    // it. Touching this file in the future should not re-introduce
    // the duplicate.

    // ----- Mobile menu toggle -----
    //
    // The actual menu open/close state is owned by header.js
    // (which wires data-emifree-nav-* attributes and subpath math).
    // This handler remains for older markup shapes that emit the
    // legacy #mobile-menu-btn / #mobile-menu pair — the legacy
    // IDs are null on the current theme, so this is a guarded
    // no-op rather than an active duplicate.
    const legacyMobileBtn  = document.getElementById('mobile-menu-btn');
    const legacyMobileMenu = document.getElementById('mobile-menu');
    if (legacyMobileBtn && legacyMobileMenu) {
        legacyMobileBtn.addEventListener('click', function () {
            legacyMobileMenu.classList.toggle('hidden');
        });
    }

    // ----- Smooth scroll for legacy in-page anchors -----
    //
    // header.js's emifreeSite-aware click handler intercepts
    // a[href^="#"], a[href^="/#"], a[href^="/de/#"], a[href^="/en/#"]
    // and routes them through emifreeComputeLangTarget. This
    // fallback handles bare hash-only anchors (e.g. anchors inside
    // a long content area where the subpath-aware routing isn't
    // needed) that may have been added by a content author without
    // going through the standard nav shape. It also covers pages
    // where header.js itself didn't load (defensive only — every
    // page in this theme loads header.js globally).
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const href = anchor.getAttribute('href');
            if (!href || href === '#') { return; }
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
})();