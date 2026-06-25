<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="utf-8">
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="SU-F">

    <title><?php echo get_bloginfo( 'name' ); ?></title>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <?php wp_body_open(); ?>

<header class="header">
    <div class="header-container grid--12">
        <?php if ( is_front_page() || is_home() ) : ?>
            <a href="#home" class="header__logo">
                <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
            </a>
        <?php else : ?>
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="header__logo">
                <?php echo file_get_contents(get_template_directory() . '/assets/svg/logo.svg'); ?>
            </a>
        <?php endif; ?>

        <div class="nav-container">
            <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-menu'
                ]);
            ?>
        </div>
    </div>
</header>

    

