// Load color schemes in editor
// (function () {
//     // Bail if we're not in the block editor, or already initialized
//     if (
//         typeof wp === 'undefined' ||
//         !wp.blocks ||
//         !wp.blockEditor ||
//         !wp.data ||
//         window.__stirEditorInit
//     ) {
//         return;
//     }
//     window.__stirEditorInit = true;

//     const { addFilter } = wp.hooks;
//     const { createHigherOrderComponent } = wp.compose;
//     const { InspectorControls } = wp.blockEditor;
//     const { PanelBody, SelectControl } = wp.components;
//     const { createElement: el, Fragment } = wp.element;

//     const SCHEMES = window.SCHEMES?.schemes || [];

//     /* 1. Add scheme attribute to core/column */
//     addFilter(
//         'blocks.registerBlockType',
//         'theme/column-scheme-attribute',
//         function (settings, name) {
//             if (name !== 'core/column' && name !== 'core/columns') return settings;

//             settings.attributes = Object.assign({}, settings.attributes, {
//                 scheme: {
//                     type: 'string',
//                     default: '',
//                 },
//             });

//             return settings;
//         }
//     );

//     /* 2. Inspector dropdown (only if Column is inside Columns) */
//     addFilter(
//         'editor.BlockEdit',
//         'theme/column-scheme-control',
//         createHigherOrderComponent(function (BlockEdit) {
//             return function (props) {
//                 if (
//                     props.name !== 'core/column' &&
//                     props.name !== 'core/columns'
//                 ) {
//                     return el(BlockEdit, props);
//                 }

//                 const { attributes, setAttributes, clientId } = props;

//                 if (props.name === 'core/column') {
//                     const parents = wp.data
//                         .select('core/block-editor')
//                         .getBlockParents(clientId);

//                     const isInColumns = parents.some(function (id) {
//                         const block = wp.data
//                             .select('core/block-editor')
//                             .getBlock(id);
//                         return block && block.name === 'core/columns';
//                     });

//                     if (!isInColumns) {
//                         return el(BlockEdit, props);
//                     }
//                 }

//                 return el(
//                     Fragment,
//                     {},
//                     el(BlockEdit, props),
//                     el(
//                         InspectorControls,
//                         {},
//                         el(
//                             PanelBody,
//                             { title: 'Color scheme', initialOpen: true },
//                             el(SelectControl, {
//                                 label: 'Scheme',
//                                 value: attributes.scheme,
//                                 options: SCHEMES,
//                                 onChange: function (value) {
//                                     setAttributes({ scheme: value });
//                                 },
//                             }),
//                             attributes.scheme &&
//                             el(
//                                 'div',
//                                 {
//                                     className: 'scheme-preview ' + attributes.scheme,
//                                 },
//                                 el('span', null, 'Aa'),
//                                 el('span')
//                             )
//                         )
//                     )
//                 );
//             };
//         })
//     );

//     /* 3. Persist scheme as class in saved markup */
//     addFilter(
//         'blocks.getSaveContent.extraProps',
//         'theme/column-scheme-class',
//         function (extraProps, blockType, attributes) {
//             if (
//                 blockType.name !== 'core/column' &&
//                 blockType.name !== 'core/columns'
//             ) {
//                 return extraProps;
//             }

//             if (attributes.scheme) {
//                 extraProps.className = [
//                     extraProps.className,
//                     attributes.scheme,
//                 ]
//                     .filter(Boolean)
//                     .join(' ');
//             }

//             return extraProps;
//         }
//     );

//     /* ── 4. Hero image animation ── */

//     const IMG_ANIMATIONS = [
//         { label: '— None —',    value: '' },
//         { label: 'Zoom in',     value: 'img-zoom-in' },
//         { label: 'Zoom out',    value: 'img-zoom-out' },
//         { label: 'Move left',   value: 'img-move-left' },
//         { label: 'Move right',  value: 'img-move-right' },
//         { label: 'Move up',     value: 'img-move-up' },
//         { label: 'Move down',   value: 'img-move-down' },
//     ];

//     // 4a. Register imgAnimation attribute on core/image
//     addFilter(
//         'blocks.registerBlockType',
//         'theme/image-animation-attribute',
//         function (settings, name) {
//             if (name !== 'core/image') return settings;
//             settings.attributes = Object.assign({}, settings.attributes, {
//                 imgAnimation: { type: 'string', default: '' },
//             });
//             return settings;
//         }
//     );

//     // 4b. Inspector panel — only show when the image is inside a hero gallery
//     addFilter(
//         'editor.BlockEdit',
//         'theme/image-animation-control',
//         createHigherOrderComponent(function (BlockEdit) {
//             return function (props) {
//                 if (props.name !== 'core/image') return el(BlockEdit, props);

//                 const { attributes, setAttributes, clientId } = props;

//                 // Walk up: image → gallery → column → columns
//                 // Show control only when an ancestor columns block has section--hero in its className
//                 const ancestors = wp.data
//                     .select('core/block-editor')
//                     .getBlockParents(clientId);

//                 const isHero = ancestors.some(function (id) {
//                     const block = wp.data.select('core/block-editor').getBlock(id);
//                     if (!block) return false;
//                     const cls = block.attributes && block.attributes.className || '';
//                     return cls.includes('section--hero');
//                 });

//                 if (!isHero) return el(BlockEdit, props);

//                 return el(
//                     Fragment,
//                     {},
//                     el(BlockEdit, props),
//                     el(
//                         InspectorControls,
//                         {},
//                         el(
//                             PanelBody,
//                             { title: 'Image animation', initialOpen: true },
//                             el(SelectControl, {
//                                 label: 'Animation',
//                                 value: attributes.imgAnimation,
//                                 options: IMG_ANIMATIONS,
//                                 onChange: function (value) {
//                                     setAttributes({ imgAnimation: value });
//                                 },
//                             })
//                         )
//                     )
//                 );
//             };
//         })
//     );

//     // 4c. Persist imgAnimation as a class on the saved <figure>
//     addFilter(
//         'blocks.getSaveContent.extraProps',
//         'theme/image-animation-class',
//         function (extraProps, blockType, attributes) {
//             if (blockType.name !== 'core/image') return extraProps;
//             if (attributes.imgAnimation) {
//                 extraProps.className = [
//                     extraProps.className,
//                     attributes.imgAnimation,
//                 ]
//                     .filter(Boolean)
//                     .join(' ');
//             }
//             return extraProps;
//         }
//     );
// })();


// Splash screen
document.addEventListener('DOMContentLoaded', () => {
    const splash = document.getElementById('splash-screen');
    if (!splash) return;

    // Only show once per browser tab (sessionStorage cleared on tab close)
    if (sessionStorage.getItem('splashShown')) {
        splash.style.display = 'none';
        return;
    }
    sessionStorage.setItem('splashShown', '1');

    // Block scroll and interaction for the full duration of the splash
    document.body.classList.add('splash-active');

    // CSS animation ends at 1480ms (1000ms delay + 480ms sequence); hold 1500ms, then slide out
    setTimeout(() => {
        splash.classList.add('is-leaving');

        function teardown() {
            splash.style.display = 'none';
            document.body.classList.remove('splash-active');
        }

        splash.addEventListener('transitionend', teardown, { once: true });
        setTimeout(teardown, 900); // fallback if transitionend never fires
    }, 3100);
});


