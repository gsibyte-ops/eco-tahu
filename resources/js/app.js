import Alpine from 'alpinejs';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import SplitType from 'split-type';

window.Alpine = Alpine;
gsap.registerPlugin(ScrollTrigger);

const REDUCE = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const CARD_SELECTOR = '.product-card, .category-card, .limbah-card, .edukasi-card, .testimoni-card';

/* ============================================================
   1) LENIS
   ============================================================ */
let lenis;
function initLenis() {
    if (REDUCE) return;
    document.documentElement.style.scrollBehavior = 'auto';
    lenis = new Lenis({
        lerp: 0.09,
        smoothWheel: true,
        wheelMultiplier: 1,
        touchMultiplier: 1.5,
    });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
}

/* ============================================================
   2) HERO ENTRANCE — fix H1 clipping
   ⚠️ Tidak pakai overflow:hidden. Line di-split, tapi animasi
      via y:60 + opacity, jadi huruf TIDAK mungkin kepotong.
   ============================================================ */
function initHero() {
    if (REDUCE) return;
    const h1 = document.querySelector('main > section:first-child h1');
    if (!h1) return;

    // Split per baris (tanpa bikin wrapper overflow hidden)
    const split = new SplitType(h1, { types: 'lines' });
    split.lines.forEach((line) => {
        // Hanya bikin display:block biar tiap baris punya baris sendiri
        line.style.display = 'block';
    });

    // ← Animasi: fade + slide, bukan mask reveal. Aman dari clipping.
    gsap.set(split.lines, { y: 60, opacity: 0 });
    gsap.to(split.lines, {
        y: 0,
        opacity: 1,
        duration: 1.4,
        stagger: 0.16,
        ease: 'power4.out',
        delay: 0.35,
    });

    const hero = h1.closest('section');
    const tl = gsap.timeline({ delay: 1.0 });
    const badge = hero.querySelector('span.pill');
    const para  = hero.querySelector('p');
    const btns  = hero.querySelectorAll('a.btn-primary, a.btn-ghost');
    const stats = hero.querySelectorAll('.grid.grid-cols-3 > div');

    if (badge) tl.from(badge, { y: 24, opacity: 0, duration: 1.0, ease: 'power3.out' });
    if (para)  tl.from(para,  { y: 24, opacity: 0, duration: 1.0, ease: 'power3.out' }, '-=0.7');
    if (btns.length) tl.from(btns, { y: 24, opacity: 0, duration: 1.0, stagger: 0.15, ease: 'power3.out' }, '-=0.7');
    if (stats.length) tl.from(stats, { y: 24, opacity: 0, duration: 1.0, stagger: 0.12, ease: 'power3.out' }, '-=0.7');
}

/* ============================================================
   3) PARALLAX
   ============================================================ */
function initParallax() {
    if (REDUCE) return;
    const tofu = document.querySelector('.tofu-float');
    if (!tofu) return;

    gsap.to(tofu, {
        yPercent: -25,
        ease: 'none',
        scrollTrigger: {
            trigger: tofu.closest('section'),
            start: 'top top',
            end: 'bottom top',
            scrub: 1,
        },
    });

    const glow = tofu.parentElement?.querySelector('[style*="radial-gradient"]');
    if (glow) {
        gsap.to(glow, {
            yPercent: 15,
            scale: 1.15,
            ease: 'none',
            scrollTrigger: {
                trigger: tofu.closest('section'),
                start: 'top top',
                end: 'bottom top',
                scrub: 1.5,
            },
        });
    }
}

/* ============================================================
   4) MARQUEE
   ============================================================ */
function initMarquee() {
    const sections = document.querySelectorAll('main > section');
    let trustBar = null;
    sections.forEach((s) => {
        if (s.textContent.includes('100% Halal') && s.textContent.includes('Zero Waste')) {
            trustBar = s;
        }
    });
    if (!trustBar) return;
    if (document.querySelector('.marquee-section')) return;

    const items = [
        '100% Organik', 'Zero Waste', 'Halal & BPOM',
        'Langsung dari Produsen', 'Same-day Jember', 'SDG 12',
    ];
    const doubled = [...items, ...items];
    const track = doubled
        .map((t) => `<span class="marquee-item">${t}</span><span class="marquee-dot">·</span>`)
        .join('');

    const wrap = document.createElement('section');
    wrap.className = 'marquee-section';
    wrap.setAttribute('data-no-reveal', '');
    wrap.innerHTML = `<div class="marquee"><div class="marquee-track">${track}</div></div>`;
    trustBar.after(wrap);
}

