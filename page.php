<?php get_header(); ?>

<?php
    $front_page_id  =   get_option('page_on_front');
    $approach_page  =   get_page_by_path('aanpak');
?>

<main class="site-main scheme-red">

    <?php if (is_front_page()): ?>
            
        <section class="section section--hero section--full" data-header-theme="light">
            <div class="section-container">
                <?php the_content_before_separator(); ?>
            </div>
        </section>

        <section class="section section--intro scheme-red" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(1); ?>
            </div>
        </section>

        <section class="section section--event scheme-grey" data-header-theme="dark">
            <div class="section-container">
                <?php the_content_after_separator(2); ?>
            </div>
        </section>

        <section class="section section--form scheme-white" data-header-theme="dark">
            <div class="section-container">
                <?php the_content_after_separator(3); ?>
            </div>
        </section>


        <section class="section section--video" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(4); ?>
            </div>
        </section>

        <section class="section section--faq" data-header-theme="light">
            <div class="section-container">
                <?php the_content_after_separator(5); ?>
            </div>
        </section>

    <?php endif?>


</main>

<?php get_footer(); ?>