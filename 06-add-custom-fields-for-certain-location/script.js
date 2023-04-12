(function( $ ) {
    'use strict';

    $(document).ready(function(){

        $('.ex6-item-color-settings').wpColorPicker();

        if ( ! $('#locations-ex6_menu').prop('checked') ) {
            $('.ex6-custom-field').hide();
        }

        $('#locations-ex6_menu').on('change', function () {
            if ( $(this).prop('checked') ) {
                $('.ex6-custom-field').show();
            } else {
                $('.ex6-custom-field').hide();
            }
        });

    });

})( jQuery );