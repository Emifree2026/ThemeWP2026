// Video carousel
const videos = [
    document.getElementById('hero-video-0'),
    document.getElementById('hero-video-1')
];
let activeVideo = 0;

function switchVideo(index) {
    if (activeVideo === index) return;
    videos[activeVideo].style.opacity = '0';
    videos[index].style.opacity = '1';
    videos[index].currentTime = 0;
    videos[index].play();
    activeVideo = index;
    // Update dots
    document.querySelectorAll('.hero-dot').forEach((dot, i) => {
        if (i === index) {
            dot.classList.add('bg-emerald-400', 'w-6');
            dot.classList.remove('bg-white/30', 'w-1.5');
        } else {
            dot.classList.remove('bg-emerald-400', 'w-6');
            dot.classList.add('bg-white/30', 'w-1.5');
        }
    });
}

// Play first video and set ended event
if (videos[0]) {
    videos[0].play().catch(e => console.log('Autoplay prevented:', e));
    videos[0].addEventListener('ended', () => switchVideo(1));
    videos[1].addEventListener('ended', () => switchVideo(0));
}

document.querySelectorAll('.hero-dot').forEach(btn => {
    btn.addEventListener('click', () => switchVideo(parseInt(btn.dataset.video)));
});

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