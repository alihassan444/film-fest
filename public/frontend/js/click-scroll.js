(() => {
    'use strict';
    const links = [...document.querySelectorAll('.navbar .click-scroll')];
    const sections = links.map(link => ({ link, section: document.querySelector(link.hash) })).filter(item => item.section);
    links.forEach(link => link.addEventListener('click', event => {
        const section = document.querySelector(link.hash);
        if (!section) return;
        event.preventDefault();
        section.scrollIntoView({
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth',
            block: 'start'
        });
    }));
    if (!sections.length) return;
    let scheduled = false;
    const update = () => {
        const offset = (document.querySelector('.navbar')?.offsetHeight || 72) + 28;
        let current = sections[0];
        sections.forEach(item => {
            if (item.section.getBoundingClientRect().top <= offset) current = item;
        });
        sections.forEach(item => {
            const selected = item === current;
            item.link.classList.toggle('active', selected);
            if (selected) item.link.setAttribute('aria-current', 'location');
            else item.link.removeAttribute('aria-current');
        });
        scheduled = false;
    };
    window.addEventListener('scroll', () => {
        if (!scheduled) { scheduled = true; requestAnimationFrame(update); }
    }, { passive: true });
    update();
})();
