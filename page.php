<?php get_header(); ?>

<?php
    $front_page_id  =   get_option('page_on_front');
?>

<main class="site-main scheme-red">

    <?php if ( is_front_page() || is_home() ) : ?>
        <a href="#home" class="header__logo">
    <?php else : ?>
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="header__logo">
    <?php endif; ?>
        <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
    </a>

    <?php if (is_front_page()): ?>
            
        <section class="section section--hero section--full" data-header-theme="light">
            <div class="section-container">
                <?php the_content_before_separator(); ?>


                <div class="hero__container">
                    <!-- <a href="#home" class="hero__logo">
                        <?php echo file_get_contents(get_template_directory() . '/assets/svg/partner_logos.svg'); ?>
                    </a>     -->
                    <a href="#home" class="hero__logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/svg/partner_logos.png" alt="Partner logos">
                    </a>
                    <h4>Present</h4>
                </div>


            </div>
        </section>

        <?php echo render_sections(get_the_content(), true); ?>

        <section class="section section--footer scheme-red"></section>
    <?php endif?>


</main>

<?php
$popup_page_id = get_page_id_by_slug('pop-up');
if ($popup_page_id): ?>
<div class="popup-overlay" id="popup-overlay" aria-hidden="true">
    <div class="popup scheme-black" role="dialog" aria-modal="true">
        <div class="popup__content">
            <?php echo_page_content($popup_page_id); ?>
        </div>
    </div>
</div>
<script>
(function () {
    var overlay = document.getElementById('popup-overlay');
    if (!overlay) return;
    setTimeout(function () {
        overlay.classList.add('popup-overlay--visible');
        document.body.classList.add('popup-open');
        overlay.removeAttribute('aria-hidden');
    }, 5000);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            overlay.classList.remove('popup-overlay--visible');
            document.body.classList.remove('popup-open');
            overlay.setAttribute('aria-hidden', 'true');
        }
    });
})();
</script>
<?php endif; ?>

<?php get_footer(); ?>