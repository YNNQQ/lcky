/**
 * Logo scroll handoff
 *
 * The original .header__logo is fixed at the bottom of the viewport.
 * As the footer scrolls up and its top edge crosses the logo's bottom edge,
 * we clip logo1 from below (making it appear to slide behind the footer) while
 * simultaneously revealing a clone (logo2) from the same edge — so the two
 * together always look like one complete logo.
 *
 * Once logo2 is fully visible it continues tracking the footer's top edge
 * upward until it reaches STOP_OFFSET px above the viewport bottom, where it
 * locks in place.
 *
 * Masking logic
 * ─────────────
 * overlap  = how many px of logo1's bottom are below the footer's top edge
 * logo1    clips: inset(0 0 overlap 0)          — hides bottom `overlap` px
 * logo2    clips: inset(logoH - overlap 0 0 0)  — hides everything EXCEPT bottom `overlap` px
 *
 * The two masks are always complementary: logo1_visible + logo2_visible = logoH (one full logo).
 *
 * Positioning
 * ───────────
 * logo2 has  `bottom: 0`  in CSS and JS drives vertical position with translateY.
 * translateY = -(distance from viewport bottom to footer's top edge),
 * capped so the logo never rises above STOP_OFFSET from the bottom.
 */

document.addEventListener('DOMContentLoaded', () => {
    const logo1  = document.querySelector('.header__logo');
    const footer = document.getElementById('site-footer');
    if (!logo1 || !footer) return;

    // ── Clone ──────────────────────────────────────────────────────────────
    // Deep-clone logo1 so dimensions, SVG content, and styles are identical.
    const logo2 = logo1.cloneNode(true);
    logo2.classList.add('header__logo--clone');
    logo2.setAttribute('aria-hidden', 'true');
    logo2.setAttribute('tabindex', '-1');
    // Start fully hidden; the update loop takes it from here.
    logo2.style.clipPath  = 'inset(100% 0 0 0)';
    logo2.style.transform = 'translateX(-50%) translateY(0)';
    document.body.appendChild(logo2);

    // How far above the viewport bottom logo2 stops rising.
    const STOP_OFFSET = 300; // px

    // ── Update ─────────────────────────────────────────────────────────────
    function update() {
        const fRect = footer.getBoundingClientRect();
        const lRect = logo1.getBoundingClientRect();
        const logoH = lRect.height;

        // Pixels of logo1's bottom that sit below the footer's top edge.
        const overlap = Math.max(0, lRect.bottom - fRect.top);

        // ── logo1: clip its bottom `overlap` pixels ──────────────────────
        logo1.style.clipPath = overlap > 0 ? `inset(0 0 ${overlap}px 0)` : '';

        // ── logo2: mirror clip + vertical tracking ───────────────────────
        if (overlap <= 0) {
            // Footer hasn't reached the logo yet.
            // Pre-position logo2 exactly behind logo1 so the first visible
            // frame is seamless (no pop when overlap hits 1px).
            const distBottom = window.innerHeight - lRect.bottom;
            logo2.style.transform = `translateX(-50%) translateY(${-distBottom}px)`;
            logo2.style.clipPath   = 'inset(100% 0 0 0)';
            return;
        }

        // Logo2's bottom edge follows the footer's top edge upward,
        // but stops once it reaches STOP_OFFSET above the viewport bottom.
        const distFromBottom = Math.min(window.innerHeight - fRect.top, STOP_OFFSET);
        logo2.style.transform = `translateX(-50%) translateY(${-distFromBottom}px)`;

        // Complementary mask: show only the `overlap` bottom pixels.
        const hideTop = Math.max(0, logoH - overlap);
        logo2.style.clipPath = hideTop > 0 ? `inset(${hideTop}px 0 0 0)` : '';
    }

    // rAF-throttled scroll + resize listeners
    let rafId = null;

    function scheduleUpdate() {
        if (rafId) return;
        rafId = requestAnimationFrame(() => {
            update();
            rafId = null;
        });
    }

    window.addEventListener('scroll', scheduleUpdate, { passive: true });
    window.addEventListener('resize', scheduleUpdate, { passive: true });

    update();
});