// Hero slider — initialise Swiper on the gallery inside .section--hero
document.addEventListener('DOMContentLoaded', () => {
    const hero = document.querySelector('.section--hero--swiper');
    if (!hero || typeof Swiper === 'undefined') return;

    const gallery = hero.querySelector('.wp-block-gallery');
    const heading = hero.querySelector('.wp-block-heading');
    if (!gallery) return;

    // Restructure: gallery → swiper container, each figure → swiper-slide
    gallery.classList.add('swiper');

    let wrapper = gallery.querySelector('.swiper-wrapper');
    if (!wrapper) {
        wrapper = document.createElement('div');
        wrapper.className = 'swiper-wrapper';
        const slides = Array.from(gallery.querySelectorAll('.wp-block-image'));
        slides.forEach(fig => {
            fig.classList.add('swiper-slide');
            wrapper.appendChild(fig);
        });
        gallery.appendChild(wrapper);
    }

    // Move heading above slider in stacking order
    if (heading) {
        hero.appendChild(heading);
    }

    const css = getComputedStyle(document.documentElement);

    const ANIM_MAP = {
        'img-zoom-in': css.getPropertyValue('--animation-zoom-in').trim(),
        'img-zoom-out': css.getPropertyValue('--animation-zoom-out').trim(),
        'img-move-left': css.getPropertyValue('--animation-move-left').trim(),
        'img-move-right': css.getPropertyValue('--animation-move-right').trim(),
        'img-move-up': css.getPropertyValue('--animation-move-up').trim(),
        'img-move-down': css.getPropertyValue('--animation-move-down').trim(),
    };
    const ANIM_CLASSES = Object.keys(ANIM_MAP);

    // Stop animation on a slide (called for all non-active slides)
    function stopSlideAnimation(slide) {
        const img = slide.querySelector('img');
        if (img) img.style.animation = 'none';
    }

    // Start animation on a slide (called for the incoming active slide)
    function startSlideAnimation(slide) {
        const img = slide.querySelector('img');
        if (!img) return;
        const cls = ANIM_CLASSES.find(c => slide.classList.contains(c));
        if (!cls) { img.style.animation = 'none'; return; }
        img.style.animation = 'none';
        void img.offsetWidth; // force reflow so animation restarts clean
        img.style.animation = ANIM_MAP[cls];
    }

    // Initialise — stop all slides, start first active
    Array.from(gallery.querySelectorAll('.swiper-slide')).forEach(stopSlideAnimation);

    const swiper = new Swiper(gallery, {
        loop: true,
        speed: 800,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: { crossFade: true },
        a11y: {
            prevSlideMessage: 'Previous slide',
            nextSlideMessage: 'Next slide',
        },
        on: {
            afterInit(sw) {
                const active = sw.slides[sw.activeIndex];
                if (active) startSlideAnimation(active);
            },
            slideChangeTransitionStart(sw) {
                // Start incoming slide animation immediately (it fades in over it)
                const incoming = sw.slides[sw.activeIndex];
                if (incoming) startSlideAnimation(incoming);
            },
            slideChangeTransitionEnd(sw) {
                // Stop all non-active slides to reset them cleanly for next time
                sw.slides.forEach((slide, i) => {
                    if (i !== sw.activeIndex) stopSlideAnimation(slide);
                });
            },
        },
    });
});


// Focus section — category hover switching
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth <= 782) return; // handled by mobile swiper
    const categories = document.querySelectorAll('.section--focus:not(.section--focus--exp) .focus__category');
    if (!categories.length) return;

    const projectLink = document.getElementById('focus-project-link');
    const projectImage = document.getElementById('focus-project-image');
    const projectTitle = document.getElementById('focus-project-title');
    const imageWrap = projectImage && projectImage.closest('.focus__image-wrap');
    if (!projectImage || !projectTitle || !imageWrap) return;

    // Make the wrap a positioning context for the crossfade overlay
    imageWrap.style.position = 'relative';

    let activeCategory = document.querySelector('.focus__category.is-active');
    let transitioning = false;

    const FADE_MS = 500;
    const EASE = `opacity ${FADE_MS}ms cubic-bezier(0.4, 0, 0.2, 1)`;

    function activateCategory(item) {
        if (item === activeCategory) return;

        // Update active state immediately (CSS handles opacity on the list)
        if (activeCategory) {
            activeCategory.classList.remove('is-active');
            activeCategory.setAttribute('aria-pressed', 'false');
        }
        item.classList.add('is-active');
        item.setAttribute('aria-pressed', 'true');
        activeCategory = item;

        const newSrc = item.dataset.image;
        const newTitle = item.dataset.title;
        const newUrl = item.dataset.url;

        // Update title and link immediately — no reason to wait for image
        projectTitle.textContent = newTitle;
        if (projectLink) projectLink.href = newUrl;

        // Create overlay image that fades in over the current one
        const overlay = document.createElement('img');
        overlay.src = newSrc;
        overlay.alt = newTitle;
        overlay.className = 'focus__image';
        overlay.style.cssText = `
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: ${EASE};
            z-index: 1;
        `;
        imageWrap.appendChild(overlay);

        function commit() {
            // Swap the base image and remove overlay
            projectImage.src = newSrc;
            projectImage.alt = newTitle;
            overlay.remove();
            transitioning = false;
        }

        // Trigger fade-in after next paint
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                overlay.style.opacity = '1';
            });
        });

        overlay.addEventListener('transitionend', commit, { once: true });

        // Fallback in case transitionend doesn't fire
        setTimeout(commit, FADE_MS + 100);
    }

    categories.forEach(item => {
        item.addEventListener('mouseenter', () => activateCategory(item));
        item.addEventListener('focus', () => activateCategory(item));
        item.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (projectLink) window.location.href = projectLink.href;
            }
        });
    });
});


// Focus 2 — click-to-activate categories with image crossfade
document.addEventListener('DOMContentLoaded', () => {
    const categories = document.querySelectorAll('.section--focus--exp .focus__category');
    if (!categories.length) return;

    const section = document.querySelector('.section--focus--exp');
    const projectLink = section && section.querySelector('.focus__project-link');
    const projectImage = section && section.querySelector('.focus__image-wrap .focus__image');
    const projectTitle = section && section.querySelector('.focus__project-title');
    const imageWrap = projectImage && projectImage.closest('.focus__image-wrap');
    if (!projectImage || !projectTitle || !imageWrap) return;

    imageWrap.style.position = 'relative';

    const FADE_MS = 400;

    function activate(item) {
        // Update active state
        categories.forEach(c => {
            c.classList.remove('is-active');
            c.setAttribute('aria-expanded', 'false');
        });
        item.classList.add('is-active');
        item.setAttribute('aria-expanded', 'true');

        const newSrc = item.dataset.image;
        const newTitle = item.dataset.title;
        const newUrl = item.dataset.url;

        // Update link and title immediately
        projectTitle.textContent = newTitle;
        if (projectLink) projectLink.href = newUrl;

        // Crossfade image
        const overlay = document.createElement('img');
        overlay.src = newSrc;
        overlay.alt = newTitle;
        overlay.className = 'focus__image';
        overlay.style.cssText = `position:absolute;inset:0;opacity:0;transition:opacity ${FADE_MS}ms ease;z-index:1;width:100%;height:100%;`;
        imageWrap.appendChild(overlay);

        requestAnimationFrame(() => requestAnimationFrame(() => {
            overlay.style.opacity = '1';
        }));

        function commit() {
            projectImage.src = newSrc;
            projectImage.alt = newTitle;
            overlay.remove();
        }
        overlay.addEventListener('transitionend', commit, { once: true });
        setTimeout(commit, FADE_MS + 100);
    }

    categories.forEach(item => {
        item.addEventListener('click', () => activate(item));
        item.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                activate(item);
            }
        });
    });
});


// Team members — click to single if has content
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.team__member.has-content').forEach(member => {
        const url = member.dataset.url;
        if (!url) return;
        member.addEventListener('click', () => { window.location.href = url; });
        member.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                window.location.href = url;
            }
        });
    });
});


