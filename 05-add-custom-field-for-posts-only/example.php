<?php
    /**
     * This example shows how to add custom field for post menu items only
     */

    add_action( 'wp_nav_menu_item_custom_fields', 'example_05_custom_menu_item_fields', 5, 5 );
    add_action( 'wp_update_nav_menu_item', 'example_05_save_custom_menu_fields', 5, 2 );
    add_action('in_admin_footer', 'example_05_add_styles_to_hide_fields');

    // display
    add_filter('nav_menu_css_class', 'example_05_maybe_add_item_classes', 10, 4);
    add_filter('nav_menu_item_title', 'example_05_maybe_show_item_pro_label', 10, 4);

    function example_05_custom_menu_item_fields($item_id, $item, $depth, $args, $id)
    {
        if ( !empty($item->rcmit_type) ) {
            return;
        }

        $field_name = 'post-pro-settings';
        $field_id_pattern = '%s-%s';

        $is_item_pro = get_post_meta( $item_id, '_item_pro_settings', true );

        ?>
        <div class="custom-field-fullw icon description-wide ex5-custom-field display-item--post">
            <?php
                $field_id = sprintf($field_id_pattern, $field_name, $item_id);
            ?>
            <p class="field-icon-pro description">
                <label for="<?php echo esc_attr($field_id); ?>">
                    <input type="checkbox"
                           id="<?php echo esc_attr($field_id); ?>"
                           value="1"
                        <?php checked('1', $is_item_pro); ?>
                           name="<?php echo esc_attr("{$field_name}[{$item_id}]"); ?>"
                    >
                    <?php _e('Add PRO label'); ?>
                </label>
            </p>
        </div>
        <?php
    }

    function example_05_save_custom_menu_fields( $menu_id, $menu_item_db_id )
    {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return;
        }

        $field_name = 'post-pro-settings';

        if ( isset( $_REQUEST['update-nav-menu-nonce'] )
            && wp_verify_nonce( $_REQUEST['update-nav-menu-nonce'], 'update-nav_menu' )
        ) {
            if ( isset($_POST[ $field_name ][ $menu_item_db_id ] ) ) {
                update_post_meta($menu_item_db_id, '_item_pro_settings', '1');
            } else {
                update_post_meta($menu_item_db_id, '_item_pro_settings', '0');
            }
        }
    }

    function example_05_add_styles_to_hide_fields()
    {
        //you can check if this is the right page
        $screen = get_current_screen();
        if ( 'nav-menus' == $screen->id ) {
            ?>
            <style>
                .ex5-custom-field {
                    display: none;
                }

                .menu-item-post .display-item--post {
                    display: initial;
                }
            </style>
            <?php
        }
    }

    function example_05_maybe_add_item_classes( $classes, $menu_item, $args, $depth )
    {
        if ( 'post' == $menu_item->object ) {
            $item_settings = get_post_meta( $menu_item->ID, '_item_pro_settings', true );
            if ( ! empty($item_settings) ) {
                $classes[] = 'item-post--is-pro';
            }
        }

        return $classes;
    }

    function example_05_maybe_show_item_pro_label($title, $menu_item, $args, $depth)
    {
        if ( 'post' == $menu_item->object ) {
            $is_item_pro = get_post_meta($menu_item->ID, '_item_pro_settings', true);

            if (!empty($is_item_pro)) {
                $title .= '<span style="color:grey">PRO</span>';
            }
        }

        return $title;
    }
