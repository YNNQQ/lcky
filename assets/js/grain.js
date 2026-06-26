const GRAIN = {
    // Pixel size of each grain dot (1 = sharpest, higher = coarser)
    size: 0.5,

    // How fast the grain shifts. Lower = faster (1 = every frame, 3 = every 3rd frame)
    speed: 1,

    // Overall opacity of the canvas overlay (0–1)
    opacity: 0.18,

    // CSS mix-blend-mode — 'overlay' gives the most cinematic look;
    // try 'multiply', 'screen', or 'soft-light' for different moods
    blendMode: 'multiply',
};

// ============================================================
// Implementation — no need to edit below
// ============================================================

(function () {
    const canvas = document.createElement('canvas');
    canvas.id = 'grain';

    Object.assign(canvas.style, {
        position: 'fixed',
        inset: '0',
        width: '100%',
        height: '100%',
        pointerEvents: 'none',
        zIndex: '9999',
        opacity: GRAIN.opacity,
        mixBlendMode: GRAIN.blendMode,
    });

    document.body.appendChild(canvas);

    const ctx = canvas.getContext('2d');
    let frame = 0;
    let imageData;

    function resize() {
        canvas.width = Math.ceil(window.innerWidth / GRAIN.size);
        canvas.height = Math.ceil(window.innerHeight / GRAIN.size);
        imageData = ctx.createImageData(canvas.width, canvas.height);
    }

    function draw() {
        const data = imageData.data;
        const len = data.length;

        for (let i = 0; i < len; i += 4) {
            const v = (Math.random() * 255) | 0;
            data[i] = v; // R
            data[i + 1] = v; // G
            data[i + 2] = v; // B
            data[i + 3] = 255;
        }

        ctx.putImageData(imageData, 0, 0);
    }

    function loop() {
        if (++frame % GRAIN.speed === 0) draw();
        requestAnimationFrame(loop);
    }

    window.addEventListener('resize', resize);
    resize();
    loop();
})();