// Project single — measure title + columns, set CSS vars, meta toggle
document.addEventListener('DOMContentLoaded', () => {
    const introSection = document.querySelector('.section--project-intro');
    const titleEl = document.getElementById('project-hero-title');
    const columnsEl = document.querySelector('.project__columns');

    function setProjectVars() {
        if (introSection && titleEl && columnsEl) {
            const titleH = titleEl.offsetHeight;
            const columnsH = columnsEl.offsetHeight;
            introSection.style.setProperty('--title-block', titleH + 'px');
            introSection.style.setProperty('--columns-height', columnsH + 'px');
        }
    }

    setProjectVars();

    // ── Meta toggle ──
    const toggle = document.querySelector('.project__meta-toggle');
    const metaMore = document.querySelector('.project__meta-more');

    if (toggle && metaMore) {
        toggle.addEventListener('click', () => {
            const expanded = metaMore.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', expanded);
            toggle.textContent = expanded ? 'Lees minder –' : 'Lees meer +';
        });
    }

    // ── Project content: gallery → Swiper ──
    const contentContainer = document.querySelector('.section--project-content__container');
    if (contentContainer && typeof Swiper !== 'undefined') {
        contentContainer.querySelectorAll('.wp-block-gallery').forEach(gallery => {
            // Build wrapper structure
            const slides = Array.from(gallery.querySelectorAll('.wp-block-image'));
            const captions = Array.from(gallery.querySelectorAll('figcaption, .wp-element-caption'));

            if (slides.length < 1) return;

            // Collect caption text before restructuring
            const captionText = captions.map(c => c.textContent.trim()).filter(Boolean).join(' ');

            // Create swiper wrapper
            const swiperWrapper = document.createElement('div');
            swiperWrapper.className = 'swiper-wrapper';
            slides.forEach(img => {
                img.classList.add('swiper-slide');
                // Remove inline captions from image
                img.querySelectorAll('figcaption, .wp-element-caption').forEach(c => c.remove());
                swiperWrapper.appendChild(img);
            });

            const swiperEl = document.createElement('div');
            swiperEl.className = 'project-content-gallery__swiper swiper';
            swiperEl.appendChild(swiperWrapper);

            // Arrows
            const arrowSvg = `<svg viewBox="0 0 32 38" xmlns="http://www.w3.org/2000/svg"><path d="M31.8,21.2l-3.4-3.4-12.7,12.7L3.2,18,0,21.2l15.9,15.9,15.9-15.9Z"/><path d="M18.1,0v23.5c0,1.2-1,2.3-2.3,2.3s-2.3-1.1-2.3-2.3V0s4.5,0,4.5,0Z"/></svg>`;
            const prevBtn = document.createElement('button');
            prevBtn.className = 'project-content-gallery__arrow project-content-gallery__arrow--prev button';
            prevBtn.innerHTML = arrowSvg;
            prevBtn.setAttribute('aria-label', 'Vorige afbeelding');
            const nextBtn = document.createElement('button');
            nextBtn.className = 'project-content-gallery__arrow project-content-gallery__arrow--next button';
            nextBtn.innerHTML = arrowSvg;
            nextBtn.setAttribute('aria-label', 'Volgende afbeelding');

            // Caption element
            const captionEl = document.createElement('p');
            captionEl.className = 'project-content-gallery__caption wp-element-caption';
            if (captionText) captionEl.textContent = captionText;

            // Build wrapper grid
            const wrapper = document.createElement('div');
            wrapper.className = 'project-content-gallery';
            wrapper.appendChild(prevBtn);
            wrapper.appendChild(swiperEl);
            wrapper.appendChild(nextBtn);
            if (captionText) wrapper.appendChild(captionEl);

            gallery.replaceWith(wrapper);

            const swiper = new Swiper(swiperEl, {
                loop: slides.length > 1,
                speed: 600,
                slidesPerView: 1,
                centeredSlides: true,
            });

            prevBtn.addEventListener('click', () => swiper.slidePrev());
            nextBtn.addEventListener('click', () => swiper.slideNext());
        });
    }
});


// Logo animation — JS-driven, interruptible
document.addEventListener('DOMContentLoaded', () => {
    const logo = document.querySelector('.header-logo');
    if (!logo) return;

    const header = document.getElementById('site-header');

    // The 7 letter elements in sequence (break-off order = build-up reverse)
    // Break-off: right-R(7) → left-R(6) → dot(5) → S(4) → T(3) → I(2) → M-paths
    // Build-up:  M-paths → I(2) → T(3) → S(4) → dot(5) → left-R(6) → right-R(7)
    function getLetters() {
        const group = logo.querySelector('.logo__group');
        const m = logo.querySelector('.logo__m');
        if (!group || !m) return { letters: [], dash: null };

        const mPaths = Array.from(m.querySelectorAll('.cls-1:not(.logo__dash)'));
        const dash = m.querySelector('.logo__dash');

        const groupChildren = Array.from(group.children);
        const letters = [
            groupChildren[6], // right R
            groupChildren[5], // I
            groupChildren[4], // T
            groupChildren[3], // S
            groupChildren[2], // dot
            groupChildren[1], // left R
            ...mPaths,        // M paths (all fade together as one step)
        ];

        return { letters, dash };
    }

    const STEP_MS = 200;  // delay between each letter
    const FADE_MS = 300;  // transition duration per letter
    const DASH_MS = 400;  // dash flip duration
    const LEAD_MS = 300;  // pause before any animation begins

    let timers = [];

    // clearTimers cancels every pending timer — both lead-in and in-flight steps
    function clearTimers() {
        timers.forEach(clearTimeout);
        timers = [];
    }

    function freezeOpacity(el) {
        const live = parseFloat(getComputedStyle(el).opacity);
        el.style.transition = 'none';
        el.style.opacity = live;
        void el.offsetWidth;
    }

    function freezeDash(dash) {
        const matrix = getComputedStyle(dash).transform;
        let scaleX = 1;
        if (matrix && matrix !== 'none') {
            const vals = matrix.match(/matrix\(([^)]+)\)/);
            if (vals) scaleX = parseFloat(vals[1].split(',')[0]);
        }
        dash.style.transition = 'none';
        dash.style.transform = `scaleX(${scaleX})`;
        void dash.offsetWidth;
    }

    function animateLetters(letters, toOpacity, stagger) {
        letters.forEach((el, i) => {
            if (!el) return;
            const delay = stagger ? i * STEP_MS : 0;
            const t = setTimeout(() => {
                el.style.transition = `opacity ${FADE_MS}ms ease`;
                el.style.opacity = toOpacity;
            }, delay);
            timers.push(t);
        });
    }

    function animateDash(dash, dashTarget, delay) {
        if (!dash) return;
        const t = setTimeout(() => {
            dash.style.transition = `transform ${DASH_MS}ms ease`;
            dash.style.transform = `scaleX(${dashTarget})`;
        }, delay);
        timers.push(t);
    }

    function isMidFlight(letters) {
        return letters.some(el => {
            if (!el) return false;
            const op = parseFloat(getComputedStyle(el).opacity);
            return op > 0.01 && op < 0.99;
        });
    }

    // ── Core animation runners (called after the lead-in delay) ──────────────

    function doBreakOff() {
        const { letters, dash } = getLetters();
        const interrupted = isMidFlight(letters);
        letters.forEach(el => el && freezeOpacity(el));
        if (dash) freezeDash(dash);
        animateLetters(letters, 0, !interrupted);
        const dashDelay = interrupted ? 0 : (letters.length - 1) * STEP_MS;
        animateDash(dash, -1, dashDelay);
    }

    function doBuildUp() {
        const { letters, dash } = getLetters();
        const interrupted = isMidFlight(letters);
        letters.forEach(el => el && freezeOpacity(el));
        if (dash) freezeDash(dash);
        animateDash(dash, 1, 0);
        animateLetters([...letters].reverse(), 1, !interrupted);
    }

    // ── Scheduled wrappers: clearTimers first, then wait `delay` ms ──────────
    // Any new call immediately cancels the previous lead-in or in-flight steps.

    function startBreakOff(delay = LEAD_MS) {
        clearTimers();
        timers.push(setTimeout(doBreakOff, delay));
    }

    function startBuildUp(delay = LEAD_MS) {
        clearTimers();
        timers.push(setTimeout(doBuildUp, delay));
    }

    // ── Initial break-off: wait for the splash to exit first if it is showing ─

    if (document.body.classList.contains('splash-active')) {
        const splashObserver = new MutationObserver(() => {
            if (!document.body.classList.contains('splash-active')) {
                splashObserver.disconnect();
                startBreakOff(LEAD_MS);
            }
        });
        splashObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    } else {
        startBreakOff(LEAD_MS);
    }

    // ── Hover: immediate (no lead-in — direct user interaction) ──────────────

    logo.addEventListener('mouseenter', () => {
        startBuildUp(0);
    });

    logo.addEventListener('mouseleave', () => {
        if (!header || !header.classList.contains('is-open')) {
            startBreakOff(0);
        }
    });

    // ── Menu open / close: track wasOpen so only is-open changes are acted on ─
    // Without this guard, closeMenu()'s delayed removeClass('scheme-dark-grey')
    // would re-fire the observer mid-animation and collapse the stagger.

    let wasOpen = header ? header.classList.contains('is-open') : false;

    const observer = new MutationObserver(() => {
        if (!header) return;
        const isOpen = header.classList.contains('is-open');
        if (isOpen === wasOpen) return; // class changed but is-open didn't — ignore
        wasOpen = isOpen;
        if (isOpen) {
            startBuildUp(LEAD_MS);
        } else {
            startBreakOff(LEAD_MS);
        }
    });
    if (header) {
        observer.observe(header, { attributes: true, attributeFilter: ['class'] });
    }
});


