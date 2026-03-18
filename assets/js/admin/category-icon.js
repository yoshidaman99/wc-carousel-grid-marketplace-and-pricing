(function($) {
    'use strict';

    var WcCgmpCategoryIcon = {
        mediaFrame: null,
        currentWrapper: null,

        init: function() {
            this.bindEvents();
            this.initFromState();
        },

        bindEvents: function() {
            $(document).on('change', 'input[name="wc_cgmp_icon_type"]', this.toggleIconType.bind(this));
            $(document).on('click', '.wc-cgmp-dashicon-btn', this.selectDashicon.bind(this));
            $(document).on('click', '.wc-cgmp-fontawesome-btn', this.selectFontawesome.bind(this));
            $(document).on('click', '.wc-cgmp-fontawesome-category-btn', this.switchFontawesomeCategory.bind(this));
            $(document).on('input', '.wc-cgmp-fontawesome-search-input', this.searchFontawesome.bind(this));
            $(document).on('click', '.wc-cgmp-upload-btn', this.openMediaUploader.bind(this));
            $(document).on('click', '.wc-cgmp-remove-btn', this.removeImage.bind(this));
        },

        initFromState: function() {
            var selectedType = $('input[name="wc_cgmp_icon_type"]:checked').val();
            if (selectedType) {
                this.showIconField(selectedType);
            }

            var selectedDashicon = $('#wc_cgmp_icon_dashicon').val();
            if (selectedDashicon) {
                this.highlightDashicon(selectedDashicon);
                this.updateDashiconPreview(selectedDashicon);
            }

            var selectedFontawesome = $('#wc_cgmp_icon_fontawesome').val();
            if (selectedFontawesome) {
                this.highlightFontawesome(selectedFontawesome);
                this.updateFontawesomePreview(selectedFontawesome);
            }
        },

        toggleIconType: function(e) {
            var type = $(e.target).val();
            this.showIconField(type);
        },

        showIconField: function(type) {
            $('.wc-cgmp-icon-field').hide();
            $('.wc-cgmp-icon-field[data-type="' + type + '"]').show();
        },

        selectDashicon: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var icon = $btn.data('icon');

            $('.wc-cgmp-dashicon-btn').removeClass('selected');
            $btn.addClass('selected');

            $('#wc_cgmp_icon_dashicon').val(icon);
            this.updateDashiconPreview(icon);
        },

        highlightDashicon: function(icon) {
            $('.wc-cgmp-dashicon-btn').removeClass('selected');
            $('.wc-cgmp-dashicon-btn[data-icon="' + icon + '"]').addClass('selected');
        },

        updateDashiconPreview: function(icon) {
            var $preview = $('.wc-cgmp-dashicon-field .wc-cgmp-preview-icon');
            $preview.html('<span class="dashicons dashicons-' + icon + '"></span>');
            $('.wc-cgmp-dashicon-field .wc-cgmp-preview-name').text(icon);
        },

        selectFontawesome: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var icon = $btn.data('icon');
            var label = $btn.data('label');

            $('.wc-cgmp-fontawesome-btn').removeClass('selected');
            $btn.addClass('selected');

            $('#wc_cgmp_icon_fontawesome').val(icon);
            this.updateFontawesomePreview(icon, label);
        },

        highlightFontawesome: function(icon) {
            $('.wc-cgmp-fontawesome-btn').removeClass('selected');
            $('.wc-cgmp-fontawesome-btn[data-icon="' + icon + '"]').addClass('selected');
        },

        updateFontawesomePreview: function(icon, label) {
            var $preview = $('.wc-cgmp-fontawesome-field .wc-cgmp-preview-icon');
            $preview.html('<i class="' + icon + '"></i>');
            $('.wc-cgmp-fontawesome-field .wc-cgmp-preview-name').text(icon);
        },

        switchFontawesomeCategory: function(e) {
            e.preventDefault();
            var $btn = $(e.currentTarget);
            var category = $btn.data('category');

            $('.wc-cgmp-fontawesome-category-btn').removeClass('active');
            $btn.addClass('active');

            if (category === 'all') {
                $('.wc-cgmp-fontawesome-btn').show();
            } else {
                $('.wc-cgmp-fontawesome-btn').hide();
                $('.wc-cgmp-fontawesome-btn[data-category="' + category + '"]').show();
            }

            $('.wc-cgmp-fontawesome-search-input').val('');
        },

        searchFontawesome: function(e) {
            var searchTerm = $(e.currentTarget).val().toLowerCase().trim();
            var $grid = $('.wc-cgmp-fontawesome-grid');
            var $noResults = $('.wc-cgmp-fontawesome-no-results');

            if (searchTerm === '') {
                var activeCategory = $('.wc-cgmp-fontawesome-category-btn.active').data('category') || 'solid';
                if (activeCategory === 'all') {
                    $('.wc-cgmp-fontawesome-btn').show();
                } else {
                    $('.wc-cgmp-fontawesome-btn').hide();
                    $('.wc-cgmp-fontawesome-btn[data-category="' + activeCategory + '"]').show();
                }
                $noResults.hide();
                return;
            }

            var visibleCount = 0;
            $('.wc-cgmp-fontawesome-btn').each(function() {
                var $btn = $(this);
                var label = ($btn.data('label') || '').toLowerCase();
                var icon = ($btn.data('icon') || '').toLowerCase();

                if (label.indexOf(searchTerm) !== -1 || icon.indexOf(searchTerm) !== -1) {
                    $btn.show();
                    visibleCount++;
                } else {
                    $btn.hide();
                }
            });

            if (visibleCount === 0) {
                $noResults.show();
            } else {
                $noResults.hide();
            }
        },

        openMediaUploader: function(e) {
            e.preventDefault();

            var $button = $(e.currentTarget);
            var $wrapper = $button.closest('.wc-cgmp-image-uploader');
            this.currentWrapper = $wrapper;

            if (this.mediaFrame) {
                this.mediaFrame.off('select');
                this.mediaFrame.dispose();
                this.mediaFrame = null;
            }

            this.mediaFrame = wp.media({
                title: wcCgmpCategoryIcon.chooseImage,
                button: {
                    text: wcCgmpCategoryIcon.useImage
                },
                multiple: false
            });

            var self = this;
            this.mediaFrame.on('select', function() {
                var attachment = self.mediaFrame.state().get('selection').first().toJSON();
                var $wrapper = self.currentWrapper;
                var $preview = $wrapper.find('.wc-cgmp-image-preview');
                var $input = $wrapper.find('#wc_cgmp_icon_image_id');
                var $removeBtn = $wrapper.find('.wc-cgmp-remove-btn');
                
                $input.val(attachment.id);
                
                var thumbUrl = attachment.url;
                if (attachment.sizes && attachment.sizes.thumbnail) {
                    thumbUrl = attachment.sizes.thumbnail.url;
                } else if (attachment.sizes && attachment.sizes.medium) {
                    thumbUrl = attachment.sizes.medium.url;
                }

                $preview.html('<img src="' + thumbUrl + '" alt="" />');
                $removeBtn.show();
            });

            this.mediaFrame.open();
        },

        removeImage: function(e) {
            e.preventDefault();

            var $button = $(e.currentTarget);
            var $wrapper = $button.closest('.wc-cgmp-image-uploader');
            var $preview = $wrapper.find('.wc-cgmp-image-preview');
            var $input = $wrapper.find('#wc_cgmp_icon_image_id');

            $input.val('');
            $preview.html('<span class="wc-cgmp-image-placeholder">' + 
                (typeof wcCgmpCategoryIcon !== 'undefined' && wcCgmpCategoryIcon.noImageSelected || 'No image selected') + 
                '</span>');
            $button.hide();
        }
    };

    $(document).ready(function() {
        WcCgmpCategoryIcon.init();
    });

})(jQuery);
