<?php
    /**
     * This example shows how to add custom field for 1st level menu items only
     */
    
    add_action( 'wp_nav_menu_item_custom_fields', 'example_04_custom_menu_item_fields', 5, 5 );
    add_action( 'wp_update_nav_menu_item', 'example_04_save_custom_menu_fields', 5, 2 );
    add_action('in_admin_footer', 'example_04_add_styles_to_hide_fields');

    // display
    add_filter('nav_menu_css_class', 'example_04_maybe_add_item_classes', 10, 4);
    add_action( 'wp_enqueue_scripts', 'example_04_enqueue_frontend_styles' );

    function example_04_custom_menu_item_fields($item_id, $item, $depth, $args, $id)
    {
        if ( !empty($item->rcmit_type) ) {
            return;
        }

        $field_name = 'submenu-settings';
        $field_id_pattern = '%s-%s';

        $styles = [
            'default'  => 'Default',
            '2-cols'   => '2 Columns',
            '3-cols'   => '3 Columns'
        ];

        $item_settings = get_post_meta( $item_id, '_item_submenu_settings', true );

        ?>
        <div class="custom-field-fullw icon description-wide ex4-custom-field display-depth--0">
            <?php
                $field_id = sprintf($field_id_pattern, $field_name, $item_id);
                $style = ( empty($item_settings) ) ? 'default' : $item_settings;
            ?>
            <p class="field-submenu-settings description description-wide">
                <label for="<?php echo esc_attr($field_id); ?>">
                    <?php _e('Show submenu items in'); ?><br>
                    <select id="<?php echo esc_attr($field_id); ?>"
                            name="<?php echo esc_attr("{$field_name}[{$item_id}]"); ?>"
                    >
                        <?php foreach ($styles as $value => $label) { ?>
                            <option value="<?php echo esc_attr($value); ?>"
                                <?php selected($value, $style, 1); ?>
                            >
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>
            </p>
        </div>
        <?php
    }

    function example_04_save_custom_menu_fields( $menu_id, $menu_item_db_id )
    {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return;
        }

        $field_name = 'submenu-settings';

        if ( isset( $_REQUEST['update-nav-menu-nonce'] )
            && wp_verify_nonce( $_REQUEST['update-nav-menu-nonce'], 'update-nav_menu' )
            && isset($_POST[ $field_name ][ $menu_item_db_id ])
        ) {
            update_post_meta($menu_item_db_id, '_item_submenu_settings', $_POST[ $field_name ][ $menu_item_db_id ]);
        }
    }

    function example_04_add_styles_to_hide_fields()
    {
        //you can check if this is the right page
        $screen = get_current_screen();
        if ( 'nav-menus' == $screen->id ) {
            ?>
            <style>
                .ex4-custom-field {
                    display: none;
                }

                .menu-item-depth-0 .display-depth--0 {
                    display: initial;
                }
            </style>
            <?php
        }
    }

    function example_04_maybe_add_item_classes( $classes, $menu_item, $args, $depth )
    {
        if ( 0 == $depth && in_array('menu-item-has-children', $classes) ) {
            $item_settings = get_post_meta( $menu_item->ID, '_item_submenu_settings', true );
            if ( ! empty($item_settings) ) {
                $classes[] = 'item-submenu-style--' . $item_settings;
            }
        }

        return $classes;
    }

    function example_04_enqueue_frontend_styles()
    {
        wp_enqueue_style( 'ex4-frontend', MENU_TESTS_PLUGIN_DIR_URI . '04-add-custom-field-for-1st-level-only/frontend.css', [], null, 'all' );
    }