// Nav menu — slide down, push main
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('.nav-toggle');
    const header = document.getElementById('site-header');
    const menu = document.getElementById('nav-menu');
    const main = document.querySelector('.site-main');
    if (!toggle || !header || !menu) return;

    // Capture the natural bar height once before any JS changes
    const barHeight = header.getBoundingClientRect().height;

    function openMenu() {
        // Add is-open first so all is-open-dependent CSS (e.g. mobile padding-bottom)
        // is already applied when we measure heights below.
        header.classList.add('is-open');
        menu.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');

        const menuH = menu.scrollHeight;

        if (window.innerWidth <= 782) {
            // On mobile the header-container is a grid whose row-gap (60px) sits between
            // the logo/toggle row and the nav-menu row. barHeight (the CSS-clipped 80px)
            // doesn't account for that gap, so we use the header's own scrollHeight
            // instead — it naturally captures: logo row + grid row-gap + menu content
            // + the is-open padding-bottom that was missing before.
            header.style.height = header.scrollHeight + 'px';
        } else {
            header.style.height = (barHeight + menuH) + 'px';
        }

        if (main) {
            // Offset margin-top by menuH so content below the sticky header shifts down.
            // Clamp to ≤ 0 so the sticky header never gets pushed below viewport top.
            const rawMargin = window.innerWidth <= 782
                ? (-4.25 * barHeight + menuH)
                : (-3 * barHeight + menuH);
            main.style.marginTop = Math.min(0, rawMargin) + 'px';
        }
    }

    function closeMenu() {
        header.style.height = '';
        if (main) main.style.marginTop = 'calc(var(--header-height) * -1)';
        header.classList.remove('is-open');
        menu.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        setTimeout(() => header.classList.remove('scheme-dark-grey'), 450);
    }

    toggle.addEventListener('click', () => {
        header.classList.contains('is-open') ? closeMenu() : openMenu();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && header.classList.contains('is-open')) closeMenu();
    });

    menu.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', closeMenu);
    });
});


// Open external links in a new tab
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a[href]').forEach(link => {
        try {
            if (link.hostname && link.hostname !== window.location.hostname) {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'noopener noreferrer');
            }
        } catch (e) { }
    });
});


// Project slider — Swiper init
document.addEventListener('DOMContentLoaded', () => {
    const sliderEl = document.querySelector('.project-slider__swiper');
    if (!sliderEl || typeof Swiper === 'undefined') return;

    const swiper = new Swiper(sliderEl, {
        slidesPerView: 'auto',
        spaceBetween: 20,
        grabCursor: true,
        loop: false,
        centeredSlides: false,
        observer: true,
        observeParents: true,
        breakpoints: {
            783: { spaceBetween: 40 },
        },
    });

    // Force recalculation after CSS custom properties have resolved
    requestAnimationFrame(() => swiper.update());
});


// Strip — mobile: restructure into sticky rows (image on top, content on bottom)
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth > 782) return;

    document.querySelectorAll('.section--strip').forEach(section => {
        const left = section.querySelector('.strip__left');
        const right = section.querySelector('.strip__right');
        if (!left || !right) return;

        const rows = parseInt(section.dataset.rows, 10) || 5;
        const leftPanels  = Array.from(left.querySelectorAll(':scope > .strip__panel'));
        const rightPanels = Array.from(right.querySelectorAll(':scope > .strip__panel'));
        const count = Math.min(rows, leftPanels.length, rightPanels.length);

        const rowsEl = document.createElement('div');
        rowsEl.className = 'strip__rows';

        for (let n = 0; n < count; n++) {
            const leftPanel  = leftPanels[count - 1 - n]; // reversed DOM
            const rightPanel = rightPanels[n];             // normal DOM

            const rowEl = document.createElement('div');
            rowEl.className = 'strip__row';
            rowEl.style.zIndex = n + 1;

            const imagePanel   = leftPanel.classList.contains('strip__panel--image')   ? leftPanel : rightPanel;
            const contentPanel = leftPanel.classList.contains('strip__panel--content') ? leftPanel : rightPanel;

            rowEl.appendChild(imagePanel);
            rowEl.appendChild(contentPanel);
            rowsEl.appendChild(rowEl);
        }

        left.remove();
        right.remove();
        section.appendChild(rowsEl);
    });
});


// Approach page — bouwstenen scroll
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth <= 782) return; // CSS sticky rows handle mobile
    const track = document.querySelector('.strip-track:has(.section--strip[data-type="bouwstenen"])');
    const section = document.querySelector('.section--strip[data-type="bouwstenen"]');
    if (!section || !track) return;

    const left = section.querySelector('.strip__left');
    const right = section.querySelector('.strip__right');
    if (!left || !right) return;

    const rows = parseInt(section.dataset.rows, 10) || 5;
    const REVEAL_ROW = 1;   // row at which second hero is quietly raised (0-indexed = bouwsteen 2)
    const TRANSITION_MS = 600;
    const secondHero = document.querySelector('.section--full--second');
    const siteHeader = document.getElementById('site-header');

    let currentRow = -2;    // sentinel — forces first paint
    let transitioning = false; // blocks rapid wheel events during animation
    let snapTimer = null;
    let isSnapping = false;

    // Extra cooldown after each wheel-triggered row change.
    // transitioning (600ms) covers the animation itself; wheelLocked adds an
    // additional window so trackpad momentum events that arrive after the
    // animation ends don't chain into another row immediately.
    let wheelLocked = false;
    let wheelLockTimer = null;
    const WHEEL_LOCK_MS = TRANSITION_MS + 600; // 1 200ms total

    function lockWheel() {
        wheelLocked = true;
        clearTimeout(wheelLockTimer);
        wheelLockTimer = setTimeout(() => { wheelLocked = false; }, WHEEL_LOCK_MS);
    }

    // Left strip  (reversed DOM): show row N → translateY((N − rows + 1) × 100%)
    //   hidden above → translateY(−rows × 100%)  |  hidden below → translateY(100%)
    // Right strip (normal DOM):   show row N → translateY(−N × 100%)
    //   hidden below → translateY(100%)           |  hidden above → translateY(−rows × 100%)
    function setTransforms(row, animate) {
        if (!animate) {
            left.style.transition = 'none';
            right.style.transition = 'none';
            void left.offsetHeight; // flush
        } else {
            left.style.transition = '';
            right.style.transition = '';
        }

        if (row < 0) {
            left.style.transform = `translateY(${-rows * 100}%)`;
            right.style.transform = 'translateY(100%)';
        } else if (row >= rows) {
            left.style.transform = 'translateY(100%)';
            right.style.transform = `translateY(${-rows * 100}%)`;
        } else {
            left.style.transform = `translateY(${(row - rows + 1) * 100}%)`;
            right.style.transform = `translateY(${-row * 100}%)`;
        }
    }

    function getTrackTop() {
        return track.getBoundingClientRect().top + window.scrollY;
    }

    // Map the current scroll position to a row value.
    function getTargetRow() {
        const scrolled = window.scrollY - getTrackTop();
        const innerH = window.innerHeight;
        if (scrolled < 0) return -1;
        if (scrolled >= rows * innerH) return rows;
        return Math.floor(scrolled / innerH);
    }

    // Update header-logo / nav-toggle fill based on which strip panels are visible.
    // Left strip uses reversed DOM: row N → panel at index (rows - 1 - N).
    // Right strip uses normal DOM:  row N → panel at index N.
    function updateHeaderColorState(row) {
        if (!siteHeader) return;
        siteHeader.classList.remove('header--left-image', 'header--right-image');
        if (row < 0 || row >= rows) return;

        const leftPanels = Array.from(left.querySelectorAll('.strip__panel'));
        const rightPanels = Array.from(right.querySelectorAll('.strip__panel'));

        if (leftPanels[rows - 1 - row]?.classList.contains('strip__panel--image')) {
            siteHeader.classList.add('header--left-image');
        }
        if (rightPanels[row]?.classList.contains('strip__panel--image')) {
            siteHeader.classList.add('header--right-image');
        }
    }

    // Instantly move the page to the canonical scroll position for `row` so the
    // scrollbar reflects the current bouwsteen. Called after every wheel step.
    function snapPageToRow(row) {
        const trackTop = getTrackTop();
        const innerH = window.innerHeight;
        let targetY;
        if (row < 0) targetY = 0;                              // top of page → first hero
        else if (row >= rows) targetY = trackTop + (rows + 1) * innerH; // start of second hero
        else targetY = trackTop + row * innerH;
        window.scrollTo({ top: targetY, behavior: 'instant' });
    }

    // Smoothly snap to the nearest row boundary after scroll/touch ends.
    function smoothSnapToRow(row) {
        if (row < 0 || row >= rows) return;
        if (isSnapping) return;
        const targetY = getTrackTop() + row * window.innerHeight;
        if (Math.abs(window.scrollY - targetY) < 2) return;
        isSnapping = true;
        window.scrollTo({ top: targetY, behavior: 'smooth' });
        setTimeout(() => { isSnapping = false; }, TRANSITION_MS + 150);
    }

    // Animate strips to `newRow`, instantly snap page position to match.
    function goToRow(newRow) {
        if (transitioning) return;
        newRow = Math.max(-1, Math.min(rows, newRow));
        if (newRow === currentRow) return;
        transitioning = true;
        currentRow = newRow;
        setTransforms(newRow, true);
        updateHeaderColorState(newRow);

        if (newRow === -1) {
            // Exit to first hero: strips animate out, safe to drop second hero now.
            if (secondHero) secondHero.classList.remove('is-revealed');
            setTimeout(() => {
                snapPageToRow(-1);
                transitioning = false;
            }, TRANSITION_MS);

        } else if (newRow === rows) {
            // Exit to second hero: is-revealed was already raised at REVEAL_ROW while
            // strips fully covered the viewport, so no swap is visible here — just snap.
            setTimeout(() => {
                snapPageToRow(rows);
                transitioning = false;
            }, TRANSITION_MS);

        } else {
            // Normal row: raise/lower second hero while strips cover the viewport.
            if (secondHero) {
                if (newRow >= REVEAL_ROW) {
                    secondHero.classList.add('is-revealed');
                } else {
                    secondHero.classList.remove('is-revealed');
                }
            }
            snapPageToRow(newRow);
            setTimeout(() => { transitioning = false; }, TRANSITION_MS);
        }
    }

    // ── Wheel: one discrete row per event ──────────────────────────────────────
    const navToggle = document.querySelector('.nav-toggle');
    window.addEventListener('wheel', (e) => {
        if (document.body.classList.contains('splash-active')) return;
        const siteHeader = document.getElementById('site-header');
        if (siteHeader && siteHeader.classList.contains('is-open')) {
            e.preventDefault();
            navToggle && navToggle.click();
            return;
        }
        if (transitioning || wheelLocked) {
            // Prevent trackpad/wheel momentum from scrolling the page while
            // a bouwstenen animation is in flight or during the post-animation
            // cooldown that absorbs trackpad inertia.
            e.preventDefault();
            return;
        }
        const down = e.deltaY > 0;

        // Scroll down on hero → slide both columns in (row 0)
        if (down && currentRow === -1) {
            e.preventDefault();
            lockWheel();
            goToRow(0);
            return;
        }

        // Within bouwstenen → advance or retreat by exactly one row
        if (currentRow >= 0 && currentRow < rows) {
            e.preventDefault();
            lockWheel();
            goToRow(down ? currentRow + 1 : currentRow - 1);
            return;
        }

        // Scroll up from exit state: only intercept once the second hero is fully in
        // view (scroll at its start). While the user is scrolling up through the
        // second hero or footer, let native scroll run.
        if (!down && currentRow === rows) {
            const heroStart = getTrackTop() + (rows + 1) * window.innerHeight;
            if (window.scrollY <= heroStart + 2) {
                e.preventDefault();
                lockWheel();
                goToRow(rows - 1);
            }
            return;
        }

        // Everything else (scroll up in hero, scroll down past exit): native scroll.
    }, { passive: false });

    // ── Scroll: driven by scrollbar / touch ────────────────────────────────────
    window.addEventListener('scroll', () => {
        if (transitioning || isSnapping) return;
        const targetRow = getTargetRow();
        if (targetRow !== currentRow) {
            setTransforms(targetRow, true);
            currentRow = targetRow;
            updateHeaderColorState(targetRow);
            // Mirror the reveal logic for scroll/touch-driven navigation.
            if (secondHero) {
                if (targetRow >= REVEAL_ROW) {
                    secondHero.classList.add('is-revealed');
                } else {
                    secondHero.classList.remove('is-revealed');
                }
            }
        }
        clearTimeout(snapTimer);
        snapTimer = setTimeout(() => smoothSnapToRow(getTargetRow()), 200);
    }, { passive: true });

    // ── Init: paint correct state without animation ────────────────────────────
    const initRow = getTargetRow();
    currentRow = initRow;
    setTransforms(initRow, false);
    updateHeaderColorState(initRow);
    if (secondHero && initRow >= REVEAL_ROW) secondHero.classList.add('is-revealed');

    // ── External navigation via custom event ──────────────────────────────────
    window.addEventListener('bouwstenen:goTo', (e) => {
        goToRow(e.detail.row);
    });
});

