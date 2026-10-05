import Alpine from 'alpinejs';

window.Alpine = Alpine;

/* ============================================================
   THEME TOGGLE
   ============================================================ */
window.toggleTheme = function () {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    try { localStorage.setItem('theme', next); } catch (e) {}
};

/* ============================================================
   SIDEBAR TOGGLE (mobile)
   FIX: Support dua class hidden — '-translate-x-[120%]' & '-translate-x-full'
   ============================================================ */
window.toggleSidebar = function () {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (!sidebar || !overlay) return;

    // Cek salah satu dari dua class ini (biar kompatibel)
    const isHidden =
        sidebar.classList.contains('-translate-x-[120%]') ||
        sidebar.classList.contains('-translate-x-full');

    if (isHidden) {
        // Buka sidebar
        sidebar.classList.remove('-translate-x-[120%]');
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
    } else {
        // Tutup sidebar
        sidebar.classList.add('-translate-x-[120%]');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
    }
};

/* ============================================================
   FLATPICKR — AUTO DARK MODE (GLOBAL)
   ============================================================ */
(function initFlatpickrTheme() {
    if (typeof window.flatpickr === 'undefined') {
        let attempts = 0;
        const waiter = setInterval(() => {
            attempts++;
            if (typeof window.flatpickr !== 'undefined') {
                clearInterval(waiter);
                patchFlatpickr();
            }
            if (attempts > 100) clearInterval(waiter);
        }, 100);
    } else {
        patchFlatpickr();
    }

    function patchFlatpickr() {
        const originalFlatpickr = window.flatpickr;

        window.flatpickr = function (selector, options) {
            options = options || {};
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (!options.theme) {
                options.theme = isDark ? 'dark' : 'light';
            }

            const instance = originalFlatpickr(selector, options);

            const calendar = instance && instance.calendarContainer;
            if (calendar) {
                const updateTheme = () => {
                    const dark = document.documentElement.getAttribute('data-theme') === 'dark';
                    calendar.classList.toggle('dark', dark);
                };
                updateTheme();

                const observer = new MutationObserver(updateTheme);
                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['data-theme'],
                });
            }

            return instance;
        };

        Object.keys(originalFlatpickr).forEach((key) => {
            window.flatpickr[key] = originalFlatpickr[key];
        });
    }
})();

Alpine.start();