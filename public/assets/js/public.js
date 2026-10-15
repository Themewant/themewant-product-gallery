(function($) {
    'use strict';

    const TWPGY_SWIPER_BASE_CLASSES = {
        containerModifierClass: 'twpgy-swiper-',
        wrapperClass: 'twpgy-swiper-wrapper',
        slideClass: 'twpgy-swiper-slide',
        slideActiveClass: 'twpgy-swiper-slide-active',
        slideVisibleClass: 'twpgy-swiper-slide-visible',
        slideFullyVisibleClass: 'twpgy-swiper-slide-fully-visible',
        slideNextClass: 'twpgy-swiper-slide-next',
        slidePrevClass: 'twpgy-swiper-slide-prev',
        slideBlankClass: 'twpgy-swiper-slide-blank',
        lazyPreloaderClass: 'twpgy-swiper-lazy-preloader'
    };

    const TWPGY_SWIPER_NAV_CLASSES = {
        disabledClass: 'twpgy-swiper-button-disabled',
        hiddenClass: 'twpgy-swiper-button-hidden',
        lockClass: 'twpgy-swiper-button-lock',
        navigationDisabledClass: 'twpgy-swiper-navigation-disabled'
    };

    const TWPGY_SWIPER_PAGINATION_CLASSES = {
        bulletClass: 'twpgy-swiper-pagination-bullet',
        bulletActiveClass: 'twpgy-swiper-pagination-bullet-active',
        modifierClass: 'twpgy-swiper-pagination-',
        currentClass: 'twpgy-swiper-pagination-current',
        totalClass: 'twpgy-swiper-pagination-total',
        hiddenClass: 'twpgy-swiper-pagination-hidden',
        progressbarFillClass: 'twpgy-swiper-pagination-progressbar-fill',
        progressbarOppositeClass: 'twpgy-swiper-pagination-progressbar-opposite',
        clickableClass: 'twpgy-swiper-pagination-clickable',
        lockClass: 'twpgy-swiper-pagination-lock',
        horizontalClass: 'twpgy-swiper-pagination-horizontal',
        verticalClass: 'twpgy-swiper-pagination-vertical',
        paginationDisabledClass: 'twpgy-swiper-pagination-disabled'
    };

    const TWPGY_SWIPER_THUMBS_CLASSES = {
        thumbsContainerClass: 'twpgy-swiper-thumbs',
        slideThumbActiveClass: 'twpgy-swiper-slide-thumb-active'
    };

    const EASYQUICKVIEW = {
        autoplayTimer: null,
        autoplayDelay: 2000,

        init: function() {

            this.cacheDOM();
            this.bindEvents();
            this.variationChange(); 
            this.enableGalleryCarousel();
            this.enableFancybox();
            this.enableZoom();
            this.updateClassOnResize();
            this.toggle360Button();
            this.observeHtmlClass();
            
        },

        cacheDOM: function() {
            this.$window = $(window);
            this.$document = $(document);
            this.$galleryArea = $('.twpgy-easy-woo-gallery-image-area');
            this.pluginOpts = window.twpgywoglry_vars && window.twpgywoglry_vars.pluginOptions ?
                window.twpgywoglry_vars.pluginOptions : {};
            if (!this.defaultGalleryHTML && this.$galleryArea.length) {
                this.defaultGalleryHTML = this.$galleryArea[0].outerHTML;
            }
        },


        bindEvents: function () {
            var self = this;

            this.$window
                .off('resize.EASYQUICKVIEW')
                .on('resize.EASYQUICKVIEW', function () {
                    self.updateClassOnResize();
                });

            this.$document
                .off('click.EASYQUICKVIEW_CLOSE')
                .on(
                    'click.EASYQUICKVIEW_CLOSE',
                    '.twpgy-easy-woo-gallery-image-area .close',
                    function () {
                        self.closeQuickViewPopup();
                    }
                );
            this.$document
                .off('click.twpgyResetVariation')
                .on('click.twpgyResetVariation', '.reset_variations', (e) => {
                    e.preventDefault();

                    if (!this.defaultGalleryHTML) return;

                    const $newGallery = $(this.defaultGalleryHTML);

                    this.$galleryArea.replaceWith($newGallery);

                    this.cacheDOM();

                    this.enableGalleryCarousel();
                    this.enableZoom();
                    this.enableFancybox();
                    this.updateClassOnResize();
                });

            this.$document
                .off('click.twpgyeapgy360')
                .on('click.twpgyeapgy360', '.twpgy-open-360-popup', function (e) {
                    e.preventDefault();

                    var $popup = $('#twpgy-product-360-view');
                    var $viewer = $popup.find('.twpgy-threed-view');

                    $popup.fadeIn(200);
                    $('body').addClass('twpgy-360-open');

                    self.generate360Images($viewer);
                    
                });

            this.$document
                .off('click.twpgy360Close')
                .on(
                    'click.twpgy360Close',
                    '.twpgy-360-close, .twpgy-360-overlay',
                    function (e) {
                        e.preventDefault();

                        $('#twpgy-product-360-view').fadeOut(200);
                        $('body').removeClass('twpgy-360-open');
                    }
                );

            this.$document
                .off('mouseenter.twpgyZoom mouseleave.twpgyZoom')
                .on(
                    'mouseenter.twpgyZoom',
                    '.themewant-product-gallery-preview-slider-container .twpgy-swiper-button-next, .themewant-product-gallery-preview-slider-container .twpgy-swiper-button-prev',
                    function () {
                        $(this)
                            .closest('.themewant-product-gallery-preview-slider-container')
                            .find('.twpgy-swiper-slide.zoom-on-mousemove')
                            .addClass('zoom-disabled');
                    }
                )
                .on(
                    'mouseleave.twpgyZoom',
                    '.themewant-product-gallery-preview-slider-container .twpgy-swiper-button-next, .themewant-product-gallery-preview-slider-container .twpgy-swiper-button-prev',
                    function () {
                        $(this)
                            .closest('.themewant-product-gallery-preview-slider-container')
                            .find('.twpgy-swiper-slide.zoom-disabled')
                            .removeClass('zoom-disabled');
                    }
                );
        },


        stringToBool: function(str) {
            return str === 'on';
        },

        enableFancybox: function() {

            const opts = this.pluginOpts;
            const self = this;
            let buttons = [];

            // Toolbar / Buttons
            if (opts.lightbox_counter === 'on') {
                buttons = ["slideShow", "thumbs", "zoom", "fullScreen", "close"];
            } else {
                buttons = ["close"];
            }

            const pro = window.EASYQUICKVIEW_PRO || {};

            // Let add-ons adjust gallery video links before Fancybox binds.
            if ( typeof pro.beforeFancybox === 'function' ) {
                pro.beforeFancybox( this.$galleryArea, opts );
            }

            this.$galleryArea.find('a[data-fancybox]').each(function() {

                let $this = $(this);
                let href = $this.attr('href');

                let dataSrc = $this.attr('data-src');

                if (!href && dataSrc) {
                    href = dataSrc;
                }

                if (href) {

                    // youtube-nocookie
                    if (href.includes('youtube.com')) {
                        href = href.replace('youtube.com', 'youtube-nocookie.com');
                    }

                    if ( href.includes('youtu.be' )) {
                        const videoId = href.split('/').pop();
                        href = 'https://www.youtube.com/watch?v=' + videoId;
                    }

                    if ( typeof pro.filterVideoHref === 'function' ) {
                        href = pro.filterVideoHref( href, $this, opts );
                    }

                    $this.attr( 'href', href );

                    if ( href.includes('youtube') ) {
                        $this.addClass('has-video');
                    }
                }

            });

            let fancyboxOptions = {
                loop: parseInt(opts.loop_video, 10) === 1,

                buttons: buttons,

                animationEffect: "zoom",
                transitionEffect: "slide",

                protect: true,

                slideShow: {
                    autoStart: opts.autoplay_video === true,
                    speed: 3000
                },

                // Thumbnails
                thumbs: {
                    autoStart: false
                },

                youtube: {
                    autoplay: opts.autoplay_video ? 1 : 0,
                    mute: opts.autoplay_video ? 1 : 0,
                    controls: 0,
                    rel: 0
                },

                iframe: {
                    preload: false,
                    css: {
                        width: '80%',
                        height: '80%'
                    }
                },

                caption: function(instance, item) {
                    if (opts.lightbox_counter === 'on') {
                        return (item.index + 1) + ' / ' + instance.group.length;
                    }
                    return item.caption || '';
                }

            };

            if ( typeof pro.filterFancyboxOptions === 'function' ) {
                fancyboxOptions = pro.filterFancyboxOptions( fancyboxOptions, opts );
            }

            this.$galleryArea.find('[data-fancybox]').fancybox( fancyboxOptions );

            if ( opts.autoplay_video && opts.video_play_mode === 'popup' ) {
                const $firstVideo = this.$galleryArea.find('.twpgy-swiper-slide.has-video .lightbox-toggle').first();
                if ( $firstVideo.length ) {
                    setTimeout(function () {
                        $firstVideo.trigger('click');
                    }, 600);
                }
            }

        },

        enableZoom: function() {
            const self = this;
            const $zoomItems = this.$galleryArea.find('.zoom-on-mousemove:not(.has-video)');

            $zoomItems.each(function(index, element) {
                const $el = $(element);

                $el.off('.EASYQUICKVIEW').on('mousemove.EASYQUICKVIEW', function(e) {
                    self.zoom(e);
                });

                $el.find('img.preview-image').off('.EASYQUICKVIEW').on('mousemove.EASYQUICKVIEW', function() {
                    $(this).css('opacity', 0);
                });

                $el.off('mouseleave.EASYQUICKVIEW').on('mouseleave.EASYQUICKVIEW', function() {
                    $(this).find('img.preview-image').css('opacity', 1);
                });
            });
        },

        zoom: function(e) {
            const zoomer = e.currentTarget;
            let offsetX, offsetY;

            if (e.offsetX !== undefined) {
                offsetX = e.offsetX;
                offsetY = e.offsetY;
            } else if (e.touches && e.touches[0]) {
                const rect = zoomer.getBoundingClientRect();
                offsetX = e.touches[0].pageX - rect.left;
                offsetY = e.touches[0].pageY - rect.top;
            } else {
                return;
            }

            const x = (offsetX / zoomer.offsetWidth) * 100;
            const y = (offsetY / zoomer.offsetHeight) * 100;
            zoomer.style.backgroundPosition = `${x}% ${y}%`;
        },

        enableGalleryCarousel: function() {
            if (!this.$galleryArea.length) return;

            const isMobile = window.matchMedia('(max-width: 767px)').matches;

            this.$galleryArea.parent().addClass('eswoglry-easy-woo-gallery-container');

            const pluginOpts = this.pluginOpts;
            const $thumbSlider = this.$galleryArea.find('.twpgy-thumb-slider-container .twpgy-swiper');
            const $mainSlider = this.$galleryArea.find('.themewant-product-gallery-preview-slider-container .twpgy-swiper');

            if ( $thumbSlider.length || $mainSlider.length ) {

                const $galleryAreaRef = this.$galleryArea;
                const pro = window.EASYQUICKVIEW_PRO || {};

                // Layouts whose thumbnail slider runs vertically. Add-ons may append their own.
                const verticalLayouts = [ 'layout-thumbs_left', 'layout-thumbs_right' ]
                    .concat( Array.isArray( pro.verticalLayouts ) ? pro.verticalLayouts : [] );

                const twpgyIsVerticalLayout = function () {
                    return verticalLayouts.some( cls => $galleryAreaRef.hasClass( cls ) );
                };

                const twpgyResolveThumbGap = function () {
                    const isVerticalLayout = twpgyIsVerticalLayout();

                    const horiGap = parseInt(
                        pluginOpts.thumbs_slides_horigap !== undefined
                            ? pluginOpts.thumbs_slides_horigap
                            : pluginOpts.thumbs_slides_space_between,
                        10
                    );
                    const verGap = parseInt(
                        pluginOpts.thumbs_slides_vergap !== undefined
                            && pluginOpts.thumbs_slides_vergap !== ''
                            ? pluginOpts.thumbs_slides_vergap
                            : (pluginOpts.thumbs_slides_horigap !== undefined
                                ? pluginOpts.thumbs_slides_horigap
                                : pluginOpts.thumbs_slides_space_between),
                        10
                    );

                    const gap = isVerticalLayout ? verGap : horiGap;
                    return isNaN(gap) ? 0 : gap;
                };

                const thumbSwiper = new Swiper($thumbSlider[0], {
                    ...TWPGY_SWIPER_BASE_CLASSES,
                    direction: 'horizontal',
                    mousewheel: pluginOpts.thumbs_slider_mouse_wheel,
                    slidesPerView: parseInt(pluginOpts.thumbs_slides_per_view, 10),
                    watchSlidesProgress: true,
                    freeMode: pluginOpts.slider_free_mode_enable,
                    spaceBetween: twpgyResolveThumbGap(),
                    on: {
                        beforeInit: function () {
                            this.params.spaceBetween = twpgyResolveThumbGap();
                        },
                        init: function () {
                            this.params.spaceBetween = twpgyResolveThumbGap();
                            this.update();
                        },
                        resize: function () {
                            this.params.spaceBetween = twpgyResolveThumbGap();
                            this.update();
                        }
                    },
                });

                this._twpgyResolveThumbGap = twpgyResolveThumbGap;


                let paginationOptions = {
                    ...TWPGY_SWIPER_PAGINATION_CLASSES,
                    el: $mainSlider.siblings('.twpgy-swiper-pagination')[0],
                    clickable: true,
                };

                
                if (pluginOpts.pagination_layout === 'number_fraction') {
                    paginationOptions.type = 'fraction'; 
                }
                
                else if (pluginOpts.pagination_layout === 'number_dynamic_bullet') {
                    paginationOptions.dynamicBullets = true; 
                }

                if ( typeof window.EASYQUICKVIEW_PRO !== 'undefined' &&
                    typeof window.EASYQUICKVIEW_PRO.applyProPagination === 'function' ) {

                    paginationOptions = window.EASYQUICKVIEW_PRO.applyProPagination( pluginOpts, paginationOptions );
                }

                const isCarouselTwo = pluginOpts.gallery_layout === 'carousel_two';

                let effectType = pluginOpts.slide_effect;

                if (isCarouselTwo && ['fade', 'cube', 'flip', 'creative'].includes(pluginOpts.slide_effect)) {
                    effectType = 'slide';
                }
               
                var self = this;
                const mainSwiperOptions = {
                    ...TWPGY_SWIPER_BASE_CLASSES,
                    direction: 'horizontal',
                    autoHeight: pluginOpts.slide_adaptive_height || pluginOpts.slider_orientaiton === 'vertical',
                    effect: effectType,
                    mousewheel: pluginOpts.slider_mouse_wheel,
                    spaceBetween: 0,
                    centeredSlides: false,
                    speed: 300,
                    loop: pluginOpts.slider_infinite,
                    rtl: pluginOpts.slider_rtl,
                    freeMode: pluginOpts.slider_free_mode_enable,
                    navigation: {
                        ...TWPGY_SWIPER_NAV_CLASSES,
                        nextEl: $mainSlider.siblings('.twpgy-swiper-button-next')[0],
                        prevEl: $mainSlider.siblings('.twpgy-swiper-button-prev')[0]
                    },
                    autoplay: pluginOpts.slider_autoplay ? {
                        delay: parseInt( pluginOpts.twpgy_slider_speed ),                 
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    } : false,
                    watchSlidesProgress: true,
                    pagination: paginationOptions,
                    on: {
                        init: function () {
                            if (
                                typeof window.EASYQUICKVIEW_PRO !== 'undefined' &&
                                typeof window.EASYQUICKVIEW_PRO.applyZoomToActiveSlide === 'function'
                            ) {
                                window.EASYQUICKVIEW_PRO.applyZoomToActiveSlide(this,pluginOpts);
                            }
                        },
                        slideChangeTransitionEnd: function () {
                            if (
                                typeof window.EASYQUICKVIEW_PRO !== 'undefined' &&
                                typeof window.EASYQUICKVIEW_PRO.applyZoomToActiveSlide === 'function'
                            ) {
                                window.EASYQUICKVIEW_PRO.applyZoomToActiveSlide(this,pluginOpts);
                            }
                        }
                    }
                };

                if ( isCarouselTwo ) {
                    mainSwiperOptions.spaceBetween =  16;
                    mainSwiperOptions.breakpoints = {
                        0: {
                            slidesPerView: 1,
                        },
                        575: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 2,
                        },
                        992: {
                            slidesPerView: 2,
                        },
                        1200: {
                            slidesPerView: 2,
                        },
                    };
                }

                if ( $thumbSlider.length && pluginOpts.enable_thumbs_slider && pluginOpts.gallery_layout != 'slider' ) {
                    mainSwiperOptions.thumbs = { ...TWPGY_SWIPER_THUMBS_CLASSES, swiper: thumbSwiper };
                }

                const mainSwiper = new Swiper(
                    $mainSlider[0],
                    mainSwiperOptions
                );

                const $thumbNext = $thumbSlider.siblings('.twpgy-swiper-button-next');
                const $thumbPrev = $thumbSlider.siblings('.twpgy-swiper-button-prev');

                function twpgyUpdateThumbNavState() {
                    const idx   = mainSwiper.realIndex;
                    const total = thumbSwiper.slides.length;
                    $thumbPrev.toggleClass('twpgy-swiper-button-disabled', idx <= 0);
                    $thumbNext.toggleClass('twpgy-swiper-button-disabled', idx >= total - 1);
                }

                if ( $thumbNext.length ) {
                    mainSwiper.on('slideChange', twpgyUpdateThumbNavState);
                    twpgyUpdateThumbNavState();

                    $thumbNext.on('click', function () {
                        if ( $(this).hasClass('twpgy-swiper-button-disabled') ) return;
                        const newIndex = mainSwiper.realIndex + 1;
                        mainSwiper.slideToLoop( newIndex );
                        thumbSwiper.slideTo( Math.min( newIndex, thumbSwiper.slides.length - 1 ) );
                    });

                    $thumbPrev.on('click', function () {
                        if ( $(this).hasClass('twpgy-swiper-button-disabled') ) return;
                        const newIndex = mainSwiper.realIndex - 1;
                        mainSwiper.slideToLoop( newIndex );
                        thumbSwiper.slideTo( Math.max( newIndex, 0 ) );
                    });
                }

                if (typeof window.EASYQUICKVIEW_PRO !== 'undefined' &&
                    typeof window.EASYQUICKVIEW_PRO.twpgyCarouselOne === 'function') {
                    
                    window.EASYQUICKVIEW_PRO.twpgyCarouselOne();
                }

                // Mutation observer to detect layout changes
                const observer = new MutationObserver(() => {
                    const newDirection = twpgyIsVerticalLayout() ? 'vertical' : 'horizontal';

                    const newGap = twpgyResolveThumbGap();
                    let needsUpdate = false;

                    if ( thumbSwiper.params.direction !== newDirection ) {
                        thumbSwiper.params.direction = newDirection;
                        needsUpdate = true;
                    }

                    if ( thumbSwiper.params.spaceBetween !== newGap ) {
                        thumbSwiper.params.spaceBetween = newGap;
                        needsUpdate = true;
                    }

                    if ( needsUpdate ) {
                        thumbSwiper.update();
                    }
                });

                observer.observe(this.$galleryArea[0], { attributes: true, attributeFilter: ['class'] });
            }

            function twpgyFixThumbNavSync() {

                var mainEl  = document.querySelector('.themewant-product-gallery-preview-slider-container .twpgy-swiper');
                var thumbEl = document.querySelector('.twpgy-thumb-slider-container .twpgy-swiper');

                if (!mainEl || !thumbEl) return;
                if (!mainEl.swiper || !thumbEl.swiper) return;

                var mainSwiper  = mainEl.swiper;
                var thumbSwiper = thumbEl.swiper;

                thumbSwiper.on('slideChange', function () {
                    var index = thumbSwiper.realIndex;
                    if (mainSwiper.realIndex !== index) {
                        mainSwiper.slideToLoop(index);
                    }
                });

                thumbEl.querySelectorAll('.twpgy-swiper-slide').forEach(function (slide, index) {
                    slide.addEventListener('click', function () {
                        mainSwiper.slideToLoop(index);
                    });
                });

                mainSwiper.on('slideChange', function () {
                    var index = mainSwiper.realIndex;
                    if (thumbSwiper.realIndex !== index) {
                        thumbSwiper.slideTo(index);
                    }
                });
            }

            var waitSwiper = setInterval(function () {
                var mainReady  = document.querySelector('.themewant-product-gallery-preview-slider-container .twpgy-swiper')?.swiper;
                var thumbReady = document.querySelector('.twpgy-thumb-slider-container .twpgy-swiper')?.swiper;

                if (mainReady && thumbReady) {
                    clearInterval(waitSwiper);
                    twpgyFixThumbNavSync();
                }
            }, 200);

        },

        variationChange: function () {
            if (this.pluginOpts.gallery_for_variation !== 'on') return;

            $('.variations_form')
                .off('woocommerce_variation_has_changed')
                .on('woocommerce_variation_has_changed', (e) => {

                    const $form = $(e.currentTarget);
                    const variationId = parseInt(
                        $form.find('input.variation_id').val(),
                        10
                    );

                    const productId = parseInt($form.data('product_id'), 10);

                    if (!variationId) {
                        if (this.defaultGalleryHTML) {
                            this.$galleryArea.replaceWith($(this.defaultGalleryHTML));
                            this.cacheDOM();

                            this.enableGalleryCarousel();
                            this.enableZoom();
                            this.enableFancybox();
                            this.updateClassOnResize();
                        }
                        return;
                    }

                    $.ajax({
                        url: woocommerce_params.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'twpgy_get_product_gallery_html',
                            variation_id: variationId,
                            product_id: productId,
                            nonce: twpgywoglry_vars.nonce
                        },

                        success: (response) => {
                            if (!response) return;

                            this.$galleryArea.replaceWith(response);
                            this.cacheDOM();

                            this.enableGalleryCarousel();
                            this.enableZoom();
                            this.enableFancybox();
                            this.updateClassOnResize();
                        },

                        error: () => console.error('Failed to fetch variation images.')
                    });
                });
        },

        closeQuickViewPopup: function() {
            this.$galleryArea.hide();
        },

        updateClassOnResize: function () {
            if (!this.$galleryArea.length) return;

            const windowWidth = this.$window.width();

            const $wrapper = $('.twpgy-woo-gallery-main-wrapper');
            const pro = window.EASYQUICKVIEW_PRO || {};

            // Add-ons may register extra layout classes.
            const classes = [
                'layout-thumbs_left',
                'layout-thumbs_right',
                'layout-thumbs_top',
                'layout-thumbs_bottom',
                'layout-slider',
                'layout-carousel_two'
            ].concat( Array.isArray( pro.layoutClasses ) ? pro.layoutClasses : [] );

            // Layouts kept as-is on small screens instead of falling back to thumbs bottom.
            const twpgyCheckClass = [
                'layout-thumbs_top'
            ].concat( Array.isArray( pro.keepOnMobileLayouts ) ? pro.keepOnMobileLayouts : [] );

            if (!this.$galleryArea.data('original-layout')) {
                const originalClass = classes.find(cls =>
                    this.$galleryArea.hasClass(cls)
                ) || 'layout-thumbs_left';

                this.$galleryArea.data('original-layout', originalClass);
            }

            const originalLayout = this.$galleryArea.data('original-layout');

            const isThumbsTop = this.$galleryArea.hasClass('layout-thumbs_top');
            const hasEapgyClass = twpgyCheckClass.some(cls =>
                this.$galleryArea.hasClass(cls)
            );

            // Add-on layouts with their own responsive rules return true when handled.
            if (
                typeof pro.updateLayoutOnResize === 'function' &&
                pro.updateLayoutOnResize( this.$galleryArea, $wrapper, originalLayout, windowWidth, classes ) === true
            ) {
                return;
            }

            if ( windowWidth <= 992 ) {

                if (isThumbsTop || hasEapgyClass) return;

                this.$galleryArea
                    .removeClass(classes.join(' '))
                    .addClass('layout-thumbs_bottom');

                return;
            }

            this.$galleryArea
                .removeClass(classes.join(' '))
                .addClass(originalLayout);

           
        },

       

        /* ===============================
        360 VIEW
        =============================== */
        init360Nav: function ($viewer) {
            var self = this;
            var $images = $viewer.find('.twpgy-threed-view-images li');
            var total = $images.length;
            var index = $images.filter('.current-image').index();

            if (total <= 1) {
                $viewer.find('.twpgy-nav-bar').hide();
                return;
            }

            if (index < 0) {
                index = 0;
            }

            function showImage(i) {
                $images.removeClass('current-image active');
                $images.eq(i).addClass('current-image active');
                index = i;
            }

            function nextImage() {
                showImage((index + 1) % total);
            }

            function prevImage() {
                showImage((index - 1 + total) % total);
            }

            function startAutoplay() {
                stopAutoplay();
                self.autoplayTimer = setInterval(nextImage, self.autoplayDelay);
            }

            function stopAutoplay() {
                clearInterval(self.autoplayTimer);
                self.autoplayTimer = null;
            }

            $viewer.find('.twpgy-nav-bar-next')
                .off('click')
                .on('click', function (e) {
                    e.preventDefault();
                    stopAutoplay();
                    nextImage();
                });

            $viewer.find('.twpgy-nav-bar-previous')
                .off('click')
                .on('click', function (e) {
                    e.preventDefault();
                    stopAutoplay();
                    prevImage();
                });

            $viewer.find('.twpgy-nav-bar-play')
                .off('click')
                .on('click', function (e) {
                    e.preventDefault();

                    var $btn = $(this);

                    if ($btn.hasClass('playing')) {
                        stopAutoplay();
                        $btn.removeClass('playing').text('Play');
                    } else {
                        startAutoplay();
                        $btn.addClass('playing').text('Pause');
                    }
                });

        },

        generate360Images: function ($viewer) {
            var data = $viewer.data('eapgyargs');
            var $list;

            if (!data || !data.images) {
                return;
            }

            $list = $viewer.find('.twpgy-threed-view-images');

            if ($list.children().length) {
                return;
            }

            $.each(data.images, function (index, imgUrl) {
                var cls = index === 0 ? 'current-image active' : '';
                $list.append(
                    '<li class="' + cls + '">' +
                    '<img src="' + imgUrl + '" draggable="false">' +
                    '</li>'
                );
            });

            $viewer.find('.twpgy-spinner').fadeOut();
            this.init360Nav($viewer);
        },
        
        toggle360Button: function () {
            const $btn = $('.twpgy-product-360-button');
            const $popup = $('.twpgy-360-view-thumbnail');

            if ($('html').hasClass('with-fancybox')) {
                $btn.hide();
            } else {
                $btn.show();
            }

            $btn.off('click').on('click', function(e){

                $popup.addClass('loading');

                setTimeout(function(){
                    $popup.removeClass('loading');
                }, 2000 );
            });
        },

        observeHtmlClass: function () {
            var self = this;
            var observer = new MutationObserver(function () {
                self.toggle360Button();
            });

            observer.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });
        },
    };

    $(function () {
        EASYQUICKVIEW.init();
    });


})( jQuery, window, document );