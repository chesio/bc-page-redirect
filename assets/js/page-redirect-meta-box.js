(function($) {
    $(function() {
        var $meta_box = $('#bc-page-redirect-meta-box');
        var $redirect_type_input = $('#bc-page-redirect-type', $meta_box);
        var $redirect_type_data = $('[data-bc-page-redirect-type-data]', $meta_box);

        var refresh = function() {
            $redirect_type_data
                .hide()
                .filter('[data-bc-page-redirect-type-data="' + $redirect_type_input.val() + '"]')
                .show()
            ;
        };

        // Refresh on init...
        refresh();
        // ...and whenever redirect type changes.
        $redirect_type_input.on('change', refresh);
    });
})(jQuery);
