<?php get_header(); ?>

<?php the_post(); ?>

<main class="site-main scheme-white">
<?php get_template_part('template-parts/header'); ?>

<?php if (get_post_type() === 'team') : ?>

    <?php
    $job_title = get_post_meta(get_the_ID(), '_team_job_title', true);
    $phone     = get_post_meta(get_the_ID(), '_team_phone', true);
    $email     = get_post_meta(get_the_ID(), '_team_email', true);
    $project_ids = get_post_meta(get_the_ID(), '_team_projects', true) ?: [];
    $first_name  = explode(' ', get_the_title())[0];

    $about_page = get_page_by_path('over-ons');
    $back_url   = $about_page ? get_permalink($about_page->ID) . '#team' : home_url('/#team');
    ?>
        <section class="section section--team-single scheme-grey">
            <div class="section-container grid--12">

                <div class="team-single__left sticky">
                    <a href="<?php echo esc_url($back_url); ?>" class="button">
                        ← Terug naar team
                    </a>

                    <?php if (has_post_thumbnail()) : ?>
                    <div class="team-single__image-wrap">
                        <?php the_post_thumbnail('large', ['class' => 'team-single__image']); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="team-single__right sticky">
                    <div class="team-single__right__data">
                        <?php if ($job_title) : ?>
                            <p class="team__job-title"><?php echo esc_html($job_title); ?></p>
                        <?php endif; ?>
    
                        <h1><?php the_title(); ?></h1>
                    </div>

                    <?php if (!empty(trim(get_the_content()))) : ?>
                    <div class="team-single__content">
                        <?php the_content(); ?>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </section>

        <?php if (!empty($project_ids)) :
            $team_projects = array_filter(array_map(function($id) {
                $p = get_post((int) $id);
                return ($p && $p->post_status === 'publish') ? $p : null;
            }, $project_ids));
        ?>

        <section class="section section--row section--slider scheme-white">
            <div class="section-container">
                <div class="row__header grid--12">
                    <h1><?php echo esc_html($first_name); ?>'s projecten</h1>
                   
                    <div class="wp-block-buttons">
                        <div class="wp-block-button">
                            <a href="<?php echo esc_url(get_post_type_archive_link('post')); ?>" class="wp-element-button">Alle projecten →</a>
                        </div>

                    </div>
                </div>

                <div class="project-slider__swiper swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($team_projects as $tp) : ?>
                        <div class="swiper-slide">
                            <?php get_template_part('template-parts/project-card', null, ['post_id' => $tp->ID]); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php endif; ?>

