(function($) {
    $(function() {
        var $meta_box = $('#bc-page-redirect-meta-box');
        var $redirect_type_input = $('#bc-page-redirect-type', $meta_box);
        var $redirect_value_inputs = $('[data-bc-redirect-value-for-type]', $meta_box);

        var refresh = function() {
            // Disable all value inputs and hide parent boxes.
            $redirect_value_inputs.prop('disabled', true)
                .closest('.js-bc-redirect-value').hide();
            // Re-enable only the input related to current redirect type (if any) and show parent box.
            $redirect_value_inputs.filter('[data-bc-redirect-value-for-type="' + $redirect_type_input.val() + '"]').prop('disabled', false)
                .closest('.js-bc-redirect-value').show();
        };

        // Refresh on init...
        refresh();
        // ...and whenever redirect type changes.
        $redirect_type_input.on('change', refresh);
    });
})(jQuery);
