<footer class="footer scheme-red" id="site-footer">
    <div class="footer-container">


        <div class="footer__bottom">
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
