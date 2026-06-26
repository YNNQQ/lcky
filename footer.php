<footer class="footer scheme-red" id="site-footer">

    <?php if (is_front_page()): ?>
    <div class="logo-clip">
        <a href="#home" class="header__logo">
            <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
        </a>
    </div>
    <?php endif; ?>

    <div class="footer-container">

        <?php wp_nav_menu([
            'theme_location' => 'tagline',
            'container'      => false,
            'menu_class'     => 'footer__tagline',
        ]); ?>

        <div class="footer__bottom grid--3">
            <p class="footer__copy">© <?php echo date('Y'); ?> ZONE5</p>
            <?php wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'items_wrap'     => '%3$s',
                'walker'         => new Footer_Menu_Walker(),
            ]); ?>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
