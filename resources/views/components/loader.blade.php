<!-- resources/views/components/loader.blade.php -->

{{-- Script Cek Session (Biar gak muncul terus-terusan) --}}
<script>
    if (sessionStorage.getItem('ecotahu_loaded')) {
        document.write('<style>#ecotahu-preloader { display: none !important; }</style>');
    }
</script>

<style>
  :root {
    --et-bg: #FFFFFF;
    --et-text-eco: #134E1E;
    --et-text-tahu: #52AE32;
    --et-track: rgba(0, 0, 0, 0.08);
    --et-subtext: rgba(0, 0, 0, 0.55);
    --et-glass-bg: rgba(255, 255, 255, 0.72);
    --et-glass-border: rgba(19, 78, 30, 0.14);
  }

  [data-theme="dark"] {
    --et-bg: #000000;
    --et-text-eco: #248F38;
    --et-text-tahu: #68C944;
    --et-track: rgba(255, 255, 255, 0.12);
    --et-subtext: rgba(255, 255, 255, 0.6);
    --et-glass-bg: rgba(18, 18, 18, 0.72);
    --et-glass-border: rgba(104, 201, 68, 0.22);
  }

  #ecotahu-preloader {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background-color: var(--et-bg);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 24px;
    transition: opacity 0.65s cubic-bezier(0.16, 1, 0.3, 1),
                visibility 0.65s cubic-bezier(0.16, 1, 0.3, 1),
                background-color 0.35s ease;
    user-select: none;
    -webkit-font-smoothing: antialiased;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  }

  #ecotahu-preloader.et-hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
  }

  .et-lockup {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    max-width: 100%;
  }

  @media (min-width: 640px) {
    .et-lockup {
      flex-direction: row;
      gap: 24px;
    }
  }

  .et-emblem-wrap {
    position: relative;
    width: clamp(104px, 22vw, 138px);
    height: clamp(104px, 22vw, 138px);
    flex-shrink: 0;
  }

  .et-emblem-svg {
    width: 100%;
    height: 100%;
    overflow: visible;
    filter: drop-shadow(0 12px 24px rgba(19, 78, 30, 0.12));
  }

  .et-leaf-left { transform-origin: 95px 158px; animation: etLeafLeft 2.4s cubic-bezier(0.34, 1.56, 0.64, 1) infinite; }
  .et-leaf-right { transform-origin: 108px 160px; animation: etLeafRight 2.4s cubic-bezier(0.34, 1.56, 0.64, 1) infinite; }
  .et-leaf-bottom { transform-origin: 95px 175px; animation: etLeafBottom 2.4s cubic-bezier(0.16, 1, 0.3, 1) infinite; }
  .et-tofu-cube { transform-origin: 100px 112px; animation: etTofuFloat 2.4s ease-in-out infinite; }
  .et-sprout-top { transform-origin: 106px 76px; animation: etSproutSway 2.4s cubic-bezier(0.34, 1.56, 0.64, 1) infinite; }

  .et-wordmark-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  @media (min-width: 640px) {
    .et-wordmark-wrap { align-items: flex-start; }
  }

  .et-brand-row {
    display: inline-flex;
    align-items: baseline;
    font-size: clamp(2.35rem, 6vw, 4.1rem);
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1;
  }

  .et-brand-eco { color: var(--et-text-eco); display: inline-flex; align-items: baseline; transition: color 0.3s ease; }
  .et-brand-tahu { color: var(--et-text-tahu); transition: color 0.3s ease; animation: etTextPulse 2.4s ease-in-out infinite; }
  .et-o-leaf { display: inline-block; width: 0.78em; height: 0.76em; margin: 0 0.015em; transform: translateY(0.065em); overflow: visible; }
  .et-o-leaf-inner { transform-origin: 50% 65%; animation: etInnerLeafBeat 2.4s ease-in-out infinite; }

  .et-progress-area {
    margin-top: clamp(24px, 4vw, 34px);
    width: clamp(210px, 60vw, 290px);
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .et-progress-track {
    width: 100%;
    height: 5px;
    background: var(--et-track);
    border-radius: 999px;
    overflow: hidden;
    position: relative;
  }

  .et-progress-bar {
    height: 100%;
    width: 0%;
    border-radius: 999px;
    background: linear-gradient(90deg, #134E1E 0%, #52AE32 100%);
    transition: width 0.15s ease-out;
  }

  .et-status-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    color: var(--et-subtext);
  }

  @keyframes etTofuFloat { 0%, 100% { transform: translateY(0px) scale(1); } 50% { transform: translateY(-6px) scale(1.025); } }
  @keyframes etLeafLeft { 0%, 100% { transform: rotate(0deg) scale(1); } 50% { transform: rotate(-5deg) scale(1.02); } }
  @keyframes etLeafRight { 0%, 100% { transform: rotate(0deg) scale(1); } 50% { transform: rotate(5deg) scale(1.02); } }
  @keyframes etLeafBottom { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(2.5px); } }
  @keyframes etSproutSway { 0%, 100% { transform: rotate(0deg) translateY(0px) scale(1); } 50% { transform: rotate(6deg) translateY(-5px) scale(1.05); } }
  @keyframes etInnerLeafBeat { 0%, 100% { transform: rotate(0deg) scale(1); } 50% { transform: rotate(-6deg) scale(1.07); } }
  @keyframes etTextPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.88; } }
