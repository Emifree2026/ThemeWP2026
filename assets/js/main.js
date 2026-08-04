// Page-level behaviors shared across the landing page.
//
// Responsibilities:
//  - Hero background video carousel — two <video> elements cross-fade
//    on the `ended` event; first paint + iOS gesture gate retry.
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

    // ----- Hero background video carousel -----
    //
    // The hero template (EN + DE) emits two <video> elements with the
    // shared class `emifree-hero-video`. Both render at the same
    // z-index and start opacity-0; this controller promotes one to
    // active (opacity-100 via the `.emifree-hero-video--active` class)
    // and, when the active video fires `ended`, fades it out and
    // fades the other in. Cross-fade is a CSS opacity transition
    // (`transition-opacity duration-1000` on the markup), so the
    // controller only flips the class.
    //
    // Why no autoplay/loop attributes on the markup? Autoplay on two
    // sibling videos caused the old "old-video flash + new-video
    // flash" symptom (both started simultaneously and the painted one
    // flipped during the load). Now only the active video plays at
    // any moment; the other sits paused at its current frame so the
    // handoff is seamless.
    //
    // Why no poster? Earlier revisions set poster="emilogo.png" and
    // the static logo flashed during decode on every page load. The
    // browser keeps the last decoded frame in place until the next
    // one is ready, so no poster is the right default.
    //
    // The first-paint gesture retry is retained from the previous
    // implementation: iOS Safari gates autoplay on user interaction
    // for any video that wasn't triggered by an explicit gesture.
    // On the first user gesture we kick the active video; subsequent
    // swaps don't need a fresh gesture because the previous `play()`
    // call already established the gesture context.
    const heroVideos = Array.from(document.querySelectorAll('.emifree-hero-video'));

    if (heroVideos.length === 2) {
        // Track which index is currently visible. starts at 0 so the
        // first <video> in document order (hero-video-primary in the
        // template — the new "Video Project hero.mp4") plays first.
        let emifreeActiveIndex = 0;
        const emifreeOtherIndex = function (i) { return 1 - i; };

        const emifreeSetActive = function (nextIndex) {
            // Cross-fade: the active class is the ONLY thing that
            // changes per swap. CSS transition on the markup handles
            // the 1 s opacity ease. After the swap, kick playback on
            // the now-visible video.
            const next = heroVideos[nextIndex];
            const prev = heroVideos[emifreeActiveIndex];

            prev.classList.remove('emifree-hero-video--active');
            next.classList.add('emifree-hero-video--active');

            // Reset the previous video to the start so its next
            // playback is a clean run-through (otherwise an `ended`
            // video sitting at the end of its timeline would either
            // replay from the wrong point or refuse to play).
            try { prev.currentTime = 0; } catch (e) { /* noop */ }

            emifreeActiveIndex = nextIndex;

            const emifreePlayPromise = next.play();
            if (emifreePlayPromise && typeof emifreePlayPromise.catch === 'function') {
                emifreePlayPromise.catch(function (e) {
                    console.log('Hero carousel video blocked from playing:', e);
                });
            }
        };

        heroVideos.forEach(function (v, idx) {
            v.addEventListener('ended', function () {
                emifreeSetActive(emifreeOtherIndex(idx));
            });
        });

        // First paint: promote video 0 to active and attempt to play.
        // If the browser blocks it (iOS, data-saver, etc.), arm a
        // one-shot gesture listener for the first user interaction.
        heroVideos[0].classList.add('emifree-hero-video--active');
        const emifreeFirstPlay = heroVideos[0].play();
        if (emifreeFirstPlay && typeof emifreeFirstPlay.catch === 'function') {
            emifreeFirstPlay.catch(function () {
                if (heroVideos[0].dataset.emifreeGestureArmed === '1') { return; }
                heroVideos[0].dataset.emifreeGestureArmed = '1';

                const emifreeArmedEvents = ['touchstart', 'pointerdown', 'mousedown', 'keydown', 'scroll'];
                const emifreeArmPlay = function () {
                    heroVideos[0].play().catch(function (e) {
                        console.log('Hero carousel autoplay still blocked after gesture:', e);
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
    } else if (heroVideos.length === 1) {
        // Defensive fallback: if a future revision ships only one
        // video, keep the original autoplay behavior so the hero
        // doesn't render a black box.
        const solo = heroVideos[0];
        solo.classList.add('emifree-hero-video--active');
        const soloPlay = solo.play();
        if (soloPlay && typeof soloPlay.catch === 'function') {
            soloPlay.catch(function () { /* swallow */ });
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

    // ----- Smooth scroll for in-page anchors -----
    //
    // header.js owns the click handler for every shape the theme
    // emits (#anchor, /#anchor, /de/#anchor, /en/#anchor). Earlier
    // revisions of this file held a duplicate a[href^="#"] handler
    // that ran alongside header.js's handler on every nav click,
    // forcing layout twice (once via document.querySelector +
    // scrollIntoView here, once via getBoundingClientRect() +
    // scrollTo() in header.js) and inflating Interaction to Next
    // Paint to ~240 ms. Removed — header.js is the single source of
    // truth for smooth-scroll behavior, and adding a second handler
    // for "bare hash" anchors is not needed because every navigation
    // anchor in this theme uses one of the four shapes header.js
    // already covers.
    //
    // If a future content author needs a bare-fragment anchor that
    // header.js doesn't handle, add the selector to header.js's
    // querySelectorAll call rather than resurrecting this block.
})();