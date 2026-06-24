<?php
/**
 * Template Part: Project Card
 *
 * Expects $args array:
 *   'post_id'  (int)    — required
 *   'img_size' (string) — optional WP image size, default 'large'
 *
 * Outputs a .project-card element with:
 *   - Modifier class for ratio:     project-card--portret | project-card--vierkant | project-card--landschap
 *   - Modifier class for size:      project-card--large | project-card--medium | project-card--small
 *   - Modifier class for hover:     project-card--hover-color | project-card--hover-second-image | project-card--hover-movement
 *   - Data attribute for hover sub-type (color scheme, movement value) where relevant
 */

$post_id  = isset($args['post_id'])  ? (int) $args['post_id']  : 0;
$img_size = isset($args['img_size']) ? $args['img_size']        : 'full';

if (!$post_id) return;

$post = get_post($post_id);
if (!$post || $post->post_status !== 'publish') return;

$ratio        = get_post_meta($post_id, '_project_ratio',           true); // portret | vierkant | landschap
$size         = get_post_meta($post_id, '_project_size',            true); // large | medium | small
$hover_type   = get_post_meta($post_id, '_project_hover_type',      true); // color | second_image | movement
$hover_color  = get_post_meta($post_id, '_project_hover_color',     true);
$hover_move   = get_post_meta($post_id, '_project_hover_movement',  true);
$hover_img_id = get_post_meta($post_id, '_project_hover_image_id',  true);
$second_title = get_post_meta($post_id, '_project_second_title',    true);

$title        = get_the_title($post_id);
$display_title = $second_title ?: $title;
$permalink    = get_permalink($post_id);
$has_content  = !empty(trim(strip_tags($post->post_content)));

// Build class list
$classes = ['project-card'];

if ($ratio) {
    $classes[] = 'project-card--' . sanitize_html_class($ratio);
}

if ($size) {
    $classes[] = 'project-card--' . sanitize_html_class($size);
}

$hover_class_map = [
    'color'        => 'project-card--hover-color',
    'second_image' => 'project-card--hover-second-image',
    'movement'     => 'project-card--hover-movement',
];
if ($hover_type && isset($hover_class_map[$hover_type])) {
    $classes[] = $hover_class_map[$hover_type];
}

// Hover color inline style
$hover_style = '';
if ($hover_type === 'color' && $hover_color) {
    $hover_style = ' style="--hover-scheme-class:' . esc_attr($hover_color) . '"';
}

// Movement data attribute
$data_move = '';
if ($hover_type === 'movement' && $hover_move) {
    $data_move = ' data-hover-movement="' . esc_attr($hover_move) . '"';
}

// Second image tag (uses srcset for full quality)
$hover_img_tag = '';
if ($hover_type === 'second_image' && $hover_img_id) {
    $hover_img_tag = wp_get_attachment_image((int) $hover_img_id, $img_size, false, [
        'alt'   => esc_attr($title),
        'class' => 'project-card__hover-img',
    ]);
}
?>
<a href="<?php echo esc_url($permalink); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $data_move; ?>>
    <div class="project-card__image-wrap">
        <?php if (has_post_thumbnail($post_id)) : ?>
            <?php echo get_the_post_thumbnail($post_id, $img_size, [
                'alt'     => esc_attr($title),
                'class'   => 'project-card__image',
                'loading' => 'lazy',
            ]); ?>
        <?php endif; ?>

        <?php if ($hover_type === 'color') : ?>
            <div class="project-card__hover <?php echo esc_attr($hover_color); ?>">
                <span class="project-card__hover-title h3"><?php echo esc_html($title); ?></span>
            </div>

        <?php elseif ($hover_type === 'second_image' && $hover_img_tag) : ?>
            <div class="project-card__hover project-card__hover--image">
                <?php echo $hover_img_tag; ?>
                <span class="project-card__hover-title h3"><?php echo esc_html($title); ?></span>
            </div>

        <?php elseif ($hover_type === 'movement') : ?>
            <div class="project-card__hover">
                <span class="project-card__hover-title h3"><?php echo esc_html($title); ?></span>
            </div>
        <?php endif; ?>
    </div>
    <p class="project-card__title"><?php echo esc_html($display_title); ?></p>
</a>
