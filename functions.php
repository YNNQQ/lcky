<?php

/**
 * stir Theme Functions
 *
 * @package Antwerp Fashion Festival
 */


// add_action('init', function() {
//   wp_create_user('tempadmin', 'temppassword123', 'your@email.com');
//   $user = get_user_by('login', 'tempadmin');
//   $user->set_role('administrator');
// });

add_filter('show_admin_bar', '__return_false');
add_filter('upload_size_limit', fn() => 512 * MB_IN_BYTES);

// Write PHP upload limits into the root .htaccess (works with Apache + mod_php)
add_action('init', function () {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/misc.php';
    insert_with_markers(get_home_path() . '.htaccess', 'PHP Upload Limits', [
        'php_value upload_max_filesize 512M',
        'php_value post_max_size 512M',
        'php_value memory_limit 512M',
    ]);
});


// ============================================================
// 1. THEME SETUP & CONFIGURATION
// ============================================================

// Theme setup
function setup()
{
    // Add title tag support
    add_theme_support('title-tag');

    // Add post thumbnails support
    add_theme_support('post-thumbnails');

    // Add automatic feed links
    add_theme_support('automatic-feed-links');

    // Add HTML5 support
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'setup');

register_nav_menus([
    'primary' => __('Primary menu'),
    'secondary' => __('Secondary menu'),
    'project' => __('Project menu'),
    'social' => __('Social menu'),
    'contact_footer' => __('Contact footer menu'),
    'contact_header' => __('Contact header menu'),
    'location' => __('Location menu'),
    'legal' => __('Legal menu'),
]);

// Removes from admin menu
add_action( 'admin_menu', 'my_remove_admin_menus' );

function my_remove_admin_menus() {
    remove_menu_page('edit-comments.php');
}

// Removes comment support from post and pages
add_action('init', 'remove_comment_support', 100);

function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}

// Removes from admin bar
add_action( 'wp_before_admin_bar_render', 'mytheme_admin_bar_render' );

function mytheme_admin_bar_render() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}

/**
 * Rename default Post type to Projects
 */
add_action('init', function () {
    global $wp_post_types;

    if (isset($wp_post_types['post'])) {
        $labels = &$wp_post_types['post']->labels;

        $labels->name = 'Projects';
        $labels->singular_name = 'Project';
        $labels->add_new = 'Add item';
        $labels->add_new_item = 'Add item';
        $labels->edit_item = 'Edit item';
        $labels->new_item = 'New item';
        $labels->view_item = 'View item';
        $labels->search_items = 'Search items';
        $labels->not_found = 'No items found';
        $labels->not_found_in_trash = 'No items found in Trash';
        $labels->all_items = 'All items';
        $labels->menu_name = 'Projects';
        $labels->name_admin_bar = 'Project';
    }
});


// ============================================================
// 2. COLOR SCHEME REGISTRY
// ============================================================

// Color scheme registry (single source of truth)
function get_color_schemes(): array {
    return [
        ''                            => __('None'),
        // 'scheme-white'       => __('White'),
        // 'scheme-black'       => __('Black'),
        'scheme-grey'           => __('Grey'),
        'scheme-dark-grey'      => __('Dark Grey'),
        'scheme-red'            => __('Red'),
        'scheme-light-red'      => __('Light Red'),
        'scheme-dark-red'       => __('Dark Red'),
        'scheme-green'          => __('Green'),
        'scheme-light-green'    => __('Light Green'),
        'scheme-dark-green'     => __('Dark Green'),
    ];
}

