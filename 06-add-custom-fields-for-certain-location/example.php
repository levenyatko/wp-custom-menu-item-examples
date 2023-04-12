<?php

    add_action('after_setup_theme', 'example_06_register_nav_menus' );
    add_action('wp_nav_menu_item_custom_fields', 'example_06_custom_menu_item_fields', 7, 5 );
    add_action('wp_update_nav_menu_item', 'example_06_save_custom_menu_fields', 7, 2 );
    add_action( 'admin_enqueue_scripts', 'example_06_enqueue_admin_menu_script' );
    add_filter( 'nav_menu_link_attributes', 'example_06_maybe_add_menu_link_color', 10, 3 );

    function example_06_register_nav_menus()
    {
        register_nav_menus( ['ex6_menu' => __( 'Example 06 Navigation') ] );
    }

    function example_06_custom_menu_item_fields($item_id, $item, $depth, $args, $id)
    {
        if ( !empty($item->rcmit_type) ) {
            return;
        }

        $field_name = 'item-color-settings';
        $field_id_pattern = '%s-%s';

        $item_color = get_post_meta( $item_id, '_item_color_settings', true );
        if ( empty($item_color) ) {
            $item_color = '#000000';
        }
        ?>
        <div class="custom-field-fullw icon description-wide ex6-custom-field">
            <?php
                $field_id = sprintf($field_id_pattern, $field_name, $item_id);
            ?>
            <p class="field-icon-pro description">
                <?php _e('Item color'); ?><br>
                <label for="<?php echo esc_attr($field_id); ?>">
                    <input type="text"
                           class="ex6-item-color-settings"
                           id="<?php echo esc_attr($field_id); ?>"
                           value="<?php echo esc_attr($item_color); ?>"
                           name="<?php echo esc_attr("{$field_name}[{$item_id}]"); ?>"
                    >
                </label>
            </p>
        </div>
        <?php
    }

    function example_06_save_custom_menu_fields( $menu_id, $menu_item_db_id )
    {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return;
        }

        $field_name = 'item-color-settings';

        if ( isset( $_REQUEST['update-nav-menu-nonce'] )
            && wp_verify_nonce( $_REQUEST['update-nav-menu-nonce'], 'update-nav_menu' )
            && isset($_POST[ $field_name ][ $menu_item_db_id ] )
        ) {
            update_post_meta($menu_item_db_id, '_item_color_settings', $_POST[ $field_name ][ $menu_item_db_id ]);
        }
    }

    function example_06_enqueue_admin_menu_script($hook)
    {
        if ( 'nav-menus.php' != $hook ) {
            return;
        }

        // Add the color picker css file
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_script( 'ex6-script', MENU_TESTS_PLUGIN_DIR_URI . '06-add-custom-fields-for-certain-location/script.js', ['jquery', 'nav-menu', 'wp-color-picker'], null );
    }

    function example_06_maybe_add_menu_link_color( $atts, $item, $args )
    {
        if ( 'ex6_menu' == $args->theme_location ) {
            $item_color = get_post_meta( $item->ID, '_item_color_settings', true );
            if ( ! empty($item_color) ) {
                $atts['style'] = 'color:' . $item_color;
            }
        }

        return $atts;
    }
