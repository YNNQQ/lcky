<?php get_header(); ?>

<?php
    $front_page_id  =   get_option('page_on_front');
    $approach_page  =   get_page_by_path('aanpak');
    $about_page     =   get_page_by_path('over-ons');
    $contact_page   =   get_page_by_path( 'contact' );
?>

<main class="site-main scheme-grey">

    <?php get_template_part('template-parts/header'); ?>

    <section id="splash-screen" class="section section--full section--splash scheme-dark-red">
        <div class="section-container">
            <div class="wordmark wordmark--reverse" aria-hidden="true">
                <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
            </div>
        </div>
    </section>

    <?php if (is_front_page()): ?>

        <div class="header-tag grid--12">
            <span><?php esc_html_e('Architect & Planner', 'mrstir'); ?></span>
        </div>
            
        <?php
        $hero_projects = get_posts([
            'post_type'   => 'post',
            'numberposts' => -1,
            'meta_key'    => '_project_featured',
            'meta_value'  => '1',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ]);
        ?>
        <section class="section section--hero section--hero--swiper section--full scheme-black" data-header-theme="dark">
            <div class="section-container">
                <?php if ($hero_projects) :
                $movement_class_map = [
                    'zoom-in'  => 'img-zoom-in',
                    'zoom-out' => 'img-zoom-out',
                    'up'       => 'img-move-up',
                    'down'     => 'img-move-down',
                    'right'    => 'img-move-right',
                    'left'     => 'img-move-left',
                ];
                ?>
                <div class="wp-block-gallery">
                    <?php foreach ($hero_projects as $hp) :
                        $movement  = get_post_meta($hp->ID, '_project_hover_movement', true);
                        $anim_cls  = isset($movement_class_map[$movement]) ? ' ' . $movement_class_map[$movement] : '';
                    ?>
                    <figure class="wp-block-image<?php echo $anim_cls; ?>">
                        <?php echo get_the_post_thumbnail($hp->ID, 'full', [
                            'alt'     => esc_attr(get_the_title($hp->ID)),
                            'loading' => 'eager',
                        ]); ?>
                    </figure>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div>
                    <?php the_content_before_separator(); ?>
                    
                </div>

                <div class="hero-arrow">
                    <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/arrow.svg'); ?>
                </div>
            </div>
        </section>

        <section class="section section--intro scheme-white" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(1); ?>
            </div>
        </section>

        <section class="section section--focus scheme-light-green" data-header-theme="light">
            <div class="section-container grid--12 scheme-light-green">
                <?php
                    // Build focus list: categories that have a focus project set via term meta
                    $all_cats = get_categories(['hide_empty' => true]);

                    $focus_cats = [];
                    foreach ($all_cats as $cat) {
                        $focus_id = (int) get_term_meta($cat->term_id, '_category_focus_project', true);
                        if (!$focus_id) continue;
                        $focus_post = get_post($focus_id);
                        if (!$focus_post || $focus_post->post_status !== 'publish') continue;
                        $second_title = get_post_meta($focus_post->ID, '_project_second_title', true);
                        $focus_cats[] = [
                            'cat'     => $cat,
                            'project' => $focus_post,
                            'image'   => get_the_post_thumbnail_url($focus_post->ID, 'full'),
                            'url'     => get_permalink($focus_post->ID),
                            'title'   => $second_title ?: get_the_title($focus_post->ID),
                        ];
                    }

                    $first = !empty($focus_cats) ? $focus_cats[0] : null;
                ?>

                <div class="focus__left">
                    <?php the_content_after_separator(2); ?>

                    <?php if (!empty($focus_cats)) : ?>
                    <ul class="focus__categories" role="list">
                        <?php foreach ($focus_cats as $i => $item) : ?>
                        <li
                            class="focus__category fs-2xl <?php echo $i === 0 ? ' is-active' : ''; ?>"
                            data-category="<?php echo esc_attr($item['cat']->term_id); ?>"
                            data-image="<?php echo esc_url($item['image']); ?>"
                            data-url="<?php echo esc_url($item['url']); ?>"
                            data-title="<?php echo esc_attr($item['title']); ?>"
                            role="button"
                            tabindex="0"
                            aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        >
                            <?php echo esc_html($item['cat']->name); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>


                    <div class="wp-block-buttons is-layout-flex wp-block-buttons-is-layout-flex">
                        <div class="wp-block-button">
                            <a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(get_post_type_archive_link('post')); ?>">Lees meer →</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="focus__right">
                    <?php if ($first) : ?>
                    <a href="<?php echo esc_url($first['url']); ?>" class="focus__project-link" id="focus-project-link">
                        <figure class="focus__image-wrap">
                            <img
                                src="<?php echo esc_url($first['image']); ?>"
                                alt="<?php echo esc_attr($first['title']); ?>"
                                class="focus__image"
                                id="focus-project-image"
                            >
                        </figure>
                        <p class="focus__project-title project-card__title" id="focus-project-title">
                            <?php echo esc_html($first['title']); ?>
                        </p>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="section section--row section--approach scheme-light-red" data-header-theme="light">
            <div class="section-container scheme-light-red">
                <?php the_content_after_separator(3); ?>

                <?php
                $bouwstenen = get_posts([
                    'post_type'   => 'bouwsteen',
                    'numberposts' => 5,
                    'orderby'     => 'menu_order',
                    'order'       => 'ASC',
                ]);
                if ($bouwstenen) :
                ?>
                <div class="approach__wrapper grid--5" data-approach-url="<?php echo esc_url(get_permalink($approach_page)); ?>">
                    <?php foreach ($bouwstenen as $i => $item) :
                        $n        = $i + 1; // 1-based
                        $icon_num = get_post_meta($item->ID, '_bouwsteen_icon', true) ?: $n;
                        $svg      = get_template_directory() . '/assets/svg/icons/bouwsteen-' . $icon_num . '.svg';
                    ?>
                    <div class="approach__item" data-index="<?php echo $n; ?>">
                        <div class="approach__item__icon">
                            <?php if (file_exists($svg)) echo file_get_contents($svg); ?>
                        </div>
                        <h2 class="approach__item__title fs-3xl"><?php echo esc_html(get_the_title($item->ID)); ?></h2>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

            </div>

            <div class="section-container">
                <?php the_content_after_separator(4); ?>
            </div>
        </section>


        <section class="section section--row section--slider scheme-white" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(5); ?>

                <?php
                $featured_projects = array_filter(get_posts([
                    'post_type'   => 'post',
                    'numberposts' => -1,
                    'meta_key'    => '_project_featured',
                    'meta_value'  => '1',
                    'orderby'     => 'menu_order',
                    'order'       => 'ASC',
                ]), function($p) {
                    return !empty(trim(strip_tags($p->post_content)));
                });
                if ($featured_projects) :
                ?>
                <div class="project-slider__swiper swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($featured_projects as $fp) : ?>
                        <div class="swiper-slide">
                            <?php get_template_part('template-parts/project-card', null, ['post_id' => $fp->ID]); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

    <?php elseif (is_page($approach_page)): ?>
        <section class="section section--full scheme-red" data-header-theme="dark">
            <div class="section-container">
                <?php the_content_before_separator(); ?>

                <div class="wordmark wordmark--forward" aria-hidden="true">
                    <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
                </div>
            </div>
        </section>

        <?php
        $bouwstenen = get_posts([
            'post_type'   => 'bouwsteen',
            'numberposts' => 5,
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ]);
        if ($bouwstenen) :
        ?>

        <div class="strip-track" style="--strip-rows: <?php echo count($bouwstenen); ?>">
            <section class="section section--strip" data-type="bouwstenen" data-rows="<?php echo count($bouwstenen); ?>">

                <div class="strip__left">
                    <?php
                    // Reversed DOM order so the track can move DOWN and reveal each
                    // new panel from the top (panel N+1 sits above panel N in the DOM).
                    $bouwstenen_rev = array_reverse($bouwstenen);
                    foreach ($bouwstenen_rev as $i => $item) :
                        $original_row = count($bouwstenen) - 1 - $i;
                        $image_left   = ($original_row % 2 === 0);
                    ?>
                    <?php $icon_num = get_post_meta($item->ID, '_bouwsteen_icon', true) ?: ($original_row + 1); ?>
                    <div class="strip__panel <?php echo $image_left ? 'strip__panel--image scheme-dark-red' : 'strip__panel--content scheme-light-red'; ?>">
                        <?php if ($image_left) : ?>
                            <?php
                            $svg_path = get_template_directory() . '/assets/svg/icons/bouwsteen-' . $icon_num . '.svg';
                            if (file_exists($svg_path)) :
                            ?>
                            <div class="strip__panel__image-wrap">
                                <?php echo file_get_contents($svg_path); ?>
                            </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div class="strip__panel__content">
                                <span class="strip__panel__label">Bouwsteen <?php echo $original_row + 1; ?></span>
                                <h2 class="strip__panel__title fs-3xl"><?php echo esc_html(get_the_title($item->ID)); ?></h2>
                                <div class="strip__panel__text h3"><?php echo apply_filters('the_content', $item->post_content); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="strip__right">
                    <?php foreach ($bouwstenen as $i => $item) :
                        $image_left = ($i % 2 === 0); // rows 1,3,5: content right
                        $icon_num   = get_post_meta($item->ID, '_bouwsteen_icon', true) ?: ($i + 1);
                    ?>
                    <div class="strip__panel <?php echo $image_left ? 'strip__panel--content scheme-light-red' : 'strip__panel--image scheme-red'; ?>">
                        <?php if ($image_left) : ?>
                            <div class="strip__panel__content">
                                <span class="strip__panel__label">Bouwsteen <?php echo $i + 1; ?></span>
                                <h2 class="strip__panel__title fs-3xl"><?php echo esc_html(get_the_title($item->ID)); ?></h2>
                                <div class="strip__panel__text h3"><?php echo apply_filters('the_content', $item->post_content); ?></div>
                            </div>
                        <?php else : ?>
                            <?php
                            $svg_path = get_template_directory() . '/assets/svg/icons/bouwsteen-' . $icon_num . '.svg';
                            if (file_exists($svg_path)) :
                            ?>
                            <div class="strip__panel__image-wrap">
                                <?php echo file_get_contents($svg_path); ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

            </section>
        </div>

        <section class="section section--full--second section--full scheme-light-green" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(1); ?>

                <div class="wordmark wordmark--icons" aria-hidden="true">
                    <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php elseif (is_page($about_page)): ?>
        <section class="section section--about-intro" data-header-theme="light">
            <div class="section-container grid--12">
                <?php the_content_before_separator(); ?>
            </div>
        </section>

        <section class="section section--focus section--focus--exp section--row scheme-light-green" data-header-theme="light">
            <?php
                // Build focus list: categories that have a focus project set via term meta
                $all_cats = get_categories(['hide_empty' => true]);

                $focus_cats = [];
                foreach ($all_cats as $cat) {
                    $focus_id = (int) get_term_meta($cat->term_id, '_category_focus_project', true);
                    if (!$focus_id) continue;
                    $focus_post = get_post($focus_id);
                    if (!$focus_post || $focus_post->post_status !== 'publish') continue;
                    $second_title = get_post_meta($focus_post->ID, '_project_second_title', true);
                    $focus_cats[] = [
                        'cat'     => $cat,
                        'project' => $focus_post,
                        'image'   => get_the_post_thumbnail_url($focus_post->ID, 'full'),
                        'url'     => get_permalink($focus_post->ID),
                        'title'   => $second_title ?: get_the_title($focus_post->ID),
                    ];
                }

                $first = !empty($focus_cats) ? $focus_cats[0] : null;
            ?>

            <div class="section-container">
                <?php the_content_after_separator(1); ?>
            </div>

            <?php if (!empty($focus_cats)) : ?>
            <div class="section-container grid--12">

                <div class="focus__image">
                    <a href="<?php echo esc_url($focus_cats[0]['url']); ?>" class="focus__project-link" id="focus-project-link">
                        <figure class="focus__image-wrap">
                            <img
                                src="<?php echo esc_url($focus_cats[0]['image']); ?>"
                                alt="<?php echo esc_attr($focus_cats[0]['title']); ?>"
                                class="focus__image"
                                id="focus-project-image"
                            >
                        </figure>
                        <p class="focus__project-title project-card__title" id="focus-project-title">
                            <?php echo esc_html($focus_cats[0]['title']); ?>
                        </p>
                    </a>
                </div>

                <ul class="focus__categories sticky" role="list">
                    <?php foreach ($focus_cats as $i => $item) :
                        $desc = category_description($item['cat']->term_id);
                        $archive_url = get_category_link($item['cat']->term_id);
                    ?>
                    <li
                        class="focus__category<?php echo $i === 0 ? ' is-active' : ''; ?>"
                        data-image="<?php echo esc_url($item['image']); ?>"
                        data-url="<?php echo esc_url($item['url']); ?>"
                        data-title="<?php echo esc_attr($item['title']); ?>"
                        data-archive="<?php echo esc_url($archive_url); ?>"
                        data-cat="<?php echo esc_attr($item['cat']->name); ?>"
                        role="button"
                        tabindex="0"
                        aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                    >
                        <span class="focus__category-name fs-2xl"><?php echo esc_html($item['cat']->name); ?></span>

                        <div class="focus__category-body">
                            <div class="focus__category-body-inner">
                                <?php if ($desc) : ?>
                                <div class="focus__category-desc">
                                    <?php echo wp_kses_post($desc); ?>
                                </div>
                                <?php endif; ?>
                                <div class="wp-block-button focus__category-cta">
                                    <a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($archive_url); ?>">Alle <?php echo esc_html($item['cat']->name); ?> →</a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>

            </div>
            <?php endif; ?>
        </section>

        <section class="section section--team scheme-dark-grey" data-header-theme="dark">
            <div class="section-container">
                <?php the_content_after_separator(2); ?>
            </div>

            <?php
            $team_members = get_posts([
                'post_type'   => 'team',
                'numberposts' => -1,
                'orderby'     => 'menu_order',
                'order'       => 'ASC',
            ]);
            if ($team_members) :
            ?>
            <div class="section-container grid--4">
                <?php foreach ($team_members as $member) :
                    $job_title = get_post_meta($member->ID, '_team_job_title', true);
                    $phone     = get_post_meta($member->ID, '_team_phone', true);
                    $email     = get_post_meta($member->ID, '_team_email', true);
                    $has_content = !empty(trim($member->post_content));
                ?>
                <div class="team__member<?php echo $has_content ? ' has-content' : ''; ?>"<?php echo $has_content ? ' data-url="' . esc_url(get_permalink($member->ID)) . '" role="button" tabindex="0"' : ''; ?>>
                    <?php if (has_post_thumbnail($member->ID)) : ?>
                    <div class="team__image-wrap">
                        <?php echo get_the_post_thumbnail($member->ID, 'large', ['class' => 'team__image']); ?>
                    </div>
                    <?php endif; ?>

                    <div class="team__info-wrapper">
                        <div class="team__info">
                            <div class="team__info--data">
                                <h3 class="team__name"><?php echo esc_html(get_the_title($member->ID)); ?></h3>
        
                                <?php if ($job_title) : ?>
                                    <p class="team__job-title"><?php echo esc_html($job_title); ?></p>
                                <?php endif; ?>
                            </div>
    
                            <?php if ($phone || $email) : ?>
                            <ul class="team__contact" role="list">
                                <?php if ($phone) : ?>
                                <li><a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></li>
                                <?php endif; ?>
                                <?php if ($email) : ?>
                                <li><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></li>
                                <?php endif; ?>
                            </ul>
                            <?php endif; ?>
                        </div>

                        
                        <div class="team__more" aria-hidden="true">
                            <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/plus.svg'); ?>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <section class="section section--credits" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(3); ?>
            </div>
        </section>

        <section class="section section--row section--slider scheme-white" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(4); ?>

                <?php
                $featured_projects = array_filter(get_posts([
                    'post_type'   => 'post',
                    'numberposts' => -1,
                    'meta_key'    => '_project_featured',
                    'meta_value'  => '1',
                    'orderby'     => 'menu_order',
                    'order'       => 'ASC',
                ]), function($p) {
                    return !empty(trim(strip_tags($p->post_content)));
                });
                if ($featured_projects) :
                ?>
                <div class="project-slider__swiper swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($featured_projects as $fp) : ?>
                        <div class="swiper-slide">
                            <?php get_template_part('template-parts/project-card', null, ['post_id' => $fp->ID]); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

    <?php elseif (is_page($contact_page)): ?>

        <?php
        // Collect core/columns blocks from the page content — each becomes one scroll row.
        $contact_rows = [];
        foreach (parse_blocks(get_post()->post_content) as $block) {
            if ($block['blockName'] === 'core/columns') {
                $contact_rows[] = $block;
                if (count($contact_rows) === 2) break;
            }
        }
        $contact_row_count = count($contact_rows);
        ?>

        <?php if ($contact_rows): ?>
        <div class="strip-track" style="--strip-rows: <?php echo $contact_row_count; ?>">
            <section class="section section--strip section--strip--contact" data-type="contact" data-rows="<?php echo $contact_row_count; ?>">

                <div class="strip__left">
                    <?php
                    // Reversed DOM order so the track moves DOWN and reveals each new panel from the top
                    $reversed_rows = array_reverse($contact_rows);
                    foreach ($reversed_rows as $i => $row_block):
                        $original_row = $contact_row_count - 1 - $i;
                        $inner_cols   = array_values(array_filter(
                            $row_block['innerBlocks'],
                            fn($b) => $b['blockName'] === 'core/column'
                        ));
                        $left_col   = $inner_cols[0] ?? null;
                        $is_image   = $left_col
                            && count($left_col['innerBlocks']) === 1
                            && $left_col['innerBlocks'][0]['blockName'] === 'core/image';
                        $extra_class = $is_image ? ' strip__panel--image' : ' strip__panel--content';
                    ?>
                    <div class="strip__panel scheme-dark-grey <?php echo $extra_class; ?>">
                        <?php if ($left_col): echo render_block($left_col); endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="strip__right">
                    <?php foreach ($contact_rows as $i => $row_block):
                        $inner_cols = array_values(array_filter(
                            $row_block['innerBlocks'],
                            fn($b) => $b['blockName'] === 'core/column'
                        ));
                        $right_col  = $inner_cols[1] ?? null;
                        $is_image   = $right_col
                            && count($right_col['innerBlocks']) === 1
                            && $right_col['innerBlocks'][0]['blockName'] === 'core/image';
                        $extra_class = $is_image ? ' strip__panel--image' : ' strip__panel--content';
                    ?>
                    <div class="strip__panel scheme-dark-grey<?php echo $extra_class; ?>">
                        <?php if ($right_col): echo render_block($right_col); endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

            </section>
        </div>
        <?php endif; ?>

        <section class="section section--full section--full--second scheme-grey">
            <div class="section-container">

                <?php the_content_after_separator(2); ?>
        
        
                <?php
                $vacatures = get_posts([
                    'post_type'   => 'vacature',
                    'numberposts' => -1,
                    'orderby'     => 'menu_order',
                    'order'       => 'ASC',
                    'post_status' => 'publish',
                ]);
                if ($vacatures):
                ?>
                <div class="vacatures__list">
                    <?php foreach ($vacatures as $vacature): ?>
                    <a class="vacature__item grid--12" href="<?php echo esc_url(get_permalink($vacature->ID)); ?>">
                        <h3 class="vacature__title"><?php echo esc_html(get_the_title($vacature->ID)); ?></h3>
                        <span class="vacature__cta">Bekijk vacature →</span>
                        <span class="vacature__cta vacature__cta--mobile">→</span>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

    <?php endif?>


</main>

<?php get_footer(); ?>