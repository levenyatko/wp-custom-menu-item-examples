<?php
    /**
     *  Add Archives menu item type to the nav menu edit page
     */

    add_action( 'admin_init', 'example_01_add_meta_box' );
    // fix missed url for custom menu item, set current class
    add_filter( 'wp_get_nav_menu_items', 'example_01_archive_menu_filter', 10, 3 );
    // Set custom menu item type label
    add_filter( 'wp_setup_nav_menu_item', 'example_01_archive_menu_item_label' );

    function example_01_add_meta_box() {
        add_meta_box(
            'example_archive_menu_item_type',
            __( 'Archives', 'domain' ),
            'example_01_archive_item_type_meta_box_display',
            'nav-menus',
            'side',
            'high'
        );
    }

    function example_01_archive_item_type_meta_box_display() {

        global $nav_menu_selected_id;

        /**
         * On the navigation menu edit page, the script is sensitive to class names and ids.
         * Let's store our custom menu item type in a variable. It will help us reuse this code
         */
        $item_type = 'cpt-archive';

        $post_types = get_post_types([
            'public'            => true,
            'show_in_nav_menus' => true,
            'has_archive'       => true
        ], 'object');

        if ( empty($post_types) ) {
            return;
        }

        // objects list to use in walker
        $post_types_options = [];

        foreach ( $post_types as $post_type ) {

            $option = [
                'object'     => $item_type,
                'type'       => $post_type->name,
                'object_id'  => $post_type->name,
                'classes'    => [],
                'title'      => $post_type->labels->name . ' ' . __( 'Archive'),
                'url'        => null,
                'menu_item_parent' => null,
                'xfn'        => null,
                'db_id'      => null,
                'target'     => null,
                'attr_title' => null
            ];


            $post_types_options[] = (object)$option;
        }

        $walker = new Walker_Nav_Menu_Checklist( [] );

        $post_types_options = array_map('wp_setup_nav_menu_item', $post_types_options);
        ?>
        <div id="<?php echo esc_attr($item_type); ?>" class="posttypediv">
            <div id="tabs-panel-<?php echo esc_attr($item_type); ?>" class="tabs-panel tabs-panel-active">
                <ul id="ctp-archive-checklist" class="categorychecklist form-no-clear">
                    <?php
                        echo walk_nav_menu_tree( $post_types_options, 0, (object) ['walker' => $walker] );
                    ?>
                </ul>
            </div><!-- /.tabs-panel -->
        </div>
        <p class="button-controls">
            <span class="add-to-menu">
                <input type="submit"
                    <?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?>
                    class="button-secondary submit-add-to-menu right"
                    value="<?php esc_attr_e( 'Add to Menu' ); ?>"
                    name="add-<?php echo esc_attr($item_type); ?>-menu-item"
                    id="submit-<?php echo esc_attr($item_type); ?>"
                />
			    <span class="spinner"></span>
          </span>
        </p>
        <?php
    }

    function example_01_archive_menu_filter( $items, $menu, $args ) {

        foreach ( $items as &$item ) {
            if ( 'cpt-archive' != $item->object ){
                continue;
            }

            $item->url = get_post_type_archive_link( $item->type );

            // set current menu item class
            if ( get_query_var( 'post_type' ) == $item->type ) {
                $item->classes []= 'current-menu-item';
                $item->current = true;
            }

        }

        return $items;
    }

    function example_01_archive_menu_item_label( $menu_item ) {

        if ( 'cpt-archive' !== $menu_item->object ) {
            return $menu_item;
        }

        $menu_item->type_label = __('Archive');

        return $menu_item;
    }