// Header — data-header-theme
document.addEventListener('DOMContentLoaded', () => {
    const siteHeader = document.getElementById('site-header');
    if (!siteHeader) return;

    const themedSections = Array.from(document.querySelectorAll('[data-header-theme]'));
    if (!themedSections.length) return;

    const THEME_AHEAD = -60;

    function updateHeaderTheme() {
        const hBottom = siteHeader.getBoundingClientRect().bottom;
        let theme = null;
        for (const section of themedSections) {
            const { top, bottom } = section.getBoundingClientRect();
            if (top < hBottom + THEME_AHEAD && bottom > 0) {
                theme = section.dataset.headerTheme;
            }
        }
        siteHeader.classList.toggle('on-dark', theme === 'dark');
    }

    let ticking = false;
    window.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => { updateHeaderTheme(); ticking = false; });
    }, { passive: true });

    updateHeaderTheme();
});


// Contact page — contact track scroll
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth <= 782) return; // CSS sticky rows handle mobile
    const track = document.querySelector('.strip-track:has(.section--strip[data-type="contact"])');
    const section = document.querySelector('.section--strip[data-type="contact"]');
    if (!section || !track) return;

    const left = section.querySelector('.strip__left');
    const right = section.querySelector('.strip__right');
    if (!left || !right) return;

    const rows = parseInt(section.dataset.rows, 10) || 2;
    const REVEAL_ROW = rows - 1; // raise second hero quietly at the last contact row
    const TRANSITION_MS = 600;
    const secondHero = document.querySelector('.section--full--second');

    let currentRow = -2;    // sentinel — forces first paint
    let transitioning = false;
    let snapTimer = null;
    let isSnapping = false;

    // Same transform formula as bouwstenen (reversed-DOM left, normal-DOM right)
    function setTransforms(row, animate) {
        if (!animate) {
            left.style.transition = 'none';
            right.style.transition = 'none';
            void left.offsetHeight;
        } else {
            left.style.transition = '';
            right.style.transition = '';
        }

        if (row >= rows) {
            left.style.transform = 'translateY(100%)';
            right.style.transform = `translateY(${-rows * 100}%)`;
        } else {
            left.style.transform = `translateY(${(row - rows + 1) * 100}%)`;
            right.style.transform = `translateY(${-row * 100}%)`;
        }
    }

    function getTrackTop() {
        return track.getBoundingClientRect().top + window.scrollY;
    }

    function getTargetRow() {
        const scrolled = window.scrollY - getTrackTop();
        const innerH = window.innerHeight;
        if (scrolled < 0) return 0; // clamp — no row -1 for contact
        if (scrolled >= rows * innerH) return rows;
        return Math.floor(scrolled / innerH);
    }

    function snapPageToRow(row) {
        const trackTop = getTrackTop();
        const innerH = window.innerHeight;
        const targetY = row >= rows
            ? trackTop + (rows + 1) * innerH
            : trackTop + row * innerH;
        window.scrollTo({ top: targetY, behavior: 'instant' });
    }

    function smoothSnapToRow(row) {
        if (row < 0 || row >= rows) return;
        if (isSnapping) return;
        const targetY = getTrackTop() + row * window.innerHeight;
        if (Math.abs(window.scrollY - targetY) < 2) return;
        isSnapping = true;
        window.scrollTo({ top: targetY, behavior: 'smooth' });
        setTimeout(() => { isSnapping = false; }, TRANSITION_MS + 150);
    }

    function goToRow(newRow) {
        if (transitioning) return;
        newRow = Math.max(0, Math.min(rows, newRow)); // min 0, no row -1
        if (newRow === currentRow) return;
        transitioning = true;
        currentRow = newRow;
        setTransforms(newRow, true);

        if (newRow === rows) {
            setTimeout(() => {
                snapPageToRow(rows);
                transitioning = false;
            }, TRANSITION_MS);
        } else {
            if (secondHero) {
                if (newRow >= REVEAL_ROW) {
                    secondHero.classList.add('is-revealed');
                } else {
                    secondHero.classList.remove('is-revealed');
                }
            }
            snapPageToRow(newRow);
            setTimeout(() => { transitioning = false; }, TRANSITION_MS);
        }
    }

    // ── Wheel ─────────────────────────────────────────────────────────────────
    const navToggle = document.querySelector('.nav-toggle');
    window.addEventListener('wheel', (e) => {
        if (document.body.classList.contains('splash-active')) return;
        const siteHeader = document.getElementById('site-header');
        if (siteHeader && siteHeader.classList.contains('is-open')) {
            e.preventDefault();
            navToggle && navToggle.click();
            return;
        }
        if (transitioning) {
            e.preventDefault();
            return;
        }
        const down = e.deltaY > 0;

        // Within contact rows: intercept scroll-down always, scroll-up only if not at row 0
        if (currentRow >= 0 && currentRow < rows) {
            if (down || currentRow > 0) {
                e.preventDefault();
                goToRow(down ? currentRow + 1 : currentRow - 1);
                return;
            }
            // At row 0 scrolling up: fall through to native scroll
            return;
        }

        // Scroll up from second hero: only intercept when fully at hero start
        if (!down && currentRow === rows) {
            const heroStart = getTrackTop() + (rows + 1) * window.innerHeight;
            if (window.scrollY <= heroStart + 2) {
                e.preventDefault();
                goToRow(rows - 1);
            }
            return;
        }
    }, { passive: false });

    // ── Scroll: scrollbar / touch ──────────────────────────────────────────────
    window.addEventListener('scroll', () => {
        if (transitioning || isSnapping) return;
        const targetRow = getTargetRow();
        if (targetRow !== currentRow) {
            setTransforms(targetRow, true);
            currentRow = targetRow;
            if (secondHero) {
                if (targetRow >= REVEAL_ROW) {
                    secondHero.classList.add('is-revealed');
                } else {
                    secondHero.classList.remove('is-revealed');
                }
            }
        }
        clearTimeout(snapTimer);
        snapTimer = setTimeout(() => smoothSnapToRow(getTargetRow()), 200);
    }, { passive: true });

    // ── Init ──────────────────────────────────────────────────────────────────
    const initRow = getTargetRow();
    currentRow = initRow;
    setTransforms(initRow, false);
    if (secondHero && initRow >= REVEAL_ROW) secondHero.classList.add('is-revealed');
});


