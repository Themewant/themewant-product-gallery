(function($) {
    'use strict';

    const EASYQUICKVIEW = {

        init: function () {
            this.eapgyPluginSettings();
            this.eapgyProduct360ViewGallery();
            this.toggleOffsetFieldsInit();
            this.sliderInit();
            this.subtabSidebarInit();
            this.innerTabInit();
            this.deviceTabInit();
            this.paginationSettingsInit();
            this.bulletOffsetSlidersInit();
            this.assignProductsModalInit();
            this.removeAllImagesInit();
            this._removeSingleImageHandler();
            this.repositionVariationGalleryFields();
            this.resetSettingsInit();
            this.toastInit();
            this.toggleLightboxSettingsInit();


            // delegated events (SAFE)
            $(document)
                .off('click.EASYQUICKVIEW', '.twpgy-woglry-upload-btn')
                .on('click.EASYQUICKVIEW', '.twpgy-woglry-upload-btn', this.eapgyAddGallery)

                .on('click.EASYQUICKVIEW', '.twpgy-easy-upload-more-image', function () {
                    EASYQUICKVIEW.eapgyAddMoreGallery.call(this);
                })

                .on('click.EASYQUICKVIEW', '.twpgy-easy-edit-gallery', function () {
                    EASYQUICKVIEW.eapgyEditGalleryByItem.call(this);
                })

                .on('click.EASYQUICKVIEW', '.twpgy-easy-edit', function () {
                    EASYQUICKVIEW.eapgyEditGalleryByEditBtn.call(this);
                });

                $(document).on('woocommerce_variations_loaded', '.woocommerce_variation', function() {
                    EASYQUICKVIEW.repositionVariationGalleryFields.call(this);
                });

                
        },

        eapgyPluginSettings: function () {

            const $tabsWrapper = $('.twpgy-woo_gallery-settings-tabs');
            const $tabs        = $tabsWrapper.find('.twpgy-tab-content');
            const $navItems    = $tabsWrapper.find('#twpgy-tabs-nav > li');
            const $currentTabInput = $tabsWrapper.find('input[name="twpgy_current_tab"]');

            let currentTab = $currentTabInput.val() || 'tab1';

            $tabs.hide();
            $('#' + currentTab).show();

            $navItems.removeClass('active');
            $navItems.find('> a[href="#' + currentTab + '"]').parent().addClass('active');

            // Main tab clicks (direct links only, not sub-nav)
            $tabsWrapper.off('click.twpgyMainTab').on('click.twpgyMainTab', '#twpgy-tabs-nav > li > a:not(.twpgy-tab-parent-link)', function (e) {
                e.preventDefault();
                const target = $(this).attr('href').replace('#', '');

                // Collapse slider sub-nav
                $navItems.filter('.twpgy-has-subtabs').removeClass('open')
                    .find('.twpgy-sub-nav').stop(true, true).slideUp(200);

                $navItems.removeClass('active');
                $(this).parent().addClass('active');

                $tabs.stop(true, true).hide();
                $('#' + target).fadeIn(150);
                $currentTabInput.val(target);
            });

            // Slider parent link — open + select tab2 (no toggle-close on parent click)
            $tabsWrapper.off('click.twpgyParentTab').on('click.twpgyParentTab', '.twpgy-tab-parent-link', function (e) {
                e.preventDefault();
                const $parentLi = $(this).closest('.twpgy-has-subtabs');
                const $subNav   = $parentLi.find('.twpgy-sub-nav');

                // Collapse any other open sub-navs
                $navItems.not($parentLi).filter('.twpgy-has-subtabs')
                    .removeClass('open').find('.twpgy-sub-nav').stop(true, true).slideUp(200);

                if (!$parentLi.hasClass('open')) {
                    $subNav.stop(true, true).slideDown(200);
                    $parentLi.addClass('open');
                } else {
                    // Already open — make sure it stays open and visible
                    $subNav.stop(true, true).show();
                }

                // Activate parent, show tab2
                $navItems.removeClass('active');
                $parentLi.addClass('active');

                $tabs.stop(true, true).hide();
                $('#tab2').fadeIn(150);
                $currentTabInput.val('tab2');

                // Show first subtab if none active
                const $activeSubItem = $parentLi.find('.twpgy-sub-nav li.active');
                if (!$activeSubItem.length) {
                    $parentLi.find('.twpgy-sub-nav li:first').addClass('active');
                }
                const activeTarget = $parentLi.find('.twpgy-sub-nav li.active a').attr('href').replace('#', '');
                $('.twpgy-sub-tab-content').hide();
                $('#' + activeTarget).show();
                $('#twpgy_current_subtab').val(activeTarget);
            });

            // Arrow icon — toggle close/open of the sub-nav (does not change which main tab is active)
            $tabsWrapper.off('click.twpgyArrow').on('click.twpgyArrow', '.twpgy-tab-parent-link .twpgy-subnav-arrow', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const $parentLi = $(this).closest('.twpgy-has-subtabs');
                const $subNav   = $parentLi.find('.twpgy-sub-nav');

                if ($parentLi.hasClass('open')) {
                    $subNav.stop(true, true).slideUp(200);
                    $parentLi.removeClass('open');
                } else {
                    $subNav.stop(true, true).slideDown(200);
                    $parentLi.addClass('open');
                }
            });

            $tabsWrapper
                .off('change.twpgyCbVal')
                .on('change.twpgyCbVal', 'input[type="checkbox"]', function () {
                    $(this).val(this.checked ? 'on' : 'off');
                });

            $tabsWrapper.find('input.color-picker')
                .wpColorPicker()
                .css({ width: '80px', height: '28px' });
        },

        repositionVariationGalleryFields: function() {
            var $galleryHTML = $(this).find('.twpgy-variation-gallery');

            if (!$galleryHTML.parent().hasClass('form-flex-box')) {
                $(this).find('.form-row.form-row-full.options').eq(0).before($galleryHTML);
            }

            $galleryHTML.find('.twpgy-easy-gallery-items').each(function(){
                if (!$(this).hasClass('ui-sortable')) {
                    $(this).sortable({
                        placeholder: "ui-state-highlight",
                        stop: function() {
                            var variableID = $(this).closest('.woocommerce_variation').find('.variable_post_id').val();
                            variableID = '#'+variableID;
                            var newArr = [];
                            $('.twpgy-easy-gallery-items'+variableID).find('.twpgy-easy-image').each(function() {
                                newArr.push($(this).data('attachmentid'));
                            });
                            $('.twpgy-easy-gallery-items'+variableID).closest('.woocommerce_variable_attributes').find('.twpgy-easy-gallery').val(JSON.stringify(newArr)).trigger('change');
                        }
                    });
                }
            });
        },

        eapgyAddGallery: function (e) {

            if ($(this).hasClass('twpgy-easy-upload-more-image')) {
                return;
            }

            e.preventDefault();

            const $btn = $(this);
            const $attrs = $btn.closest('.woocommerce_variable_attributes');

            // Safe JSON parse
            let ids = [];
            try {
                const val = $attrs.find('.twpgy-easy-gallery').val();
                ids = val ? JSON.parse(val) : [];
            } catch (err) {
                ids = [];
                console.warn('Invalid gallery JSON, resetting to empty array.');
            }

            const variationID = '#' + $btn.data('variation-id');

            const frame = wp.media({
                library: { type: 'image' },
                frame: 'post',
                state: 'gallery',
                multiple: true
            });

            frame.open();

            frame.on('update', function (selection) {

                selection.models.forEach(function (attachment) {
                    const item = attachment.toJSON();
                    const url = item.sizes?.thumbnail?.url || item.url;

                    ids.push(item.id);

                    if ( ids.length <= 3 ) {
                        $(`.twpgy-easy-gallery-items${variationID}`).append(`
                            <div class="twpgy-easy-image" data-attachmentid="${item.id}">
                                <img src="${url}" />
                                <div class="twpgy-easy-image-remover">
                                    <span class="dashicons dashicons-no"></span>
                                </div>
                            </div>
                        `);
                    }
                });

                $attrs.find('.twpgy-easy-gallery')
                    .val(JSON.stringify(ids))
                    .trigger('change');

                $btn.html('<span class="dashicons dashicons-plus-alt"></span> ').append(document.createTextNode(twpgygalleryObj.i18n.add_more));
            });
        },

        eapgyEditGalleryByEditBtn: function () { 
            let btn = $(this).parent().parent().find('.twpgy-woglry-upload-btn-wrapper .twpgy-woglry-upload-btn');
            EASYQUICKVIEW.eapgyEditGallery(btn);
        },

        eapgyEditGalleryByItem: function () { 
            let btn = $(this).parent().find('.twpgy-woglry-upload-btn-wrapper .twpgy-woglry-upload-btn');
            EASYQUICKVIEW.eapgyEditGallery(btn);
        },

        eapgyEditGallery: function(btn) {
            const btnHtmlClone = $(btn).parent().clone();
            const variationID = `#${$(btn).attr('data-variation-id')}`;
            const galleryInput = $(btn).parents('.woocommerce_variable_attributes').find('.twpgy-easy-gallery');
            const initialAttachmentArr = JSON.parse(galleryInput.val() || '[]');

            function updateGalleryDisplay(selection) {
                const newAttachmentArr = [];
                const $galleryItems = $(`.twpgy-easy-gallery-items${variationID}`);
                $galleryItems.empty();

                selection.models.forEach(function(attachment) {
                    const item = attachment.toJSON();
                    const imageSrc = item.sizes?.thumbnail?.url || item.sizes?.full?.url || item.url;
                    newAttachmentArr.push(item.id);

                    if (newAttachmentArr.length <= 3 ) {
                        const $image = $(
                            `<div class="twpgy-easy-image" data-attachmentid="${item.id}">
                                <img src="${imageSrc}" class="eapgy-image-thumb" />
                                <div class="twpgy-easy-image-remover">
                                    <span class="dashicons dashicons-no"></span>
                                </div>
                            </div>`
                        );
                        $galleryItems.append($image);
                    }
                });

                galleryInput.val(JSON.stringify(newAttachmentArr)).trigger('change');
                $galleryItems.append(btnHtmlClone);

                const $removeAllBtn = $galleryItems.next('p').find('.twpgy-easy-remove-all-images');
                if (newAttachmentArr.length > 0) $removeAllBtn.show();
            }

            window.wp.media.gallery.edit(`[gallery ids="${initialAttachmentArr}"]`).on('update', updateGalleryDisplay);
        },

        eapgyAddMoreGallery: function () {
            EASYQUICKVIEW.eapgyEditGallery($(this));
        },

        eapgyProduct360ViewGallery: function() {
            let productGalleryFrame;
            const $imageGalleryIds = $('#twpgy_360_image_gallery');
            const $productImages = $('#eapgy-product360-images-container').find('ul.eapgy-product360-images');

            function onSelectImages() {
                const selection = productGalleryFrame.state().get('selection');
                let attachmentIds = $imageGalleryIds.val();

                selection.map(function(attachment) {
                    const attachmentData = attachment.toJSON();

                    if (attachmentData.id) {
                        attachmentIds = attachmentIds ? attachmentIds + ',' + attachmentData.id : attachmentData.id;

                        const attachmentImage = attachmentData.sizes && attachmentData.sizes.thumbnail
                            ? attachmentData.sizes.thumbnail.url
                            : attachmentData.url;

                        $productImages.append(
                            `<li class="image" data-attachment_id="${attachmentData.id}">
                                <img src="${attachmentImage}" />
                                <ul class="actions">
                                    <li>
                                        <a href="#" class="delete" title="${$('.eapgy-add-product360-images a').data('delete')}">
                                            ${$('.eapgy-add-product360-images a').data('text')}
                                        </a>
                                    </li>
                                </ul>
                            </li>`
                        );
                    }
                });

                $imageGalleryIds.val(attachmentIds);
            }

            // Add product images button
            $('.eapgy-add-product360-images').off('click').on('click', 'a', function(event) {
                event.preventDefault();
                const $el = $(this);

                if (productGalleryFrame) {
                    productGalleryFrame.open();
                    return;
                }

                productGalleryFrame = wp.media.frames.product_gallery = wp.media({
                    title: $el.data('choose'),
                    button: { text: $el.data('update') },
                    states: [
                        new wp.media.controller.Library({
                            title: $el.data('choose'),
                            filterable: 'all',
                            multiple: true
                        })
                    ]
                });

                productGalleryFrame.on('select', onSelectImages);
                productGalleryFrame.open();
            });

            // Enable sortable if available
            if (typeof $productImages.sortable !== 'undefined') {
                $productImages.sortable({
                    items: 'li.image',
                    cursor: 'move',
                    scrollSensitivity: 40,
                    forcePlaceholderSize: true,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: 'wc-metabox-sortable-placeholder',
                    start: function(event, ui) {
                        ui.item.css('background-color', '#f6f6f6');
                    },
                    stop: function(event, ui) {
                        ui.item.removeAttr('style');
                    },
                    update: function() {
                        const attachmentIds = $productImages.find('li.image')
                            .toArray()
                            .map(li => $(li).attr('data-attachment_id'))
                            .join(',');
                        $imageGalleryIds.val(attachmentIds);
                    }
                });
            }

            // Remove images safely
            $('#eapgy-product360-images-container').off('click', 'a.delete').on('click', 'a.delete', function() {
                $(this).closest('li.image').remove();

                const attachmentIds = $productImages.find('li.image')
                    .toArray()
                    .map(li => $(li).attr('data-attachment_id'))
                    .join(',');

                $imageGalleryIds.val(attachmentIds);

                // Remove lingering tooltips
                $('#tiptip_holder, #tiptip_arrow').removeAttr('style');

                return false;
            });
        },

        toggleOffsetFieldsInit: function() {
            function is360Enabled() {
                const $cb = $('input[type="checkbox"][name$="[enable_360degree_gallery]"]');
                return ! $cb.length || $cb.is(':checked');
            }

            // Offset sliders shown for each 360° button position. Add-ons may register more positions.
            const offsetsByPosition = $.extend(
                { 'bottom-left': [ 'bottom', 'left' ] },
                window.twpgyOffsetPositions || {}
            );

            function toggleOffsetFields(position) {
                const $offsetFields = $('[data-offset]');
                $offsetFields.hide();

                if ( ! is360Enabled() ) {
                    return;
                }

                const offsets = offsetsByPosition[ position ] || offsetsByPosition['bottom-left'];

                offsets.forEach(function (side) {
                    $('[data-offset="' + side + '"]').show();
                });
            }

            const $positionSelect = $('select[name$="[twpgy_popup_button_position]"]');

            if ($positionSelect.length) {
                toggleOffsetFields($positionSelect.val());
                $positionSelect.off('change.twpgyOffset').on('change.twpgyOffset', function() {
                    toggleOffsetFields($(this).val());
                });
            }

            EASYQUICKVIEW._refreshOffsetFields = function () {
                toggleOffsetFields($positionSelect.val());
            };
        },

        sliderInit: function() {
            $('.eapgy-wooproduct-slider-ui').each(function() {
                const $slider = $(this);
                const $handle = $('<span class="ui-slider-handle ui-corner-all ui-state-default" tabindex="0"></span>');
                $slider.append($handle);

                const inputName = $slider.data('target-input');
                const $input = $('input[name="' + inputName + '"]');
                const min = parseInt($slider.data('min'), 10) || 0;
                const max = parseInt($slider.data('max'), 10) || 100;
                const step = parseInt($slider.data('step'), 10) || 1;
                let value = parseInt($slider.data('value'), 10) || min;

                function updateFill(val) {
                    const perc = ((val - min) / (max - min)) * 100;
                    $slider.css('--slider-fill', perc + '%');
                    $handle.css('left', perc + '%');
                }

                $slider.slider({
                    min: min,
                    max: max,
                    step: step,
                    value: value,
                    slide: function(event, ui) {
                        $input.val(ui.value);
                        updateFill(ui.value);
                    }
                });

                $input.off('input change').on('input change', function() {
                    let val = parseInt($(this).val(), 10);
                    if (isNaN(val)) val = min;
                    if (val < min) val = min;
                    if (val > max) val = max;
                    $(this).val(val);
                    $slider.slider('value', val);
                    updateFill(val);
                });

                updateFill(value);
            });
        },

        subtabInit: function () {

            const $subTabInput = $('#twpgy_current_subtab');
            const savedSubTab = $subTabInput.val();

            if (savedSubTab && $('#' + savedSubTab).length) {

                $('.twpgy-sub-tab-content').hide();
                $('#' + savedSubTab).show();

                $('.eapgy-sub-tabs-nav li').removeClass('active');
                $('.eapgy-sub-tabs-nav a[href="#' + savedSubTab + '"]')
                    .parent()
                    .addClass('active');
            }

           
            $('.eapgy-sub-tabs-nav a')
                .off('click.eapgySubtab')
                .on('click.eapgySubtab', function (e) {

                    e.preventDefault();

                    const targetID = $(this).attr('href').replace('#', '');

                    $subTabInput.val(targetID);

                    $('.eapgy-sub-tabs-nav li').removeClass('active');
                    $(this).parent().addClass('active');

                    $('.twpgy-sub-tab-content').hide();
                    $('#' + targetID).fadeIn(150);
                });
        },

        paginationSettingsInit: function() {

            function togglePaginationNumberSettings( context = document, animate = true ) {
                const $paginationCheckbox = $('input[name$="[enable_main_product_pagination]"]');
                const paginationEnabled   = ! $paginationCheckbox.length || $paginationCheckbox.is(':checked');

                const selectedLayout = $('input[type="radio"][name$="[pagination_layout]"]:checked', context).val();
                const $fractionColor   = $('.eapgy-pagination-fraction-color', context);
                const $backgroundColor = $('.eapgy-pagination-background-color', context);

                const hide = function ($el) { animate ? $el.stop(true, true).slideUp(200) : $el.hide(); };
                const show = function ($el) { animate ? $el.stop(true, true).slideDown(200) : $el.show(); };

                hide($fractionColor);

                if ( ! paginationEnabled ) {
                    hide($backgroundColor);
                } else {
                    show($backgroundColor);

                    if (selectedLayout === 'number_fraction') {
                        show($fractionColor);
                        hide($backgroundColor);
                    }
                }

                // Let add-ons toggle the color fields of the pagination styles they register.
                $(document).trigger('twpgy:paginationLayoutChanged', [ {
                    layout: selectedLayout,
                    enabled: paginationEnabled,
                    context: context,
                    $backgroundColor: $backgroundColor,
                    show: show,
                    hide: hide
                } ]);
            }

            togglePaginationNumberSettings(document, false);

            $(document).off('change', 'input[type="radio"][name$="[pagination_layout]"]').on('change', 'input[type="radio"][name$="[pagination_layout]"]', function() {
                const wrapper = $(this).closest('.eapgy-settings-wrapper, .postbox, body');
                togglePaginationNumberSettings(wrapper, true);
            });

        },

        bulletOffsetSlidersInit: function() {
            function is360Enabled() {
                const $cb = $('input[type="checkbox"][name$="[enable_360degree_gallery]"]');
                return ! $cb.length || $cb.is(':checked');
            }

            function toggleBulletOffsetSliders() {
                const position = $('select[name$="[twpgy_popup_button_position]"]').val();

                const $top = $('input[name$="[twpgy_popup_button_offset_top]"]').closest('.twpgy-settings-item');
                const $right = $('input[name$="[twpgy_popup_button_offset_right]"]').closest('.twpgy-settings-item');
                const $bottom = $('input[name$="[twpgy_popup_button_offset_bottom]"]').closest('.twpgy-settings-item');
                const $left = $('input[name$="[twpgy_popup_button_offset_left]"]').closest('.twpgy-settings-item');

                [$top, $right, $bottom, $left].forEach($el => $el.hide());

                if ( ! is360Enabled() ) return;
                if (!position) return;
                if (position.startsWith('top')) $top.slideDown(150);
                if (position.startsWith('bottom')) $bottom.slideDown(150);
                if (position.endsWith('left')) $left.slideDown(150);
                if (position.endsWith('right')) $right.slideDown(150);
            }

            toggleBulletOffsetSliders();
            $(document).off('change', 'select[name$="[twpgy_bullet_position]"]')
                    .on('change', 'select[name$="[twpgy_bullet_position]"]', toggleBulletOffsetSliders);
        },

        assignProductsModalInit: function() {
            let currentLayout = null;
            $('#eapgy-products-select').select2({ placeholder: 'Select Products', width: '100%' });

            $('.eapgy-assign-products').off('click').on('click', function(e) {
                e.preventDefault();
                currentLayout = $(this).data('layout-id');
                $('.eapgy-assign-product-message').html('');

                $.post(twpgygalleryObj.ajaxurl, {
                    action: 'twpgy_get_assigned_products',
                    layout_id: currentLayout,
                    nonce: twpgygalleryObj.assign_nonce
                }, function(response) {
                    $('#eapgy-products-select').val(response).trigger('change');
                    $('#eapgy-assign-products-modal-overlay, #eapgy-assign-products-modal').fadeIn();
                });
            });

            $('#eapgy-save-assigned-products').off('click').on('click', function() {
                $.post(twpgygalleryObj.ajaxurl, {
                    action: 'twpgy_save_assigned_products',
                    layout_id: currentLayout,
                    products: $('#eapgy-products-select').val(),
                    nonce: twpgygalleryObj.assign_nonce
                }, function(response) {
                    const $msg = $('.eapgy-assign-product-message');
                    if (response.success) {
                        $msg.empty().append($('<span>').css('color', 'green').text(response.data));
                        setTimeout(function() {
                            $('#eapgy-assign-products-modal-overlay, #eapgy-assign-products-modal').fadeOut();
                            $msg.html('');
                        }, 1000);
                    } else {
                        $msg.empty().append($('<span>').css('color', 'red').text(response.data));
                    }
                });
            });

            $('#eapgy-assign-products-modal-overlay, #eapgy-assign-products-modal .eapgy-close-modal').off('click').on('click', function() {
                $('#eapgy-assign-products-modal-overlay, #eapgy-assign-products-modal').fadeOut();
                $('.eapgy-assign-product-message').html('');
            });
        },

        removeAllImagesInit: function() {
            $(document).off('click', '.twpgy-easy-remove-all-images').on('click', '.twpgy-easy-remove-all-images', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const $wrapper = $btn.closest('.twpgy-variation-gallery');
                const $galleryItems = $wrapper.find('.twpgy-easy-gallery-items');

                $galleryItems.find('.twpgy-easy-image').not('.twpgy-woglry-upload-btn-wrapper').remove();
                $wrapper.find('.twpgy-easy-gallery').val('').trigger('change');

                $galleryItems.find('.twpgy-woglry-upload-btn').removeClass('hidden');
                $galleryItems.find('.twpgy-easy-upload-more-image').addClass('hidden');
            });
        },
        /**
         * Single Image remove click
         */
        _removeSingleImageHandler: function () {
            $(document).off('click', '.twpgy-easy-image-remover').on('click', '.twpgy-easy-image-remover', function(e) {
                e.preventDefault();
                e.stopPropagation(); // stop edit gallery from opening

                const $remover = $(this);
                const $imageWrapper = $remover.closest('.twpgy-easy-image');
                const $galleryWrapper = $remover.closest('.twpgy-variation-gallery');
                const $galleryInput = $galleryWrapper.find('.twpgy-easy-gallery');

                $imageWrapper.remove();

                const attachmentIds = [];
                $galleryWrapper.find('.twpgy-easy-image').each(function() {
                    const id = $(this).data('attachmentid');
                    if (id) attachmentIds.push(id);
                });

                $galleryInput.val(JSON.stringify(attachmentIds)).trigger('change');

                const $galleryItems = $galleryWrapper.find('.twpgy-easy-gallery-items');
                if ($galleryItems.find('.twpgy-easy-image').length <= 1) {
                    $galleryItems.find('.twpgy-woglry-upload-btn').removeClass('hidden');
                    $galleryItems.find('.twpgy-easy-upload-more-image').addClass('hidden');
                }
            });
        },

        deviceTabInit: function () {
            $(document).off('click.twpgyDeviceTab', '.twpgy-device-tab')
                .on('click.twpgyDeviceTab', '.twpgy-device-tab', function (e) {
                    e.preventDefault();

                    const device   = $(this).data('device');
                    const $control = $(this).closest('.twpgy-responsive-control');

                    // Update active icon tab
                    $control.find('.twpgy-device-tab').removeClass('active');
                    $(this).addClass('active');

                    // Show correct device panel
                    $control.find('.twpgy-device-panel').hide();
                    $control.find('.twpgy-device-panel[data-device="' + device + '"]').show();
                });
        },

        innerTabInit: function () {
            $(document).off('click.twpgyInnerTab', '.twpgy-inner-tab-link')
                .on('click.twpgyInnerTab', '.twpgy-inner-tab-link', function (e) {
                    e.preventDefault();

                    const targetID = $(this).attr('href').replace('#', '');
                    const $wrapper = $(this).closest('.twpgy-sub-tab-content');

                    $wrapper.find('.twpgy-inner-tab-link').removeClass('active');
                    $(this).addClass('active');

                    $wrapper.find('.twpgy-inner-tab-content').hide();
                    $('#' + targetID).fadeIn(150);

                    $wrapper.find('input[type="hidden"][name^="twpgy_current_"][name$="_subtab"]').val(targetID);
                });
        },

        subtabSidebarInit: function () {
            $(document).off('click.twpgySubNav', '.twpgy-sub-nav a')
                .on('click.twpgySubNav', '.twpgy-sub-nav a', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const targetID = $(this).attr('href').replace('#', '');
                    const $parentLi = $(this).closest('.twpgy-has-subtabs');

                    // Update sub-nav active state
                    $parentLi.find('.twpgy-sub-nav li').removeClass('active');
                    $(this).parent().addClass('active');

                    // Make sure tab2 is visible
                    $('.twpgy-tab-content').hide();
                    $('#tab2').show();

                    // Show correct sub-tab content
                    $('.twpgy-sub-tab-content').hide();
                    $('#' + targetID).fadeIn(150);

                    // Persist
                    $('#twpgy_current_subtab').val(targetID);
                    $('input[name="twpgy_current_tab"]').val('tab2');
                });
        },

        resetSettingsInit: function () {
            $('#twpgy-reset-btn').off('click.twpgyReset').on('click.twpgyReset', function () {
                if ( window.confirm(twpgygalleryObj.i18n.reset_confirm) ) {
                    $('#twpgy_reset_settings').val('1');
                    $('#twpgy-woo_gallery-settings').trigger('submit');
                }
            });
        },

        toastInit: function () {
            const $toast = $('.twpgy-toast');
            if ( ! $toast.length ) return;

            setTimeout(function () {
                $toast.addClass('twpgy-toast-hide');
                setTimeout(function () {
                    $toast.remove();
                }, 420);
            }, 5000);
        },

        toggleLightboxSettingsInit: function () {
            const dependencies = [
                { checkbox: 'gallery_lightbox',                cls: 'twpgy-lightbox-dependent' },
                { checkbox: 'enable_main_product_nav',         cls: 'twpgy-main-nav-dependent' },
                { checkbox: 'enable_main_product_pagination',  cls: 'twpgy-main-pagination-dependent', afterShow: function () {
                    $('input[type="radio"][name$="[pagination_layout]"]:checked').first().trigger('change');
                } },
                { checkbox: 'enable_thumbs_nav',               cls: 'twpgy-thumbs-nav-dependent' },
                { checkbox: 'gallery_video',                   cls: 'twpgy-video-dependent' },
                { checkbox: 'enable_360degree_gallery',        cls: 'twpgy-360degree-dependent', afterShow: function () {
                    if ( typeof EASYQUICKVIEW._refreshOffsetFields === 'function' ) {
                        EASYQUICKVIEW._refreshOffsetFields();
                    }
                } },
            ];

            dependencies.forEach(function (dep) {
                const $checkbox = $('input[name$="[' + dep.checkbox + ']"]').filter('[type="checkbox"]');
                if ( ! $checkbox.length ) return;

                const apply = function (checked, animate) {
                    const $items = $('.' + dep.cls);
                    if ( ! $items.length ) return;

                    $items.stop(true, false);

                    if ( checked ) {
                        if ( animate ) {
                            $items.slideDown(200);
                        } else {
                            $items.show();
                        }
                        if ( typeof dep.afterShow === 'function' ) dep.afterShow();
                    } else {
                        if ( animate ) {
                            $items.slideUp(200);
                        } else {
                            $items.hide();
                        }
                    }
                };

                $checkbox
                    .off('change.twpgyDep')
                    .on('change.twpgyDep', function () {
                        const isChecked = this.checked;
                        $(this).val( isChecked ? 'on' : 'off' );
                        apply( isChecked, true );
                    });
            });

            this.toggleZoomSettingsInit();
        },

        toggleZoomSettingsInit: function () {
            const $zoomCheckbox = $('input[name$="[gallery_image_zoom]"]');
            const $zoomStyle    = $('select[name$="[zoom_style]"]');
            if ( ! $zoomCheckbox.length ) return;

            function update() {
                const zoomEnabled  = $zoomCheckbox.is(':checked');
                const style        = $zoomStyle.length ? $zoomStyle.val() : 'default';

                const $zoomStyleField = $('.twpgy-zoom-dependent');

                const show = function ($el) { $el.stop(true, true).slideDown(200); };
                const hide = function ($el) { $el.stop(true, true).slideUp(200); };

                if ( zoomEnabled ) {
                    show($zoomStyleField);
                } else {
                    hide($zoomStyleField);
                }

                // Let add-ons toggle the fields of the zoom styles they register.
                $(document).trigger('twpgy:zoomSettingsChanged', [ {
                    enabled: zoomEnabled,
                    style: style,
                    show: show,
                    hide: hide
                } ]);
            }

            $zoomCheckbox.off('change.twpgyZoom').on('change.twpgyZoom', update);
            if ( $zoomStyle.length ) {
                $zoomStyle.off('change.twpgyZoom').on('change.twpgyZoom', update);
            }
        },

    };

    $(function () {
        EASYQUICKVIEW.init();
    });


    $(function ($) {

        function eapgyToggleCustomImageSize(animate) {

            var largeSize = $('select[name$="[easyel_grid_large_image_size]"]').val();

            if (largeSize === 'custom') {
                animate ? $('.eapgy-grid-large-image-size').slideDown(200) : $('.eapgy-grid-large-image-size').show();
            } else {
                animate ? $('.eapgy-grid-large-image-size').slideUp(200) : $('.eapgy-grid-large-image-size').hide();
            }

            var sliderSize = $('select[name$="[easyel_slider_large_image_size]"]').val();

            if (sliderSize === 'custom') {
                animate ? $('.eapgy-slider-large-image-size-custom').slideDown(200) : $('.eapgy-slider-large-image-size-custom').show();
            } else {
                animate ? $('.eapgy-slider-large-image-size-custom').slideUp(200) : $('.eapgy-slider-large-image-size-custom').hide();
            }
        }

        function eapgyToggleGridControls(animate) {

            var layout = $('input[name$="[gallery_layout]"]:checked').val();

            if (layout && layout.indexOf('grid') !== -1) {
                animate ? $('.eapgy-grid-large-image-size-select').slideDown(200) : $('.eapgy-grid-large-image-size-select').show();
                eapgyToggleCustomImageSize(animate);
            } else {
                animate ? $('.eapgy-grid-large-image-size-select').slideUp(200) : $('.eapgy-grid-large-image-size-select').hide();
                animate ? $('.eapgy-grid-large-image-size').slideUp(200) : $('.eapgy-grid-large-image-size').hide();
            }
        }

        eapgyToggleCustomImageSize(false);
        eapgyToggleGridControls(false);

        $(document).on('change', 'select[name$="[easyel_grid_large_image_size]"]', function () { eapgyToggleCustomImageSize(true); });
        $(document).on('change', 'select[name$="[easyel_slider_large_image_size]"]', function () { eapgyToggleCustomImageSize(true); });
        $(document).on('change', 'input[name$="[gallery_layout]"]', function () { eapgyToggleGridControls(true); });

    });
       

})(jQuery, window, document);