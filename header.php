<?php
/**
 * Header — full markup mirroring src/components/Header.jsx from the
 * React landing page (commit e0b55f3e).
 *
 * Differences from the React version, intentional for WordPress:
 *  - Single logo (not the dual-logo combination the React app had —
 *    cleaner hierarchy, the original critique flagged the dual logo
 *    as confusing).
 *  - Nav data is read from inc/nav.php so it's editable in one place.
 *  - Sticky-on-scroll backdrop-blur is handled by assets/js/sections/header.js
 *    (loaded when the header is present).
 *  - Mobile menu toggle + language selector dropdown are vanilla JS in the
 *    same per-section file. No React state.
 */

require_once get_template_directory() . '/inc/nav.php';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="emifree-header" class="fixed top-0 left-0 right-0 z-50 bg-white transition-all duration-300">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex items-center justify-between h-16">

			<!-- Dual logo (logo.png wordmark + emilogo.png) — matches the
			     baseline React Header's setup. -->
			<div class="flex-shrink-0 flex items-center gap-3">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Emifree — back to home">
					<img
						src="<?php echo esc_url( get_template_directory_uri() . '/assets/logo.png' ); ?>"
						alt="Emifree"
						class="h-10 w-auto"
						width="120"
						height="40"
					>
				</a>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Emifree — back to home">
					<img
						src="<?php echo esc_url( get_template_directory_uri() . '/assets/emilogo.png' ); ?>"
						alt="Emifree"
						class="h-8 w-auto"
						width="100"
						height="32"
					>
				</a>
			</div>

			<!-- Desktop nav (md+) -->
			<nav class="hidden md:block" aria-label="Primary">
				<div class="flex items-center space-x-1 bg-slate-100 rounded-full px-2 py-2">
					<?php foreach ( emifree_nav_items() as $emifree_nav_item ) : ?>
						<a
							href="<?php echo esc_url( $emifree_nav_item['href'] ); ?>"
							class="px-4 py-2 text-sm font-medium text-zinc-900 hover:text-blue-700 rounded-full transition-all duration-200"
						>
							<?php echo esc_html( $emifree_nav_item['label'] ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</nav>

			<!-- Right side: phone CTA + language selector pill -->
			<div class="hidden md:flex items-center space-x-3">

				<a
					href="tel:+493076283520"
					class="bg-blue-700 text-white px-5 py-2 rounded-full font-medium text-sm hover:bg-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl inline-flex items-center gap-1.5 whitespace-nowrap flex-shrink-0"
				>
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
						</path>
					</svg>
					+49 3076283520
				</a>

				<div class="relative">
					<button
						id="emifree-lang-btn"
						type="button"
						class="flex items-center space-x-2 bg-white border border-slate-200 rounded-full px-4 py-2 hover:bg-slate-50 transition-all duration-200"
						aria-haspopup="true"
						aria-expanded="false"
					>
						<svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 12l2 2m0 0l2-2m-2 2h10M7 4v16M17 4v16M21 12l-2 2m0 0l-2-2m2 2H7">
							</path>
						</svg>
						<span id="emifree-lang-label" class="text-sm font-medium text-zinc-700">EN</span>
						<svg class="w-4 h-4 text-zinc-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
						</svg>
					</button>

					<div
						id="emifree-lang-menu"
						class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden hidden z-50"
						role="menu"
					>
						<?php
						$emifree_languages = array(
							array( 'code' => 'EN', 'name' => 'English' ),
							array( 'code' => 'DE', 'name' => 'Deutsch' ),
							array( 'code' => 'ES', 'name' => 'Espanol' ),
							array( 'code' => 'FR', 'name' => 'Francais' ),
						);
						foreach ( $emifree_languages as $emifree_lang ) :
							?>
							<button
								type="button"
								class="emifree-lang-option w-full px-4 py-3 text-left text-sm hover:bg-blue-50 transition-colors duration-200 text-zinc-700"
								data-lang="<?php echo esc_attr( $emifree_lang['code'] ); ?>"
								role="menuitem"
							>
								<?php echo esc_html( $emifree_lang['name'] ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<!-- Mobile menu trigger (visible <md) -->
			<button
				id="emifree-mobile-menu-btn"
				type="button"
				class="md:hidden text-zinc-900 hover:text-blue-700 transition-colors duration-200"
				aria-label="Toggle mobile menu"
				aria-expanded="false"
			>
				<svg id="emifree-mobile-menu-icon-open"  class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
				</svg>
				<svg id="emifree-mobile-menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>
	</div>

	<!-- Mobile menu dropdown (visible when toggled on, <md) -->
	<div id="emifree-mobile-menu" class="hidden md:hidden bg-white border-t border-slate-200">
		<div class="px-4 py-4 space-y-2">
			<?php foreach ( emifree_nav_items() as $emifree_nav_item ) : ?>
				<a
					href="<?php echo esc_url( $emifree_nav_item['href'] ); ?>"
					class="block px-4 py-2 text-sm font-medium text-zinc-900 hover:text-blue-700 hover:bg-slate-100 rounded-lg transition-all duration-200"
				>
					<?php echo esc_html( $emifree_nav_item['label'] ); ?>
				</a>
			<?php endforeach; ?>

			<a
				href="tel:+493076283520"
				class="block w-full bg-blue-700 text-white px-6 py-2 rounded-full font-medium text-sm hover:bg-blue-800 transition-all duration-200 mt-4 text-center"
			>
				<span class="inline-flex items-center justify-center gap-1.5">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
						</path>
					</svg>
					+49 3076283520
				</span>
			</a>
		</div>
	</div>
</header>