// Bouwstenen — hover adjacency
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.querySelector('.approach__wrapper');
    if (!grid) return;

    const items = grid.querySelectorAll('.approach__item');

    items.forEach(item => {
        item.addEventListener('mouseenter', () => {
            grid.setAttribute('data-active', item.dataset.index);
        });
        item.addEventListener('mouseleave', () => {
            grid.removeAttribute('data-active');
        });
    });
});


// Approach page — wordmark icons navigate to corresponding bouwsteen
document.addEventListener('DOMContentLoaded', () => {
    // Only runs when the bouwstenen section is present
    if (!document.querySelector('.section--strip[data-type="bouwstenen"]')) return;

    const wordmark = document.querySelector(
        '.section--full:not(.section--full--second):not(.section--splash) .wordmark'
    );
    if (!wordmark) return;

    const iconRows = [
        ['icon__square', 0],
        ['icon__plus', 1],
        ['icon__asterisk', 2],
        ['icon__circle', 3],
        ['icon__triangle', 4],
    ];

    iconRows.forEach(([cls, row]) => {
        const el = wordmark.querySelector('.' + cls);
        if (!el) return;
        el.addEventListener('click', () => {
            window.dispatchEvent(new CustomEvent('bouwstenen:goTo', { detail: { row } }));
        });
    });
});


// Home page — approach__item click → approach page at the matching row
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.querySelector('.approach__wrapper[data-approach-url]');
    if (!wrapper) return;

    const baseUrl = wrapper.dataset.approachUrl.replace(/\/$/, '');

    wrapper.querySelectorAll('.approach__item').forEach(item => {
        const row = parseInt(item.dataset.index, 10) - 1; // data-index is 1-based
        item.addEventListener('click', () => {
            window.location.href = baseUrl + '#row-' + row;
        });
    });
});


// Approach page — jump to the row requested via URL hash (#row-N)
document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash;
    if (!hash || !hash.startsWith('#row-')) return;

    const row = parseInt(hash.slice(5), 10);
    if (isNaN(row) || row < 0) return;

    // Push to the next tick so layout is settled and the bouwstenen listener is ready
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('bouwstenen:goTo', { detail: { row } }));
        // Clean up the hash so the URL stays tidy after the row is consumed
        history.replaceState(null, '', window.location.pathname);
    }, 0);
});


// Focus section — mobile swiper (≤ 782px), with resize reinit
document.addEventListener('DOMContentLoaded', () => {
    const section = document.querySelector('.section--focus:not(.section--focus--exp)');
    if (!section || typeof Swiper === 'undefined') return;

    const categories = Array.from(section.querySelectorAll('.focus__category'));
    const catsList = section.querySelector('.focus__categories');
    const focusRight = section.querySelector('.focus__right');
    if (!categories.length || !focusRight) return;

    // Save original desktop HTML so we can restore it when crossing back to desktop
    const originalFocusRightHTML = focusRight.innerHTML;

    let swiperInstance = null;
    let isMobileMode = false;

    function setActiveCategory(index) {
        categories.forEach((cat, i) => {
            const active = i === index;
            cat.classList.toggle('is-active', active);
            cat.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        const activeCat = categories[index];
        if (activeCat && catsList) {
            catsList.scrollTo({ left: activeCat.offsetLeft, behavior: 'smooth' });
        }
    }

    function initMobileSwiper() {
        const swiperWrapper = document.createElement('div');
        swiperWrapper.className = 'swiper-wrapper';

        categories.forEach(cat => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            const img = cat.dataset.image || '';
            const url = cat.dataset.url || '#';
            const title = cat.dataset.title || '';
            slide.innerHTML = `<a href="${url}" class="focus__project-link"><figure class="focus__image-wrap"><img src="${img}" alt="${title}" class="focus__image" loading="lazy"></figure><p class="focus__project-title project-card__title">${title}</p></a>`;
            swiperWrapper.appendChild(slide);
        });

        focusRight.innerHTML = '';
        const swiperEl = document.createElement('div');
        swiperEl.className = 'focus__mobile-swiper swiper';
        swiperEl.appendChild(swiperWrapper);
        focusRight.appendChild(swiperEl);

        const sectionContainer = section.querySelector('.section-container');
        const spaceBetween = sectionContainer
            ? parseFloat(getComputedStyle(sectionContainer).paddingLeft)
            : 20;

        swiperInstance = new Swiper(swiperEl, {
            slidesPerView: 'auto',
            spaceBetween,
            loop: true,
            speed: 600,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            on: {
                slideChange(sw) {
                    setActiveCategory(sw.realIndex);
                },
            },
        });

        setActiveCategory(0);

        categories.forEach((cat, i) => {
            cat.addEventListener('click', () => {
                swiperInstance.slideToLoop(i);
            });
        });

        isMobileMode = true;
    }

    function destroyMobileSwiper() {
        if (swiperInstance) {
            swiperInstance.destroy(true, true);
            swiperInstance = null;
        }
        focusRight.innerHTML = originalFocusRightHTML;
        isMobileMode = false;
    }

    // Initial state
    if (window.innerWidth <= 782) {
        initMobileSwiper();
    }

    // Reinitialize on breakpoint crossing
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const shouldBeMobile = window.innerWidth <= 782;
            if (shouldBeMobile && !isMobileMode) {
                initMobileSwiper();
            } else if (!shouldBeMobile && isMobileMode) {
                destroyMobileSwiper();
            }
        }, 150);
    });
});


