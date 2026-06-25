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

    /* ── 4. Hero image animation ── */

    const IMG_ANIMATIONS = [
        { label: '— None —',    value: '' },
        { label: 'Zoom in',     value: 'img-zoom-in' },
        { label: 'Zoom out',    value: 'img-zoom-out' },
        { label: 'Move left',   value: 'img-move-left' },
        { label: 'Move right',  value: 'img-move-right' },
        { label: 'Move up',     value: 'img-move-up' },
        { label: 'Move down',   value: 'img-move-down' },
    ];

    // 4a. Register imgAnimation attribute on core/image
    addFilter(
        'blocks.registerBlockType',
        'theme/image-animation-attribute',
        function (settings, name) {
            if (name !== 'core/image') return settings;
            settings.attributes = Object.assign({}, settings.attributes, {
                imgAnimation: { type: 'string', default: '' },
            });
            return settings;
        }
    );

    // 4b. Inspector panel — only show when the image is inside a hero gallery
    addFilter(
        'editor.BlockEdit',
        'theme/image-animation-control',
        createHigherOrderComponent(function (BlockEdit) {
            return function (props) {
                if (props.name !== 'core/image') return el(BlockEdit, props);

                const { attributes, setAttributes, clientId } = props;

                // Walk up: image → gallery → column → columns
                // Show control only when an ancestor columns block has section--hero in its className
                const ancestors = wp.data
                    .select('core/block-editor')
                    .getBlockParents(clientId);

                const isHero = ancestors.some(function (id) {
                    const block = wp.data.select('core/block-editor').getBlock(id);
                    if (!block) return false;
                    const cls = block.attributes && block.attributes.className || '';
                    return cls.includes('section--hero');
                });

                if (!isHero) return el(BlockEdit, props);

                return el(
                    Fragment,
                    {},
                    el(BlockEdit, props),
                    el(
                        InspectorControls,
                        {},
                        el(
                            PanelBody,
                            { title: 'Image animation', initialOpen: true },
                            el(SelectControl, {
                                label: 'Animation',
                                value: attributes.imgAnimation,
                                options: IMG_ANIMATIONS,
                                onChange: function (value) {
                                    setAttributes({ imgAnimation: value });
                                },
                            })
                        )
                    )
                );
            };
        })
    );

    // 4c. Persist imgAnimation as a class on the saved <figure>
    addFilter(
        'blocks.getSaveContent.extraProps',
        'theme/image-animation-class',
        function (extraProps, blockType, attributes) {
            if (blockType.name !== 'core/image') return extraProps;
            if (attributes.imgAnimation) {
                extraProps.className = [
                    extraProps.className,
                    attributes.imgAnimation,
                ]
                    .filter(Boolean)
                    .join(' ');
            }
            return extraProps;
        }
    );
})();
