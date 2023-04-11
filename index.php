<?php
    /**
     * Plugin Name: WP menu advanced examples
     * Description: There are few examples how to can add custom menu item types or custom fields to a navigation menu
     * Author: Daria Levchenko
     * Author URI: https://github.com/levenyatko
     * Version: 0.0.1
     */

    defined( 'ABSPATH' ) || exit;

    if ( ! defined('MENU_TESTS_PLUGIN_DIR') ) {
        define( 'MENU_TESTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    }

    if ( ! defined('MENU_TESTS_PLUGIN_DIR_URI') ) {
        define( 'MENU_TESTS_PLUGIN_DIR_URI', plugin_dir_url( __FILE__ ) );
    }

    require_once MENU_TESTS_PLUGIN_DIR . '/01-add-archive-menu-item-type/example.php';
    require_once MENU_TESTS_PLUGIN_DIR . '/02-add-menu-item-without-link/example.php';
    require_once MENU_TESTS_PLUGIN_DIR . '/03-add-custom-fields-to-item/example.php';
    require_once MENU_TESTS_PLUGIN_DIR . '/04-add-custom-field-for-1st-level-only/example.php';