// Admin meta box interactions (only runs when jQuery is present)
if (typeof jQuery !== 'undefined') {
    (function ($) {
        'use strict';

        // Team: add / remove project rows
        $(document).on('click', '.stir-add-project', function () {
            var options = '<option value="">— Select project —</option>';
            ((window.STIR_ADMIN && window.STIR_ADMIN.projects) || []).forEach(function (p) {
                options += '<option value="' + p.id + '">' + $('<span>').text(p.title).html() + '</option>';
            });
            var row = '<div class="stir-project-row">' +
                '<select name="team_projects[]">' + options + '</select>' +
                '<button type="button" class="stir-remove-project">✕</button>' +
                '</div>';
            $('#stir-projects-list').append(row);
        });

        $(document).on('click', '.stir-remove-project', function () {
            $(this).closest('.stir-project-row').remove();
        });

        // Projects: hover-type conditional fields (movement is always visible)
        function updateHoverFields() {
            var selected = $('#hover-type-select').val();
            $('#hover-color-field, #hover-image-field').removeClass('visible');
            if (selected === 'color') $('#hover-color-field').addClass('visible');
            else if (selected === 'second_image') $('#hover-image-field').addClass('visible');
        }

        // Run on load (classic editor) and whenever the block editor injects meta boxes
        $(document).ready(function () {
            updateHoverFields();

            // Block editor renders meta boxes asynchronously — watch for the select to appear
            var observer = new MutationObserver(function () {
                if ($('#hover-type-select').length) {
                    updateHoverFields();
                    observer.disconnect();
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        });

        $(document).on('change', '#hover-type-select', updateHoverFields);

        // WordPress media picker
        var mediaFrame;

        $(document).on('click', '.stir-select-image', function (e) {
            e.preventDefault();
            var targetId = $(this).data('target');

            mediaFrame = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false,
            });

            mediaFrame.on('select', function () {
                var attachment = mediaFrame.state().get('selection').first().toJSON();
                var thumbUrl = attachment.sizes && attachment.sizes.thumbnail
                    ? attachment.sizes.thumbnail.url
                    : attachment.url;

                $('#' + targetId).val(attachment.id);
                $('#' + targetId + '_preview').attr('src', thumbUrl).show();
                $('[data-target="' + targetId + '"].stir-remove-image').show();
            });

            mediaFrame.open();
        });

        $(document).on('click', '.stir-remove-image', function () {
            var targetId = $(this).data('target');
            $('#' + targetId).val('');
            $('#' + targetId + '_preview').attr('src', '').hide();
            $(this).hide();
        });

        // Color scheme preview dot (admin meta boxes)
        $(document).on('change', '.color-scheme-select', function () {
            var scheme = $(this).val();
            $(this).siblings('.scheme-preview').attr('class', 'scheme-preview ' + scheme);
        });

    })(jQuery);
}

document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    // All section-containers except the project-content one (disabled)
    document.querySelectorAll('main .section-container:not(.section--project-content__container)')
        .forEach(el => observer.observe(el));

    // Direct children of the project-content container get individual reveal
    document.querySelectorAll('.section--project-content__container > *')
        .forEach(el => observer.observe(el));
});


