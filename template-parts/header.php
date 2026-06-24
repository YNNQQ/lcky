<header class="header" id="site-header">
    <div class="header-container grid--12">

        <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo" aria-label="MR. STIR — Home">
            <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
        </a>

        <div class="nav-menu grid--6 scheme-dark-grey" id="nav-menu" aria-hidden="true">

            <div class="nav-menu__col">
                <span class="nav-menu__label"><?php esc_html_e('Architect & Planner', 'mrstir'); ?></span>
                <?php wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-menu__list',
                    'fallback_cb'    => false,
                ]); ?>
            </div>

            <div class="nav-menu__col">
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="nav-menu__label"><?php esc_html_e('Projecten', 'mrstir'); ?></a>
                <?php wp_nav_menu([
                    'theme_location' => 'project',
                    'container'      => false,
                    'menu_class'     => 'nav-menu__list',
                    'fallback_cb'    => false,
                ]); ?>
            </div>

            <div class="nav-menu__col nav-menu__col--mobile">
                <?php wp_nav_menu([
                    'theme_location' => 'contact_header',
                    'container'      => false,
                    'menu_class'     => 'nav-menu__list',
                    'fallback_cb'    => false,
                ]); ?>
            </div>

            <div class="nav-menu__col nav-menu__col--mobile">
                <?php wp_nav_menu([
                    'theme_location' => 'location',
                    'container'      => false,
                    'menu_class'     => 'nav-menu__list',
                    'fallback_cb'    => false,
                ]); ?>
            </div>

            <div class="nav-menu__col nav-menu__col--mobile">
                <?php wp_nav_menu([
                    'theme_location' => 'social',
                    'container'      => false,
                    'menu_class'     => 'nav-menu__list',
                    'fallback_cb'    => false,
                ]); ?>
            </div>

        </div>

        <button class="nav-toggle menu-item" aria-expanded="false" aria-controls="nav-menu" aria-label="Open menu">
            <?php echo file_get_contents(get_template_directory() . '/assets/svg/icons/plus.svg'); ?>
        </button>

    </div>
</header>
