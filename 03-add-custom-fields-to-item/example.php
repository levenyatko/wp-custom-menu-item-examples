<?php
    /**
     *  Example shows how to add custom fields to menu items.
     *  In this example we use custom fields to add an icon for a menu item.
     *
     *  Brief description for added fields:
     *  - Icon display - Checkbox - if checked we should display other fields
     *  - Icon class - Text input
     *  - Icon position - Radio buttons - show icon before or after link text
     *  - Icon Style - Select - color schema for displayed icon
     */

    add_action( 'wp_nav_menu_item_custom_fields', 'example_03_custom_menu_item_fields', 10, 5 );
    add_action( 'wp_update_nav_menu_item', 'example_03_save_custom_menu_fields', 10, 2 );
    // display
    add_filter('nav_menu_item_title', 'example_03_maybe_show_item_icon', 10, 4);

    function example_03_custom_menu_item_fields($item_id, $item, $depth, $args, $id)
    {
        if ( !empty($item->rcmit_type) ) {
            return;
        }

        $field_name = 'menu-item-icon-settings';
        $field_id_pattern = '%s-%s-%s';

        $checkbox_field_name = 'display';
        $text_field_name = 'class';
        $radio_field_name = 'position';
        $select_field_name = 'style';

        $positions = [
            [
                'value' => 'before',
                'label' => 'Before'
            ],
            [
                'value' => 'after',
                'label' => 'After'
            ]
        ];

        $styles = [
            'default'  => 'Default',
            'light'    => 'Light',
            'dark'     => 'Dark',
            'vivid'    => 'Vivid',
        ];

        $item_settings = get_post_meta( $item_id, '_menu_icon_settings', true );

        ?>
        <div class="custom-field-fullw icon description-wide">
            <p><strong><?php esc_html_e('Icon Settings'); ?></strong></p>
            <hr>
            <!-- Checkbox -->
            <?php
                $field_id = sprintf($field_id_pattern, $field_name, $checkbox_field_name, $item_id);
                $show_icon = ( empty($item_settings[ $checkbox_field_name ]) ) ? '0' : $item_settings[ $checkbox_field_name ];
            ?>
            <p class="field-icon-<?php echo $checkbox_field_name; ?> description">
                <label for="<?php echo esc_attr($field_id); ?>">
                    <input type="checkbox"
                           id="<?php echo esc_attr($field_id); ?>"
                           value="1"
                           <?php checked('1', $show_icon); ?>
                           name="<?php echo esc_attr("{$field_name}[{$item_id}][{$checkbox_field_name}]"); ?>"
                    >
                    <?php _e('Display icon'); ?>
                </label>
            </p>
            <!-- Checkbox END -->
            <!-- Text -->
            <?php
                $field_id = sprintf($field_id_pattern, $field_name, $text_field_name, $item_id);
                $text_value = '';
                if ( ! empty($item_settings[ $text_field_name ]) ) {
                    $text_value = $item_settings[ $text_field_name ];
                }
            ?>
            <p class="field-icon-<?php echo $text_field_name; ?> description description-wide">
                <label for="<?php echo esc_attr($field_id); ?>">
                    <?php _e('Icon class'); ?><br>
                    <input type="text"
                           id="<?php echo esc_attr($field_id); ?>"
                           class="widefat"
                           name="<?php echo esc_attr("{$field_name}[{$item_id}][{$text_field_name}]"); ?>"
                           value="<?php echo esc_attr( $text_value ); ?>"
                    >
                </label>
            </p>
            <!-- Text END -->
            <!-- Radio -->
            <?php
                $field_id = sprintf($field_id_pattern, $field_name, $radio_field_name, $item_id);
                $position = ( empty($item_settings[ $radio_field_name ]) ) ? 'before' : $item_settings[ $radio_field_name ];
            ?>
            <p class="field-icon-<?php echo $radio_field_name; ?> description">
                <?php _e('Position'); ?><br>
                <?php foreach ($positions as $i => $pos) { ?>
                    <label for="<?php echo esc_attr($field_id . '-' . $i); ?>">
                        <input type="radio"
                               id="<?php echo esc_attr($field_id . '-' . $i); ?>"
                               value="<?php echo esc_attr($pos['value']) ?>"
                               <?php checked($pos['value'], $position); ?>
                               name="<?php echo esc_attr("{$field_name}[{$item_id}][{$radio_field_name}]"); ?>"
                        >
                        <?php echo esc_html($pos['label']) ?>
                    </label>
                <?php } ?>
            </p>
            <!-- Radio END -->
            <!-- Select -->
            <?php
                $field_id = sprintf($field_id_pattern, $field_name, $select_field_name, $item_id);
                $style = ( empty($item_settings[ $select_field_name ]) ) ? 'default' : $item_settings[ $select_field_name ];
            ?>
            <p class="field-icon-<?php echo $select_field_name; ?> description description-wide">
                <label for="<?php echo esc_attr($field_id); ?>">
                    <?php _e('Icon style'); ?><br>
                    <select id="<?php echo esc_attr($field_id); ?>"
                            name="<?php echo esc_attr("{$field_name}[{$item_id}][{$select_field_name}]"); ?>"
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
            <!-- Select END -->
        </div>
        <?php
    }

    function example_03_save_custom_menu_fields( $menu_id, $menu_item_db_id )
    {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            return;
        }

        $field_name = 'menu-item-icon-settings';

        if ( isset( $_REQUEST['update-nav-menu-nonce'] )
             && wp_verify_nonce( $_REQUEST['update-nav-menu-nonce'], 'update-nav_menu' )
             && isset($_POST[ $field_name ][ $menu_item_db_id ])
        ) {
            update_post_meta($menu_item_db_id, '_menu_icon_settings', $_POST[ $field_name ][ $menu_item_db_id ]);
        }
    }

    function example_03_maybe_show_item_icon($title, $menu_item, $args, $depth)
    {
        $item_settings = get_post_meta( $menu_item->ID, '_menu_icon_settings', true );

        if ( ! empty($item_settings['display']) ) {

            $icon_item_class = '';

            if ( ! empty($item_settings['class']) ) {
                $icon_item_class = $item_settings['class'];
            }

            if ( ! empty($item_settings['style']) ) {
                $icon_item_class .= ' item-style--' . $item_settings['style'];
            }

            $icon_item = '<i class="' . $icon_item_class . '"></i>';

            if ( ! empty($item_settings['position']) && 'after' == $item_settings['position'] ) {
                $title .= $icon_item;
            } else {
                $title = $icon_item . $title;
            }
        }

        return $title;
    }
