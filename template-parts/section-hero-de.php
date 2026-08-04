<?php
/**
 * Hero section — German.
 *
 * Hard-coded German translation of section-hero.php. No data loader,
 * no shared i18n helpers — each German section template is fully
 * self-contained. Strings come from inc/hero_de.php (the previous
 * agent's translation of the React Hero); the German logo set is
 * identical to the English one.
 *
 * Composed by front-page-de.php with
 * get_template_part( 'template-parts/section', 'hero-de' ).
 */
?>

<section id="hero" class="relative w-full min-h-[100dvh] flex flex-col overflow-hidden bg-[#0a0a0a]">

	<!-- Background videos — two-up carousel (mirrors EN). Two videos
	     play alternately: when the active one ends, JS cross-fades to
	     the other and lets it play through; on its end, back to the
	     first. Both <video> elements sit at the same z-index (z-0) —
	     the carousel is driven by opacity, not stacking order. The
	     dark gradient overlay sits at z-20 (above both videos) and
	     foreground content at z-30 so the headline + chips always read
	     on the same scrim regardless of which video is visible.

	     Why no poster? Without it, the browser keeps the last decoded
	     frame visible until the next one is ready (no flashing still
	     image on every swap).

	     Why no `loop` on the markup? The carousel swaps on the
	     `ended` event; the carousel logic in assets/js/main.js owns
	     the loop.

	     Why `opacity-0` by default? Both videos must render into the
	     same compositing layer so the cross-fade is smooth. main.js
	     adds `emifree-hero-video--active` to whichever video should
	     be visible; CSS transitions handle the fade. -->
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
		<div class="absolute inset-0 z-20 bg-gradient-to-b from-black/55 via-black/50 to-black/70"></div>
		<div class="absolute inset-0 z-20 bg-gradient-to-br from-emerald-500/5 to-transparent"></div>
	</div>

	<!-- Main content -->
	<div class="relative z-30 flex flex-col items-center justify-center flex-1 px-5 text-center" style="padding-bottom: clamp(120px, 22vh, 180px);">
		<h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white leading-tight tracking-tight mb-6 hero-fade-up">
			Wartungsarme Luftfiltrationslösungen
		</h1>

		<?php /* Value strip — mirrors the English hero. Three short
		   keyword chips rendered as rounded pills with a small blue
		   SVG icon prefix + label. Built inline (rather than loaded
		   from a data file) because this DE section template ships
		   fully self-contained per the architecture note at the top of
		   this file. Same chip styling + vertical rhythm as the EN hero. */ ?>
		<?php
		$emifree_de_chips        = array(
			array( 'label' => 'Selbstreinigend',         'icon' => 'cycle' ),
			array( 'label' => 'Kein Kartuschenwechsel',  'icon' => 'cartridge' ),
			array( 'label' => 'HEPA-Filter',             'icon' => 'hepa' ),
		);
		$emifree_de_chip_icons   = array(
			'cycle'     => '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3.5-7.1"/><path d="M21 4v6h-6"/></svg>',
			'cartridge' => '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="10" rx="2"/><path d="M7 7v10"/><path d="M11 7v10"/><path d="M15 7v10"/></svg>',
			'hepa'      => '<svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-12V5l-8-3-8 3v5c0 8 8 12 8 12z"/><path d="M9 12l2 2 4-4"/></svg>',
		);
		?>
		<ul
			class="flex flex-wrap items-center justify-center gap-3 sm:gap-4 mb-10 hero-fade-up"
			style="animation-delay: 150ms;"
			aria-label="<?php echo esc_attr( implode( ', ', array_column( $emifree_de_chips, 'label' ) ) ); ?>"
		>
			<?php foreach ( $emifree_de_chips as $emifree_de_chip ) :
				$emifree_de_chip_icon = isset( $emifree_de_chip_icons[ $emifree_de_chip['icon'] ] ) ? $emifree_de_chip_icons[ $emifree_de_chip['icon'] ] : '';
				?>
				<li>
					<span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 backdrop-blur-sm px-4 py-2 text-sm sm:text-base font-medium text-white whitespace-nowrap">
						<?php if ( '' !== $emifree_de_chip_icon ) : ?>
							<span class="text-blue-300 flex items-center justify-center"><?php echo $emifree_de_chip_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — closed allowlist above, no user input. ?></span>
						<?php endif; ?>
						<?php echo esc_html( $emifree_de_chip['label'] ); ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php /* Secondary CTA — quiet underlined link, mirrors EN.
		   Sits between keywords and primary CTA. text-shadow lifts the
		   small underlined text off bright frames. mb-8 below gives a
		   clear gap before the primary button. */ ?>
		<a
			href="#technology"
			class="text-white underline underline-offset-4 decoration-white/70 hover:decoration-white hover:text-white text-sm sm:text-base font-medium transition-colors duration-200 hero-fade-up mb-8"
			style="animation-delay: 300ms; text-shadow: 0 1px 2px rgba(0,0,0,0.5), 0 2px 6px rgba(0,0,0,0.4);"
		>
			Die Technologie dahinter ansehen →
		</a>

		<?php /* Primary CTA — single dominant button at the bottom of the
		   stack, mirrors EN. Anchors to #contact. No arrow on the button
		   label — visual weight stays on the "Kontakt aufnehmen!" text. */ ?>
		<a
			href="#contact"
			class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white px-10 py-5 rounded-full font-semibold text-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl hero-fade-up"
			style="animation-delay: 450ms; background-image: linear-gradient(90deg, #1d4ed8 0%, #06b6d4 100%); background-color: #2563eb;"
		>
			Kontakt aufnehmen!
		</a>
	</div>

	<!-- Client logos strip -->
	<div class="absolute left-0 right-0 z-20 w-full" style="bottom: clamp(24px, 5vh, 48px);">
		<div class="max-w-5xl mx-auto px-4">
			<p class="text-white/60 text-[10px] sm:text-xs uppercase tracking-wider mb-2 text-center">
				Vertraut von Branchenführern
			</p>
			<div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-3 sm:flex-nowrap sm:gap-6 md:gap-8 lg:gap-12">
				<?php
				$emifree_hero_logos = array(
					array( 'name' => 'Mercedes-Benz', 'file' => 'mb_svg.svg',       'max' => 'clamp(28px, 4.5vw, 50px)' ),
					array( 'name' => 'BMW',           'file' => 'bmw.svg',          'max' => 'clamp(30px, 5vw, 55px)' ),
					array( 'name' => 'GM',            'file' => 'gm.svg',           'max' => 'clamp(30px, 5vw, 55px)' ),
					array( 'name' => 'NSK',           'file' => 'NSK.svg',          'max' => 'clamp(45px, 8vw, 100px)' ),
					array( 'name' => 'Knorr-Bremse',  'file' => 'knorr.svg',        'max' => 'clamp(60px, 11vw, 130px)' ),
					array( 'name' => 'Siemens',       'file' => 'siemens_logo.svg', 'max' => 'clamp(55px, 9vw, 100px)' ),
				);
				foreach ( $emifree_hero_logos as $emifree_logo ) :
					?>
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