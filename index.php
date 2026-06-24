<?php get_header(); ?>

<?php
$all_cats = get_categories(['hide_empty' => true]);

$cat_data = [];
foreach ($all_cats as $cat) {
    if ($cat->parent != 0) continue;
    $children = get_categories([
        'parent'     => $cat->term_id,
        'hide_empty' => true,
    ]);
    $cat_data[] = [
        'cat'      => $cat,
        'children' => $children,
    ];
}

$all_projects = get_posts([
    'post_type'   => 'post',
    'numberposts' => -1,
    'orderby'     => 'none',
    'post_status' => 'publish',
]);

$size_cols = [
    'large'  => 9,
    'medium' => 6,
    'small'  => 3,
];
?>

<main class="site-main scheme-white">

    <?php get_template_part('template-parts/header'); ?>

    <section class="section section--filters filter-open">

        <div class="section-container filter__bar grid--12">

            <button class="button filter__toggle" aria-expanded="false" aria-controls="filter-panel">
                <span>Filters</span>
                <span class="filter__active-summary">
                    <span class="filter__active-min">
                        <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/min.svg'); ?>
                    </span>
                    <span class="filter__active-tag--cats" aria-live="polite"></span>
                    <span class="filter__active-x">✕</span>
                </span>
            </button>

            <button class="button filter__types-bar">
                <span class="filter__types-close">Types</span>
                <span class="filter__active-summary">
                    <span class="filter__active-min">
                        <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/min.svg'); ?>
                    </span>
                    <span class="filter__active-tag--types" aria-live="polite"></span>
                    <span class="filter__active-x">✕</span>
                </span>
            </button>

            <div class="filter__controls">
                <span class="filter__list">Lijst</span>

                <div class="filter__zoom">
                    <span class="filter__zoom-out">
                        <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/min.svg'); ?>
                    </span>
                    <span>Grid</span>
                    <span class="filter__zoom-in">
                        <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/plus.svg'); ?>
                    </span>
                </div>

                <span class="filter__search">
                    <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/search.svg'); ?>
                </span>
            </div>

        </div>

        <div class="filter__panel is-open" aria-hidden="false">
            <div class="filter__panel__inner grid--12">

                <div class="filter__col filter__col--categories">
                    <?php if (!empty($cat_data)) : ?>
                    <ul class="filter__category-list" role="list">
                        <?php foreach ($cat_data as $entry) : ?>
                        <li
                            class="filter__category fs-2xl"
                            data-cat-id="<?php echo esc_attr($entry['cat']->term_id); ?>"
                            data-cat-slug="<?php echo esc_attr($entry['cat']->slug); ?>"
                            role="button"
                            tabindex="0"
                        >
                            <?php echo esc_html($entry['cat']->name); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <div class="filter__col filter__col--types">
                    <ul class="filter__type-list" role="list"></ul>
                </div>

            </div>
        </div>

        <div class="search__panel" aria-hidden="true">
            <div class="search__panel__inner">
                <input
                    class="search__panel__input h3"
                    type="search"
                    placeholder="Zoek projecten…"
                    autocomplete="off"
                    aria-label="Zoek projecten"
                >
            </div>
        </div>

    </section>

    <section class="section section--projects">
        <div class="section-container projects__grid grid--12" data-zoom="0">
            <?php foreach ($all_projects as $project) :
                $size        = get_post_meta($project->ID, '_project_size', true) ?: 'medium';
                $cols        = $size_cols[$size] ?? 6;
                $cat_ids     = wp_get_post_categories($project->ID, ['fields' => 'ids']);
                $cat_slugs   = array_map(function($id) {
                    $t = get_term($id, 'category');
                    return $t ? $t->slug : '';
                }, $cat_ids);
                $has_content = !empty(trim(strip_tags($project->post_content)));

                // Parent category (top-level)
                $parent_cat = null;
                foreach ($cat_ids as $cid) {
                    $term = get_term($cid, 'category');
                    if ($term && $term->parent == 0) { $parent_cat = $term; break; }
                }
                // Subcategory (child)
                $sub_cat = null;
                foreach ($cat_ids as $cid) {
                    $term = get_term($cid, 'category');
                    if ($term && $term->parent != 0) { $sub_cat = $term; break; }
                }
                // Bouwjaar
                $bouwjaar = get_post_meta($project->ID, '_project_bouwjaar', true);
                // Second title
                $second_title = get_post_meta($project->ID, '_project_second_title', true);
            ?>
            <div
                class="projects__item<?php echo $has_content ? ' has-content' : ' no-content'; ?>"
                data-cols="<?php echo $cols; ?>"
                style="grid-column: span <?php echo $cols; ?>;"
                data-cats="<?php echo esc_attr(implode(' ', $cat_slugs)); ?>"
                data-cat-ids="<?php echo esc_attr(implode(' ', $cat_ids)); ?>"
                data-bouwjaar="<?php echo esc_attr($bouwjaar); ?>"
                data-title="<?php echo esc_attr(strtolower($second_title ?: get_the_title($project->ID))); ?>"
                data-permalink="<?php echo esc_url(get_permalink($project->ID)); ?>"
            >
                <?php get_template_part('template-parts/project-card', null, ['post_id' => $project->ID]); ?>

                <div class="project__item__meta grid--12">
                    <span class="project__item__list-title"><?php echo esc_html($second_title ?: get_the_title($project->ID)); ?></span>
                    <span class="project__item__list-spacer"></span>
                    <span class="project__item__list-parent-cat"><?php echo $parent_cat ? esc_html($parent_cat->name) : ''; ?></span>
                    <span class="project__item__list-sub-cat"><?php echo $sub_cat ? esc_html($sub_cat->name) : ''; ?></span>
                    <span class="project__item__list-year"><?php echo esc_html($bouwjaar); ?></span>
                    <?php if ($has_content) : ?>
                    <a href="<?php echo esc_url(get_permalink($project->ID)); ?>" class="project__item__list-cta">Bekijk project →</a>
                    <?php else : ?>
                    <span class="project__item__list-cta project__item__list-cta--empty"></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<script>
window.STIR_CATS = <?php echo json_encode(array_map(function($entry) {
    return [
        'id'       => $entry['cat']->term_id,
        'slug'     => $entry['cat']->slug,
        'name'     => $entry['cat']->name,
        'children' => array_map(function($child) {
            return [
                'id'   => $child->term_id,
                'slug' => $child->slug,
                'name' => $child->name,
            ];
        }, $entry['children']),
    ];
}, $cat_data), JSON_UNESCAPED_UNICODE); ?>;
</script>

<?php get_footer(); ?>
