<?php
    /**
     * Plugin Name: WP custom menu item examples
     * Description: There are few examples how we can add custom menu item types to the navigation menu
     */

    defined( 'ABSPATH' ) || exit;

    if ( ! defined('MENU_TESTS_PLUGIN_DIR') ) {
        define( 'MENU_TESTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    }
    
    require_once MENU_TESTS_PLUGIN_DIR . '/01-add-archive-menu-item-type/example.php';
