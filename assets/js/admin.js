(function($) {
    'use strict';

    var wc_cgmp_admin = {
        init: function() {
            this.bindEvents();
            this.initPricePreviews();
        },

        bindEvents: function() {
            $(document).on('change', '#_wc_cgmp_enabled', this.toggleMarketplace);
            $(document).on('click', '.wc-cgmp-display-header, .wc-cgmp-button-header', this.toggleSection);
            $(document).on('input', '.wc-cgmp-price-input', this.updatePricePreview);
            $(document).on('input', '.wc-cgmp-tier-name-input', this.updateTierHeader);
            $(document).on('input', '.wc-cgmp-url-input', this.validateUrlInput);
        },

        toggleMarketplace: function() {
            var checked = $(this).is(':checked');
            var $section = $('.wc-cgmp-tier-pricing-section');
            var $displaySection = $('.wc-cgmp-display-section');
            var $buttonSection = $('.wc-cgmp-button-section');
            
            if (checked) {
                $section.slideDown(200);
                $displaySection.slideDown(200);
                $buttonSection.slideDown(200);
            } else {
                $section.slideUp(200);
                $displaySection.slideUp(200);
                $buttonSection.slideUp(200);
            }
        },

        toggleSection: function() {
            var $section = $(this).closest('.wc-cgmp-display-section, .wc-cgmp-button-section');
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

        validateUrlInput: function() {
            var url = $(this).val().trim();
            
            if (url && !wc_cgmp_admin.isValidURL(url)) {
                $(this).addClass('invalid-url');
            } else {
                $(this).removeClass('invalid-url');
            }
        },

        isValidURL: function(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        },

        placeholderText: 'Enter prices to preview'
    };

    $(document).ready(function() {
        wc_cgmp_admin.init();
    });

})(jQuery);
