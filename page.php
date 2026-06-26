<?php get_header(); ?>

<?php
    $front_page_id  =   get_option('page_on_front');
    $approach_page  =   get_page_by_path('aanpak');
?>

<main class="site-main scheme-red">

    <?php if (is_front_page()): ?>
    <div class="logo-clip">
        <a href="#home" class="header__logo">
            <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
        </a>
    </div>
    <?php endif; ?>

    <?php if (is_front_page()): ?>
            
        <section class="section section--hero section--full" data-header-theme="light">
            <div class="section-container">
                <?php the_content_before_separator(); ?>


                <a href="#home" class="hero__logo">
                    <?php echo file_get_contents(get_template_directory() . '/assets/svg/partner_logos.svg'); ?>
                </a>    
            </div>
        </section>

        <?php echo render_sections(get_the_content(), true); ?>

    <?php endif?>


</main>

<?php get_footer(); ?>