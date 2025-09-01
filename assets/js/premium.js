/**
 * UXD Premium JavaScript
 */

(function($) {
    'use strict';

    var UXDPremium = {
        
        init: function() {
            this.initAdvancedFilters();
            this.initPremiumAnimations();
            this.initPremiumCarousels();
            this.initMasonryLayouts();
            this.initAdvancedInteractions();
            this.bindPremiumEvents();
        },

        initAdvancedFilters: function() {
            $('.uxd-advanced-filter').each(function() {
                var $filter = $(this);
                UXDPremium.setupAdvancedFilter($filter);
            });
        },

        setupAdvancedFilter: function($filter) {
            var $searchInput = $filter.find('.uxd-filter-search input');
            var $priceSlider = $filter.find('.uxd-price-slider');
            var $categoryCheckboxes = $filter.find('.uxd-filter-categories input');
            var $ratingOptions = $filter.find('.uxd-filter-rating input');
            
            var filterTimeout;
            
            // Search filter
            if ($searchInput.length) {
                $searchInput.on('input', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() {
                        UXDPremium.applyFilters($filter);
                    }, 300);
                });
            }
            
            // Price range slider
            if ($priceSlider.length) {
                UXDPremium.initPriceSlider($priceSlider);
            }
            
            // Category and rating filters
            $categoryCheckboxes.add($ratingOptions).on('change', function() {
                UXDPremium.applyFilters($filter);
            });
        },

        initPriceSlider: function($slider) {
            // Initialize price range slider (would use a library like noUiSlider)
            var minPrice = parseInt($slider.data('min-price')) || 0;
            var maxPrice = parseInt($slider.data('max-price')) || 1000;
            
            $slider.html(`
                <div class="uxd-price-range-container">
                    <input type="range" class="uxd-min-price" min="${minPrice}" max="${maxPrice}" value="${minPrice}">
                    <input type="range" class="uxd-max-price" min="${minPrice}" max="${maxPrice}" value="${maxPrice}">
                </div>
                <div class="uxd-price-range">
                    <span class="uxd-min-price-display">$${minPrice}</span>
                    <span class="uxd-max-price-display">$${maxPrice}</span>
                </div>
            `);
            
            var $minRange = $slider.find('.uxd-min-price');
            var $maxRange = $slider.find('.uxd-max-price');
            var $minDisplay = $slider.find('.uxd-min-price-display');
            var $maxDisplay = $slider.find('.uxd-max-price-display');
            
            function updatePriceDisplay() {
                var minVal = parseInt($minRange.val());
                var maxVal = parseInt($maxRange.val());
                
                if (minVal > maxVal - 10) {
                    minVal = maxVal - 10;
                    $minRange.val(minVal);
                }
                
                if (maxVal < minVal + 10) {
                    maxVal = minVal + 10;
                    $maxRange.val(maxVal);
                }
                
                $minDisplay.text('$' + minVal);
                $maxDisplay.text('$' + maxVal);
            }
            
            $minRange.add($maxRange).on('input change', function() {
                updatePriceDisplay();
                UXDPremium.applyFilters($slider.closest('.uxd-advanced-filter'));
            });
        },

        applyFilters: function($filter) {
            var filterData = UXDPremium.collectFilterData($filter);
            var $targetGrid = $('.uxd-products-grid').first(); // Find target grid
            
            if (!$targetGrid.length) return;
            
            // Show loading state
            $targetGrid.addClass('uxd-filtering');
            
            // AJAX call to filter products
            $.ajax({
                url: uxd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'uxd_filter_products',
                    filters: filterData,
                    nonce: uxd_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $targetGrid.html(response.data.html);
                        UXDPremium.initPremiumAnimations();
                        
                        // Update result count
                        $('.uxd-results-count').text(response.data.count + ' products found');
                    }
                },
                complete: function() {
                    $targetGrid.removeClass('uxd-filtering');
                }
            });
        },

        collectFilterData: function($filter) {
            var data = {};
            
            // Search term
            var searchTerm = $filter.find('.uxd-filter-search input').val();
            if (searchTerm) {
                data.search = searchTerm;
            }
            
            // Price range
            var minPrice = $filter.find('.uxd-min-price').val();
            var maxPrice = $filter.find('.uxd-max-price').val();
            if (minPrice || maxPrice) {
                data.price = {
                    min: minPrice,
                    max: maxPrice
                };
            }
            
            // Categories
            var categories = [];
            $filter.find('.uxd-filter-categories input:checked').each(function() {
                categories.push($(this).val());
            });
            if (categories.length) {
                data.categories = categories;
            }
            
            // Rating
            var rating = $filter.find('.uxd-filter-rating input:checked').val();
            if (rating) {
                data.rating = rating;
            }
            
            return data;
        },

        initPremiumAnimations: function() {
            // Intersection Observer for scroll animations
            if ('IntersectionObserver' in window) {
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var $element = $(entry.target);
                            $element.addClass('uxd-animate-in');
                            
                            // Stagger animations for grid items
                            if ($element.hasClass('uxd-product-item')) {
                                var index = $element.index();
                                $element.css('--animation-order', index);
                            }
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });
                
                $('.uxd-product-item, .uxd-gallery-item').each(function() {
                    observer.observe(this);
                });
            }
            
            // Premium loading animations
            $('.uxd-premium-loading').each(function() {
                var $element = $(this);
                setTimeout(function() {
                    $element.removeClass('uxd-premium-loading');
                }, Math.random() * 2000 + 1000);
            });
        },

        initPremiumCarousels: function() {
            $('.uxd-premium-carousel').each(function() {
                var $carousel = $(this);
                var settings = $carousel.data('premium-settings') || {};
                
                // Enhanced Swiper configuration with premium features
                var swiperConfig = $.extend({
                    effect: settings.effect || 'slide',
                    parallax: true,
                    lazy: true,
                    keyboard: {
                        enabled: true,
                        onlyInViewport: true,
                    },
                    mousewheel: {
                        enabled: settings.mousewheel || false,
                    },
                    virtual: settings.virtualSlides || false,
                    freeMode: settings.freeMode || false,
                    thumbs: settings.thumbs ? {
                        swiper: new Swiper(settings.thumbs.selector, settings.thumbs.options)
                    } : undefined
                }, settings);
                
                var swiper = new Swiper($carousel[0], swiperConfig);
                
                // Store premium swiper instance
                $carousel.data('premium-swiper', swiper);
            });
        },

        initMasonryLayouts: function() {
            $('.uxd-premium-masonry').each(function() {
                var $masonry = $(this);
                UXDPremium.setupMasonry($masonry);
            });
            
            // Reinitialize on window resize
            var resizeTimeout;
            $(window).on('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    $('.uxd-premium-masonry').each(function() {
                        UXDPremium.setupMasonry($(this));
                    });
                }, 250);
            });
        },

        setupMasonry: function($container) {
            // Simple masonry implementation using CSS columns
            var items = $container.find('.uxd-product-item, .uxd-gallery-item');
            var columns = parseInt($container.css('column-count')) || 3;
            
            // Balance items across columns
            items.each(function(index) {
                var $item = $(this);
                var column = index % columns;
                $item.css('break-inside', 'avoid');
            });
            
            // Force redraw
            $container.hide().show(0);
        },

        initAdvancedInteractions: function() {
            // Premium hover effects
            $('.uxd-premium-hover').on('mouseenter', function() {
                $(this).addClass('uxd-premium-hover-active');
            }).on('mouseleave', function() {
                $(this).removeClass('uxd-premium-hover-active');
            });
            
            // Premium tooltips
            $('.uxd-premium-tooltip').on('mouseenter', function() {
                var $tooltip = $(this);
                var tooltipText = $tooltip.data('tooltip');
                
                if (!tooltipText) return;
                
                var $tooltipElement = $('<div class="uxd-premium-tooltip-content">' + tooltipText + '</div>');
                $('body').append($tooltipElement);
                
                var position = $tooltip.offset();
                var tooltipWidth = $tooltipElement.outerWidth();
                var tooltipHeight = $tooltipElement.outerHeight();
                
                $tooltipElement.css({
                    position: 'absolute',
                    top: position.top - tooltipHeight - 10,
                    left: position.left + ($tooltip.outerWidth() / 2) - (tooltipWidth / 2),
                    zIndex: 9999
                });
                
                $tooltipElement.fadeIn(200);
                $tooltip.data('tooltip-element', $tooltipElement);
                
            }).on('mouseleave', function() {
                var $tooltipElement = $(this).data('tooltip-element');
                if ($tooltipElement) {
                    $tooltipElement.fadeOut(200, function() {
                        $(this).remove();
                    });
                }
            });
            
            // Premium progress bars
            $('.uxd-premium-progress-bar').each(function() {
                var $bar = $(this);
                var targetWidth = $bar.data('width') || '75%';
                
                setTimeout(function() {
                    $bar.css('width', targetWidth);
                }, 500);
            });
            
            // Advanced product quick actions
            $('.uxd-product-quick-actions').on('click', '.uxd-quick-add-to-cart', function(e) {
                e.preventDefault();
                UXDPremium.handleQuickAddToCart($(this));
            });
            
            $('.uxd-product-quick-actions').on('click', '.uxd-quick-compare', function(e) {
                e.preventDefault();
                UXDPremium.handleQuickCompare($(this));
            });
        },

        handleQuickAddToCart: function($button) {
            var productId = $button.data('product-id');
            var $product = $button.closest('.uxd-product-item');
            
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: uxd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'uxd_quick_add_to_cart',
                    product_id: productId,
                    nonce: uxd_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $button.addClass('added').text('Added!');
                        $product.addClass('uxd-product-added');
                        
                        // Show success animation
                        UXDPremium.showSuccessAnimation($product);
                        
                        // Update cart count if available
                        $('.cart-count').text(response.data.cart_count);
                        
                        setTimeout(function() {
                            $button.removeClass('added').text('Add to Cart');
                        }, 3000);
                    } else {
                        UXDPremium.showError(response.data || 'Error adding to cart');
                    }
                },
                error: function() {
                    UXDPremium.showError('Network error occurred');
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        handleQuickCompare: function($button) {
            var productId = $button.data('product-id');
            var compareList = UXDPremium.getCompareList();
            
            if (compareList.indexOf(productId) !== -1) {
                // Remove from compare
                UXDPremium.removeFromCompare(productId);
                $button.removeClass('active').attr('title', 'Add to Compare');
            } else {
                // Add to compare
                if (compareList.length >= 4) {
                    UXDPremium.showError('You can compare maximum 4 products');
                    return;
                }
                
                UXDPremium.addToCompare(productId);
                $button.addClass('active').attr('title', 'Remove from Compare');
            }
            
            UXDPremium.updateCompareCount();
        },

        getCompareList: function() {
            var compareList = localStorage.getItem('uxd_compare_list');
            return compareList ? JSON.parse(compareList) : [];
        },

        addToCompare: function(productId) {
            var compareList = this.getCompareList();
            compareList.push(productId);
            localStorage.setItem('uxd_compare_list', JSON.stringify(compareList));
        },

        removeFromCompare: function(productId) {
            var compareList = this.getCompareList();
            var index = compareList.indexOf(productId);
            if (index > -1) {
                compareList.splice(index, 1);
                localStorage.setItem('uxd_compare_list', JSON.stringify(compareList));
            }
        },

        updateCompareCount: function() {
            var count = this.getCompareList().length;
            $('.uxd-compare-count').text(count);
            
            if (count > 0) {
                $('.uxd-compare-widget').addClass('has-products');
            } else {
                $('.uxd-compare-widget').removeClass('has-products');
            }
        },

        showSuccessAnimation: function($element) {
            $element.addClass('uxd-success-flash');
            setTimeout(function() {
                $element.removeClass('uxd-success-flash');
            }, 1000);
        },

        showError: function(message) {
            var $error = $('<div class="uxd-premium-error">' + message + '</div>');
            $('body').append($error);
            
            $error.fadeIn(300);
            
            setTimeout(function() {
                $error.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        },

        initParallaxEffects: function() {
            var $parallaxElements = $('.uxd-parallax');
            
            if ($parallaxElements.length === 0) return;
            
            $(window).on('scroll', UXDPremium.throttle(function() {
                var scrollTop = $(window).scrollTop();
                var windowHeight = $(window).height();
                
                $parallaxElements.each(function() {
                    var $element = $(this);
                    var elementTop = $element.offset().top;
                    var elementHeight = $element.outerHeight();
                    var speed = $element.data('parallax-speed') || 0.5;
                    
                    // Check if element is in viewport
                    if (elementTop + elementHeight >= scrollTop && elementTop <= scrollTop + windowHeight) {
                        var yPos = -(scrollTop - elementTop) * speed;
                        $element.css('transform', 'translate3d(0, ' + yPos + 'px, 0)');
                    }
                });
            }, 16));
        },

        initInfiniteScroll: function() {
            var $container = $('.uxd-infinite-scroll');
            var $loadMore = $('.uxd-load-more');
            
            if ($container.length === 0) return;
            
            var loading = false;
            var page = 2;
            
            function loadMoreProducts() {
                if (loading) return;
                
                loading = true;
                $loadMore.addClass('loading');
                
                $.ajax({
                    url: uxd_ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'uxd_load_more_products',
                        page: page,
                        nonce: uxd_ajax_object.nonce
                    },
                    success: function(response) {
                        if (response.success && response.data.html) {
                            var $newItems = $(response.data.html);
                            $container.append($newItems);
                            
                            // Animate new items
                            $newItems.addClass('uxd-fade-in');
                            
                            page++;
                            
                            if (!response.data.has_more) {
                                $loadMore.hide();
                            }
                        } else {
                            $loadMore.hide();
                        }
                    },
                    error: function() {
                        UXDPremium.showError('Failed to load more products');
                    },
                    complete: function() {
                        loading = false;
                        $loadMore.removeClass('loading');
                    }
                });
            }
            
            // Auto-load on scroll
            $(window).on('scroll', UXDPremium.throttle(function() {
                if ($loadMore.length === 0 || loading) return;
                
                var scrollTop = $(window).scrollTop();
                var windowHeight = $(window).height();
                var loadMoreTop = $loadMore.offset().top;
                
                if (scrollTop + windowHeight >= loadMoreTop - 200) {
                    loadMoreProducts();
                }
            }, 250));
            
            // Manual load more button
            $loadMore.on('click', function(e) {
                e.preventDefault();
                loadMoreProducts();
            });
        },

        bindPremiumEvents: function() {
            // Reinitialize premium features when Elementor refreshes
            if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
                elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
                    setTimeout(function() {
                        UXDPremium.initPremiumAnimations();
                        UXDPremium.initMasonryLayouts();
                        UXDPremium.initAdvancedInteractions();
                    }, 100);
                });
            }
            
            // Handle dynamic content loading
            $(document).on('uxd:content:loaded', function() {
                UXDPremium.initPremiumAnimations();
                UXDPremium.initMasonryLayouts();
            });
            
            // Initialize compare functionality
            $(document).on('click', '.uxd-view-compare', function() {
                UXDPremium.openCompareModal();
            });
        },

        openCompareModal: function() {
            var compareList = this.getCompareList();
            
            if (compareList.length === 0) {
                UXDPremium.showError('No products to compare');
                return;
            }
            
            // Create and show compare modal
            $.ajax({
                url: uxd_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'uxd_get_compare_modal',
                    products: compareList,
                    nonce: uxd_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var $modal = $(response.data.html);
                        $('body').append($modal);
                        $modal.fadeIn(300);
                        
                        // Close modal events
                        $modal.on('click', '.uxd-modal-close, .uxd-modal-overlay', function(e) {
                            if (e.target === this) {
                                $modal.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }
                        });
                    }
                }
            });
        },

        // Utility functions
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
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        // Only initialize premium features if user has premium access
        if (typeof uxd_ajax_object !== 'undefined' && uxd_ajax_object.is_premium) {
            UXDPremium.init();
        }
    });

    // Initialize premium features on window load for better performance
    $(window).on('load', function() {
        if (typeof uxd_ajax_object !== 'undefined' && uxd_ajax_object.is_premium) {
            UXDPremium.initParallaxEffects();
            UXDPremium.initInfiniteScroll();
        }
    });

    // Make UXDPremium globally accessible
    window.UXDPremium = UXDPremium;

})(jQuery);

