/**
 * Logo scroll handoff
 *
 * Two logos exist in the DOM — one inside .site-main, one inside .footer —
 * both position:fixed at the same coordinates. JS drives complementary
 * clip-path values so together they always show exactly one complete logo,
 * and the footer logo's bottom position tracks upward to STOP once the
 * handoff completes.
 *
 * Using JS for the clip (rather than CSS clip-path on the container) because
 * clipping position:fixed children via an ancestor's clip-path is not
 * consistent across browsers.
 */

document.addEventListener('DOMContentLoaded', () => {
    const footer     = document.getElementById('site-footer');
    const footerLogo = footer?.querySelector('.logo-clip > .header__logo');
    const mainLogo   = document.querySelector('.site-main .logo-clip > .header__logo');

    if (!footer || !footerLogo || !mainLogo) return;

    const STOP    = 300;
    const PADDING = window.innerHeight - mainLogo.getBoundingClientRect().bottom;

    function update() {
        const fTop    = footer.getBoundingClientRect().top;
        const logoH   = mainLogo.offsetHeight;
        const logoBot = window.innerHeight - PADDING;

        // Pixels of the logo zone the footer's top edge has crossed
        const overlap = Math.max(0, logoBot - fTop);

        // Main logo: clip the bottom `overlap` px behind the footer
        mainLogo.style.clipPath = overlap > 0 ? `inset(0 0 ${overlap}px 0)` : '';

        // Footer logo: reveal only the bottom `overlap` px (complementary mask)
        footerLogo.style.clipPath = `inset(${Math.max(0, logoH - overlap)}px 0 0 0)`;

        // Footer logo bottom: stay at PADDING during handoff, then rise to STOP
        const bottom = overlap <= logoH
            ? PADDING
            : PADDING + Math.min(overlap - logoH, STOP - PADDING);
        footerLogo.style.bottom = bottom + 'px';
    }

    let raf = null;
    function schedule() {
        if (raf) return;
        raf = requestAnimationFrame(() => { update(); raf = null; });
    }

    window.addEventListener('scroll', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    update();
});
