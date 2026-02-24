import './bootstrap';
import './admin-bulk';
import './animations';
import './dark-mode';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

// GLightbox - Image Gallery/Lightbox
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.min.css';

Alpine.plugin(collapse);

window.Alpine = Alpine;

Alpine.start();

// Initialize GLightbox on pages with gallery elements
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('[data-glightbox]')) {
        GLightbox({
            touchNavigation: true,
            loop: true,
            autoplayVideos: true,
            openEffect: 'fade',
            closeEffect: 'fade',
            cssEfects: {
                fade: { in: 'fadeIn', out: 'fadeOut' }
            }
        });
    }
});

// Fade-in for hero video when loaded using Vimeo Player API
// Only loads Vimeo API script when a Vimeo iframe is present on the page
window.addEventListener('DOMContentLoaded', () => {
    const heroVideoIframe = document.getElementById('home-hero-video');

    if (!heroVideoIframe) return;

    // Check if Vimeo API is already loaded (e.g. cached from prior navigation)
    if (window.Vimeo) {
        const player = new window.Vimeo.Player(heroVideoIframe);
        player.ready().then(() => {
            setTimeout(() => heroVideoIframe.classList.add('loaded'), 500);
        }).catch(() => {
            setTimeout(() => heroVideoIframe.classList.add('loaded'), 1000);
        });
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://player.vimeo.com/api/player.js';
    script.async = true;

    script.onload = () => {
        if (window.Vimeo) {
            const player = new window.Vimeo.Player(heroVideoIframe);
            player.ready().then(() => {
                setTimeout(() => heroVideoIframe.classList.add('loaded'), 500);
            }).catch(() => {
                setTimeout(() => heroVideoIframe.classList.add('loaded'), 1000);
            });
        } else {
            setTimeout(() => heroVideoIframe.classList.add('loaded'), 1000);
        }
    };

    script.onerror = () => {
        setTimeout(() => heroVideoIframe.classList.add('loaded'), 1000);
    };

    document.head.appendChild(script);
});
