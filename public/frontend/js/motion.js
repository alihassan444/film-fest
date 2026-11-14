(() => {
    'use strict';
    const initialize = () => {
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const active = new Set();
    const seen = new WeakSet();
    const selectors = [
        '.hero-content > *', '.hero-bottom-info', '.about-section .services-info',
        '.about-text-wrap', '.society-pillars > div', 'section h2', '.leadership-eyebrow', '.artists-thumb',
        '.pricing-thumb', '.festival-price-card', '.schedule-heading', '.schedule-day-card', '.google-map',
        '.ticket-form', '.footer-venue-panel', '.site-footer-top .col-lg-6', '.admin-page-head',
        '.admin-content .card', '.admin-card', '.admin-stat-card', '.admin-alert',
        '#app main .card', '.submission-intro > *', '.submission-art', '.submission-fee', '.submission-register'
    ];
    const targets = [...document.querySelectorAll(selectors.join(','))];
    const reveal = element => {
        if (seen.has(element)) return;
        seen.add(element);
        if (preference.matches || !element.animate || element.contains(document.activeElement)) return;
        // Stagger only adjacent cards; never delay an entire page of content.
        const siblings = [...(element.parentElement?.parentElement?.children || [])];
        const index = Math.max(0, siblings.indexOf(element.parentElement));
        const animation = element.animate([
            { opacity: 0, transform: 'translateY(18px)' },
            { opacity: 1, transform: 'translateY(0)' }
        ], {
            duration: 600, delay: Math.min(index, 3) * 65,
            easing: 'cubic-bezier(.22, 1, .36, 1)', fill: 'backwards'
        });
        active.add(animation);
        animation.onfinish = animation.oncancel = () => active.delete(animation);
        element.addEventListener('focusin', () => animation.cancel(), { once: true });
    };
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                reveal(entry.target);
                observer.unobserve(entry.target);
            });
        }, { threshold: .08 });
        targets.forEach(element => observer.observe(element));
    }
    const onPreferenceChange = () => {
        if (preference.matches) active.forEach(animation => animation.cancel());
    };
    if (preference.addEventListener) preference.addEventListener('change', onPreferenceChange);
    else if (preference.addListener) preference.addListener(onPreferenceChange);
    const navbar = document.querySelector('main > .navbar, .sticky-wrapper .navbar');
    if (navbar) {
        let scheduled = false;
        const update = () => { navbar.classList.toggle('is-scrolled', window.scrollY > 12); scheduled = false; };
        window.addEventListener('scroll', () => {
            if (!scheduled) { scheduled = true; requestAnimationFrame(update); }
        }, { passive: true });
        update();
    }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else initialize();
})();
