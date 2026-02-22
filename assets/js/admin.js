(function($) {
    'use strict';

    var wc_cgmp_admin = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('change', '#wc_cgm_enable_tier_pricing', this.toggleTierPricing);
            $(document).on('click', '.wc-cgmp-tier-heading', this.toggleTierRow);
        },

        toggleTierPricing: function() {
            var checked = $(this).is(':checked');
            if (checked) {
                $('.wc-cgmp-tiers-wrap').slideDown();
            } else {
                $('.wc-cgmp-tiers-wrap').slideUp();
            }
        },

        toggleTierRow: function() {
            $(this).closest('.wc-cgmp-tier-row').toggleClass('collapsed');
        }
    };

    $(document).ready(function() {
        wc_cgmp_admin.init();
    });

})(jQuery);
