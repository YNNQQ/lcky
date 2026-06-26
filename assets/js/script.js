// Load color schemes in editor
(function () {
    // Bail if we're not in the block editor, or already initialized
    if (
        typeof wp === 'undefined' ||
        !wp.blocks ||
        !wp.blockEditor ||
        !wp.data ||
        window.__stirEditorInit
    ) {
        return;
    }
    window.__stirEditorInit = true;

    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl } = wp.components;
    const { createElement: el, Fragment } = wp.element;

    const SCHEMES = window.SCHEMES?.schemes || [];

    /* 1. Add scheme attribute to core/column */
    addFilter(
        'blocks.registerBlockType',
        'theme/column-scheme-attribute',
        function (settings, name) {
            if (name !== 'core/column' && name !== 'core/columns') return settings;

            settings.attributes = Object.assign({}, settings.attributes, {
                scheme: {
                    type: 'string',
                    default: '',
                },
            });

            return settings;
        }
    );

    /* 2. Inspector dropdown (only if Column is inside Columns) */
    addFilter(
        'editor.BlockEdit',
        'theme/column-scheme-control',
        createHigherOrderComponent(function (BlockEdit) {
            return function (props) {
                if (
                    props.name !== 'core/column' &&
                    props.name !== 'core/columns'
                ) {
                    return el(BlockEdit, props);
                }

                const { attributes, setAttributes, clientId } = props;

                if (props.name === 'core/column') {
                    const parents = wp.data
                        .select('core/block-editor')
                        .getBlockParents(clientId);

                    const isInColumns = parents.some(function (id) {
                        const block = wp.data
                            .select('core/block-editor')
                            .getBlock(id);
                        return block && block.name === 'core/columns';
                    });

                    if (!isInColumns) {
                        return el(BlockEdit, props);
                    }
                }

                return el(
                    Fragment,
                    {},
                    el(BlockEdit, props),
                    el(
                        InspectorControls,
                        {},
                        el(
                            PanelBody,
                            { title: 'Color scheme', initialOpen: true },
                            el(SelectControl, {
                                label: 'Scheme',
                                value: attributes.scheme,
                                options: SCHEMES,
                                onChange: function (value) {
                                    setAttributes({ scheme: value });
                                },
                            }),
                            attributes.scheme &&
                            el(
                                'div',
                                {
                                    className: 'scheme-preview ' + attributes.scheme,
                                },
                                el('span', null, 'Aa'),
                                el('span')
                            )
                        )
                    )
                );
            };
        })
    );

    /* 3. Persist scheme as class in saved markup */
    addFilter(
        'blocks.getSaveContent.extraProps',
        'theme/column-scheme-class',
        function (extraProps, blockType, attributes) {
            if (
                blockType.name !== 'core/column' &&
                blockType.name !== 'core/columns'
            ) {
                return extraProps;
            }

            if (attributes.scheme) {
                extraProps.className = [
                    extraProps.className,
                    attributes.scheme,
                ]
                    .filter(Boolean)
                    .join(' ');
            }

            return extraProps;
        }
    );
})();

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
    document.querySelectorAll('main .section-container')
        .forEach(el => observer.observe(el));
});

// Header scheme-aware theme toggle
// Reads the scheme-* class of whatever element sits at the header's bottom
// edge and toggles .on-dark on the header accordingly.
//   scheme-white / scheme-grey → logo fill black  (light background)
//   scheme-black / scheme-red  → logo fill white  (dark background, .on-dark)
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.header__logo');
    if (!header) return;

    const DARK_SCHEMES = new Set(['scheme-black', 'scheme-red']);

    // Kill transitions briefly during the toggle to prevent fill animating
    // through intermediate colors when scrolling quickly between sections.
    const logoSvg  = header.querySelector('.header__logo svg');
    const transEls = [logoSvg].filter(Boolean);

    function instantToggle(fn) {
        transEls.forEach(el => { el.style.transition = 'none'; });
        void header.offsetHeight;
        fn();
        requestAnimationFrame(() => {
            transEls.forEach(el => { el.style.transition = ''; });
        });
    }

    function updateHeaderTheme() {
        // Sample the element stack at the horizontal centre, one px below
        // the header's bottom edge (viewport coordinates).
        const scanX    = window.innerWidth / 2;
        const scanY    = header.getBoundingClientRect().bottom + 1;
        const elements = document.elementsFromPoint(scanX, scanY);

        for (const el of elements) {
            if (el === header || header.contains(el)) continue;
            const scheme = [...el.classList].find(c => c.startsWith('scheme-'));
            if (scheme) {
                instantToggle(() => {
                    header.classList.toggle('on-dark', DARK_SCHEMES.has(scheme));
                });
                return;
            }
        }
    }

    window.addEventListener('scroll', updateHeaderTheme, { passive: true });
    window.addEventListener('resize', updateHeaderTheme, { passive: true });
    updateHeaderTheme();
});