function render_color_scheme_selector($name, $current = '') {
    ?>
    <select name="<?php echo esc_attr($name); ?>" class="color-scheme-select">
        <option value="">—</option>
        <?php foreach (get_color_schemes() as $value => $label) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($current, $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="scheme-preview <?php echo esc_attr($current); ?>">
    </div>
    <?php
}


// ============================================================
// 3. ASSET ENQUEUING
// ============================================================

function stir_meta_box_css(): string {
    return '
        .stir-meta-box { padding: 0; }
        .stir-field { display: flex; flex-direction: column; gap: 6px; padding: 6px 0; }
        .stir-field:last-child { border-bottom: none; }
        .stir-field > label { font-weight: 600; color: #1d2327; font-size: 13px; }
        .stir-field input[type="text"],
        .stir-field input[type="email"],
        .stir-field input[type="number"],
        .stir-field select { max-width: 500px; }
        .stir-project-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .stir-project-row select { flex: 1; max-width: 380px; }
        .stir-remove-project { background: none; border: 1px solid #d63638; color: #d63638; border-radius: 3px; cursor: pointer; padding: 3px 8px; font-size: 13px; line-height: 1.6; }
        .stir-remove-project:hover { background: #d63638; color: #fff; }
        .stir-field.stir-conditional { display: none; }
        .stir-field.stir-conditional.visible { display: flex; }
        .stir-checkbox-row { display: flex; align-items: center; gap: 8px; padding: 6px 0; }
        .stir-checkbox-row label { font-weight: 700; font-size: 13px; color: #1d2327; cursor: pointer; }
        .stir-image-preview { max-width: 120px; height: auto; display: block; margin-top: 8px; border-radius: 4px; }
        .stir-image-col { display: flex; flex-direction: column; }
        .stir-image-col .stir-image-buttons { display: flex; gap: 8px; }
        .stir-icon-grid { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 6px; }
        .stir-icon-option { display: flex; flex-direction: column; align-items: center; gap: 6px; cursor: pointer; padding: 8px; border: 2px solid #ddd; border-radius: 6px; min-width: 64px; }
        .stir-icon-option.is-selected { border-color: #2271b1; background: #f0f6fc; }
        .stir-icon-option:hover { border-color: #2271b1; }
        .stir-icon-preview { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; }
        .stir-icon-preview svg { width: 100%; height: 100%; }
        .stir-icon-option span { font-size: 11px; color: #50575e; }
    ';
}

// Enqueue styles and scripts
function enqueue_assets()
{
    // Theme stylesheet
    wp_enqueue_style(
        'stir-style',
        get_stylesheet_uri(),
        [],
        filemtime(get_stylesheet_directory() . '/style.css')
    );

    // Theme script
    wp_enqueue_script(
        'stir-script',
        get_template_directory_uri() . '/assets/js/script.js',
        [],
        filemtime(get_template_directory() . '/assets/js/script.js'),
        true
    );

    // Expose color schemes to JS
    wp_localize_script(
        'stir-script',
        'SCHEMES',
        [
            'schemes' => array_map(
                fn($label, $value) => ['label' => $label, 'value' => $value],
                get_color_schemes(),
                array_keys(get_color_schemes())
            ),
        ]
    );

    // Expose theme URI so JS can fetch SVG assets
    wp_add_inline_script(
        'stir-script',
        'window.STIR_THEME_URI = ' . json_encode(get_template_directory_uri()) . ';',
        'before'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_assets');

add_action('enqueue_block_editor_assets', function () {

    wp_enqueue_script(
        'stir-editor-script',
        get_template_directory_uri() . '/assets/js/script.js',
        [
            'wp-blocks',
            'wp-element',
            'wp-components',
            'wp-compose',
            'wp-editor',
            'wp-block-editor',
            'wp-data',
            'wp-hooks',
        ],
        filemtime(get_template_directory() . '/assets/js/script.js'),
        true
    );

    wp_localize_script(
        'stir-editor-script',
        'SCHEMES',
        [
            'schemes' => array_map(
                fn($label, $value) => ['label' => $label, 'value' => $value],
                get_color_schemes(),
                array_keys(get_color_schemes())
            ),
        ]
    );

    wp_enqueue_style(
        'stir-editor-color-schemes',
        get_template_directory_uri() . '/assets/css/colors.css',
        [],
        filemtime(get_template_directory() . '/assets/css/colors.css')
    );

    // Meta box styles for the block editor (meta boxes render below the editor)
    wp_add_inline_style('stir-editor-color-schemes', stir_meta_box_css());

    // Make media picker and meta box data available inside the block editor
    wp_enqueue_media();
    $admin_projects = get_posts(['post_type' => 'post', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    wp_localize_script('stir-editor-script', 'STIR_ADMIN', [
        'projects' => array_map(fn($p) => ['id' => $p->ID, 'title' => $p->post_title], $admin_projects),
    ]);
});


// ============================================================
// 4. CUSTOM POST TYPES & TAXONOMIES
// ============================================================

add_action('init', function () {
    // Team
    register_post_type('team', [
        'labels' => [
            'name'               => 'Team',
            'singular_name'      => 'Team Member',
            'add_new'            => 'Add item',
            'add_new_item'       => 'Add item',
            'edit_item'          => 'Edit item',
            'new_item'           => 'New item',
            'view_item'          => 'View item',
            'search_items'       => 'Search items',
            'not_found'          => 'No items found',
            'not_found_in_trash' => 'No items found in Trash',
            'all_items'          => 'All items',
            'menu_name'          => 'Team',
        ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => ['title', 'editor', 'thumbnail'],
        'menu_position' => 6,
        'rewrite'      => ['slug' => 'team'],
        'show_in_rest' => true,
    ]);

    // Approach (formerly Bouwstenen)
    register_post_type('bouwsteen', [
        'labels' => [
            'name'               => 'Approach',
            'singular_name'      => 'Approach',
            'add_new'            => 'Add item',
            'add_new_item'       => 'Add item',
            'edit_item'          => 'Edit item',
            'new_item'           => 'New item',
            'view_item'          => 'View item',
            'search_items'       => 'Search items',
            'not_found'          => 'No items found',
            'not_found_in_trash' => 'No items found in Trash',
            'all_items'          => 'All items',
            'menu_name'          => 'Approach',
        ],
        'public'       => true,
        'has_archive'  => true,
        'supports'     => ['title', 'editor'],
        'menu_position' => 7,
        'rewrite'      => ['slug' => 'bouwstenen'],
        'show_in_rest' => true,
    ]);

    // Vacatures
    register_post_type('vacature', [
        'labels' => [
            'name'               => 'Vacatures',
            'singular_name'      => 'Vacature',
            'add_new'            => 'Add item',
            'add_new_item'       => 'Add item',
            'edit_item'          => 'Edit item',
            'new_item'           => 'New item',
            'view_item'          => 'View item',
            'search_items'       => 'Search items',
            'not_found'          => 'No items found',
            'not_found_in_trash' => 'No items found in Trash',
            'all_items'          => 'All items',
            'menu_name'          => 'Vacatures',
        ],
        'public'        => true,
        'has_archive'   => false,
        'supports'      => ['title', 'editor'],
        'menu_position' => 8,
        'rewrite'       => ['slug' => 'vacatures'],
        'show_in_rest'  => true,
    ]);
});


// ============================================================
// 5. META BOXES & POST META
// ============================================================

// Admin scripts & styles
add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'])) return;

    wp_enqueue_media();

    $screen = get_current_screen();
    if ($screen && $screen->is_block_editor()) return; // block editor pages handled by enqueue_block_editor_assets

    wp_enqueue_script(
        'stir-admin',
        get_template_directory_uri() . '/assets/js/script.js',
        ['jquery'],
        filemtime(get_template_directory() . '/assets/js/script.js'),
        true
    );

    $admin_projects = get_posts(['post_type' => 'post', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    wp_localize_script('stir-admin', 'STIR_ADMIN', [
        'projects' => array_map(fn($p) => ['id' => $p->ID, 'title' => $p->post_title], $admin_projects),
    ]);

    wp_add_inline_style('wp-admin', stir_meta_box_css());
});


// ---- TEAM meta box ----

add_action('add_meta_boxes', function () {
    add_meta_box('team_details', 'Details', 'render_team_meta_box', 'team', 'normal', 'high');
});

function render_team_meta_box($post) {
    wp_nonce_field('stir_team_meta', 'stir_team_nonce');

    $job_title = get_post_meta($post->ID, '_team_job_title', true);
    $email     = get_post_meta($post->ID, '_team_email', true);
    $phone     = get_post_meta($post->ID, '_team_phone', true);
    $projects  = get_post_meta($post->ID, '_team_projects', true) ?: [];

    $all_projects = get_posts([
        'post_type'   => 'post',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ]);
    ?>
    <div class="stir-meta-box">
        <div class="stir-field">
            <label for="team_job_title">Job title</label>
            <input type="text" id="team_job_title" name="team_job_title" value="<?php echo esc_attr($job_title); ?>">
        </div>
        <div class="stir-field">
            <label for="team_email">Email</label>
            <input type="email" id="team_email" name="team_email" value="<?php echo esc_attr($email); ?>">
        </div>
        <div class="stir-field">
            <label for="team_phone">Phone</label>
            <input type="text" id="team_phone" name="team_phone" value="<?php echo esc_attr($phone); ?>">
        </div>

        <div class="stir-section">
            <span class="stir-section-title">Projects</span>
            <div id="stir-projects-list">
                <?php foreach ($projects as $pid) : ?>
                    <div class="stir-project-row">
                        <select name="team_projects[]">
                            <option value="">— Select project —</option>
                            <?php foreach ($all_projects as $p) : ?>
                                <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($p->ID, (int) $pid); ?>>
                                    <?php echo esc_html($p->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="stir-remove-project">✕</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button stir-add-project" style="margin-top:8px;">+ Add project</button>
        </div>
    </div>
    <?php
}

add_action('save_post_team', function ($post_id) {
    if (!isset($_POST['stir_team_nonce']) || !wp_verify_nonce($_POST['stir_team_nonce'], 'stir_team_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, '_team_job_title', sanitize_text_field($_POST['team_job_title'] ?? ''));
    update_post_meta($post_id, '_team_email',     sanitize_email($_POST['team_email'] ?? ''));
    update_post_meta($post_id, '_team_phone',     sanitize_text_field($_POST['team_phone'] ?? ''));

    $projects = array_values(array_filter(array_map('intval', $_POST['team_projects'] ?? [])));
    update_post_meta($post_id, '_team_projects', $projects);
});


// ---- PROJECTS meta box ----

add_action('add_meta_boxes', function () {
    add_meta_box('project_tile',    'Tile',    'render_project_tile_meta_box',    'post', 'normal', 'high');
    add_meta_box('project_details', 'Details', 'render_project_details_meta_box', 'post', 'normal', 'high');
});

// ---- Tile meta box ----

function render_project_tile_meta_box($post) {
    wp_nonce_field('stir_project_meta', 'stir_project_nonce');

    $featured       = get_post_meta($post->ID, '_project_featured',        true);
    $ratio          = get_post_meta($post->ID, '_project_ratio',           true);
    $size           = get_post_meta($post->ID, '_project_size',            true);
    $hover_type     = get_post_meta($post->ID, '_project_hover_type',      true);
    $hover_color    = get_post_meta($post->ID, '_project_hover_color',     true);
    $hover_image_id = get_post_meta($post->ID, '_project_hover_image_id',  true);
    $hover_movement = get_post_meta($post->ID, '_project_hover_movement',  true);
    ?>
    <div class="stir-meta-box">

        <div class="stir-checkbox-row">
            <input type="checkbox" id="project_featured" name="project_featured" value="1" <?php checked($featured, '1'); ?>>
            <label for="project_featured">Featured project</label>
        </div>

        <div class="stir-field">
            <label for="project_ratio">Ratio</label>
            <select name="project_ratio" id="project_ratio">
                <option value="">— Select —</option>
                <?php foreach (['portret' => 'Portret', 'vierkant' => 'Vierkant', 'landschap' => 'Landschap'] as $val => $lbl) : ?>
                    <option value="<?php echo esc_attr($val); ?>" <?php selected($ratio, $val); ?>><?php echo esc_html($lbl); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="stir-field">
            <label for="project_size">Size</label>
            <select name="project_size" id="project_size">
                <option value="">— Select —</option>
                <?php foreach (['large' => 'Large', 'medium' => 'Medium', 'small' => 'Small'] as $val => $lbl) : ?>
                    <option value="<?php echo esc_attr($val); ?>" <?php selected($size, $val); ?>><?php echo esc_html($lbl); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="stir-field">
            <label for="hover-type-select">Hover option</label>
            <select name="project_hover_type" id="hover-type-select">
                <option value="">— None —</option>
                <option value="color"        <?php selected($hover_type, 'color');        ?>>Color</option>
                <option value="second_image" <?php selected($hover_type, 'second_image'); ?>>Second image</option>
                <option value="movement"     <?php selected($hover_type, 'movement');     ?>>Movement</option>
            </select>
        </div>

        <div class="stir-field stir-conditional <?php echo $hover_type === 'color' ? 'visible' : ''; ?>" id="hover-color-field">
            <label>Color scheme</label>
            <?php render_color_scheme_selector('project_hover_color', $hover_color); ?>
        </div>

        <div class="stir-field stir-conditional <?php echo $hover_type === 'second_image' ? 'visible' : ''; ?>" id="hover-image-field">
            <label>Second image</label>
            <div class="stir-image-col">
                <div class="stir-image-buttons">
                    <button type="button" class="button stir-select-image" data-target="project_hover_image_id">Select image</button>
                    <button type="button" class="button stir-remove-image" data-target="project_hover_image_id" <?php echo $hover_image_id ? '' : 'style="display:none"'; ?>>Remove</button>
                </div>
                <input type="hidden" name="project_hover_image_id" id="project_hover_image_id" value="<?php echo esc_attr($hover_image_id); ?>">
                <img
                    src="<?php echo $hover_image_id ? esc_url(wp_get_attachment_image_url($hover_image_id, 'thumbnail')) : ''; ?>"
                    id="project_hover_image_id_preview"
                    class="stir-image-preview"
                    <?php echo $hover_image_id ? '' : 'style="display:none"'; ?>
                >
            </div>
        </div>

        <div class="stir-field" id="hover-movement-field">
            <label>Movement</label>
            <select name="project_hover_movement">
                <option value="">— Select —</option>
                <?php foreach (['zoom-in' => 'Zoom in', 'zoom-out' => 'Zoom out', 'up' => 'Up', 'down' => 'Down', 'right' => 'Right', 'left' => 'Left'] as $val => $lbl) : ?>
                    <option value="<?php echo esc_attr($val); ?>" <?php selected($hover_movement, $val); ?>><?php echo esc_html($lbl); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
    <?php
}

// ---- Details meta box ----

function render_project_details_meta_box($post) {
    $second_title   = get_post_meta($post->ID, '_project_second_title',    true);
    $bouwjaar       = get_post_meta($post->ID, '_project_bouwjaar',        true);
    $oppervlakte    = get_post_meta($post->ID, '_project_oppervlakte',     true);
    $samenwerkingen = get_post_meta($post->ID, '_project_samenwerkingen',  true);
    $opdrachtgever  = get_post_meta($post->ID, '_project_opdrachtgever',   true);
    $locatienaam    = get_post_meta($post->ID, '_project_locatienaam',     true);
    ?>
    <div class="stir-meta-box">

        <div class="stir-field">
            <label for="project_second_title">Second title</label>
            <input type="text" id="project_second_title" name="project_second_title" value="<?php echo esc_attr($second_title); ?>">
        </div>
        <div class="stir-field">
            <label for="project_bouwjaar">Bouwjaar</label>
            <input type="text" id="project_bouwjaar" name="project_bouwjaar" value="<?php echo esc_attr($bouwjaar); ?>">
        </div>
        <div class="stir-field">
            <label for="project_oppervlakte">Oppervlakte</label>
            <input type="text" id="project_oppervlakte" name="project_oppervlakte" value="<?php echo esc_attr($oppervlakte); ?>">
        </div>
        <div class="stir-field">
            <label for="project_samenwerkingen">Samenwerkingen</label>
            <input type="text" id="project_samenwerkingen" name="project_samenwerkingen" value="<?php echo esc_attr($samenwerkingen); ?>">
        </div>
        <div class="stir-field">
            <label for="project_opdrachtgever">Opdrachtgever</label>
            <input type="text" id="project_opdrachtgever" name="project_opdrachtgever" value="<?php echo esc_attr($opdrachtgever); ?>">
        </div>
        <div class="stir-field">
            <label for="project_locatienaam">Locatienaam</label>
            <input type="text" id="project_locatienaam" name="project_locatienaam" value="<?php echo esc_attr($locatienaam); ?>">
        </div>

    </div>
    <?php
}

add_action('save_post_post', function ($post_id) {
    if (!isset($_POST['stir_project_nonce']) || !wp_verify_nonce($_POST['stir_project_nonce'], 'stir_project_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    update_post_meta($post_id, '_project_featured',       isset($_POST['project_featured']) ? '1' : '');
    update_post_meta($post_id, '_project_second_title',   sanitize_text_field($_POST['project_second_title']   ?? ''));
    update_post_meta($post_id, '_project_bouwjaar',       sanitize_text_field($_POST['project_bouwjaar']       ?? ''));
    update_post_meta($post_id, '_project_oppervlakte',    sanitize_text_field($_POST['project_oppervlakte']    ?? ''));
    update_post_meta($post_id, '_project_samenwerkingen', sanitize_text_field($_POST['project_samenwerkingen'] ?? ''));
    update_post_meta($post_id, '_project_opdrachtgever',  sanitize_text_field($_POST['project_opdrachtgever']  ?? ''));
    update_post_meta($post_id, '_project_locatienaam',    sanitize_text_field($_POST['project_locatienaam']    ?? ''));

    $ratio = sanitize_text_field($_POST['project_ratio'] ?? '');
    update_post_meta($post_id, '_project_ratio', in_array($ratio, ['portret', 'vierkant', 'landschap']) ? $ratio : '');

    $size = sanitize_text_field($_POST['project_size'] ?? '');
    update_post_meta($post_id, '_project_size', in_array($size, ['large', 'medium', 'small']) ? $size : '');

    $hover_type = sanitize_text_field($_POST['project_hover_type'] ?? '');
    update_post_meta($post_id, '_project_hover_type', in_array($hover_type, ['color', 'second_image', 'movement']) ? $hover_type : '');

    update_post_meta($post_id, '_project_hover_color',    sanitize_text_field($_POST['project_hover_color']    ?? ''));
    update_post_meta($post_id, '_project_hover_image_id', absint($_POST['project_hover_image_id']               ?? 0));

    $movement = sanitize_text_field($_POST['project_hover_movement'] ?? '');
    update_post_meta($post_id, '_project_hover_movement', in_array($movement, ['zoom-in', 'zoom-out', 'up', 'down', 'right', 'left']) ? $movement : '');
});


// ---- APPROACH (BOUWSTEEN) meta box ----

add_action('add_meta_boxes', function () {
    add_meta_box('bouwsteen_icon', 'Icon', 'render_bouwsteen_meta_box', 'bouwsteen', 'normal', 'high');
});

function render_bouwsteen_meta_box($post) {
    wp_nonce_field('stir_bouwsteen_meta', 'stir_bouwsteen_nonce');
    $icon = get_post_meta($post->ID, '_bouwsteen_icon', true) ?: '1';
    ?>
    <div class="stir-meta-box">
        <div class="stir-field">
            <label>Icon</label>
            <div class="stir-icon-grid">
                <?php for ($i = 1; $i <= 5; $i++) :
                    $svg_path = get_template_directory() . '/assets/svg/icons/bouwsteen-' . $i . '.svg';
                    $selected = ($icon == $i);
                ?>
                <label class="stir-icon-option<?php echo $selected ? ' is-selected' : ''; ?>">
                    <input type="radio" name="bouwsteen_icon" value="<?php echo $i; ?>" <?php checked($icon, $i); ?> style="display:none">
                    <div class="stir-icon-preview">
                        <?php if (file_exists($svg_path)) echo file_get_contents($svg_path); ?>
                    </div>
                    <span><?php echo $i; ?></span>
                </label>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <script>
    (function() {
        document.querySelectorAll('.stir-icon-option input[type="radio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.stir-icon-option').forEach(function(opt) {
                    opt.classList.remove('is-selected');
                });
                this.closest('.stir-icon-option').classList.add('is-selected');
            });
        });
    })();
    </script>
    <?php
}

add_action('save_post_bouwsteen', function ($post_id) {
    if (!isset($_POST['stir_bouwsteen_nonce']) || !wp_verify_nonce($_POST['stir_bouwsteen_nonce'], 'stir_bouwsteen_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $icon = absint($_POST['bouwsteen_icon'] ?? 1);
    if ($icon < 1 || $icon > 5) $icon = 1;
    update_post_meta($post_id, '_bouwsteen_icon', (string) $icon);
});


// ============================================================
// 6. GUTENBERG BLOCKS
// ============================================================

/**
 * Change the default image size for core/image blocks to 'full'.
 * WordPress defaults to 'large'; this ensures newly inserted images
 * always use the original full-resolution file unless the editor
 * explicitly selects a smaller size.
 */
add_filter('register_block_type_args', function ($args, $block_type) {
    if ($block_type === 'core/image' && isset($args['attributes']['sizeSlug'])) {
        $args['attributes']['sizeSlug']['default'] = 'full';
    }
    return $args;
}, 10, 2);


// ============================================================
// 7. TEMPLATE HELPER FUNCTIONS
// ============================================================

// Get page by slug
function get_page_id_by_slug(string $slug): ?int {
    $page = get_page_by_path($slug);
    return $page ? (int) $page->ID : null;
}

function echo_page_content(int $page_id): void {
    $post = get_post($page_id);
    if ($post) {
        echo apply_filters('the_content', $post->post_content);
    }
}

function the_content_after_separator($index = 1, $page_id = null) {
    $post = $page_id ? get_post($page_id) : get_post();
    if (! $post) return;

    $blocks = parse_blocks($post->post_content);

    $currentSeparatorIndex = 0;
    $foundStartSeparator = false;

    foreach ($blocks as $block) {
        if ($block['blockName'] === 'core/separator') {
            $currentSeparatorIndex++;
            if ($currentSeparatorIndex === $index) {
                $foundStartSeparator = true;
                continue;
            } elseif ($currentSeparatorIndex === $index + 1) {
                break;
            }
        }

        if ($foundStartSeparator && $currentSeparatorIndex === $index) {
            echo do_shortcode(render_block($block));
        }
    }
}

function the_content_before_separator($index = 1) {
    global $post;
    $blocks = parse_blocks($post->post_content);

    $currentSeparatorIndex = 0;
    $contentBlocks = [];

    foreach ($blocks as $block) {
        if ($block['blockName'] === 'core/separator') {
            $currentSeparatorIndex++;
            if ($currentSeparatorIndex >= $index) {
                break; // Stop when reaching or exceeding the specified separator index
            }
        } else {
            // Add block to contentBlocks array if it's before the specified separator
            $contentBlocks[] = $block;
        }
    }

    foreach ($contentBlocks as $block) {
        echo render_block($block);
    }
}

function get_first_block($post = null)
{
  $post = get_post($post);
  if (! $post) return '';

  $blocks = parse_blocks($post->post_content);
  if (! empty($blocks)) {
    return apply_filters('the_content', render_block($blocks[0]));
  }

  return '';
}

function render_sections($content) {
    $blocks = parse_blocks($content);
    $output = '';

    foreach ($blocks as $block) {

        // Skip empty blocks
        if (empty($block['blockName']) && trim($block['innerHTML'] ?? '') === '') {
            continue;
        }

        $className = $block['attrs']['className'] ?? '';

        // Detect section blocks
        $is_section = str_contains($className, 'section--');

        // Remove section-related classes from the original block
        if ($is_section && !empty($block['attrs']['className'])) {
            $classes = explode(' ', $block['attrs']['className']);

            $filtered = array_filter($classes, function ($cls) {
                return !str_starts_with($cls, 'section--') && $cls !== 'section';
            });

            if (!empty($filtered)) {
                $block['attrs']['className'] = implode(' ', $filtered);
            } else {
                unset($block['attrs']['className']);
            }
        }

        $block_html = render_block($block);

        // Remove section classes that might still exist in rendered HTML
        if ($is_section) {
            $block_html = preg_replace_callback('/\bclass="([^"]*)"/i', function ($m) {
                $classes = preg_replace('/\bsection(?:--[\w-]+)?\b/', '', $m[1]);
                $classes = preg_replace('/\s{2,}/', ' ', trim($classes));
                return 'class="' . $classes . '"';
            }, $block_html);
        }

        if ($is_section) {

            $classes = explode(' ', $className);

            $section_classes_array = array_filter($classes, fn($cls) => str_starts_with($cls, 'section--') || str_starts_with($cls, 'scheme-'));

            $scheme_attr = trim($block['attrs']['scheme'] ?? '');
            if ($scheme_attr) {
                $section_classes_array[] = $scheme_attr;
            }

            $section_classes = 'section ' . implode(' ', $section_classes_array);

            $section_html  = '<section class="' . esc_attr($section_classes) . '">';
            $section_html .= '<div class="section-container">';
            $section_html .= $block_html;
            $section_html .= '</div>';
            $section_html .= '</section>';

            if (preg_match('/<div class="section-container">\s*<\/div>/', $section_html)) {
                continue;
            }

            $output .= $section_html;

        } else {

            // fallback: just render normally
            $output .= $block_html;

        }
    }

    return $output;
}


// ============================================================
// 8. CATEGORY TERM META — focus project
// ============================================================

/**
 * Register term meta for storing the focus project per category.
 */
add_action('init', function () {
    register_term_meta('category', '_category_focus_project', [
        'type'              => 'integer',
        'single'            => true,
        'sanitize_callback' => 'absint',
        'show_in_rest'      => false,
    ]);
});

/**
 * Render the focus-project dropdown on the Add Category screen.
 */
add_action('category_add_form_fields', function () {
    $all_projects = get_posts([
        'post_type'   => 'post',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ]);
    ?>
    <div class="form-field">
        <label for="category_focus_project">Focus project</label>
        <select name="category_focus_project" id="category_focus_project">
            <option value="">— Select project —</option>
            <?php foreach ($all_projects as $p) : ?>
                <option value="<?php echo esc_attr($p->ID); ?>">
                    <?php echo esc_html($p->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">Project shown in the Focus section for this category.</p>
    </div>
    <?php
});

/**
 * Render the focus-project dropdown on the Edit Category screen.
 */
add_action('category_edit_form_fields', function ($term) {
    $current = (int) get_term_meta($term->term_id, '_category_focus_project', true);

    // Fetch projects in this category for the dropdown
    $cat_projects = get_posts([
        'post_type'   => 'post',
        'numberposts' => -1,
        'cat'         => $term->term_id,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ]);

    // Also grab all projects so the editor can pick any project
    $all_projects = get_posts([
        'post_type'   => 'post',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ]);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="category_focus_project">Focus project</label></th>
        <td>
            <select name="category_focus_project" id="category_focus_project">
                <option value="">— Select project —</option>
                <?php if (!empty($cat_projects)) : ?>
                    <optgroup label="In this category">
                        <?php foreach ($cat_projects as $p) : ?>
                            <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($current, $p->ID); ?>>
                                <?php echo esc_html($p->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endif; ?>
                <optgroup label="All projects">
                    <?php foreach ($all_projects as $p) : ?>
                        <option value="<?php echo esc_attr($p->ID); ?>" <?php selected($current, $p->ID); ?>>
                            <?php echo esc_html($p->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <p class="description">Project shown in the Focus section for this category.</p>
        </td>
    </tr>
    <?php
});

/**
 * Save focus project term meta on both create and edit.
 */
add_action('created_category', 'stir_save_category_focus_project');
add_action('edited_category',  'stir_save_category_focus_project');

function stir_save_category_focus_project($term_id) {
    if (!isset($_POST['category_focus_project'])) return;
    $project_id = absint($_POST['category_focus_project']);
    if ($project_id) {
        update_term_meta($term_id, '_category_focus_project', $project_id);
    } else {
        delete_term_meta($term_id, '_category_focus_project');
    }
}


// ============================================================
// 9. ADMIN PAGES
// ============================================================


// ============================================================
// 10. PROJECTS LIST TABLE — second title + featured columns
// ============================================================

/**
 * Add "Second Title" (after Title) and "Featured" (at end) columns.
 */
add_filter('manage_post_posts_columns', function ($columns) {
    $new = [];
    foreach ($columns as $key => $label) {
        $new[$key] = $label;
        if ($key === 'title') {
            $new['second_title'] = 'Second Title';
        }
    }
    $new['featured'] = 'Featured';
    return $new;
});

add_action('manage_post_posts_custom_column', function ($column, $post_id) {
    if ($column === 'second_title') {
        $second_title = get_post_meta($post_id, '_project_second_title', true);
        echo $second_title ? esc_html($second_title) : '<span style="color:#8c8f94">—</span>';
    } elseif ($column === 'featured') {
        $featured = get_post_meta($post_id, '_project_featured', true);
        echo $featured === '1'
            ? '<span style="color:#d63638;font-size:16px;" title="Featured">★</span>'
            : '<span style="color:#8c8f94">—</span>';
    }
}, 10, 2);

add_filter('manage_edit-post_sortable_columns', function ($columns) {
    $columns['second_title'] = 'second_title';
    $columns['featured']     = 'featured';
    return $columns;
});

add_action('pre_get_posts', function ($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ($query->get('orderby') === 'second_title') {
        $query->set('meta_key', '_project_second_title');
        $query->set('orderby', 'meta_value');
    }
    if ($query->get('orderby') === 'featured') {
        $query->set('meta_key', '_project_featured');
        $query->set('orderby', 'meta_value');
    }
});

add_action('admin_head-edit.php', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'post') return;
    echo '<style>.column-second_title { width: 18%; } .column-featured { width: 80px; text-align: center; } .manage-column.column-featured { text-align: center; }</style>';
});