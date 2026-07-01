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
 */
?>

<section id="hero" class="relative w-full min-h-[100dvh] flex flex-col overflow-hidden bg-[#0a0a0a]">

	<!-- Background video — single source. -->
	<div class="absolute inset-0 w-full h-full">
		<video
			id="hero-video"
			muted
			loop
			playsinline
			preload="auto"
			poster="<?php echo esc_url( get_template_directory_uri() . '/assets/images/emilogo.png' ); ?>"
			class="absolute inset-0 w-full h-full object-cover hero-fade-in"
		>
			<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/Video1_WEBHANDLanding.mp4' ); ?>" type="video/mp4">
		</video>
		<div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/60 to-black/30"></div>
		<div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent"></div>
	</div>

	<!-- Main content -->
	<div class="relative z-10 flex flex-col items-center justify-center flex-1 px-5 text-center" style="padding-bottom: clamp(120px, 22vh, 180px);">
		<h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white leading-tight tracking-tight mb-4 hero-fade-up">
			Low maintenance filtration solutions
		</h1>
		<p class="text-xl sm:text-2xl text-emerald-100 mb-8 hero-fade-up" style="animation-delay: 200ms;">
			Engineered to clean itself.
		</p>
		<button
			type="button"
			id="emifree-hero-cta"
			class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white px-8 py-4 rounded-full font-semibold text-lg flex items-center gap-3 transition-all duration-300 shadow-lg hero-fade-up"
			style="animation-delay: 400ms;"
		>
			See how it works
			<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
				<path d="M5 12h14"></path>
				<path d="m12 5 7 7-7 7"></path>
			</svg>
		</button>
	</div>

	<!-- Client logos strip -->
	<div class="absolute left-0 right-0 z-20 w-full" style="bottom: clamp(24px, 5vh, 48px);">
		<div class="max-w-5xl mx-auto px-4">
			<p class="text-white/60 text-[10px] sm:text-xs uppercase tracking-wider mb-2 text-center">
				Trusted by industry leaders
			</p>
			<div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-3 sm:flex-nowrap sm:gap-6 md:gap-8 lg:gap-12">
				<?php
				$emifree_client_logos = array(
					array( 'name' => 'Mercedes-Benz', 'file' => 'mb_svg.svg',       'max' => 'clamp(28px, 4.5vw, 50px)' ),
					array( 'name' => 'BMW',           'file' => 'bmw.svg',          'max' => 'clamp(30px, 5vw, 55px)' ),
					array( 'name' => 'GM',            'file' => 'gm.svg',           'max' => 'clamp(30px, 5vw, 55px)' ),
					array( 'name' => 'NSK',           'file' => 'NSK.svg',          'max' => 'clamp(45px, 8vw, 100px)' ),
					array( 'name' => 'Knorr-Bremse',  'file' => 'knorr.svg',        'max' => 'clamp(60px, 11vw, 130px)' ),
					array( 'name' => 'Siemens',       'file' => 'siemens_logo.svg', 'max' => 'clamp(55px, 9vw, 100px)' ),
				);
				foreach ( $emifree_client_logos as $emifree_logo ) :
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