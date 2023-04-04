(function( $ ) {
    'use strict';

    let withoutLinkMenuItem;

    withoutLinkMenuItem = {
        api : undefined,
        itemWrapper: undefined,
        itemTitleField: undefined,
        itemSpinner: undefined,
        errorClass: 'form-invalid',
        activeClass: 'is-active',
        init: function () {
            withoutLinkMenuItem.api = window.wpNavMenu;
            withoutLinkMenuItem.itemWrapper = $('#without-link');
            withoutLinkMenuItem.itemTitleField = $('#without-link-menu-item-name');
            withoutLinkMenuItem.itemSpinner = $( '#without-link .spinner' );
        },
        addMenuItem: function () {

            let label = this.itemTitleField.val();

            if ( label ) {
                label = label.trim();
            }

            if ( '' === label) {
                this.itemWrapper.addClass( this.errorClass );
                return false;
            }

            // Show the Ajax spinner.
            this.itemSpinner.addClass( this.activeClass );

            this.addItemToMenu( {
                '-1': {
                    'menu-item-type': 'without-link',
                    'menu-item-object-id': 'without-link',
                    'menu-item-db-id': 0,
                    'menu-item-object': 'without-link',
                    'menu-item-url': '#',
                    'menu-item-title': label
                }
            }, function() {
                // Remove the Ajax spinner.
                withoutLinkMenuItem.itemSpinner.removeClass( 'is-active' );
                // Set custom link form back to defaults.
                withoutLinkMenuItem.itemTitleField.val('').trigger( 'blur' );
            });
        },
        registerChange : function() {
            this.api.menusChanged = true;
        },
        addItemToMenu: function(menuItem, callback) {
            var menu = $('#menu').val(),
                nonce = $('#menu-settings-column-nonce').val(),
                params;

            callback = callback || function(){};

            params = {
                'action': 'add_nolink_menu_item',
                'menu': menu,
                'menu-settings-column-nonce': nonce,
                'menu-item': menuItem
            };

            $.post( ajaxurl, params, function(menuMarkup) {
                var ins = $('#menu-instructions');

                menuMarkup = menuMarkup || '';
                menuMarkup = menuMarkup.toString().trim(); // Trim leading whitespaces.

                withoutLinkMenuItem.addMenuItemToBottom(menuMarkup, params);

                // Make it stand out a bit more visually, by adding a fadeIn.
                $( 'li.pending' ).hide().fadeIn('slow');
                $( '.drag-instructions' ).show();

                if( ! ins.hasClass( 'menu-instructions-inactive' ) && ins.siblings().length )
                    ins.addClass( 'menu-instructions-inactive' );

                callback();
            });
        },
        addMenuItemToBottom : function( menuMarkup ) {
            var $menuMarkup = $( menuMarkup );
            $menuMarkup.hideAdvancedMenuItemFields().appendTo( this.api.targetList );
            this.api.refreshKeyboardAccessibility();
            this.api.refreshAdvancedAccessibility();
            wp.a11y.speak( menus.itemAdded );
            $( document ).trigger( 'menu-item-added', [ $menuMarkup ] );
        },
    };

    $(document).ready(function(){

        withoutLinkMenuItem.init();

        $('#menu-settings-column').on('click', function(e) {
            let target = $(e.target);

            if ( target.hasClass('custom-submit-add-to-menu') ) {
                withoutLinkMenuItem.registerChange();
                withoutLinkMenuItem.addMenuItem();
            }
        });

        $('#without-link input[type="text"]').on( 'keypress', function(e){
            $('#without-link').removeClass( withoutLinkMenuItem.errorClass );

            if ( e.keyCode === 13 ) {
                e.preventDefault();
                $( '#submit-without-link' ).trigger( 'click' );
            }
        });

    });

})( jQuery );