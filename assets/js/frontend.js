(function($) {
    'use strict';

    var WC_CGM_Frontend = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('change', '.wc-cgmp-tier-selector input', this.updateTierSelection);
            $(document).on('change', '.wc-cgmp-price-type-toggle input', this.updatePriceType);
        },

        updateTierSelection: function() {
            var $selector = $(this).closest('.wc-cgmp-tier-selector');
            var $option = $(this).closest('.wc-cgmp-tier-option');
            var tierName = $(this).data('tier-name');
            var hourlyPrice = parseFloat($(this).data('hourly-price')) || 0;
            var monthlyPrice = parseFloat($(this).data('monthly-price')) || 0;
            var priceType = $selector.find('.wc-cgmp-price-type-toggle input:checked').val() || 'hourly';

            $selector.find('.wc-cgmp-tier-option').removeClass('selected');
            $option.addClass('selected');

            var price = priceType === 'monthly' ? monthlyPrice : hourlyPrice;

            $('form.cart').find('button[type="submit"]').data('tier-price', price);
        },

        updatePriceType: function() {
            var $selector = $(this).closest('.wc-cgmp-tier-selector');
            var $checked = $selector.find('.wc-cgmp-tier-option input:checked');

            if ($checked.length) {
                $checked.trigger('change');
            }

            var priceType = $(this).val();
            $selector.find('.wc-cgmp-tier-price').each(function() {
                if (priceType === 'monthly' && $(this).hasClass('wc-cgmp-hourly')) {
                    $(this).hide();
                } else if (priceType === 'hourly' && $(this).hasClass('wc-cgmp-monthly')) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        }
    };

    $(document).ready(function() {
        WC_CGM_Frontend.init();
    });

})(jQuery);