// Add premium CSS animations
jQuery(document).ready(function($) {
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .uxd-fade-in {
                animation: uxdFadeIn 0.6s ease-out forwards;
            }
            
            @keyframes uxdFadeIn {
                0% { opacity: 0; transform: translateY(20px); }
                100% { opacity: 1; transform: translateY(0); }
            }
            
            .uxd-success-flash {
                animation: uxdSuccessFlash 1s ease-in-out;
            }
            
            @keyframes uxdSuccessFlash {
                0%, 100% { background-color: transparent; }
                50% { background-color: rgba(40, 167, 69, 0.2); }
            }
            
            .uxd-premium-error {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #dc3545;
                color: white;
                padding: 15px 20px;
                border-radius: 6px;
                z-index: 10000;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                display: none;
            }
            
            .uxd-filtering {
                opacity: 0.6;
                pointer-events: none;
                position: relative;
            }
            
            .uxd-filtering::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #007cba;
                border-radius: 50%;
                animation: uxdSpin 1s linear infinite;
                transform: translate(-50%, -50%);
            }
            
            @keyframes uxdSpin {
                0% { transform: translate(-50%, -50%) rotate(0deg); }
                100% { transform: translate(-50%, -50%) rotate(360deg); }
            }
        `)
        .appendTo('head');
});