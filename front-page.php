<?php
/**
 * Front page — Piece 1: minimal Hero (single landing video, fade-in entrance).
 *
 * Mirrors src/components/Hero.jsx from the React landing page at the state
 * we shipped (commit e0b55f3e): one video instead of the old dual-video
 * carousel, no inline style.opacity fight, framer-motion-like fade-in via CSS.
 *
 * The full Hero — including the 6-client-logo strip, sticky nav, mobile menu —
 * lands in subsequent pieces via get_template_part().
 */
get_header();
?>

<main>
	<section class="relative w-full min-h-[100dvh] flex flex-col overflow-hidden bg-[#0a0a0a]">

		<!-- Background video — single source, CSS-only fade-in, no style.opacity fight. -->
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

		<!-- Main content — vertically centered, leaves room for logos at bottom. -->
		<div class="relative z-10 flex flex-col items-center justify-center flex-1 px-5 text-center" style="padding-bottom: clamp(120px, 22vh, 180px);">
			<h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white leading-tight tracking-tight mb-4 hero-fade-up">
				Low maintenance filtration solutions
			</h1>
			<p class="text-xl sm:text-2xl text-emerald-100 mb-8 hero-fade-up" style="animation-delay: 200ms;">
				Engineered to clean itself.
			</p>
			<button
				id="hero-cta"
				class="bg-gradient-to-r from-blue-700 to-cyan-500 text-white px-8 py-4 rounded-full font-semibold text-lg flex items-center gap-3 transition-all shadow-lg hero-fade-up"
				style="animation-delay: 400ms;"
				onclick="document.getElementById('contact')?.scrollIntoView({ behavior: 'smooth' });"
			>
				See how it works
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
				</svg>
			</button>
		</div>

		<!-- Client logos — anchored to bottom; two rows on mobile. The full logo
		     strip (6 logos + heading) lands with the full Hero in Piece 4. -->
		<div class="absolute left-0 right-0 z-20 w-full" style="bottom: clamp(24px, 5vh, 48px);">
			<div class="max-w-5xl mx-auto px-4">
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

		<!-- Anchor target for the "See how it works" CTA — real Contact section
		     ships in Piece 9. Until then this anchor is below the fold and unused. -->
		<div id="contact" class="absolute bottom-0"></div>

	</section>
</main>

<?php
get_footer();