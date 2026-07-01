/**
 * Emifree Header — per-section behaviors.
 *
 *  - Sticky-on-scroll: switch the fixed header's background from
 *    transparent over the hero to white + backdrop blur once the user
 *    scrolls past the hero threshold. This mirrors the React Header's
 *    sticky-on-scroll behavior with a backdrop-blur pill.
 *  - Mobile menu toggle: open/close the mobile dropdown + swap the
 *    hamburger / X icon + set aria-expanded.
 *  - Language selector: open the dropdown, set aria-expanded, swap
 *    the label, and close on outside click.
 *
 * Loaded only when the header is present on the page. See
 * functions.php for enqueue.
 */

(function () {
	'use strict';

	const emifreeHeader = document.getElementById('emifree-header');
	if ( ! emifreeHeader ) {
		return;
	}

	// ---- Sticky-on-scroll: backdrop blur past threshold ----
	// Throttled via requestAnimationFrame so iOS Safari scroll (~60 events
	// per second) doesn't trigger 60 classList mutations per second.
	const emifreeHeaderThreshold = 20;
	let emifreeScrollTicking = false;
	const emifreeOnScroll = () => {
		if ( emifreeScrollTicking ) {
			return;
		}
		emifreeScrollTicking = true;
		requestAnimationFrame( () => {
			emifreeScrollTicking = false;
			if ( window.scrollY > emifreeHeaderThreshold ) {
				emifreeHeader.classList.add( 'bg-white/95', 'backdrop-blur-md', 'shadow-lg' );
				emifreeHeader.classList.remove( 'bg-white' );
			} else {
				emifreeHeader.classList.add( 'bg-white' );
				emifreeHeader.classList.remove( 'bg-white/95', 'backdrop-blur-md', 'shadow-lg' );
			}
		} );
	};
	window.addEventListener( 'scroll', emifreeOnScroll, { passive: true } );
	emifreeOnScroll();

	// ---- Mobile menu toggle ----
	const emifreeMobileBtn   = document.getElementById( 'emifree-mobile-menu-btn' );
	const emifreeMobileMenu  = document.getElementById( 'emifree-mobile-menu' );
	const emifreeIconOpen    = document.getElementById( 'emifree-mobile-menu-icon-open' );
	const emifreeIconClose   = document.getElementById( 'emifree-mobile-menu-icon-close' );

	if ( emifreeMobileBtn && emifreeMobileMenu ) {
		emifreeMobileBtn.addEventListener( 'click', () => {
			const emifreeIsOpen = ! emifreeMobileMenu.classList.contains( 'hidden' );
			if ( emifreeIsOpen ) {
				emifreeMobileMenu.classList.add( 'hidden' );
				emifreeMobileBtn.setAttribute( 'aria-expanded', 'false' );
				if ( emifreeIconOpen )  emifreeIconOpen.classList.remove( 'hidden' );
				if ( emifreeIconClose ) emifreeIconClose.classList.add( 'hidden' );
			} else {
				emifreeMobileMenu.classList.remove( 'hidden' );
				emifreeMobileBtn.setAttribute( 'aria-expanded', 'true' );
				if ( emifreeIconOpen )  emifreeIconOpen.classList.add( 'hidden' );
				if ( emifreeIconClose ) emifreeIconClose.classList.remove( 'hidden' );
			}
		} );

		// Close mobile menu when a nav link is clicked (in-page anchor)
		emifreeMobileMenu.querySelectorAll( 'a[href^="#"]' ).forEach( ( anchor ) => {
			anchor.addEventListener( 'click', () => {
				emifreeMobileMenu.classList.add( 'hidden' );
				emifreeMobileBtn.setAttribute( 'aria-expanded', 'false' );
				if ( emifreeIconOpen )  emifreeIconOpen.classList.remove( 'hidden' );
				if ( emifreeIconClose ) emifreeIconClose.classList.add( 'hidden' );
			} );
		} );

		// Escape key — close the menu if it's open, return focus to trigger.
		document.addEventListener( 'keydown', ( e ) => {
			if ( e.key !== 'Escape' ) {
				return;
			}
			if ( emifreeMobileMenu.classList.contains( 'hidden' ) ) {
				return;
			}
			emifreeMobileMenu.classList.add( 'hidden' );
			emifreeMobileBtn.setAttribute( 'aria-expanded', 'false' );
			if ( emifreeIconOpen )  emifreeIconOpen.classList.remove( 'hidden' );
			if ( emifreeIconClose ) emifreeIconClose.classList.add( 'hidden' );
			emifreeMobileBtn.focus();
		} );

		// Outside click — close the menu. Excludes clicks on the trigger
		// itself (the trigger has its own toggle handler) and clicks
		// anywhere inside the menu (links, pills, etc.).
		document.addEventListener( 'click', ( e ) => {
			if ( emifreeMobileMenu.classList.contains( 'hidden' ) ) {
				return;
			}
			if ( emifreeMobileBtn.contains( e.target ) ) {
				return;
			}
			if ( emifreeMobileMenu.contains( e.target ) ) {
				return;
			}
			emifreeMobileMenu.classList.add( 'hidden' );
			emifreeMobileBtn.setAttribute( 'aria-expanded', 'false' );
			if ( emifreeIconOpen )  emifreeIconOpen.classList.remove( 'hidden' );
			if ( emifreeIconClose ) emifreeIconClose.classList.add( 'hidden' );
		} );
	}

	// ---- Language selector ----
	const emifreeLangBtn     = document.getElementById( 'emifree-lang-btn' );
	const emifreeLangMenu    = document.getElementById( 'emifree-lang-menu' );
	const emifreeLangLabel   = document.getElementById( 'emifree-lang-label' );

	if ( emifreeLangBtn && emifreeLangMenu ) {
		emifreeLangBtn.addEventListener( 'click', ( e ) => {
			e.stopPropagation();
			const emifreeIsOpen = ! emifreeLangMenu.classList.contains( 'hidden' );
			if ( emifreeIsOpen ) {
				emifreeLangMenu.classList.add( 'hidden' );
				emifreeLangBtn.setAttribute( 'aria-expanded', 'false' );
			} else {
				emifreeLangMenu.classList.remove( 'hidden' );
				emifreeLangBtn.setAttribute( 'aria-expanded', 'true' );
			}
		} );

		emifreeLangMenu.querySelectorAll( '.emifree-lang-option' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', () => {
				const emifreeCode = btn.getAttribute( 'data-lang' );
				if ( emifreeLangLabel && emifreeCode ) {
					emifreeLangLabel.textContent = emifreeCode;
				}
				emifreeLangMenu.classList.add( 'hidden' );
				emifreeLangBtn.setAttribute( 'aria-expanded', 'false' );
			} );
		} );

		// Close on outside click
		document.addEventListener( 'click', () => {
			emifreeLangMenu.classList.add( 'hidden' );
			emifreeLangBtn.setAttribute( 'aria-expanded', 'false' );
		} );
	}

	// ---- Hero CTA — scrolls to #technology with header offset ----
	const emifreeHeroCta = document.getElementById( 'emifree-hero-cta' );
	if ( emifreeHeroCta ) {
		emifreeHeroCta.addEventListener( 'click', () => {
			const emifreeTech = document.getElementById( 'technology' );
			if ( emifreeTech ) {
				const emifreeOffset = ( emifreeHeader ? emifreeHeader.offsetHeight : 64 ) + 8;
				const emifreeTop = emifreeTech.getBoundingClientRect().top + window.pageYOffset - emifreeOffset;
				window.scrollTo( { top: emifreeTop, behavior: 'smooth' } );
			}
		} );
	}

	// ---- Smooth scroll for in-page anchors (header nav, footer links) ----
	document.querySelectorAll( 'a[href^="#"]' ).forEach( ( anchor ) => {
		anchor.addEventListener( 'click', function ( e ) {
			const emifreeHref = this.getAttribute( 'href' );
			if ( ! emifreeHref || emifreeHref === '#' ) {
				return;
			}
			const emifreeTarget = document.querySelector( emifreeHref );
			if ( emifreeTarget ) {
				e.preventDefault();
				const emifreeOffset = ( emifreeHeader ? emifreeHeader.offsetHeight : 64 ) + 8;
				const emifreeTop = emifreeTarget.getBoundingClientRect().top + window.pageYOffset - emifreeOffset;
				window.scrollTo( { top: emifreeTop, behavior: 'smooth' } );
			}
		} );
	} );
})();