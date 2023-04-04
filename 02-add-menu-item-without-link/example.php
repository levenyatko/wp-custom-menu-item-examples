<?php
    /**
     *  Add menu item type without link to use as title for nested menu levels
     */

    add_action( 'admin_init', 'example_02_add_meta_box' );
    add_action( 'admin_enqueue_scripts', 'example_02_enqueue_admin_menu_script' );
    // Set custom menu item type label
    add_filter( 'wp_setup_nav_menu_item', 'example_02_nolink_menu_item_label' );
    add_action( 'wp_ajax_add_nolink_menu_item', 'example_02_ajax_add_menu_item' );
    // replacing the tag for a new menu item. Walker can be used for this purpose
    add_filter( 'walker_nav_menu_start_el', 'example_02_replace_item_tag', 10, 4 );

    function example_02_add_meta_box() {
        add_meta_box(
            'example_empty_menu_item_type',
            __( 'Without link', 'domain' ),
            'example_02_nolink_item_type_meta_box_display',
            'nav-menus',
            'side',
            'high'
        );
    }

    function example_02_nolink_item_type_meta_box_display() {

        global $_nav_menu_placeholder, $nav_menu_selected_id;
        $_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : -1;

        /**
         * On the navigation menu edit page, the script is sensitive to class names and ids.
         * Let's store our custom menu item type in a variable. It will help us reuse this code
         */
        $item_type = 'without-link';
        ?>
        <div class="customlinkdiv" id="<?php echo esc_attr($item_type); ?>">
            <input type="hidden" value="without-link" name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-type]" />

            <p id="menu-item-name-wrap" class="wp-clearfix">
                <label class="howto" for="<?php echo esc_attr($item_type) ?>-menu-item-name"><?php _e( 'Text' ); ?></label>
                <input id="<?php echo esc_attr($item_type) ?>-menu-item-name"
                       name="menu-item[<?php echo $_nav_menu_placeholder; ?>][menu-item-title]"
                       type="text"
                       <?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?>
                       class="regular-text menu-item-textbox form-required"
                />
            </p>

            <p class="button-controls  wp-clearfix">
                <span class="add-to-menu">
                    <input type="submit"
                        <?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?>
                        class="button-secondary custom-submit-add-to-menu right"
                           value="<?php esc_attr_e( 'Add to Menu' ); ?>"
                           name="add-<?php echo esc_attr($item_type); ?>-menu-item"
                           id="submit-<?php echo esc_attr($item_type); ?>"
                    />
                    <span class="spinner"></span>
                </span>
            </p>

        </div><!-- /.customlinkdiv -->
        <?php
    }

    function example_02_enqueue_admin_menu_script($hook) {
        if ( 'nav-menus.php' != $hook ) {
            return;
        }
        wp_enqueue_script( 'example-menu-item-without-link', MENU_TESTS_PLUGIN_DIR_URI . '02-add-menu-item-without-link/script.js', ['jquery', 'nav-menu'], null );
    }

    function example_02_nolink_menu_item_label( $menu_item ) {

        if ( 'without-link' !== $menu_item->object ) {
            return $menu_item;
        }

        $menu_item->type_label = __('Without link');

        return $menu_item;
    }

    /**
     * Ajax handler for adding a menu item.
     * see wp_ajax_add_menu_item()
     */
    function example_02_ajax_add_menu_item() {

        check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_die( -1 );
        }

        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

        // For performance reasons, we omit some object properties from the checklist.
        // The following is a hacky way to restore them when adding non-custom items.
        $menu_items_data = array();

        foreach ( (array) $_POST['menu-item'] as $menu_item_data ) {
            $menu_items_data[] = $menu_item_data;
        }

        $item_ids = wp_save_nav_menu_items( 0, $menu_items_data );

        if ( is_wp_error( $item_ids ) ) {
            wp_die( 0 );
        }

        $menu_items = array();

        foreach ( (array) $item_ids as $menu_item_id ) {
            $menu_obj = get_post( $menu_item_id );

            if ( ! empty( $menu_obj->ID ) ) {
                $menu_obj        = wp_setup_nav_menu_item( $menu_obj );
                $menu_obj->title = empty( $menu_obj->title ) ? __( 'Menu Item' ) : $menu_obj->title;
                $menu_obj->label = $menu_obj->title; // Don't show "(pending)" in ajax-added items.
                $menu_items[]    = $menu_obj;
            }
        }

        /** This filter is documented in wp-admin/includes/nav-menu.php */
        $walker_class_name = apply_filters( 'wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $_POST['menu'] );

        if ( ! class_exists( $walker_class_name ) ) {
            wp_die( 0 );
        }

        if ( ! empty( $menu_items ) ) {
            $args = array(
                'after'       => '',
                'before'      => '',
                'link_after'  => '',
                'link_before' => '',
                'walker'      => new $walker_class_name,
            );

            echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
        }

        wp_die();
    }

    function example_02_replace_item_tag( $item_output, $item, $depth, $args ) {

        if ( 'without-link' == $item->type ) {
            $item_output = str_replace(['<a', '/a>'], ['<span', '/span>'], $item_output);
        }

        return $item_output;
    }