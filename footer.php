<footer class="footer scheme-dark-green" id="site-footer">
    <div class="footer-container">

        <div class="footer__top grid--12">

            <!-- Contact -->
            <div class="footer__col footer__col--contact">
                <h4 class="footer__label">Kantoor Strijp-S</h4>
                <?php wp_nav_menu([
                    'theme_location' => 'contact_footer',
                    'container'      => false,
                    'menu_class'     => 'footer__menu',
                ]); ?>
            </div>

            <!-- Sitemap / secondary -->
            <div class="footer__col footer__col--sitemap">
                <h4 class="footer__label">Sitemap</h4>
                <?php wp_nav_menu([
                    'theme_location' => 'secondary',
                    'container'      => false,
                    'menu_class'     => 'footer__menu',
                ]); ?>
            </div>

            <!-- Newsletter + Socials -->
            <div class="footer__col footer__col--right">

                <div class="footer__newsletter footer__newsletter--desktop">
                    <h4 class="footer__label">Nieuwsbrief</h4>
                    <!-- paste newsletter embed here -->
                </div>

                <div class="footer__socials">
                    <h4 class="footer__label">Socials</h4>
                    <?php wp_nav_menu([
                        'theme_location' => 'social',
                        'container'      => false,
                        'menu_class'     => 'footer__menu',
                    ]); ?>
                </div>
            </div>

            <div class="footer__col footer__col--newsletter">

                <div class="footer__newsletter footer__newsletter--mobile">
                    <h4 class="footer__label">Nieuwsbrief</h4>
                    <!-- paste newsletter embed here -->
                </div>
            </div>


        </div>

        <!-- Wordmark -->
        <div class="wordmark wordmark--footer" aria-hidden="true">
            <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
        </div>

        <!-- Bottom bar -->
        <div class="footer__bottom grid--12">
            <p class="footer__copy">© <?php echo date('Y'); ?> MR. STIR</p>
            <?php wp_nav_menu([
                'theme_location' => 'legal',
                'container'      => false,
                'menu_class'     => 'footer__menu footer__menu--legal',
            ]); ?>
            <p><a href="https://suf.studio/" target="_blank" rel="noopener">Website door SU—F</a></p>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
