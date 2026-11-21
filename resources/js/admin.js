import Alpine from 'alpinejs';

window.Alpine = Alpine;

/* ============================================================
   THEME TOGGLE
   ============================================================ */
window.toggleTheme = function () {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    document.documentElement.style.colorScheme = next;
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

/* ============================================================
   GLOBAL INPUT & MEDIA VALIDATION FILTERS (ADMIN)
   ============================================================ */
(function initGlobalInputFilters() {
    const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp'];
    const IMAGE_EXTS = ['jpg', 'jpeg', 'png', 'webp'];
    const VIDEO_MIMES = ['video/mp4', 'video/quicktime', 'video/webm'];
    const VIDEO_EXTS = ['mp4', 'mov', 'webm'];

    window.extractYoutubeId = function (url) {
        if (!url || typeof url !== 'string') return null;
        const trimmed = url.trim();
        if (!/^https?:\/\//i.test(trimmed)) return null;
        try {
            const parsed = new URL(trimmed);
            const host = parsed.hostname.toLowerCase();
            if (!['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be'].includes(host)) {
                return null;
            }
            if (host === 'youtu.be') {
                const m = parsed.pathname.match(/^\/([A-Za-z0-9_-]{11})(\/.*)?$/);
                return m ? m[1] : null;
            }
            const mPath = parsed.pathname.match(/^\/(shorts|embed)\/([A-Za-z0-9_-]{11})(\/.*)?$/);
            if (mPath) return mPath[2];
            const v = parsed.searchParams.get('v');
            if (v && /^[A-Za-z0-9_-]{11}$/.test(v)) return v;
        } catch (e) {
            return null;
        }
        return null;
    };

    window.isValidYoutubeUrl = function (url) {
        return window.extractYoutubeId(url) !== null;
    };

    document.addEventListener('input', function (e) {
        const el = e.target;
        if (!el || !el.dataset || !el.dataset.filter) return;
        const mode = el.dataset.filter;
        const oldVal = el.value;
        let nextVal = oldVal;
        if (mode === 'number') {
            nextVal = oldVal.replace(/[^0-9]/g, '');
        } else if (mode === 'letters') {
            nextVal = oldVal.replace(/[^A-Za-z\s]/g, '');
        } else if (mode === 'title') {
            nextVal = oldVal.replace(/[^A-Za-z0-9\s.,:!?()\-]/g, '');
        }
        if (nextVal !== oldVal) {
            el.value = nextVal;
        }
    });

    document.addEventListener('change', function (e) {
        const el = e.target;
        if (!el || el.type !== 'file' || !el.dataset || !el.dataset.filter) return;
        const mode = el.dataset.filter;
        if (!['image-only', 'video-only', 'media-only'].includes(mode)) return;
        const files = el.files;
        if (!files || !files.length) return;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const mime = (file.type || '').toLowerCase();
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            const isImg = IMAGE_MIMES.includes(mime) && IMAGE_EXTS.includes(ext);
            const isVid = VIDEO_MIMES.includes(mime) && VIDEO_EXTS.includes(ext);

            if (mode === 'image-only' && !isImg) {
                el.value = '';
                e.stopImmediatePropagation();
                window.alert('Hanya file FOTO (JPG, PNG, WEBP) yang diperbolehkan!');
                return;
            }
            if (mode === 'video-only' && !isVid) {
                el.value = '';
                e.stopImmediatePropagation();
                window.alert('Hanya file VIDEO (MP4, MOV, WEBM) yang diperbolehkan!');
                return;
            }
            if (mode === 'media-only' && !isImg && !isVid) {
                el.value = '';
                e.stopImmediatePropagation();
                window.alert('Hanya file FOTO (JPG, PNG, WEBP) atau VIDEO (MP4, MOV, WEBM) yang diperbolehkan!');
                return;
            }
        }
    }, true);
})();

Alpine.start();