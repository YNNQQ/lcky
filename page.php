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
                <div class="hero__content">
                    <?php
                        wp_nav_menu([
                            'theme_location' => 'hero',
                            'container'      => false,
                            'menu_class'     => 'hero-menu'
                        ]);
                    ?>    
                    <?php the_content_before_separator(); ?>
                </div>


                <div class="hero__container">
                    <div class="hero__container__logos">
                        <a href="https://lsrc.world/" target="_blank" class="hero__logo hero__logo--ls">
                            <?php echo file_get_contents(get_template_directory() . '/assets/svg/LS.svg'); ?>
                        </a>
                        <div class="separator"></div>
                        <a href="https://www.tomorrowland.com/" target="_blank" class="hero__logo hero__logo--tml">
                            <?php echo file_get_contents(get_template_directory() . '/assets/svg/TML.svg'); ?>
                        </a>
                    </div>
                    <h4>
                        Present
                    </h4>        
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
    <div class="popup scheme-white" role="dialog" aria-modal="true">
        <?php echo_page_content($popup_page_id); ?>
    </div>
</div>
<script>
(function () {
    var overlay = document.getElementById('popup-overlay');
    if (!overlay) return;

    function closePopup() {
        overlay.classList.remove('popup-overlay--visible');
        document.body.classList.remove('popup-open');
        overlay.setAttribute('aria-hidden', 'true');
    }

    setTimeout(function () {
        overlay.classList.add('popup-overlay--visible');
        document.body.classList.add('popup-open');
        overlay.removeAttribute('aria-hidden');
    }, 5000);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            closePopup();
            return;
        }

        var trigger = e.target.closest('a[href], button');
        if (!trigger) return;

        var href = trigger.getAttribute('href');
        e.preventDefault();
        closePopup();

        if (!href) return;

        var delay = 300;
        if (trigger.getAttribute('target') === '_blank') {
            setTimeout(function () { window.open(href, '_blank', 'noopener'); }, delay);
        } else {
            setTimeout(function () { window.location.href = href; }, delay);
        }
    });
})();
</script>
<?php endif; ?>

<?php get_footer(); ?>