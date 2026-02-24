/**
 * GSAP Scroll Animations - Design System 2026
 *
 * Data-attribute based scroll animations:
 * - data-animate="fade-up"    → Element fades up 40px on scroll
 * - data-animate="fade-in"    → Element fades in on scroll
 * - data-animate="stagger"    → Children animate in with 100ms stagger
 * - data-animate="scale-in"   → Element scales from 0.95 with spring ease
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

// Check for reduced motion preference
export const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Initialize all scroll-triggered animations
 */
function initScrollAnimations() {
    if (prefersReducedMotion) return;

    // fade-up: element slides up 40px and fades in
    document.querySelectorAll('[data-animate="fade-up"]').forEach(el => {
        gsap.fromTo(el,
            { opacity: 0, y: 40 },
            {
                opacity: 1,
                y: 0,
                duration: 0.8,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    once: true,
                },
            }
        );
    });

    // fade-in: element fades in
    document.querySelectorAll('[data-animate="fade-in"]').forEach(el => {
        gsap.fromTo(el,
            { opacity: 0 },
            {
                opacity: 1,
                duration: 0.8,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    once: true,
                },
            }
        );
    });

    // stagger: children animate in with stagger
    document.querySelectorAll('[data-animate="stagger"]').forEach(container => {
        const children = container.children;
        if (!children.length) return;

        gsap.fromTo(children,
            { opacity: 0, y: 30 },
            {
                opacity: 1,
                y: 0,
                duration: 0.6,
                stagger: 0.1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: container,
                    start: 'top 85%',
                    once: true,
                },
            }
        );
    });

    // scale-in: element scales from 0.95 with spring ease
    document.querySelectorAll('[data-animate="scale-in"]').forEach(el => {
        gsap.fromTo(el,
            { opacity: 0, scale: 0.95 },
            {
                opacity: 1,
                scale: 1,
                duration: 0.8,
                ease: 'back.out(1.7)',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    once: true,
                },
            }
        );
    });
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
} else {
    initScrollAnimations();
}

// Safety fallback: ensure all animated elements become visible after 4 seconds
// This prevents content from being permanently hidden if GSAP/ScrollTrigger fails
// Includes stagger children which get set to opacity:0 by gsap.fromTo() immediately
setTimeout(() => {
    document.querySelectorAll('[data-animate], [data-animate="stagger"] > *, [data-hero-label], [data-hero-heading], [data-hero-description], [data-hero-cta]').forEach(el => {
        const style = getComputedStyle(el);
        if (parseFloat(style.opacity) < 0.1) {
            el.style.opacity = '1';
            el.style.transform = 'none';
        }
    });
}, 4000);

// Re-export for page-specific scripts
export { gsap, ScrollTrigger };
