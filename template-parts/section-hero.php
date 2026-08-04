<?php
/**
 * Hero section — Piece 4 (extract from front-page.php into a template part).
 *
 * Full markup mirrors the live state of src/components/Hero.jsx from the
 * React landing page (commit e0b55f3e):
 *  - Single landing video (no dual-video carousel, no inline style.opacity fight)
 *  - CSS-only fade-in entrance via hero-fade-in / hero-fade-up keyframes
 *  - 6-client-logo strip anchored to the bottom, grayscale until hover
 *
 * Composed by front-page.php with get_template_part( 'template-parts/section', 'hero' ).
 *
 * Strings come from inc/hero.php (English) or inc/hero_de.php (German).
 * The i18n loader picks the right one based on the emifree_lang cookie.
 */

require_once get_template_directory() . '/inc/i18n.php';
emifree_require_section_data( 'hero' );
$emifree_hero = emifree_hero_data();
?>

<section id="hero" class="relative w-full min-h-[100dvh] flex flex-col overflow-hidden bg-[#0a0a0a]">

	<!-- Background videos — two-up carousel.
	     Two videos play alternately: when the active one ends, JS
	     cross-fades to the other and lets it play through; on its end,
	     back to the first. Both <video> elements sit at the same
	     z-index (z-0) — the carousel is driven by opacity, not stacking
	     order. The dark gradient overlay sits at z-20 so the
	     foreground copy always reads on the same scrim regardless of
	     which video is visible; foreground content sits at z-30.

	     Why no poster? The previous stack used poster="emilogo.png"
	     which made the static logo flash for a moment while each video
	     downloaded/decoded. With two videos alternating, that flash
	     would happen on every swap. Dropping the poster attribute
	     means the browser keeps the last decoded frame visible until
	     the next one is ready (no flashing still image).

	     Why no `loop` on the markup? The carousel swaps on the
	     `ended` event, so each video plays once and then hands off.
	     The carousel logic in assets/js/main.js owns the loop.

	     Why `opacity-0` by default? Both videos must render into the
	     same compositing layer so the cross-fade is smooth. main.js
	     adds `emifree-hero-video--active` to whichever video should
	     be visible at any moment; CSS transitions handle the fade. -->
	<div class="absolute inset-0 w-full h-full bg-[#0a0a0a]">
		<video
			id="hero-video-primary"
			muted
			playsinline
			webkit-playsinline
			preload="metadata"
			class="emifree-hero-video absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000 z-0"
		>
			<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/Video Project hero.mp4' ); ?>" type="video/mp4">
		</video>
		<video
			id="hero-video-secondary"
			muted
			playsinline
			webkit-playsinline
			preload="metadata"
			class="emifree-hero-video absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000 z-0"
		>
			<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/Landing Video_2.1.mp4' ); ?>" type="video/mp4">
		</video>
		<!-- Dark gradient overlay. Light enough that the video keeps
		     its visual presence; dark enough that the headline and chips
		     stay readable on bright frames. Top edge eases down to 35%
		     black; middle band (where the headline + chips live) sits at
		     50%; bottom (logos) goes a bit darker for legibility. -->
		<div class="absolute inset-0 z-20 bg-gradient-to-b from-black/55 via-black/50 to-black/70"></div>
		<div class="absolute inset-0 z-20 bg-gradient-to-br from-emerald-500/5 to-transparent"></div>
	</div>

	<!-- Main content -->
	<div class="relative z-30 flex flex-col items-center justify-center flex-1 px-5 text-center" style="padding-bottom: clamp(120px, 22vh, 180px);">
		<h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white leading-tight tracking-tight mb-6 hero-fade-up">
			<?php echo esc_html( $emifree_hero['headline'] ); ?>
		</h1>

		<?php /* Value strip — three short keyword chips. Each chip is a
		   rounded-pill with a small blue SVG icon prefix + label. The
		   data array provides {label, icon} pairs; the icon "kind" maps
		   to inline SVG below so the data layer stays free of raw
		   markup. Chips are <li>s inside a <ul> for screen readers, and
		   the ul carries an aria-label that joins the labels so AT
		   reads it as a single list ("Self-cleaning, No cartridge
		   exchange, HEPA filter") instead of three separated spans.

		   Vertical rhythm: tight gap to the headline (mb-6 on h1), then
		   chips live close to the headline as a "what you get" subtitle.
		   Larger gap (mb-10) before the secondary link so the eye reads
		   (H1 → chips → quiet link → primary CTA) as four distinct
		   phases.

		   Chip styling — contrast pass after the first iteration read as
		   too subtle on bright video frames. Background is now a real
		   tinted navy with backdrop-blur (the frosted look from the
		   reference screenshot) instead of a 4% white wash, and the
		   label/icon sit at full white / full blue for guaranteed
		   readability. */ ?>
		<?php
		$emifree_chip_icons = array(
			'cycle'     => '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3.5-7.1"/><path d="M21 4v6h-6"/></svg>',
			'cartridge' => '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="10" rx="2"/><path d="M7 7v10"/><path d="M11 7v10"/><path d="M15 7v10"/></svg>',
			'hepa'      => '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-12V5l-8-3-8 3v5c0 8 8 12 8 12z"/><path d="M9 12l2 2 4-4"/></svg>',
		);
		?>
		<ul
			class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 mb-10 hero-fade-up"
			style="animation-delay: 150ms;"
			aria-label="<?php echo esc_attr( implode( ', ', array_column( $emifree_hero['value_strip'], 'label' ) ) ); ?>"
		>
			<?php foreach ( $emifree_hero['value_strip'] as $emifree_chip ) :
				$emifree_chip_icon = isset( $emifree_chip_icons[ $emifree_chip['icon'] ] ) ? $emifree_chip_icons[ $emifree_chip['icon'] ] : '';
				?>
				<li>
					<span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 backdrop-blur-sm px-4 py-2 text-sm sm:text-base font-medium text-white whitespace-nowrap">
						<?php if ( '' !== $emifree_chip_icon ) : ?>
							<span class="text-blue-300 flex items-center justify-center"><?php echo $emifree_chip_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — closed allowlist above, no user input. ?></span>
						<?php endif; ?>
						<?php echo esc_html( $emifree_chip['label'] ); ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php /* Secondary CTA — a quiet underlined text link, NOT a button.
		   Sits below the keywords and ABOVE the primary button so the
		   primary remains the visual peak (last + most-button-shaped in
		   the stack). White text + soft underline keep it intentionally
		   subordinate; the arrow is part of the label so it's read as
		   one phrase by assistive tech. Routes to #technology. The
		   smooth-scroll handler in header.js picks up href="#technology"
		   without any extra wiring.

		   text-shadow lifts the small underlined text off bright frames
		   (white machining flashes) where a plain white link would lose
		   its underline. mb-8 below gives a clear gap before the
		   primary button so the two CTAs read as separate intentions. */ ?>
		<a
			href="#technology"
			class="text-white underline underline-offset-4 decoration-white/70 hover:decoration-white hover:text-white text-sm sm:text-base font-medium transition-colors duration-200 hero-fade-up mb-8"
			style="animation-delay: 300ms; text-shadow: 0 1px 2px rgba(0,0,0,0.5), 0 2px 6px rgba(0,0,0,0.4);"
		>
			<?php echo esc_html( $emifree_hero['secondary_link_label'] ); ?>
		</a>

		<?php /* Primary CTA — single dominant button at the bottom of the
		   stack. Anchors to #contact (existing contact section). Uses an
		   <a> styled as a button so the browser's native smooth-scroll
		   (wired in assets/js/sections/header.js for all in-page anchors)
		   does the right thing without any per-button JS. NO arrow icon —
		   the user asked for a single, dominant CTA, so visual weight
		   should land on the label, not on a directional glyph. */ ?>
		<a
			href="#contact"
			class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white px-10 py-5 rounded-full font-semibold text-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl hero-fade-up"
			style="animation-delay: 450ms; background-image: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%); background-color: #2563eb;"
		>
			<?php echo esc_html( $emifree_hero['primary_cta_label'] ); ?>
		</a>
	</div>

	<!-- Client logos strip -->
	<div class="absolute left-0 right-0 z-20 w-full" style="bottom: clamp(24px, 5vh, 48px);">
		<div class="max-w-5xl mx-auto px-4">
			<p class="text-white/60 text-[10px] sm:text-xs uppercase tracking-wider mb-2 text-center">
				<?php echo esc_html( $emifree_hero['logos_label'] ); ?>
			</p>
			<div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-3 sm:flex-nowrap sm:gap-6 md:gap-8 lg:gap-12">
				<?php foreach ( $emifree_hero['logos'] as $emifree_logo ) : ?>
					<div class="flex items-center justify-center w-[30%] sm:w-auto h-6 sm:h-8 md:h-10 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-300">
						<img
							src="<?php echo esc_url( get_template_directory_uri() . '/assets/logo_clients/' . $emifree_logo['file'] ); ?>"
							alt="<?php echo esc_attr( $emifree_logo['name'] ); ?>"
							class="h-full w-auto object-contain brightness-0 invert"
							style="max-width: <?php echo esc_attr( $emifree_logo['max'] ); ?>;"
							loading="lazy"
							decoding="async"
							width="200"
							height="80"
						>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

</section>