<?php elseif (get_post_type() === 'post') : ?>

    <?php
    $post_id        = get_the_ID();
    $second_title   = get_post_meta($post_id, '_project_second_title',    true);
    $bouwjaar       = get_post_meta($post_id, '_project_bouwjaar',        true);
    $oppervlakte    = get_post_meta($post_id, '_project_oppervlakte',     true);
    $samenwerkingen = get_post_meta($post_id, '_project_samenwerkingen',  true);
    $opdrachtgever  = get_post_meta($post_id, '_project_opdrachtgever',   true);
    $locatienaam    = get_post_meta($post_id, '_project_locatienaam',     true);

    $cat_ids  = wp_get_post_categories($post_id, ['fields' => 'ids']);
    $type_str = '';
    foreach ($cat_ids as $cid) {
        $term = get_term($cid, 'category');
        if ($term && $term->parent == 0) { $type_str = $term->name; break; }
    }

    $meta_fields = [
        'Type'           => $type_str,
        'Bouwjaar'       => $bouwjaar,
        'Oppervlakte'    => $oppervlakte,
        'Samenwerkingen' => $samenwerkingen,
        'Opdrachtgever'  => $opdrachtgever,
        'Locatie'        => $locatienaam,
    ];
    $meta_fields = array_filter($meta_fields);
    ?>

        <section class="section section--hero section--hero--project scheme-white">

            <div class="intro-banner_media">
                <?php if (has_post_thumbnail()) : ?>
                    <figure class="project-hero__figure">
                        <?php the_post_thumbnail('full', ['class' => 'project-hero__image']); ?>
                    </figure>
                <?php endif; ?>

                <div class="hero__title-container project-hero__title-container grid--12" id="project-hero-title">
                    <?php if ($second_title) : ?>
                    <h4 class="project-hero__second-title"><?php echo esc_html($second_title); ?></h4>
                    <?php endif; ?>
                    <h1 class="project-hero__title"><?php the_title(); ?></h1>
                </div>

                <div class="hero-arrow">
                    <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/arrow.svg'); ?>
                </div>

              
            </div>

            <div class="hero__sticky-wrap">
                <div class="hero__title-container hero__title-container--sticky project-hero__title-container grid--12">
                    <?php if ($second_title) : ?>
                        <h4 class="project-hero__second-title"><?php echo esc_html($second_title); ?></h4>
                    <?php endif; ?>
                    <h1 class="project-hero__title"><?php the_title(); ?></h1>
                </div>
            </div>

        </section>

        <section class="section scheme-white section--project-intro" id="project-intro">
            <div class="section-container grid--12">

                <div class="project__info">
                    <?php the_content_before_separator(); ?>
                </div>

                <div class="project__data">
                    <?php if (!empty($meta_fields)) : ?>

                        <?php $show_toggle = count($meta_fields) > 2; ?>

                        <dl class="project__meta">
                            <?php foreach (array_slice($meta_fields, 0, 2, true) as $label => $value) : ?>
                            <div class="project__meta-item">
                                <dt><h5><?php echo esc_html($label); ?></h5></dt>
                                <dd><p><?php echo esc_html($value); ?></p></dd>
                            </div>
                            <?php endforeach; ?>
                        </dl>

                        <?php if ($show_toggle) : ?>
                            <div class="project__meta-more">
                                <div class="project__meta-more__inner">
                                    <dl class="project__meta">
                                        <?php foreach (array_slice($meta_fields, 2, null, true) as $label => $value) : ?>
                                        <div class="project__meta-item">
                                            <dt><h5><?php echo esc_html($label); ?></h5></dt>
                                            <dd><p><?php echo esc_html($value); ?></p></dd>
                                        </div>
                                        <?php endforeach; ?>
                                    </dl>
                                </div>
                            </div>
                            <div class="wp-block-button">
                                <a class="wp-block-button__link wp-element-button project__meta-toggle" aria-expanded="false">Lees meer +</a>
                            </div>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>

            </div>
        </section>

        <section class="section scheme-white section--project-content">
            <div class="section-container section--project-content__container">
                <?php the_content_after_separator(); ?>
            </div>
        </section>

        <?php
        // Helper: filter posts to only those with actual content
        $has_content = function($p) {
            return !empty(trim(strip_tags($p->post_content)));
        };

        $tag_ids = wp_get_post_tags($post_id, ['fields' => 'ids']);

        // Fetch related by category + tag (max 6), exclude current
        $related_query = new WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => 6,
            'post__not_in'   => [$post_id],
            'orderby'        => 'rand',
            'tax_query'      => [
                'relation' => 'OR',
                [
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $cat_ids,
                ],
                ...(!empty($tag_ids) ? [[
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $tag_ids,
                ]] : []),
            ],
        ]);
        $slider_posts = array_values(array_filter($related_query->posts, $has_content));
        wp_reset_postdata();

        // Cap at 6
        $slider_posts = array_slice($slider_posts, 0, 6);

        // If fewer than 4, fill up to 4 with featured projects
        if (count($slider_posts) < 4) {
            $needed      = 4 - count($slider_posts);
            $exclude_ids = array_merge([$post_id], array_column($slider_posts, 'ID'));
            $featured    = get_posts([
                'post_type'   => 'post',
                'numberposts' => $needed * 2, // fetch extra to account for content filtering
                'post__not_in'=> $exclude_ids,
                'meta_key'    => '_project_featured',
                'meta_value'  => '1',
                'orderby'     => 'menu_order',
                'order'       => 'ASC',
            ]);
            $featured     = array_values(array_filter($featured, $has_content));
            $slider_posts = array_merge($slider_posts, array_slice($featured, 0, $needed));
        }

        if (!empty($slider_posts)) : ?>
        <section class="section section--row section--slider scheme-grey">
            <div class="section-container">
                <div class="row__header grid--12">
                    <h1>Meer projecten</h1>
                    <div class="wp-block-buttons">
                        <div class="wp-block-button">
                            <a href="<?php echo esc_url(get_post_type_archive_link('post')); ?>" class="wp-element-button">Alle projecten →</a>
                        </div>
                    </div>
                </div>
                <div class="project-slider__swiper swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($slider_posts as $sp) : ?>
                        <div class="swiper-slide">
                            <?php get_template_part('template-parts/project-card', null, ['post_id' => $sp->ID]); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

<?php elseif (get_post_type() === 'vacature') : ?>

    <section class="section section--vacature--single scheme-white">
        <div class="section-container">
            <div class="vacature__top">
                <h4>Vacature</h4>
                <h1><?php the_title(); ?></h1>
            </div>
            <div class="vacature__content">
                <?php the_content(); ?>

                <span class="vacature__cta--single">Interesse? Stuur je portfolio en motivatie naar
                    <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:info@mr-stir.com">info@mr-stir.com</a></div>
                </span>
            </div>
        </div>
    </section>

    <?php
    $vacatures = get_posts([
        'post_type'   => 'vacature',
        'numberposts' => -1,
        'orderby'     => 'menu_order',
        'order'       => 'ASC',
        'post_status' => 'publish',
        'post__not_in' => [get_the_ID()],
    ]);
    if ($vacatures) :
    ?>
    <section class="section section--full section--full--second scheme-grey">
        <div class="section-container">
            <?php
        $contact_page = get_page_by_path('contact');
        if ($contact_page) the_content_after_separator(2, $contact_page->ID);
        ?>

        <div class="vacatures__list">
                <?php foreach ($vacatures as $vacature) : ?>
                <a class="vacature__item grid--12" href="<?php echo esc_url(get_permalink($vacature->ID)); ?>">
                    <h3 class="vacature__title"><?php echo esc_html(get_the_title($vacature->ID)); ?></h3>
                    <span class="vacature__cta">Bekijk vacature →</span>
                    <span class="vacature__cta vacature__cta--mobile">→</span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>


<?php else : ?>

    <section class="section scheme-grey">
        <div class="section-container">
            <?php the_content(); ?>
        </div>
    </section>

<?php endif; ?>



</main>
<?php get_footer(); ?>