// Projects page — filter + zoom + search
document.addEventListener('DOMContentLoaded', () => {
    const filterSection = document.querySelector('.section--filters');
    const filterToggle = document.querySelector('.filter__toggle');
    const filterPanel = document.querySelector('.filter__panel');
    const typesCol = document.querySelector('.filter__col--types');
    const typeList = document.querySelector('.filter__type-list');
    const typesBar = document.querySelector('.filter__types-bar');
    const typesCloseBtn = typesBar && typesBar.querySelector('.filter__types-close');
    const activeTagCats = document.querySelector('.filter__active-tag--cats');
    const activeTagTypes = document.querySelector('.filter__active-tag--types');
    const activeSummaryCats = filterToggle && filterToggle.querySelector('.filter__active-summary');
    const activeSummaryTypes = typesBar && typesBar.querySelector('.filter__active-summary');
    const projectsSection = document.querySelector('.section--projects');
    const grid = document.querySelector('.projects__grid');
    const zoomOut = document.querySelector('.filter__zoom-out');
    const zoomIn = document.querySelector('.filter__zoom-in');
    const gridLabel = document.querySelector('.filter__zoom > span:not(.filter__zoom-out):not(.filter__zoom-in)');
    const listBtn = document.querySelector('.filter__list');
    const searchBtn = document.querySelector('.filter__search');
    const searchPanel = document.querySelector('.search__panel');
    const searchInput = searchPanel && searchPanel.querySelector('.search__panel__input');
    let searchOpen = false;
    if (!filterToggle || !grid) return;

    const cats = window.STIR_CATS || [];
    const MIN_ZOOM = 0;
    const MAX_ZOOM = 2;
    const FILTER_MIN_ZOOM = 1; // when filtering: min zoom level (span-4 = zoom 1)
    const FILTER_MAX_ZOOM = 2; // when filtering: max zoom level (span-3 = zoom 2)

    let activeCategory = null;
    let activeTypes = [];
    let panelOpen = filterPanel ? filterPanel.classList.contains('is-open') : false;
    let zoomLevel = 0;
    let searchQuery = '';

    // ── URL state ──

    function pushURL() {
        const params = new URLSearchParams();
        if (activeCategory) params.set('cat', activeCategory.slug);
        if (activeTypes.length) params.set('types', activeTypes.map(t => t.slug).join(','));
        const qs = params.toString();
        history.replaceState(null, '', qs ? '?' + qs : location.pathname);
    }

    function readURL() {
        const params = new URLSearchParams(location.search);
        // Also handle WordPress category archive URLs: /category/SLUG/
        const pathMatch = location.pathname.match(/\/category\/([^/]+)\/?$/);
        const catSlug = params.get('cat') || (pathMatch ? pathMatch[1] : null);
        const typeSlugs = (params.get('types') || '').split(',').filter(Boolean);

        if (catSlug) {
            const catEntry = cats.find(c => c.slug === catSlug);
            if (catEntry) {
                activeCategory = { id: catEntry.id, slug: catEntry.slug, name: catEntry.name };
                document.querySelectorAll('.filter__category').forEach(btn => {
                    btn.classList.toggle('is-active', btn.dataset.catSlug === catSlug);
                });
                renderTypes(catEntry);
                if (typeSlugs.length) {
                    typeSlugs.forEach(slug => {
                        const child = catEntry.children && catEntry.children.find(c => c.slug === slug);
                        if (child) activeTypes.push({ id: child.id, slug: child.slug, name: child.name });
                    });
                    typeList.querySelectorAll('.filter__type').forEach(btn => {
                        btn.classList.toggle('is-active', activeTypes.some(t => t.slug === btn.dataset.typeSlug));
                    });
                }
            }
        }

        applyFilters();
        renderActiveTags();
        updateStateClasses();
        clampZoomToFilterState();
        // Normalize URL: replace /category/slug/ paths with ?cat=slug
        if (pathMatch) pushURL();
        // Open the filter panel when a category is pre-applied (e.g. arriving from nav menu)
        // so the types column is immediately visible
        if (catSlug && filterPanel && !panelOpen) openPanel();
    }

    // ── Fade helper ──

    const FADE_MS = 200;

    function withGridFade(fn) {
        grid.classList.add('is-switching');
        setTimeout(() => {
            fn();
            grid.classList.remove('is-switching');
        }, FADE_MS);
    }

    // ── Zoom ──

    function isFiltering() {
        return !!(activeCategory || activeTypes.length);
    }

    function updateZoom() {
        grid.dataset.zoom = zoomLevel;

        const filtering = isFiltering();
        const minZoom = filtering ? FILTER_MIN_ZOOM : MIN_ZOOM;
        const maxZoom = filtering ? FILTER_MAX_ZOOM : MAX_ZOOM;

        // zoomIn  (−) is disabled when already at minimum zoom for the current mode
        zoomIn.classList.toggle('is-disabled', zoomLevel <= minZoom);
        // zoomOut (+) is disabled when already at maximum zoom for the current mode
        zoomOut.classList.toggle('is-disabled', zoomLevel >= maxZoom);
    }

    zoomOut.addEventListener('click', () => {
        if (projectsSection.classList.contains('is-list-view')) {
            withGridFade(() => exitListView());
        } else {
            const maxZoom = isFiltering() ? FILTER_MAX_ZOOM : MAX_ZOOM;
            if (zoomLevel < maxZoom) { zoomLevel++; updateZoom(); }
        }
    });
    zoomIn.addEventListener('click', () => {
        if (projectsSection.classList.contains('is-list-view')) {
            withGridFade(() => exitListView());
        } else {
            const minZoom = isFiltering() ? FILTER_MIN_ZOOM : MIN_ZOOM;
            if (zoomLevel > minZoom) { zoomLevel--; updateZoom(); }
        }
    });
    if (gridLabel) {
        gridLabel.addEventListener('click', () => {
            if (projectsSection.classList.contains('is-list-view')) {
                withGridFade(() => exitListView());
            }
        });
    }
    updateZoom();

    // ── List / Grid toggle ──

    function sortListItems() {
        const items = Array.from(grid.querySelectorAll('.projects__item:not(.is-hidden)'));
        items.sort((a, b) => {
            const ya = a.dataset.bouwjaar ? parseInt(a.dataset.bouwjaar, 10) : null;
            const yb = b.dataset.bouwjaar ? parseInt(b.dataset.bouwjaar, 10) : null;
            // No year → bottom
            if (ya === null && yb === null) {
                return (a.dataset.title || '').localeCompare(b.dataset.title || '');
            }
            if (ya === null) return 1;
            if (yb === null) return -1;
            // Newer year first
            if (yb !== ya) return yb - ya;
            // Same year → alpha
            return (a.dataset.title || '').localeCompare(b.dataset.title || '');
        });
        items.forEach(item => grid.appendChild(item));
    }

    function enterListView() {
        projectsSection.classList.add('is-list-view');
        listBtn.classList.add('is-active');
        sortListItems();
    }

    function exitListView() {
        projectsSection.classList.remove('is-list-view');
        listBtn.classList.remove('is-active');
    }

    if (listBtn && projectsSection) {
        listBtn.addEventListener('click', () => {
            withGridFade(() => {
                projectsSection.classList.contains('is-list-view') ? exitListView() : enterListView();
            });
        });
    }

    // ── List row click ──

    grid.addEventListener('click', e => {
        if (!projectsSection.classList.contains('is-list-view')) return;
        // Don't double-navigate if clicking the CTA link itself
        if (e.target.closest('.project__item__list-cta')) return;
        const item = e.target.closest('.projects__item');
        if (!item || !item.dataset.permalink) return;
        window.location.href = item.dataset.permalink;
    });

    // ── Panel open/close ──

    function openPanel() {
        if (searchOpen) closeSearch();
        panelOpen = true;
        filterPanel.classList.add('is-open');
        filterPanel.setAttribute('aria-hidden', 'false');
        filterToggle.setAttribute('aria-expanded', 'true');
        filterSection.classList.add('filter-open');
        updateStateClasses();
    }

    function closePanel() {
        panelOpen = false;
        filterPanel.classList.remove('is-open');
        filterPanel.setAttribute('aria-hidden', 'true');
        filterToggle.setAttribute('aria-expanded', 'false');
        filterSection.classList.remove('filter-open');
        updateStateClasses();
    }

    filterToggle.addEventListener('click', () => { panelOpen ? closePanel() : openPanel(); });

    // Close filter panel when the user starts scrolling
    window.addEventListener('scroll', () => {
        if (panelOpen) closePanel();
    }, { passive: true });

    // Pressing "Types" toggles the filter panel;
    // when types are already active it opens the panel so user can adjust
    if (typesCloseBtn) {
        typesCloseBtn.addEventListener('click', () => { panelOpen ? closePanel() : openPanel(); });
    }

    // ── State classes on filterSection ──
    // CSS uses these to drive all visibility:
    //   .has-cat-filter   → category is active
    //   .has-type-filter  → type(s) are active
    //   .filter-open      → panel is open

    function updateStateClasses() {
        filterSection.classList.toggle('has-cat-filter', !!activeCategory);
        filterSection.classList.toggle('has-type-filter', activeTypes.length > 0);
    }

    // ── Category activation ──

    function renderTypes(catEntry) {
        typeList.innerHTML = '';
        if (!catEntry || !catEntry.children || !catEntry.children.length) {
            typesCol.classList.remove('is-open');
            typesCol.setAttribute('aria-hidden', 'true');
            return;
        }
        catEntry.children.forEach(child => {
            const li = document.createElement('li');
            li.className = 'filter__type h3';
            li.textContent = child.name;
            li.dataset.typeId = child.id;
            li.dataset.typeSlug = child.slug;
            li.dataset.typeName = child.name;
            li.setAttribute('role', 'button');
            li.setAttribute('tabindex', '0');
            li.addEventListener('click', () => toggleType(child));
            li.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleType(child); }
            });
            typeList.appendChild(li);
        });
        typesCol.classList.add('is-open');
        typesCol.setAttribute('aria-hidden', 'false');
    }

    function clampZoomToFilterState() {
        // When filtering is now active, ensure zoom is at least FILTER_MIN_ZOOM
        if (isFiltering() && zoomLevel < FILTER_MIN_ZOOM) {
            zoomLevel = FILTER_MIN_ZOOM;
        }
        updateZoom();
    }

    function setCategory(catEntry) {
        activeCategory = catEntry ? { id: catEntry.id, slug: catEntry.slug, name: catEntry.name } : null;
        activeTypes = [];

        document.querySelectorAll('.filter__category').forEach(btn => {
            btn.classList.toggle('is-active', !!(catEntry && btn.dataset.catId == catEntry.id));
        });

        renderTypes(catEntry || null);
        applyFilters();
        renderActiveTags();
        updateStateClasses();
        clampZoomToFilterState();
        pushURL();
    }

    document.querySelectorAll('.filter__category').forEach(btn => {
        const catEntry = cats.find(c => c.id == btn.dataset.catId);
        btn.addEventListener('click', () => {
            activeCategory && activeCategory.id == btn.dataset.catId ? setCategory(null) : setCategory(catEntry);
        });
        btn.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); btn.click(); }
        });
    });

    // ── Type toggle — max one active at a time ──

    function toggleType(child) {
        const idx = activeTypes.findIndex(t => t.id == child.id);
        if (idx > -1) {
            // Already active — deactivate
            activeTypes = [];
        } else {
            // Switch to this type (replace any previously active one)
            activeTypes = [{ id: child.id, slug: child.slug, name: child.name }];
        }

        typeList.querySelectorAll('.filter__type').forEach(btn => {
            btn.classList.toggle('is-active', activeTypes.some(t => t.id == btn.dataset.typeId));
        });
        filterSection.classList.toggle('has-type-filter', activeTypes.length > 0);
        applyFilters();
        renderActiveTags();
        updateStateClasses();
        clampZoomToFilterState();
        pushURL();
    }

    // ── Filter + search ──

    function applyFilters() {
        const q = searchQuery.toLowerCase().trim();
        document.querySelectorAll('.projects__item').forEach(item => {
            const itemCatIds = (item.dataset.catIds || '').split(' ').map(s => s.trim()).filter(Boolean);
            let visible = true;

            if (activeCategory) {
                const catAndChildren = [String(activeCategory.id)];
                const catEntry = cats.find(c => c.id == activeCategory.id);
                if (catEntry && catEntry.children) catEntry.children.forEach(ch => catAndChildren.push(String(ch.id)));
                visible = itemCatIds.some(id => catAndChildren.includes(id));
            }
            if (visible && activeTypes.length) {
                visible = itemCatIds.some(id => activeTypes.some(t => String(t.id) === id));
            }
            if (visible && q) {
                const title = (item.querySelector('.project-card__title') || {}).textContent || '';
                visible = title.toLowerCase().includes(q);
            }

            item.classList.toggle('is-hidden', !visible);
        });
    }

    // ── Active tags ──

    // Cats ✕ is a span inside the toggle — stop click bubbling to toggle
    const xCats = activeSummaryCats && activeSummaryCats.querySelector('.filter__active-x');
    if (xCats) {
        xCats.addEventListener('click', e => { e.stopPropagation(); setCategory(null); });
    }

    function clearTypes() {
        activeTypes = [];
        typeList.querySelectorAll('.filter__type').forEach(li => li.classList.remove('is-active'));
        filterSection.classList.remove('has-type-filter');
        applyFilters();
        renderActiveTags();
        updateStateClasses();
        clampZoomToFilterState();
        pushURL();
    }

    // Types ✕ is a span inside the types bar summary
    const xTypes = activeSummaryTypes && activeSummaryTypes.querySelector('.filter__active-x');
    if (xTypes) {
        xTypes.addEventListener('click', e => { e.stopPropagation(); clearTypes(); });
    }

    // Clicking the tag text itself also clears types
    if (activeTagTypes) {
        activeTagTypes.addEventListener('click', e => { e.stopPropagation(); clearTypes(); });
    }

    // Clicking the cat tag text clears the category
    if (activeTagCats) {
        activeTagCats.addEventListener('click', e => { e.stopPropagation(); setCategory(null); });
    }

    function renderActiveTags() {
        if (activeTagCats) activeTagCats.textContent = activeCategory ? activeCategory.name : '';

        if (activeTagTypes) {
            activeTagTypes.textContent = activeTypes.length
                ? activeTypes.map(t => t.name).join(', ')
                : '';
        }

        updateStateClasses();
    }

    // ── Search panel (push-down) ──

    function openSearch() {
        if (panelOpen) closePanel();
        searchOpen = true;
        filterSection.classList.add('search-open');
        searchPanel && searchPanel.setAttribute('aria-hidden', 'false');
        if (searchInput) { searchInput.focus(); }
    }

    function closeSearch() {
        searchOpen = false;
        filterSection.classList.remove('search-open');
        searchPanel && searchPanel.setAttribute('aria-hidden', 'true');
        if (searchInput) {
            searchQuery = '';
            searchInput.value = '';
            applyFilters();
        }
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', () => {
            searchOpen ? closeSearch() : openSearch();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            searchQuery = searchInput.value;
            applyFilters();
        });
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && searchOpen) closeSearch();
    });

    // ── Init ──
    readURL();
});