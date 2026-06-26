// ============================================================
// GRAIN OVERLAY — tune these values
// ============================================================

const GRAIN = {
    // Tile size in px — smaller = finer grain texture
    textureSize: 200,

    // Overall opacity of the overlay (0–1)
    opacity: 0.05,

    // Animation duration — lower = faster flicker (css seconds)
    speed: '0.1s',

    // CSS mix-blend-mode
    blendMode: 'multiply',
};

// ============================================================

(function () {
    // Generate a small noise tile once
    const canvas = document.createElement('canvas');
    canvas.width  = GRAIN.textureSize;
    canvas.height = GRAIN.textureSize;
    const ctx  = canvas.getContext('2d');
    const data = ctx.createImageData(GRAIN.textureSize, GRAIN.textureSize);

    for (let i = 0; i < data.data.length; i += 4) {
        const v = (Math.random() * 255) | 0;
        data.data[i]     = v;
        data.data[i + 1] = v;
        data.data[i + 2] = v;
        data.data[i + 3] = 255;
    }

    ctx.putImageData(data, 0, 0);
    const url = canvas.toDataURL();

    // Inject keyframes + overlay via a single style tag
    const style = document.createElement('style');
    style.textContent = `
        @keyframes grain {
            0%  { transform: translate(0, 0); }
            20% { transform: translate(-10%, -15%); }
            40% { transform: translate(5%, 10%); }
            60% { transform: translate(-5%, 5%); }
            80% { transform: translate(10%, -5%); }
            100%{ transform: translate(0, 0); }
        }
        #grain {
            position: fixed;
            inset: -20%;
            width: 140%;
            height: 140%;
            background-image: url('${url}');
            background-repeat: repeat;
            opacity: ${GRAIN.opacity};
            mix-blend-mode: ${GRAIN.blendMode};
            pointer-events: none;
            z-index: 9999;
            animation: grain ${GRAIN.speed} steps(1) infinite;
            will-change: transform;
        }
    `;
    document.head.appendChild(style);

    const el = document.createElement('div');
    el.id = 'grain';
    document.body.appendChild(el);
})();