/* ============================================================
   5) REVEALS
   ============================================================ */
function initReveals() {
    if (REDUCE) return;

    gsap.utils.toArray('main > section:not(:first-child)').forEach((section) => {
        if (section.hasAttribute('data-no-reveal')) return;
        if (section.classList.contains('marquee-section')) return;

        const h2 = section.querySelector('h2, h3');
        if (h2) {
            ScrollTrigger.create({
                trigger: h2,
                start: 'top 82%',
                once: true,
                onEnter: () => {
                    gsap.fromTo(h2,
                        { y: 60, opacity: 0 },
                        { y: 0, opacity: 1, duration: 1.4, ease: 'power4.out' }
                    );
                },
            });
        }

        const cards = section.querySelectorAll(CARD_SELECTOR);
        if (cards.length) {
            ScrollTrigger.create({
                trigger: cards[0],
                start: 'top 82%',
                once: true,
                onEnter: () => {
                    gsap.fromTo(cards,
                        { y: 70, opacity: 0 },
                        { y: 0, opacity: 1, duration: 1.2, stagger: 0.18, ease: 'power3.out' }
                    );
                },
            });
        }
    });
}

/* ============================================================
   6) MAGNETIC
   ============================================================ */
function initMagnetic() {
    if (REDUCE) return;
    document.querySelectorAll(
        '.btn-primary, .btn-ghost, .glass-btn-amber, .btn-daftar-gradient'
    ).forEach((btn) => {
        btn.addEventListener('mousemove', (e) => {
            const r = btn.getBoundingClientRect();
            const x = e.clientX - r.left - r.width / 2;
            const y = e.clientY - r.top - r.height / 2;
            gsap.to(btn, { x: x * 0.22, y: y * 0.22, duration: 0.6, ease: 'power3.out' });
        });
        btn.addEventListener('mouseleave', () => {
            gsap.to(btn, { x: 0, y: 0, duration: 1.0, ease: 'elastic.out(1, 0.5)' });
        });
    });
}

/* ============================================================
   7) CARD TILT + SPOTLIGHT + HOVER LIFT
   ============================================================ */
function initTilt() {
    if (REDUCE) return;

    document.querySelectorAll(CARD_SELECTOR).forEach((card) => {
        card.style.transformStyle = 'preserve-3d';

        card.addEventListener('mouseenter', () => {
            gsap.to(card, {
                scale: 1.03,
                z: 30,
                duration: 0.45,
                ease: 'power2.out',
            });
        });

        card.addEventListener('mousemove', (e) => {
            const r = card.getBoundingClientRect();
            const px = e.clientX - r.left;
            const py = e.clientY - r.top;
            const nx = px / r.width - 0.5;
            const ny = py / r.height - 0.5;

            card.style.setProperty('--mx', px + 'px');
            card.style.setProperty('--my', py + 'px');

            gsap.to(card, {
                rotationY: nx * 7,
                rotationX: -ny * 7,
                transformPerspective: 1200,
                duration: 0.7,
                ease: 'power3.out',
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                scale: 1,
                z: 0,
                rotationX: 0,
                rotationY: 0,
                duration: 0.9,
                ease: 'power3.out',
            });
        });
    });
}

/* ============================================================
   SAFETY NET
   ============================================================ */
function safetyNet() {
    setTimeout(() => {
        document.querySelectorAll(CARD_SELECTOR).forEach((el) => {
            const opacity = parseFloat(window.getComputedStyle(el).opacity);
            if (opacity < 0.5) {
                gsap.to(el, { opacity: 1, y: 0, duration: 0.6, ease: 'power2.out', overwrite: true });
            }
        });
    }, 3000);
}

/* ============================================================
   INIT
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    initLenis();
    initHero();
    initParallax();
    initMarquee();
    initReveals();
    initMagnetic();
    initTilt();
    safetyNet();

    window.addEventListener('load', () => ScrollTrigger.refresh());
    setTimeout(() => ScrollTrigger.refresh(), 300);
    setTimeout(() => ScrollTrigger.refresh(), 1000);
});

Alpine.start();