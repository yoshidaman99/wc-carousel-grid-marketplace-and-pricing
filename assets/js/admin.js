(function($) {
    'use strict';

    var wc_cgmp_admin = {
        init: function() {
            this.bindEvents();
            this.initPricePreviews();
        },

        bindEvents: function() {
            $(document).on('change', '#_wc_cgmp_enabled', this.toggleMarketplace);
            $(document).on('click', '.wc-cgmp-display-header', this.toggleDisplaySection);
            $(document).on('input', '.wc-cgmp-price-input', this.updatePricePreview);
            $(document).on('input', '.wc-cgmp-tier-name-input', this.updateTierHeader);
        },

        toggleMarketplace: function() {
            var checked = $(this).is(':checked');
            var $section = $('.wc-cgmp-tier-pricing-section');
            var $displaySection = $('.wc-cgmp-display-section');
            
            if (checked) {
                $section.slideDown(200);
                $displaySection.slideDown(200);
            } else {
                $section.slideUp(200);
                $displaySection.slideUp(200);
            }
        },

        toggleDisplaySection: function() {
            var $section = $(this).closest('.wc-cgmp-display-section');
            $section.toggleClass('collapsed');
        },

        initPricePreviews: function() {
            var self = this;
            $('.wc-cgmp-tier-card').each(function() {
                self.updatePreviewForTier($(this));
            });
        },

        updatePricePreview: function() {
            var $card = $(this).closest('.wc-cgmp-tier-card');
            wc_cgmp_admin.updatePreviewForTier($card);
        },

        updatePreviewForTier: function($card) {
            var tier = $card.data('tier');
            var $monthlyInput = $card.find('.wc-cgmp-price-input[data-type="monthly"]');
            var $hourlyInput = $card.find('.wc-cgmp-price-input[data-type="hourly"]');
            var $preview = $card.find('.wc-cgmp-preview-prices');
            
            var monthly = parseFloat($monthlyInput.val()) || 0;
            var hourly = parseFloat($hourlyInput.val()) || 0;
            
            var previewHtml = '';
            
            if (monthly > 0) {
                previewHtml += '<span class="wc-cgmp-preview-price monthly-preview">$' + 
                    wc_cgmp_admin.formatPrice(monthly) + '<span class="period">/mo</span></span>';
            }
            
            if (hourly > 0) {
                previewHtml += '<span class="wc-cgmp-preview-price hourly-preview">$' + 
                    wc_cgmp_admin.formatPrice(hourly) + '<span class="period">/hr</span></span>';
            }
            
            if (!previewHtml) {
                previewHtml = '<span class="wc-cgmp-preview-price" style="color: #999; font-weight: 400;">' +
                    wc_cgmp_admin.placeholderText + '</span>';
            }
            
            $preview.html(previewHtml);
        },

        formatPrice: function(price) {
            return price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        updateTierHeader: function() {
            var $card = $(this).closest('.wc-cgmp-tier-card');
            var $header = $card.find('.wc-cgmp-tier-name');
            var value = $(this).val().trim();
            var placeholder = $(this).attr('placeholder');
            
            $header.text(value || placeholder);
        },

        placeholderText: 'Enter prices to preview'
    };

    $(document).ready(function() {
        wc_cgmp_admin.init();
    });

})(jQuery);
