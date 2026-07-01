/**
 * Emifree Products — per-section behaviors.
 *
 *  - Tab switching: click a tab button, show its panel, hide the others.
 *  - Active-image cycling inside a panel: click a thumb, show the
 *    matching full image, mark the thumb as active.
 *
 * Both driven by data-emifree-* attributes on the markup rather than
 * brittle positional indexing, so future section reorderings don't
 * break the JS.
 */

(function () {
	'use strict';

	// Scope tab/panel queries to #products so we never act on Knowledge's
	// tabs (which share the same data-emifree-* attribute names). Without
	// this, a click on a Knowledge tab would also fire here and toggle
	// Products panels off, breaking the visible state.
	const emifreeRoot = document.getElementById( 'products' );
	if ( ! emifreeRoot ) {
		return;
	}

	const emifreeTabs   = emifreeRoot.querySelectorAll( '[data-emifree-tab]' );
	const emifreePanels = emifreeRoot.querySelectorAll( '[data-emifree-panel]' );

	if ( ! emifreeTabs.length ) {
		return;
	}

	// ---- Auto-advance gallery setup (mirrors React Products.jsx:113-119) ----
	// Opt-in only: skipped entirely if the user has prefers-reduced-motion
	// enabled. Each [data-emifree-gallery] runs its own 4 s interval; the
	// interval is paused on mouseenter / focusin (so hovering a thumb
	// or keyboard-navigating into the gallery doesn't fight the user) and
	// resumed on mouseleave / focusout. emifreeStartAuto / emifreeStopAuto
	// are exposed via `let` bindings so the thumbnail click handler can
	// reset the timer (emifreeRestartAuto) and the bottom wiring block
	// can use the same stop helper.
	const emifreeReducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const emifreeAutoState = emifreeReducedMotion ? null : new WeakMap();
	let emifreeStartAuto = () => {};
	let emifreeStopAuto = () => {};
	let emifreeRestartAuto = () => {};

	if ( ! emifreeReducedMotion ) {
		emifreeStopAuto = ( emifreeGallery ) => {
			const emifreeHandle = emifreeAutoState.get( emifreeGallery );
			if ( emifreeHandle ) {
				clearInterval( emifreeHandle );
				emifreeAutoState.delete( emifreeGallery );
			}
		};

		emifreeStartAuto = ( emifreeGallery ) => {
			emifreeStopAuto( emifreeGallery );
			const emifreeImages = emifreeGallery.querySelectorAll( '[data-emifree-image]' );
			if ( emifreeImages.length < 2 ) {
				return;
			}
			const emifreeHandle = setInterval( () => {
				// Find the currently-visible image in this gallery. A
				// hidden tab means all its images are display:none, in
				// which case we skip this tick — only the visible panel
				// advances.
				const emifreeActive = emifreeGallery.querySelector( '[data-emifree-image]:not(.hidden)' );
				if ( ! emifreeActive ) {
					return;
				}
				const emifreeCurrent = parseInt( emifreeActive.getAttribute( 'data-emifree-image' ), 10 );
				const emifreeNext = ( emifreeCurrent + 1 ) % emifreeImages.length;
				const emifreeNextThumb = emifreeGallery.querySelector( '[data-emifree-thumb="' + emifreeNext + '"]' );
				if ( emifreeNextThumb ) {
					emifreeNextThumb.click();
				}
			}, 4000 );
			emifreeAutoState.set( emifreeGallery, emifreeHandle );
		};

		emifreeRestartAuto = ( emifreeGallery ) => {
			emifreeStartAuto( emifreeGallery );
		};
	}

	// ---- Tab switching ----
	emifreeTabs.forEach( ( emifreeTab ) => {
		emifreeTab.addEventListener( 'click', () => {
			const emifreeTarget = emifreeTab.getAttribute( 'data-emifree-tab' );

			emifreeTabs.forEach( ( emifreeOtherTab ) => {
				const emifreeIsActive = emifreeOtherTab === emifreeTab;
				emifreeOtherTab.setAttribute( 'aria-selected', emifreeIsActive ? 'true' : 'false' );
				emifreeOtherTab.classList.toggle( 'bg-blue-700', emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'text-white', emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'shadow-lg', emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'bg-white', ! emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'text-zinc-600', ! emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'border', ! emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'border-slate-200', ! emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'hover:bg-slate-100', ! emifreeIsActive );
				emifreeOtherTab.classList.toggle( 'hover:text-blue-700', ! emifreeIsActive );
			} );

			emifreePanels.forEach( ( emifreePanel ) => {
				emifreePanel.classList.toggle(
					'hidden',
					emifreePanel.getAttribute( 'data-emifree-panel' ) !== emifreeTarget
				);
			} );
		} );
	} );

	// ---- Thumbnail switching inside each panel ----
	document.querySelectorAll( '[data-emifree-gallery]' ).forEach( ( emifreeGallery ) => {
		const emifreeImages = emifreeGallery.querySelectorAll( '[data-emifree-image]' );
		const emifreeThumbs = emifreeGallery.querySelectorAll( '[data-emifree-thumb]' );

		emifreeThumbs.forEach( ( emifreeThumb ) => {
			emifreeThumb.addEventListener( 'click', () => {
				const emifreeIndex = emifreeThumb.getAttribute( 'data-emifree-thumb' );

				emifreeImages.forEach( ( emifreeImg ) => {
					emifreeImg.classList.toggle(
						'hidden',
						emifreeImg.getAttribute( 'data-emifree-image' ) !== emifreeIndex
					);
				} );

				emifreeThumbs.forEach( ( emifreeOther ) => {
					const emifreeIsActive = emifreeOther === emifreeThumb;
					emifreeOther.classList.toggle( 'ring-2', emifreeIsActive );
					emifreeOther.classList.toggle( 'ring-blue-700', emifreeIsActive );
					emifreeOther.classList.toggle( 'ring-offset-2', emifreeIsActive );
					emifreeOther.classList.toggle( 'scale-105', emifreeIsActive );
					emifreeOther.classList.toggle( 'opacity-70', ! emifreeIsActive );
				} );

				// Reset the auto-advance timer so a manual click doesn't
				// get immediately overridden by an in-flight tick.
				emifreeRestartAuto( emifreeGallery );
			} );
		} );
	} );

	// ---- Inquiry CTA — opens the modal from Piece 10, or scrolls to
	// #contact as a graceful fallback if the modal hasn't shipped yet.
	document.querySelectorAll( '[data-emifree-inquiry]' ).forEach( ( emifreeCta ) => {
		emifreeCta.addEventListener( 'click', () => {
			const emifreeProductType = emifreeCta.getAttribute( 'data-emifree-inquiry' );
			const emifreeEvent = new CustomEvent( 'emifree:open-inquiry', {
				detail: { productType: emifreeProductType },
			} );
			window.dispatchEvent( emifreeEvent );

			// Fallback: scroll to #contact. The modal handler (Piece 10)
			// will call preventDefault() on the event to swallow this.
			// requestAnimationFrame survives iOS scroll where setTimeout
			// is throttled.
			requestAnimationFrame( () => {
				if ( document.getElementById( 'emifree-inquiry-modal' ) ) {
					return;
				}
				const emifreeContact = document.getElementById( 'contact' );
				if ( emifreeContact ) {
					emifreeContact.scrollIntoView( { behavior: 'smooth' } );
				}
			} );
		} );
	} );

	// ---- Auto-advance: kick off each gallery's interval + wire
	// mouseenter/mouseleave/focusin/focusout to pause/resume.
	if ( ! emifreeReducedMotion ) {
		document.querySelectorAll( '[data-emifree-gallery]' ).forEach( ( emifreeGallery ) => {
			emifreeStartAuto( emifreeGallery );
			emifreeGallery.addEventListener( 'mouseenter', () => emifreeStopAuto( emifreeGallery ) );
			emifreeGallery.addEventListener( 'mouseleave', () => emifreeStartAuto( emifreeGallery ) );
			emifreeGallery.addEventListener( 'focusin', () => emifreeStopAuto( emifreeGallery ) );
			emifreeGallery.addEventListener( 'focusout', () => emifreeStartAuto( emifreeGallery ) );
		} );
	}
})();