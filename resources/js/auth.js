import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.toggleTheme = function () {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    document.documentElement.style.colorScheme = next;
    try { localStorage.setItem('theme', next); } catch (e) {}
};

Alpine.start();