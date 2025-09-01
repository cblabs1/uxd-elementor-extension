/**
 * UXD Elementor Extension - JavaScript
 */

(function($) {
    'use strict';

    var UXDElementorExtension = {
        
        init: function() {
            this.initCarousels();
            this.initProductGrid();
            this.initAddToCart();
            this.initGallery();
            this.initTaxonomyAccordion();
            this.bindEvents();
            this.handleEqualHeights();
        },

        initTaxonomyAccordion: function() {
            $('.uxd-taxonomy-accordion-widget').each(function() {
                var $accordion = $(this);
                
                // Skip if already initialized
                if ($accordion.hasClass('uxd-accordion-initialized')) {
                    return;
                }
                
                $accordion.addClass('uxd-accordion-initialized');
                UXDElementorExtension.setupTaxonomyAccordion($accordion);
            });
        },

        setupTaxonomyAccordion: function($accordion) {
            var accordionType = $accordion.data('accordion-type') || 'multiple';
            var animationSpeed = $accordion.data('animation-speed') || 300;
            
            // Set CSS transition duration
            $accordion.find('.uxd-taxonomy-children').css('transition-duration', animationSpeed + 'ms');
            
            // Initialize expanded items
            $accordion.find('.uxd-taxonomy-item.expanded').each(function() {
                var $item = $(this);
                var $children = $item.find('> .uxd-taxonomy-children');
                if ($children.length) {
                    UXDElementorExtension.setTaxonomyMaxHeight($children, true);
                }
            });
            
            // Bind accordion toggle events
            $accordion.on('click', '.uxd-accordion-toggle', function(e) {
                e.preventDefault();
                UXDElementorExtension.toggleTaxonomyAccordion($(this), accordionType, animationSpeed);
            });
            
            // Add keyboard navigation
            $accordion.on('keydown', '.uxd-accordion-toggle', function(e) {
                UXDElementorExtension.handleTaxonomyKeyboard(e, $(this), $accordion);
            });
            
            // Initialize ARIA attributes for accessibility
            UXDElementorExtension.initTaxonomyAccessibility($accordion);
        },

        toggleTaxonomyAccordion: function($trigger, accordionType, animationSpeed) {
            var $parentItem = $trigger.closest('.uxd-taxonomy-item');
            var $children = $parentItem.find('> .uxd-taxonomy-children');
            var $accordion = $trigger.closest('.uxd-taxonomy-accordion-widget');
            var isExpanded = $parentItem.hasClass('expanded');
            
            // Close other accordions if single mode
            if (accordionType === 'single' && !isExpanded) {
                $accordion.find('.uxd-taxonomy-item.expanded').not($parentItem).each(function() {
                    var $otherItem = $(this);
                    var $otherChildren = $otherItem.find('> .uxd-taxonomy-children');
                    UXDElementorExtension.collapseTaxonomyItem($otherItem, $otherChildren);
                });
            }
            
            // Toggle current accordion
            if (isExpanded) {
                UXDElementorExtension.collapseTaxonomyItem($parentItem, $children);
            } else {
                UXDElementorExtension.expandTaxonomyItem($parentItem, $children);
            }
            
            // Update ARIA attributes
            var newExpanded = !isExpanded;
            $trigger.attr('aria-expanded', newExpanded);
            $children.attr('aria-hidden', !newExpanded);
            
            // Trigger custom event
            var eventType = isExpanded ? 'uxd:accordion:collapsed' : 'uxd:accordion:expanded';
            $accordion.trigger(eventType, {
                item: $parentItem,
                termId: $trigger.data('term-id')
            });
        },

        expandTaxonomyItem: function($item, $children) {
            $item.addClass('expanded expanding').removeClass('collapsed');
            
            // Set max-height for animation
            UXDElementorExtension.setTaxonomyMaxHeight($children, true);
            
            // Clean up classes after animation
            var animationSpeed = $children.css('transition-duration');
            var speed = parseInt(animationSpeed) || 300;
            
            setTimeout(function() {
                $item.removeClass('expanding');
            }, speed);
        },

        collapseTaxonomyItem: function($item, $children) {
            $item.addClass('collapsing').removeClass('expanded');
            
            // Set max-height to 0 for animation
            UXDElementorExtension.setTaxonomyMaxHeight($children, false);
            
            // Clean up classes after animation
            var animationSpeed = $children.css('transition-duration');
            var speed = parseInt(animationSpeed) || 300;
            
            setTimeout(function() {
                $item.removeClass('collapsing').addClass('collapsed');
            }, speed);
        },

        setTaxonomyMaxHeight: function($children, expand) {
            if (expand) {
                // Calculate natural height
                var naturalHeight = UXDElementorExtension.calculateTaxonomyNaturalHeight($children);
                $children.css('max-height', naturalHeight + 'px');
            } else {
                $children.css('max-height', '0px');
            }
        },

        calculateTaxonomyNaturalHeight: function($children) {
            // Temporarily show the element to measure its natural height
            var originalMaxHeight = $children.css('max-height');
            var originalOverflow = $children.css('overflow');
            
            $children.css({
                'max-height': 'none',
                'overflow': 'visible'
            });
            
            var naturalHeight = $children[0].scrollHeight;
            
            // Restore original styles
            $children.css({
                'max-height': originalMaxHeight,
                'overflow': originalOverflow
            });
            
            return naturalHeight;
        },

        handleTaxonomyKeyboard: function(e, $trigger, $accordion) {
            var keyCode = e.which || e.keyCode;
            
            switch(keyCode) {
                case 13: // Enter
                case 32: // Space
                    e.preventDefault();
                    $trigger.click();
                    break;
                
                case 38: // Arrow Up
                    e.preventDefault();
                    UXDElementorExtension.focusPreviousTaxonomy($trigger, $accordion);
                    break;
                
                case 40: // Arrow Down
                    e.preventDefault();
                    UXDElementorExtension.focusNextTaxonomy($trigger, $accordion);
                    break;
                
                case 36: // Home
                    e.preventDefault();
                    $accordion.find('.uxd-accordion-toggle').first().focus();
                    break;
                
                case 35: // End
                    e.preventDefault();
                    $accordion.find('.uxd-accordion-toggle').last().focus();
                    break;
            }
        },

        focusPreviousTaxonomy: function($current, $accordion) {
            var $allToggles = $accordion.find('.uxd-accordion-toggle');
            var currentIndex = $allToggles.index($current);
            var $previous = $allToggles.eq(currentIndex - 1);
            
            if ($previous.length) {
                $previous.focus();
            } else {
                // Loop to last item
                $allToggles.last().focus();
            }
        },

        focusNextTaxonomy: function($current, $accordion) {
            var $allToggles = $accordion.find('.uxd-accordion-toggle');
            var currentIndex = $allToggles.index($current);
            var $next = $allToggles.eq(currentIndex + 1);
            
            if ($next.length) {
                $next.focus();
            } else {
                // Loop to first item
                $allToggles.first().focus();
            }
        },

        initTaxonomyAccessibility: function($accordion) {
            $accordion.find('.uxd-taxonomy-item.has-children').each(function(index) {
                var $item = $(this);
                var $toggle = $item.find('> .uxd-accordion-toggle');
                var $children = $item.find('> .uxd-taxonomy-children');
                var isExpanded = $item.hasClass('expanded');
                
                // Set ARIA attributes
                $toggle.attr({
                    'aria-expanded': isExpanded,
                    'aria-controls': 'uxd-taxonomy-children-' + index,
                    'role': 'button',
                    'tabindex': '0'
                });
                
                $children.attr({
                    'id': 'uxd-taxonomy-children-' + index,
                    'aria-hidden': !isExpanded
                });
            });
        },

        expandAllTaxonomyAccordions: function(accordionSelector) {
            $(accordionSelector || '.uxd-taxonomy-accordion-widget').each(function() {
                var $accordion = $(this);
                $accordion.find('.uxd-taxonomy-item.has-children').each(function() {
                    var $item = $(this);
                    var $children = $item.find('> .uxd-taxonomy-children');
                    if (!$item.hasClass('expanded')) {
                        UXDElementorExtension.expandTaxonomyItem($item, $children);
                    }
                });
            });
        },

        collapseAllTaxonomyAccordions: function(accordionSelector) {
            $(accordionSelector || '.uxd-taxonomy-accordion-widget').each(function() {
                var $accordion = $(this);
                $accordion.find('.uxd-taxonomy-item.has-children.expanded').each(function() {
                    var $item = $(this);
                    var $children = $item.find('> .uxd-taxonomy-children');
                    UXDElementorExtension.collapseTaxonomyItem($item, $children);
                });
            });
        },

        refreshTaxonomyAccordion: function(accordionId) {
            var $accordion = $('#' + accordionId);
            if ($accordion.length) {
                $accordion.removeClass('uxd-accordion-initialized');
                UXDElementorExtension.initTaxonomyAccordion();
            }
        },

        initGallery: function() {
            $('.uxd-gallery-wrapper').each(function() {
                var $gallery = $(this);
                UXDElementorExtension.setupGalleryEvents($gallery);
                UXDElementorExtension.initImageLoading($gallery);
            });
        },

        setupGalleryEvents: function($gallery) {
            var $items = $gallery.find('.uxd-gallery-item');
            var $searchInput = $gallery.find('.uxd-search-input');
            var $searchButton = $gallery.find('.uxd-search-button');
            var $searchClear = $gallery.find('.uxd-search-clear');
            var $noResults = $gallery.find('.uxd-gallery-no-results');
            
            // Lightbox functionality
            $items.on('click', function(e) {
                e.preventDefault();
                var index = $items.index(this);
                UXDElementorExtension.openLightbox($items, index);
            });
            
            // Search functionality
            if ($searchInput.length) {
                var searchTimeout;
                
                $searchInput.on('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        UXDElementorExtension.performSearch($gallery);
                    }, 300);
                });
                
                $searchButton.on('click', function() {
                    UXDElementorExtension.performSearch($gallery);
                });
                
                $searchInput.on('keypress', function(e) {
                    if (e.which === 13) { // Enter key
                        e.preventDefault();
                        UXDElementorExtension.performSearch($gallery);
                    }
                });
                
                $searchClear.on('click', function() {
                    $searchInput.val('');
                    $searchClear.hide();
                    UXDElementorExtension.clearSearch($gallery);
                });
            }
        },

        initImageLoading: function($gallery) {
            $gallery.find('.uxd-gallery-item img').each(function() {
                var $img = $(this);
                var $item = $img.closest('.uxd-gallery-item');
                var $loader = $item.find('.uxd-image-loader');
                
                $img.on('load', function() {
                    $img.css('opacity', '1');
                    $loader.fadeOut(300);
                }).on('error', function() {
                    $loader.html('<div style="color: #dc3545;">Failed to load image</div>');
                });
                
                // If image is already cached
                if (this.complete) {
                    $img.trigger('load');
                }
            });
        },

        performSearch: function($gallery) {
            var $searchInput = $gallery.find('.uxd-search-input');
            var $searchClear = $gallery.find('.uxd-search-clear');
            var $items = $gallery.find('.uxd-gallery-item');
            var $noResults = $gallery.find('.uxd-gallery-no-results');
            var searchTerm = $searchInput.val().toLowerCase().trim();
            var searchFields = $searchInput.data('search-fields').split(',');
            
            if (searchTerm === '') {
                UXDElementorExtension.clearSearch($gallery);
                return;
            }
            
            $searchClear.show();
            var hasResults = false;
            
            $items.each(function() {
                var $item = $(this);
                var searchData = $item.data('search');
                var isMatch = false;
                
                // Search in specified fields
                searchFields.forEach(function(field) {
                    if (searchData[field] && searchData[field].toLowerCase().indexOf(searchTerm) !== -1) {
                        isMatch = true;
                    }
                });
                
                if (isMatch) {
                    $item.show();
                    hasResults = true;
                } else {
                    $item.hide();
                }
            });
            
            if (hasResults) {
                $noResults.hide();
            } else {
                $noResults.show();
            }
        },

        clearSearch: function($gallery) {
            var $items = $gallery.find('.uxd-gallery-item');
            var $noResults = $gallery.find('.uxd-gallery-no-results');
            var $searchClear = $gallery.find('.uxd-search-clear');
            
            $items.show();
            $noResults.hide();
            $searchClear.hide();
        },

        openLightbox: function($items, startIndex) {
            var $lightbox = $('.uxd-lightbox-overlay');
            var $lightboxImage = $lightbox.find('.uxd-lightbox-image');
            var $lightboxCaption = $lightbox.find('.uxd-lightbox-caption');
            var $currentCounter = $lightbox.find('.uxd-current');
            var $totalCounter = $lightbox.find('.uxd-total');
            var $prevBtn = $lightbox.find('.uxd-lightbox-prev');
            var $nextBtn = $lightbox.find('.uxd-lightbox-next');
            var $closeBtn = $lightbox.find('.uxd-lightbox-close');
            
            var currentIndex = startIndex;
            var visibleItems = $items.filter(':visible');
            var totalItems = visibleItems.length;
            
            // Store original scroll position
            var originalScrollTop = $(window).scrollTop();
            
            function updateLightbox() {
                var $currentItem = $(visibleItems[currentIndex]);
                var $img = $currentItem.find('img');
                var fullSrc = $img.data('full-src');
                var caption = $img.data('caption') || $img.data('title') || '';
                
                $lightboxImage.attr('src', fullSrc);
                $lightboxImage.attr('alt', $img.attr('alt'));
                $lightboxCaption.text(caption);
                $currentCounter.text(currentIndex + 1);
                $totalCounter.text(totalItems);
                
                // Show/hide navigation buttons
                $prevBtn.toggle(totalItems > 1);
                $nextBtn.toggle(totalItems > 1);
            }
            
            function showLightbox() {
                // Prevent body scroll and store current position
                $('body').css({
                    'position': 'fixed',
                    'top': -originalScrollTop + 'px',
                    'width': '100%',
                    'overflow': 'hidden'
                }).addClass('uxd-lightbox-open');
                
                $lightbox.show();
                setTimeout(function() {
                    $lightbox.addClass('show');
                }, 10);
            }
            
            function hideLightbox() {
                $lightbox.removeClass('show');
                
                // Remove event listeners
                $(document).off('keydown.uxd-lightbox');
                $closeBtn.off('click.uxd-lightbox');
                $nextBtn.off('click.uxd-lightbox');
                $prevBtn.off('click.uxd-lightbox');
                $lightbox.off('click.uxd-lightbox');
                
                setTimeout(function() {
                    $lightbox.hide();
                    
                    // Restore body scroll
                    $('body').removeClass('uxd-lightbox-open').css({
                        'position': '',
                        'top': '',
                        'width': '',
                        'overflow': ''
                    });
                    
                    // Restore scroll position
                    $(window).scrollTop(originalScrollTop);
                }, 300);
            }
            
            function nextImage() {
                currentIndex = (currentIndex + 1) % totalItems;
                updateLightbox();
            }
            
            function prevImage() {
                currentIndex = (currentIndex - 1 + totalItems) % totalItems;
                updateLightbox();
            }
            
            // Update current index for visible items only
            currentIndex = visibleItems.index($items.eq(startIndex));
            if (currentIndex === -1) currentIndex = 0;
            
            updateLightbox();
            showLightbox();
            
            // Event handlers with namespaced events
            $closeBtn.on('click.uxd-lightbox', hideLightbox);
            $nextBtn.on('click.uxd-lightbox', nextImage);
            $prevBtn.on('click.uxd-lightbox', prevImage);
            
            $lightbox.on('click.uxd-lightbox', function(e) {
                if (e.target === this) {
                    hideLightbox();
                }
            });
            
            // Keyboard navigation
            $(document).on('keydown.uxd-lightbox', function(e) {
                switch(e.keyCode) {
                    case 27: // ESC
                        hideLightbox();
                        break;
                    case 37: // Left arrow
                        if (totalItems > 1) prevImage();
                        break;
                    case 39: // Right arrow
                        if (totalItems > 1) nextImage();
                        break;
                }
            });
        },

        initCarousels: function() {
            $('.uxd-product-carousel-wrapper .swiper').each(function() {
                var $carousel = $(this);
                var carouselId = $carousel.attr('id');
                
                // Skip if already initialized
                if ($carousel.hasClass('swiper-initialized')) {
                    return;
                }

                // Get settings from data attributes
                var settings = $carousel.data('settings') || {};
                
                var swiperConfig = {
                    slidesPerView: settings.slidesPerView || 4,
                    slidesPerGroup: settings.slidesPerGroup || 1,
                    spaceBetween: settings.spaceBetween || 30,
                    loop: settings.loop !== false,
                    speed: settings.speed || 800,
                    grabCursor: true,
                    watchOverflow: true,
                    autoplay: false,
                    navigation: false,
                    pagination: false,
                    breakpoints: {
                        320: {
                            slidesPerView: 1,
                            slidesPerGroup: 1,
                            spaceBetween: 10
                        },
                        480: {
                            slidesPerView: 1,
                            slidesPerGroup: 1,
                            spaceBetween: 15
                        },
                        768: {
                            slidesPerView: 2,
                            slidesPerGroup: 1,
                            spaceBetween: 20
                        },
                        1024: {
                            slidesPerView: Math.min(3, settings.slidesPerView || 4),
                            slidesPerGroup: 1,
                            spaceBetween: 25
                        },
                        1200: {
                            slidesPerView: settings.slidesPerView || 4,
                            slidesPerGroup: settings.slidesPerGroup || 1,
                            spaceBetween: settings.spaceBetween || 30
                        }
                    },
                    on: {
                        init: function() {
                            UXDElementorExtension.equalizeHeights($carousel);
                        },
                        slideChange: function() {
                            UXDElementorExtension.equalizeHeights($carousel);
                        },
                        resize: function() {
                            UXDElementorExtension.equalizeHeights($carousel);
                        }
                    }
                };

                // Add autoplay if enabled
                if (settings.autoplay) {
                    swiperConfig.autoplay = {
                        delay: settings.autoplayDelay || 3000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: settings.pauseOnHover !== false
                    };
                }

                // Add navigation if enabled
                if (settings.navigation) {
                    swiperConfig.navigation = {
                        nextEl: '#' + carouselId + ' .swiper-button-next',
                        prevEl: '#' + carouselId + ' .swiper-button-prev',
                    };
                }

                // Add pagination if enabled
                if (settings.pagination) {
                    swiperConfig.pagination = {
                        el: '#' + carouselId + ' .swiper-pagination',
                        clickable: true,
                        dynamicBullets: true
                    };
                }

                // Initialize Swiper
                var swiper = new Swiper($carousel[0], swiperConfig);

                // Store swiper instance
                $carousel.data('swiper', swiper);

                // Pause autoplay on hover if enabled
                if (settings.autoplay && settings.pauseOnHover) {
                    $carousel.on('mouseenter', function() {
                        swiper.autoplay.stop();
                    }).on('mouseleave', function() {
                        swiper.autoplay.start();
                    });
                }
            });
        },

        initProductGrid: function() {
            $('.uxd-products-grid').each(function() {
                var $grid = $(this);
                UXDElementorExtension.equalizeHeights($grid);
            });
        },

        handleEqualHeights: function() {
            // Handle equal heights for grids with the class
            $('.uxd-products-grid.uxd-equal-height').each(function() {
                UXDElementorExtension.equalizeHeights($(this));
            });
        },

        equalizeHeights: function($container) {
            var $items = $container.find('.uxd-product-item');
            
            // Only equalize if container has equal height class or is carousel
            if (!$container.hasClass('uxd-equal-height') && !$container.closest('.uxd-product-carousel-wrapper').length) {
                return;
            }
            
            var maxHeight = 0;

            // Reset heights
            $items.css('height', 'auto');

            // Find max height
            $items.each(function() {
                var height = $(this).outerHeight();
                if (height > maxHeight) {
                    maxHeight = height;
                }
            });

            // Set all items to max height only if there are multiple items
            if ($items.length > 1 && maxHeight > 0) {
                $items.css('height', maxHeight + 'px');
            }
        },

        initAddToCart: function() {
            $(document).on('click', '.uxd-add-to-cart .ajax_add_to_cart', function(e) {
                e.preventDefault();
                
                var $button = $(this);
                var productId = $button.data('product_id');
                var productSku = $button.data('product_sku');
                var quantity = $button.data('quantity') || 1;
                var originalText = $button.text();

                if (!productId) {
                    return;
                }

                // Add loading state
                $button.addClass('loading');
                $button.prop('disabled', true);
                $button.text(uxd_ajax_object.loading_text || 'Loading...');

                // AJAX request
                $.ajax({
                    type: 'POST',
                    url: uxd_ajax_object.ajax_url || wc_add_to_cart_params.ajax_url,
                    data: {
                        action: 'woocommerce_add_to_cart',
                        product_id: productId,
                        product_sku: productSku,
                        quantity: quantity,
                        nonce: uxd_ajax_object.nonce
                    },
                    success: function(response) {
                        if (response.error && response.product_url) {
                            window.location = response.product_url;
                            return;
                        }

                        // Update cart fragments
                        if (response.fragments) {
                            $.each(response.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }

                        // Add success state
                        $button.removeClass('loading').addClass('added');
                        $button.text(uxd_ajax_object.added_to_cart_text || 'Added to cart');

                        // Trigger added to cart event
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);

                        // Show notification
                        UXDElementorExtension.showNotification('Product added to cart successfully!', 'success');

                        // Reset button after delay
                        setTimeout(function() {
                            $button.removeClass('added');
                            $button.text(originalText);
                        }, 2000);
                    },
                    error: function() {
                        UXDElementorExtension.showNotification('Error adding product to cart. Please try again.', 'error');
                    },
                    complete: function() {
                        $button.removeClass('loading');
                        $button.prop('disabled', false);
                        if (!$button.hasClass('added')) {
                            $button.text(originalText);
                        }
                    }
                });
            });
        },

        showNotification: function(message, type) {
            type = type || 'info';
            
            var $notification = $('<div class="uxd-notification uxd-notification-' + type + '">' + message + '</div>');
            
            $('body').append($notification);
            
            // Show notification
            setTimeout(function() {
                $notification.addClass('show');
            }, 100);
            
            // Hide notification after 3 seconds
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 3000);
        },

        bindEvents: function() {
            // Reinitialize on Elementor preview
            if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/uxd-product-carousel.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initCarousels();
                    }, 100);
                });

                elementorFrontend.hooks.addAction('frontend/element_ready/uxd-product-grid.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initProductGrid();
                    }, 100);
                });
                
                elementorFrontend.hooks.addAction('frontend/element_ready/uxd-gallery-grid.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initGallery();
                    }, 100);
                });

                elementorFrontend.hooks.addAction('frontend/element_ready/uxd_taxonomy_accordion.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initTaxonomyAccordion();
                    }, 100);
                });
            }

            // Alternative approach for non-Elementor pages or when hooks aren't available
            $(window).on('load', function() {
                setTimeout(function() {
                    UXDElementorExtension.initCarousels();
                    UXDElementorExtension.initProductGrid();
                    UXDElementorExtension.initGallery();
                    UXDElementorExtension.initTaxonomyAccordion();
                }, 100);
            });

            // Reinitialize on window resize
            var resizeTimer;
            $(window).on('resize', UXDElementorExtension.debounce(function() {
                $('.uxd-products-grid').each(function() {
                    UXDElementorExtension.equalizeHeights($(this));
                });
                
                $('.uxd-product-carousel-wrapper .swiper').each(function() {
                    var swiper = $(this).data('swiper');
                    if (swiper && typeof swiper.update === 'function') {
                        swiper.update();
                        setTimeout(function() {
                            UXDElementorExtension.equalizeHeights($(this));
                        }.bind(this), 100);
                    }
                });

                // Handle taxonomy accordion responsive behavior
                $('.uxd-taxonomy-accordion-widget .uxd-taxonomy-item.expanded').each(function() {
                    var $item = $(this);
                    var $children = $item.find('> .uxd-taxonomy-children');
                    // Recalculate heights on resize
                    setTimeout(function() {
                        UXDElementorExtension.setTaxonomyMaxHeight($children, true);
                    }, 100);
                });
            }, 250));

            // Handle image loading
            $(document).on('load', '.uxd-product-image img', function() {
                var $container = $(this).closest('.uxd-products-grid, .uxd-product-carousel-wrapper');
                if ($container.length) {
                    setTimeout(function() {
                        UXDElementorExtension.equalizeHeights($container);
                    }, 100);
                }
            });

            // Store original button text on first hover
            $(document).on('mouseenter', '.uxd-add-to-cart .button', function() {
                var $button = $(this);
                if (!$button.data('original-text') && !$button.hasClass('loading')) {
                    $button.data('original-text', $button.text());
                }
            });

            // Quick view functionality
            $(document).on('click', '.uxd-quick-view', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                UXDElementorExtension.openQuickView(productId);
            });

            // Wishlist functionality
            $(document).on('click', '.uxd-wishlist', function(e) {
                e.preventDefault();
                var $button = $(this);
                var productId = $button.data('product-id');
                UXDElementorExtension.toggleWishlist(productId, $button);
            });

            // Handle tab visibility change to resume/pause carousels
            $(document).on('visibilitychange', function() {
                $('.uxd-product-carousel-wrapper .swiper').each(function() {
                    var swiper = $(this).data('swiper');
                    if (swiper && swiper.autoplay) {
                        if (document.hidden) {
                            swiper.autoplay.stop();
                        } else {
                            swiper.autoplay.start();
                        }
                    }
                });
            });
        },

        openQuickView: function(productId) {
            // Show loading
            UXDElementorExtension.showNotification('Loading product details...', 'info');
            
            // AJAX request to get product details
            $.ajax({
                type: 'POST',
                url: uxd_ajax_object.ajax_url || ajaxurl,
                data: {
                    action: 'uxd_get_product_quick_view',
                    product_id: productId,
                    nonce: uxd_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        UXDElementorExtension.showQuickViewModal(response.data);
                    } else {
                        UXDElementorExtension.showNotification('Error loading product details.', 'error');
                    }
                },
                error: function() {
                    UXDElementorExtension.showNotification('Error loading product details.', 'error');
                }
            });
        },

        showQuickViewModal: function(productData) {
            // Create modal HTML
            var modalHtml = `
                <div class="uxd-quick-view-overlay">
                    <div class="uxd-quick-view-modal">
                        <button class="uxd-quick-view-close">&times;</button>
                        <div class="uxd-quick-view-content">
                            <div class="uxd-quick-view-image">
                                <img src="${productData.image}" alt="${productData.title}">
                            </div>
                            <div class="uxd-quick-view-details">
                                <h2>${productData.title}</h2>
                                <div class="uxd-quick-view-price">${productData.price}</div>
                                <div class="uxd-quick-view-rating">${productData.rating}</div>
                                <div class="uxd-quick-view-excerpt">${productData.excerpt}</div>
                                <div class="uxd-quick-view-actions">
                                    <a href="${productData.permalink}" class="uxd-view-product-btn">View Full Product</a>
                                    ${productData.add_to_cart_button}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            $('body').append(modalHtml);
            
            // Show modal
            setTimeout(function() {
                $('.uxd-quick-view-overlay').addClass('show');
            }, 50);
            
            // Close modal events
            $(document).on('click', '.uxd-quick-view-close, .uxd-quick-view-overlay', function(e) {
                if (e.target === this) {
                    $('.uxd-quick-view-overlay').removeClass('show');
                    setTimeout(function() {
                        $('.uxd-quick-view-overlay').remove();
                    }, 300);
                }
            });
            
            // Prevent modal content click from closing
            $(document).on('click', '.uxd-quick-view-modal', function(e) {
                e.stopPropagation();
            });
            
            // ESC key to close
            $(document).on('keyup', function(e) {
                if (e.keyCode === 27) { // ESC key
                    $('.uxd-quick-view-close').click();
                }
            });
        },

        toggleWishlist: function(productId, $button) {
            var isActive = $button.hasClass('active');
            var action = isActive ? 'remove' : 'add';
            
            // Add loading state
            $button.prop('disabled', true);
            
            // AJAX request
            $.ajax({
                type: 'POST',
                url: uxd_ajax_object.ajax_url || ajaxurl,
                data: {
                    action: 'uxd_toggle_wishlist',
                    product_id: productId,
                    wishlist_action: action,
                    nonce: uxd_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $button.toggleClass('active');
                        var message = action === 'add' ? 'Added to wishlist!' : 'Removed from wishlist!';
                        var type = action === 'add' ? 'success' : 'info';
                        UXDElementorExtension.showNotification(message, type);
                        
                        // Update button title
                        var newTitle = action === 'add' ? 'Remove from Wishlist' : 'Add to Wishlist';
                        $button.attr('title', newTitle);
                    } else {
                        UXDElementorExtension.showNotification(response.data || 'Error updating wishlist.', 'error');
                    }
                },
                error: function() {
                    UXDElementorExtension.showNotification('Error updating wishlist.', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        },

        // Utility function to debounce events
        debounce: function(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        // Utility function to throttle events
        throttle: function(func, limit) {
            var inThrottle;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() {
                        inThrottle = false;
                    }, limit);
                }
            };
        },

        // Reinitialize specific carousel
        reinitCarousel: function(carouselId) {
            var $carousel = $('#' + carouselId);
            if ($carousel.length) {
                var swiper = $carousel.data('swiper');
                if (swiper) {
                    swiper.destroy(true, true);
                }
                setTimeout(function() {
                    UXDElementorExtension.initCarousels();
                }, 100);
            }
        },

        // Destroy all carousels
        destroyCarousels: function() {
            $('.uxd-product-carousel-wrapper .swiper').each(function() {
                var swiper = $(this).data('swiper');
                if (swiper && typeof swiper.destroy === 'function') {
                    swiper.destroy(true, true);
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        UXDElementorExtension.init();
    });

    // Also initialize when images are loaded
    $(window).on('load', function() {
        setTimeout(function() {
            $('.uxd-products-grid, .uxd-product-carousel-wrapper, .uxd-gallery-wrapper, .uxd-taxonomy-accordion-widget').each(function() {
                if ($(this).hasClass('uxd-taxonomy-accordion-widget')) {
                    UXDElementorExtension.initTaxonomyAccordion();
                } else {
                    UXDElementorExtension.equalizeHeights($(this));
                }
            });
        }, 100);
    });

    // Reinitialize when Elementor preview is refreshed
    $(window).on('elementor/frontend/init', function() {
        setTimeout(function() {
            UXDElementorExtension.init();
        }, 500);
    });

    // Additional check for Elementor hooks after frontend is ready
    if (typeof elementorFrontend !== 'undefined') {
        $(window).on('elementor/frontend/init', function() {
            if (elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/uxd-product-carousel.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initCarousels();
                    }, 100);
                });

                elementorFrontend.hooks.addAction('frontend/element_ready/uxd-product-grid.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initProductGrid();
                    }, 100);
                });
                
                elementorFrontend.hooks.addAction('frontend/element_ready/uxd-gallery-grid.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initGallery();
                    }, 100);
                });

                elementorFrontend.hooks.addAction('frontend/element_ready/uxd_taxonomy_accordion.default', function($scope) {
                    setTimeout(function() {
                        UXDElementorExtension.initTaxonomyAccordion();
                    }, 100);
                });
            }
        });
    }

    // Make UXDElementorExtension globally accessible for external use
    window.UXDElementorExtension = UXDElementorExtension;

})(jQuery);