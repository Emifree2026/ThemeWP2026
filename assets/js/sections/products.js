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

	const emifreeTabs   = document.querySelectorAll( '[data-emifree-tab]' );
	const emifreePanels = document.querySelectorAll( '[data-emifree-panel]' );

	if ( ! emifreeTabs.length ) {
		return;
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
			setTimeout( () => {
				if ( ! document.getElementById( 'emifree-inquiry-modal' ) ) {
					const emifreeContact = document.getElementById( 'contact' );
					if ( emifreeContact ) {
						emifreeContact.scrollIntoView( { behavior: 'smooth' } );
					}
				}
			}, 50 );
		} );
	} );
})();