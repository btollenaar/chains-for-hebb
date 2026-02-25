/**
 * Homepage-specific GSAP animations
 * Loaded conditionally via @push('scripts') in home.blade.php
 */

import { gsap, ScrollTrigger, prefersReducedMotion } from '../animations';

if (!prefersReducedMotion) {
    // Hero entrance timeline
    document.addEventListener('DOMContentLoaded', () => {
        const heroSection = document.querySelector('[data-hero]');
        if (!heroSection) return;

        // Set initial hidden state via JS (not Tailwind) so content is visible if JS fails
        gsap.set('[data-hero-label], [data-hero-heading], [data-hero-description], [data-hero-cta], [data-hero-progress], [data-hero-scroll]', { opacity: 0 });

        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

        tl.fromTo('[data-hero-label]',
            { opacity: 0, y: 20 },
            { opacity: 1, y: 0, duration: 0.6 }
        )
        .fromTo('[data-hero-heading]',
            { opacity: 0, y: 30 },
            { opacity: 1, y: 0, duration: 0.8 },
            '-=0.3'
        )
        .fromTo('[data-hero-description]',
            { opacity: 0, y: 20 },
            { opacity: 1, y: 0, duration: 0.6 },
            '-=0.4'
        )
        .fromTo('[data-hero-cta]',
            { opacity: 0, scale: 0.95 },
            { opacity: 1, scale: 1, duration: 0.5, ease: 'back.out(1.7)' },
            '-=0.3'
        )
        .fromTo('[data-hero-progress]',
            { opacity: 0, y: 15 },
            { opacity: 1, y: 0, duration: 0.5 },
            '-=0.2'
        )
        .fromTo('[data-hero-scroll]',
            { opacity: 0, y: -10 },
            { opacity: 0.7, y: 0, duration: 0.5 },
            '-=0.1'
        );

        // Floating decorative elements parallax
        gsap.utils.toArray('[data-float]').forEach((el, i) => {
            gsap.to(el, {
                y: -30 - (i * 10),
                scrollTrigger: {
                    trigger: heroSection,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 1,
                },
            });
        });
    });
}