</style>

<div id="ecotahu-preloader" role="status" aria-live="polite" aria-label="Memuat halaman EcoTahu">
  <div class="et-lockup">
    <div class="et-emblem-wrap">
      <svg class="et-emblem-svg" viewBox="0 0 210 210" fill="none" xmlns="http://www.w3.org/2000/svg">
        <defs>
          <linearGradient id="etGradBackLeft" x1="25" y1="65" x2="105" y2="165" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#68C83E" /><stop offset="65%" stop-color="#278B2B" /><stop offset="100%" stop-color="#11511D" /></linearGradient>
          <linearGradient id="etGradBackRight" x1="175" y1="75" x2="105" y2="165" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#68C83E" /><stop offset="70%" stop-color="#25872A" /><stop offset="100%" stop-color="#145A1F" /></linearGradient>
          <linearGradient id="etGradFrontLeft" x1="20" y1="105" x2="120" y2="188" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#80D846" /><stop offset="55%" stop-color="#3BA433" /><stop offset="100%" stop-color="#196A23" /></linearGradient>
          <linearGradient id="etGradFrontRight" x1="180" y1="100" x2="105" y2="175" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#64C43B" /><stop offset="60%" stop-color="#228629" /><stop offset="100%" stop-color="#0F4E1C" /></linearGradient>
          <linearGradient id="etGradSprout" x1="100" y1="18" x2="155" y2="75" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#76D142" /><stop offset="60%" stop-color="#339A30" /><stop offset="100%" stop-color="#176221" /></linearGradient>
          <linearGradient id="etTofuTop" x1="60" y1="68" x2="140" y2="112" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#FBF9EE" /><stop offset="100%" stop-color="#EFEAD2" /></linearGradient>
          <linearGradient id="etTofuLeft" x1="56" y1="90" x2="102" y2="162" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#F1ECCE" /><stop offset="100%" stop-color="#DFD4AE" /></linearGradient>
          <linearGradient id="etTofuRight" x1="102" y1="92" x2="146" y2="162" gradientUnits="userSpaceOnUse"><stop offset="0%" stop-color="#E4D9B5" /><stop offset="100%" stop-color="#CBBCA0" /></linearGradient>
        </defs>

        <g class="et-sprout-top">
          <path d="M104 78 C104 64, 108 50, 118 39" stroke="#227E28" stroke-width="4.2" stroke-linecap="round" />
          <path d="M105 64 C98 58, 91 55, 83 55" stroke="#186421" stroke-width="3.4" stroke-linecap="round" />
          <path d="M103 61 C97 49, 82 47, 74 52 C81 62, 94 65, 103 61 Z" fill="#176521" />
          <path d="M108 56 C106 33, 128 17, 160 15 C153 43, 136 61, 108 56 Z" fill="url(#etGradSprout)" />
          <path d="M109 55 C123 44, 137 33, 151 22" stroke="#8CE058" stroke-width="2.2" stroke-linecap="round" />
        </g>

        <g class="et-leaf-left"><path d="M98 76 C58 64, 30 92, 36 132 C48 115, 68 108, 98 76 Z" fill="url(#etGradBackLeft)" /></g>
        <g class="et-leaf-right"><path d="M110 80 C148 70, 178 96, 170 138 C158 116, 138 106, 110 80 Z" fill="url(#etGradBackRight)" /></g>

        <g class="et-tofu-cube">
          <path d="M102 68 L145 89 L102 111 L59 89 Z" fill="url(#etTofuTop)" />
          <path d="M59 89 L102 111 L102 162 L59 136 Z" fill="url(#etTofuLeft)" />
          <path d="M102 111 L145 89 L145 138 L102 162 Z" fill="url(#etTofuRight)" />
          <path d="M59 89 L102 111 L145 89" stroke="#FFFFFF" stroke-width="1.4" stroke-opacity="0.7" stroke-linecap="round" stroke-linejoin="round" />
          <path d="M102 111 L102 160" stroke="#F9F5E3" stroke-width="1.2" stroke-opacity="0.55" stroke-linecap="round" />
          <ellipse cx="92" cy="84" rx="3.2" ry="1.6" fill="#D8CEAC" opacity="0.65" /><ellipse cx="108" cy="91" rx="4.2" ry="2" fill="#D8CEAC" opacity="0.55" /><ellipse cx="83" cy="93" rx="2.4" ry="1.3" fill="#D8CEAC" opacity="0.6" /><ellipse cx="118" cy="85" rx="2.5" ry="1.2" fill="#D8CEAC" opacity="0.5" /><ellipse cx="101" cy="99" rx="2.8" ry="1.4" fill="#D8CEAC" opacity="0.6" />
          <ellipse cx="72" cy="110" rx="2.2" ry="3.2" transform="rotate(-18 72 110)" fill="#C9BC96" opacity="0.65" /><ellipse cx="88" cy="122" rx="2.8" ry="3.8" transform="rotate(-12 88 122)" fill="#C9BC96" opacity="0.6" /><circle cx="77" cy="127" r="2" fill="#C9BC96" opacity="0.55" /><ellipse cx="93" cy="139" rx="2" ry="3" fill="#C9BC96" opacity="0.65" /><circle cx="68" cy="122" r="1.5" fill="#C9BC96" opacity="0.5" /><circle cx="84" cy="108" r="1.7" fill="#C9BC96" opacity="0.5" />
          <ellipse cx="116" cy="118" rx="2.5" ry="3.6" transform="rotate(15 116 118)" fill="#B5A47E" opacity="0.65" /><ellipse cx="131" cy="109" rx="2" ry="3" transform="rotate(18 131 109)" fill="#B5A47E" opacity="0.6" /><circle cx="125" cy="128" r="2.2" fill="#B5A47E" opacity="0.55" /><ellipse cx="112" cy="138" rx="1.8" ry="2.8" fill="#B5A47E" opacity="0.6" /><circle cx="136" cy="121" r="1.6" fill="#B5A47E" opacity="0.5" />
        </g>

        <g class="et-leaf-left">
          <path d="M32 108 C24 148, 56 184, 98 176 C84 152, 68 136, 32 108 Z" fill="url(#etGradFrontLeft)" />
          <path d="M55 144 C72 156, 86 168, 97 177" stroke="var(--et-bg)" stroke-width="4.5" stroke-linecap="round" />
        </g>

        <g class="et-leaf-right">
          <path d="M170 106 C172 146, 144 176, 106 168 C114 138, 136 118, 170 106 Z" fill="url(#etGradFrontRight)" />
          <path d="M140 128 C154 126, 164 134, 156 148 C144 156, 126 162, 108 167 C118 148, 128 134, 140 128 Z" fill="#46AF35" opacity="0.85" />
        </g>

        <g class="et-leaf-bottom"><path d="M44 162 C68 190, 108 192, 134 175 C104 182, 72 178, 44 162 Z" fill="#186B23" /></g>
      </svg>
    </div>

    <div class="et-wordmark-wrap">
      <div class="et-brand-row">
        <span class="et-brand-eco">
          Ec<svg class="et-o-leaf" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M50 8 C25.5 8, 7 26.5, 7 51 C7 75.5, 25.5 94, 50 94 C74.5 94, 93 75.5, 93 51 C93 26.5, 74.5 8, 50 8 Z M50 25 C35.5 25, 24 36.5, 24 51 C24 65.5, 35.5 77, 50 77 C64.5 77, 76 65.5, 76 51 C76 36.5, 64.5 25, 50 25 Z" fill="currentColor" />
            <g class="et-o-leaf-inner">
              <path d="M27 74 C27 42, 49 23, 83 18 C79 52, 61 74, 27 74 Z" fill="var(--et-bg)" />
              <path d="M31 70 C34 45, 52 29, 79 23 C74 51, 58 69, 31 70 Z" fill="var(--et-bg)" />
              <path d="M28 73 C44 58, 56 44, 70 31" stroke="currentColor" stroke-width="6.5" stroke-linecap="round" />
            </g>
          </svg>
        </span><span class="et-brand-tahu">Tahu</span>
      </div>

      <div class="et-progress-area">
        <div class="et-progress-track"><div class="et-progress-bar" id="et-progress-bar"></div></div>
        <div class="et-status-row">
          <span id="et-status-text">Menyiapkan kesegaran alami...</span>
          <span id="et-status-percent">0%</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    const preloader = document.getElementById('ecotahu-preloader');
    if (sessionStorage.getItem('ecotahu_loaded')) {
        return; // Berhenti kalau udah pernah load
    }

    const progressBar = document.getElementById('et-progress-bar');
    const percentText = document.getElementById('et-status-percent');
    if (!preloader || !progressBar || !percentText) return;

    let progress = 0;
    let isPageLoaded = document.readyState === 'complete';

    const updateProgress = (val) => {
      progress = Math.min(100, Math.max(0, Math.round(val)));
      progressBar.style.width = progress + '%';
      percentText.textContent = progress + '%';
    };

    const timer = setInterval(() => {
      if (!isPageLoaded) {
        if (progress < 85) {
          updateProgress(progress + Math.max(1, (85 - progress) * 0.12));
        }
      } else {
        updateProgress(100);
        clearInterval(timer);
        setTimeout(() => {
          preloader.classList.add('et-hidden');
          sessionStorage.setItem('ecotahu_loaded', 'true'); // Tandai udah pernah load
        }, 380);
      }
    }, 60);

    window.addEventListener('load', () => {
      isPageLoaded = true;
    });
  })();
</script>