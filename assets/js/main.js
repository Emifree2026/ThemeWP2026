// Hero background video — autoplay on every paint.
//
// The template-parts emit a single <video id="hero-video"> (the
// previous agent's dual-video carousel was rolled back when the
// landing page was migrated from React to WordPress — see
// template-parts/section-hero.php for the matching comment). The
// page-level autoplay attribute on the <video> element fires the
// first paint attempt; this script catches any case where the
// autoplay attribute was blocked (mobile Safari data-saver, iOS
// Low Power Mode, autoplay disabled by browser policy) and
// retries, plus logs a debug breadcrumb when even the retry is
// rejected so the failure is visible in dev tools.
//
// Single source of truth: the markup's autoplay attribute is what
// we trust on initial load. JS only kicks in if autoplay didn't
// take — typically because the user gesture gate wasn't satisfied
// yet (mobile) or the muted/playsinline hint was missed.
const heroVideo = document.getElementById('hero-video');

if (heroVideo) {
    // Loop is set in markup but some browsers (older mobile Safari)
    // drop the loop attribute on programmatic load. Re-assert.
    heroVideo.loop = true;

    // Two-stage autoplay retry.
    //
    // Stage 1: try to play immediately on script load. This catches
    // desktop browsers and any mobile browser that allowed the
    // markup's autoplay attribute to fire.
    const emifreePlayPromise = heroVideo.play();
    if (emifreePlayPromise && typeof emifreePlayPromise.catch === 'function') {
        emifreePlayPromise.catch(function () {
            // Stage 1 rejected — typically iOS Safari data-saver
            // mode or Low Power Mode blocking autoplay until the
            // first user gesture. Defer retry until first touch.
            // Once-flag so we don't pile up listeners if multiple
            // events fire in quick succession.
            if (heroVideo.dataset.emifreeGestureArmed === '1') { return; }
            heroVideo.dataset.emifreeGestureArmed = '1';

            const emifreeArmedEvents = ['touchstart', 'pointerdown', 'mousedown', 'keydown', 'scroll'];
            const emifreeArmPlay = function () {
                heroVideo.play().catch(function (e) {
                    console.log('Hero autoplay still blocked after gesture:', e);
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
}

// Scroll header background
window.addEventListener('scroll', () => {
    const header = document.getElementById('main-header');
    if (window.scrollY > 20) {
        header.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
        header.classList.remove('bg-transparent');
    } else {
        header.classList.add('bg-transparent');
        header.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
    }
});

// Mobile menu toggle
const mobileBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
}

// Contact form submission (AJAX)
const contactForm = document.getElementById('contact-form');
if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(contactForm);
        const data = {
            name: formData.get('name'),
            email: formData.get('email'),
            company: formData.get('company'),
            message: formData.get('message')
        };

        // Clear previous errors
        document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));

        try {
            const response = await fetch(emifree_ajax.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'send_contact',
                    nonce: emifree_ajax.nonce,
                    ...data
                })
            });
            const result = await response.json();
            if (result.success) {
                alert(result.data); // Simple toast alternative
                contactForm.reset();
            } else {
                // Display field errors
                for (const [field, msg] of Object.entries(result.data)) {
                    const input = contactForm.querySelector(`[name="${field}"]`);
                    if (input) {
                        const errorDiv = input.parentElement.querySelector('.error-message');
                        if (errorDiv) {
                            errorDiv.textContent = msg;
                            errorDiv.classList.remove('hidden');
                        }
                    }
                }
            }
        } catch (err) {
            alert('Something went wrong. Please try again.');
        }
    });
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});