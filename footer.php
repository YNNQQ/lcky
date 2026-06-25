<footer class="footer scheme-red" id="site-footer">
    <div class="footer-container">


        <div class="footer__bottom">
            <p class="footer__copy">© <?php echo date('Y'); ?> ZONE5</p>
            <?php wp_nav_menu([
                'theme_location' => 'legal',
                'container'      => false,
                'menu_class'     => 'footer__menu footer__menu--legal',
            ]); ?>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
