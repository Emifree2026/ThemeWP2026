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

	// Subpath metadata — see emifreeSite localize in functions.php. The
	// path-swap math below treats the URL as if WordPress were installed
	// at the document root, then prepends the subpath on the way back
	// out. This keeps the same code working on both root installs
	// (homeSubpath='') and subpath installs (homeSubpath='/wordpress').
	const EMIFREE_HOME_SUBPATH =
		( window.emifreeSite && window.emifreeSite.homeSubpath ) || '';

	function emifreeStripSubpath( path ) {
		if ( EMIFREE_HOME_SUBPATH && path.startsWith( EMIFREE_HOME_SUBPATH ) ) {
			return path.substring( EMIFREE_HOME_SUBPATH.length ) || '/';
		}
		return path;
	}

	function emifreeApplySubpath( path ) {
		if ( ! EMIFREE_HOME_SUBPATH ) {
			return path;
		}
		// Don't double-prepend if the path already includes the subpath
		// (defensive — callers normally pass site-relative paths).
		if ( path.startsWith( EMIFREE_HOME_SUBPATH ) ) {
			return path;
		}
		return EMIFREE_HOME_SUBPATH + path;
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

		// Close mobile menu when a nav link is clicked. The selector covers
		// three href shapes the theme emits:
		//   - "#anchor"            bare fragment (not currently used in
		//                          mobile nav, but harmless to handle)
		//   - "/de/#anchor"        DE absolute path with fragment
		//   - "/#anchor"           EN absolute path with fragment
		// The smooth-scroll handler below decides whether to intercept
		// the navigation; this handler just guarantees the menu closes
		// (otherwise the document outside-click handler races the link
		// click and the menu can stay open on slow mobile networks).
		emifreeMobileMenu.querySelectorAll( 'a[href^="#"], a[href^="/#"], a[href^="/de/#"]' ).forEach( ( anchor ) => {
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

	// Persisted language key in localStorage
	const EMIFREE_LANG_KEY = 'emifree_lang';

	// Detect the current page's URI language in SITE-RELATIVE space
	// (subpath stripped). Returns 'en', 'de', or '' if neither prefix
	// is present (e.g. /impressum/, /privacy/, /terms/).
	//
	// The server-side emifree_get_lang() uses the same URI-first
	// resolution; keeping the JS in sync means the chip stays
	// consistent with the page content the server actually rendered.
	function emifreeDetectUriLang() {
		const emifreePath = emifreeStripSubpath( window.location.pathname );
		if ( emifreePath.startsWith( '/de' ) ) {
			return 'de';
		}
		if ( emifreePath.startsWith( '/en' ) ) {
			return 'en';
		}
		return '';
	}

	function emifreeApplyStoredLang() {
		try {
			let stored = localStorage.getItem( EMIFREE_LANG_KEY );
				// If localStorage is empty, fall back to cookie set on previous selection.
				if ( ! stored ) {
					try {
						const m = document.cookie.match( new RegExp('(?:^|; )' + EMIFREE_LANG_KEY + '=([^;]+)') );
						if ( m && m[1] ) {
							stored = decodeURIComponent( m[1] );
						}
					} catch ( e ) { /* ignore */ }
				}
			if ( stored ) {
				// Normalize code to upper-case label used in UI (EN/DE)
				const code = String( stored ).toUpperCase();
				const codeLower = String( stored ).toLowerCase();

				// Resolve which language the chip should display. The URI
				// is the source of truth for what's on the page: if the
				// user just clicked the logo and landed on /en/, the
				// server rendered the English page and the chip must
				// match. Only fall back to the stored value when the
				// URI is language-agnostic (e.g. /impressum/) — there,
				// the user's stored preference is the only signal we
				// have for which language they were last viewing.
				const emifreeUriLang = emifreeDetectUriLang();
				const emifreeChipCode = emifreeUriLang
					? emifreeUriLang.toUpperCase()
					: code;

				if ( emifreeLangLabel ) {
					emifreeLangLabel.textContent = emifreeChipCode;
				}
				// Update footer links and mobile pills using the stored
				// preference (so legal-page language follows the user's
				// last-chosen language even if they navigated from the
				// other-locale homepage via the logo). The chip and the
				// mobile pill active styling agree on URI-anchored pages;
				// on language-agnostic pages they follow the stored pick.
				emifreeUpdateFooterLegalLinks( code );
				document.querySelectorAll( '.emifree-mobile-lang' ).forEach( ( pill ) => {
					const pillCode = pill.getAttribute( 'data-emifree-mobile-lang' );
					const isActive = pillCode && pillCode.toLowerCase() === codeLower;
					pill.classList.toggle( 'bg-blue-700', isActive );
					pill.classList.toggle( 'text-white', isActive );
					pill.classList.toggle( 'bg-slate-100', ! isActive );
					pill.classList.toggle( 'text-zinc-700', ! isActive );
					pill.classList.toggle( 'hover:bg-slate-200', ! isActive );
				} );
			}
		} catch ( e ) {
			// ignore storage errors
		}
	}

	// Apply stored language immediately on load
	emifreeApplyStoredLang();

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

		// Path-swap helper shared by desktop + mobile language switchers.
		// Returns the URL to navigate to after the user picks a language,
		// or the current pathname if no navigation is needed.
		//
		// All math runs in SITE-RELATIVE space (the WP install subpath
		// is stripped at entry and re-prepended at exit) so the same
		// code works on root installs (subpath='') and subpath installs
		// (subpath='/wordpress').
		//
		// Rules (in site-relative space):
		//   - DE selection: prepend /de/ if not already there. Homepage
		//     paths (/, /en, /en/) all map to /de/.
		//   - EN selection: strip /de/ prefix to land on the EN equivalent.
		//     Homepage paths (/, /en, /en/) all map to /en/. EN selection
		//     on a non-homepage path (e.g. /impressum/) is a no-op since
		//     /en/{slug}/ siblings don't ship yet.
		function emifreeComputeLangTarget( code ) {
				const emifreeLangLower = String( code ).toLowerCase();
				const emifreeCurSite   = emifreeStripSubpath( window.location.pathname );
				const emifreeIsHome    = '/' === emifreeCurSite
					|| '/en' === emifreeCurSite
					|| '/en/' === emifreeCurSite;
				if ( 'de' === emifreeLangLower ) {
					if ( emifreeCurSite.startsWith( '/de' ) ) {
						return emifreeApplySubpath( emifreeCurSite );
					}
					return emifreeApplySubpath( '/de' + ( emifreeIsHome ? '/' : emifreeCurSite ) );
				}
				if ( 'en' === emifreeLangLower ) {
					if ( emifreeCurSite.startsWith( '/de' ) ) {
						// Homepage case: /de or /de/ → /en/. The bare replace
						// below would yield "/" which 301s back to /de/ via
						// emifree_maybe_redirect_home_to_de(), so we have to
						// short-circuit explicitly here.
						if ( '/de' === emifreeCurSite || '/de/' === emifreeCurSite ) {
							return emifreeApplySubpath( '/en/' );
						}
						// Non-homepage /de/* route — strip the /de prefix to
						// land on the EN equivalent (e.g. /de/impressum/ →
						// /impressum/). Slugs without an EN counterpart
						// (e.g. /de/datenschutz/, /de/agb/) will hit a 404
						// until /en/{slug}/ siblings ship.
						return emifreeApplySubpath( emifreeCurSite.replace( /^\/de/, '' ) );
					}
					if ( emifreeIsHome ) {
						return emifreeApplySubpath( '/en/' );
					}
					return emifreeApplySubpath( emifreeCurSite ); // no-op: no /en/{slug}/ sibling yet
				}
				return emifreeApplySubpath( emifreeCurSite );
			}

		emifreeLangMenu.querySelectorAll( '.emifree-lang-option' ).forEach( ( btn ) => {
			btn.addEventListener( 'click', () => {
				const emifreeCode = btn.getAttribute( 'data-lang' );
				if ( emifreeLangLabel && emifreeCode ) {
					emifreeLangLabel.textContent = emifreeCode;
				}
					// Persist and update footer links to the selected language
					try { localStorage.setItem( EMIFREE_LANG_KEY, String( emifreeCode ).toLowerCase() ); } catch ( e ) {}
					try { document.cookie = EMIFREE_LANG_KEY + '=' + encodeURIComponent( String( emifreeCode ).toLowerCase() ) + '; path=/; max-age=' + (60*60*24*30); } catch ( e ) {}
					emifreeUpdateFooterLegalLinks( emifreeCode );
				emifreeLangMenu.classList.add( 'hidden' );
				emifreeLangBtn.setAttribute( 'aria-expanded', 'false' );

				const emifreeTargetPath = emifreeComputeLangTarget( emifreeCode );
				if ( emifreeTargetPath !== window.location.pathname ) {
					window.location.href = emifreeTargetPath;
				}
			} );
		} );

			// Update footer legal links to match selected language
			function emifreeUpdateFooterLegalLinks( code ) {
				if ( ! code ) {
					return;
				}
				const emifreeTarget = document.querySelectorAll( '.emifree-legal-link' );
				emifreeTarget.forEach( ( el ) => {
					const hrefEn = el.dataset.hrefEn;
					const hrefDe = el.dataset.hrefDe;
					if ( ! hrefEn || ! hrefDe ) {
						return;
					}
					if ( String( code ).toLowerCase() === 'de' ) {
						el.setAttribute( 'href', hrefDe );
					} else {
						el.setAttribute( 'href', hrefEn );
					}
				} );
			}

		// Mobile menu language pills — visually highlight the active pill,
		// sync the desktop label, persist the choice, and navigate to the
		// equivalent page in the chosen language (same path-swap helper as
		// the desktop dropdown). The mobile menu is also closed before
		// navigating so it doesn't flash open on slow networks.
		document.querySelectorAll( '.emifree-mobile-lang' ).forEach( ( emifreePill ) => {
			emifreePill.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				const emifreeCode = emifreePill.getAttribute( 'data-emifree-mobile-lang' );
				if ( ! emifreeCode ) {
					return;
				}
				document.querySelectorAll( '.emifree-mobile-lang' ).forEach( ( emifreeOther ) => {
					const emifreeIsActive = emifreeOther === emifreePill;
					emifreeOther.classList.toggle( 'bg-blue-700', emifreeIsActive );
					emifreeOther.classList.toggle( 'text-white', emifreeIsActive );
					emifreeOther.classList.toggle( 'bg-slate-100', ! emifreeIsActive );
					emifreeOther.classList.toggle( 'text-zinc-700', ! emifreeIsActive );
					emifreeOther.classList.toggle( 'hover:bg-slate-200', ! emifreeIsActive );
				} );
				if ( emifreeLangLabel ) {
					emifreeLangLabel.textContent = emifreeCode;
				}
					// Persist and update footer links to the selected language
					try { localStorage.setItem( EMIFREE_LANG_KEY, String( emifreeCode ).toLowerCase() ); } catch ( e ) {}
					try { document.cookie = EMIFREE_LANG_KEY + '=' + encodeURIComponent( String( emifreeCode ).toLowerCase() ) + '; path=/; max-age=' + (60*60*24*30); } catch ( e ) {}
					emifreeUpdateFooterLegalLinks( emifreeCode );

					const emifreeTargetPath = emifreeComputeLangTarget( emifreeCode );
					if ( emifreeTargetPath !== window.location.pathname ) {
						// Close the mobile menu before navigating.
						if ( emifreeMobileMenu ) {
							emifreeMobileMenu.classList.add( 'hidden' );
						}
						if ( emifreeMobileBtn ) {
							emifreeMobileBtn.setAttribute( 'aria-expanded', 'false' );
							if ( emifreeIconOpen )  emifreeIconOpen.classList.remove( 'hidden' );
							if ( emifreeIconClose ) emifreeIconClose.classList.add( 'hidden' );
						}
						window.location.href = emifreeTargetPath;
					}
			} );
		} );

		// Close on outside click
		document.addEventListener( 'click', () => {
			emifreeLangMenu.classList.add( 'hidden' );
			emifreeLangBtn.setAttribute( 'aria-expanded', 'false' );
		} );
	}

	// ---- Hero CTA — scrolls to the ECO AIR Process card with header offset ----
	// Targets the first Process section (#technology-eco-air) rather than
	// the outer #technology wrapper, so the user lands directly on the
	// step-by-step cards (How ECO AIR Works / How EARIA Works) instead
	// of the selector cards above them. Both Process sections live side
	// by side on desktop and stack on mobile, so landing at the first
	// one reveals the full "How it works" UI immediately.
	const emifreeHeroCta = document.getElementById( 'emifree-hero-cta' );
	if ( emifreeHeroCta ) {
		emifreeHeroCta.addEventListener( 'click', () => {
			// Prefer the explicit ECO AIR anchor if it exists (the
			// canonical "how it works" Process target on landing pages).
			// Fall back to the outer #technology wrapper on pages where
			// the Process sections aren't rendered (e.g. legal pages).
			const emifreeHeroTarget = document.getElementById( 'technology-eco-air' )
				|| document.getElementById( 'technology' );
			if ( emifreeHeroTarget ) {
				const emifreeOffset = ( emifreeHeader ? emifreeHeader.offsetHeight : 64 ) + 8;
				const emifreeTop = emifreeHeroTarget.getBoundingClientRect().top + window.pageYOffset - emifreeOffset;
				window.scrollTo( { top: emifreeTop, behavior: 'smooth' } );
			}
		} );
	}

	// ---- Smooth scroll for in-page anchors (header nav, footer links) ----
	// Handles three href shapes uniformly:
	//   "#anchor"            in-page fragment (e.g. on /)
	//   "/#anchor"           absolute path + fragment (the canonical
	//                        shape used by header nav data so that
	//                        clicks work cross-page from /impressum/,
	//                        /privacy/, /terms/ — see inc/nav.php).
	// For /#anchor, only preventDefault + smooth-scroll if we're
	// already on the same path; otherwise let the browser navigate.
	// The same-path check covers both / and /de/ so a click on
	// /de/#products from /de/ still scrolls in place (no full reload).
	document.querySelectorAll( 'a[href^="#"], a[href^="/#"], a[href^="/de/#"], a[href^="/en/#"]' ).forEach( ( anchor ) => {
		anchor.addEventListener( 'click', function ( e ) {
			const emifreeHref = this.getAttribute( 'href' );
			if ( ! emifreeHref || emifreeHref === '#' ) {
				return;
			}
			// Strip leading slashes AND any /en/ or /de/ prefix so we end
			// up with just the fragment. /en/#applications → #applications,
			// /de/#products → #products, /#contact → #contact.
			let emifreeFragment = emifreeHref.replace( /^\/+/, '' ).replace( /^(en|de)\//, '' );
			if ( ! emifreeFragment.startsWith( '#' ) ) {
				return;
			}
			// For absolute-path anchors (e.g. /de/#products, /en/#applications),
			// only smooth-scroll if we're already on the same page. If we are
			// not, let the browser do a full navigation to the path + fragment.
			// The same-path check runs in SITE-RELATIVE space (subpath
			// stripped) so it works on both root installs and subpath
			// installs like /wordpress/.
			if ( emifreeHref.startsWith( '/' ) ) {
				const emifreePath    = emifreeStripSubpath( window.location.pathname ).replace( /\/$/, '' );
				let emifreeHrefPath  = emifreeHref.split( '#' )[ 0 ].replace( /\/$/, '' ) || '/';
				emifreeHrefPath      = emifreeStripSubpath( emifreeHrefPath );
				const emifreeSamePath = emifreePath === emifreeHrefPath;
				if ( ! emifreeSamePath ) {
					return;
				}
			}
			const emifreeTarget = document.querySelector( emifreeFragment );
			if ( emifreeTarget ) {
				e.preventDefault();
				const emifreeOffset = ( emifreeHeader ? emifreeHeader.offsetHeight : 64 ) + 8;
				const emifreeTop = emifreeTarget.getBoundingClientRect().top + window.pageYOffset - emifreeOffset;
				window.scrollTo( { top: emifreeTop, behavior: 'smooth' } );
			}
		} );
	} );
})();