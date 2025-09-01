<?php
/**
 * Wishlist Shortcode Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$wishlist_instance = new UXD_Wishlist();
$wishlist = $wishlist_instance->get_user_wishlist();

// Handle shared wishlist
$shared_data = null;
if (isset($_GET['wishlist_share'])) {
    $shared_data = $wishlist_instance->get_shared_wishlist_data($_GET['wishlist_share']);
    if ($shared_data) {
        $wishlist = $shared_data['wishlist'];
    }
}

if (empty($wishlist)) {
    ?>
    <div class="uxd-wishlist-empty">
        <div class="uxd-empty-icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>
        <h3>
            <?php 
            if ($shared_data) {
                printf(esc_html__('%s\'s wishlist is empty', 'uxd-elementor-extension'), esc_html($shared_data['user_name']));
            } else {
                esc_html_e('Your wishlist is empty', 'uxd-elementor-extension'); 
            }
            ?>
        </h3>
        <p><?php esc_html_e('Add products you love to your wishlist and come back to them later.', 'uxd-elementor-extension'); ?></p>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button">
            <?php esc_html_e('Continue Shopping', 'uxd-elementor-extension'); ?>
        </a>
    </div>
    <?php
    return;
}
?>

<div class="uxd-wishlist-container">
    <?php if ($shared_data): ?>
        <div class="uxd-wishlist-header">
            <h2><?php printf(esc_html__('%s\'s Wishlist', 'uxd-elementor-extension'), esc_html($shared_data['user_name'])); ?></h2>
            <p><?php printf(esc_html__('%d products in this wishlist', 'uxd-elementor-extension'), count($wishlist)); ?></p>
        </div>
    <?php else: ?>
        <div class="uxd-wishlist-header">
            <h2><?php esc_html_e('My Wishlist', 'uxd-elementor-extension'); ?></h2>
            <div class="uxd-wishlist-actions">
                <span class="uxd-wishlist-count"><?php printf(esc_html__('%d products', 'uxd-elementor-extension'), count($wishlist)); ?></span>
                
                <?php if (!empty($wishlist)): ?>
                    <button type="button" class="uxd-clear-wishlist button" data-confirm="<?php esc_attr_e('Are you sure you want to clear your wishlist?', 'uxd-elementor-extension'); ?>">
                        <?php esc_html_e('Clear All', 'uxd-elementor-extension'); ?>
                    </button>
                    
                    <button type="button" class="uxd-share-wishlist button">
                        <?php esc_html_e('Share Wishlist', 'uxd-elementor-extension'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="uxd-wishlist-grid" style="grid-template-columns: repeat(<?php echo intval($atts['columns']); ?>, 1fr);">
        <?php foreach ($wishlist as $product_id): ?>
            <?php 
            $product = wc_get_product($product_id);
            if (!$product || !$product->exists()) {
                continue;
            }
            ?>
            <div class="uxd-wishlist-item" data-product-id="<?php echo esc_attr($product_id); ?>">
                <div class="uxd-wishlist-item-image">
                    <a href="<?php echo esc_url($product->get_permalink()); ?>">
                        <?php echo wp_get_attachment_image($product->get_image_id(), 'medium'); ?>
                    </a>
                    
                    <?php if ($product->is_on_sale()): ?>
                        <span class="uxd-sale-badge"><?php esc_html_e('Sale!', 'uxd-elementor-extension'); ?></span>
                    <?php endif; ?>
                    
                    <?php if (!$shared_data && $atts['show_remove'] === 'yes'): ?>
                        <button class="uxd-remove-from-wishlist" data-product-id="<?php echo esc_attr($product_id); ?>" title="<?php esc_attr_e('Remove from Wishlist', 'uxd-elementor-extension'); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="uxd-wishlist-item-content">
                    <h3 class="uxd-wishlist-item-title">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo esc_html($product->get_name()); ?>
                        </a>
                    </h3>
                    
                    <?php if ($atts['show_rating'] === 'yes'): ?>
                        <div class="uxd-wishlist-item-rating">
                            <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_price'] === 'yes'): ?>
                        <div class="uxd-wishlist-item-price">
                            <?php echo $product->get_price_html(); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="uxd-wishlist-item-stock">
                        <?php if ($product->is_in_stock()): ?>
                            <span class="uxd-in-stock"><?php esc_html_e('In Stock', 'uxd-elementor-extension'); ?></span>
                        <?php else: ?>
                            <span class="uxd-out-of-stock"><?php esc_html_e('Out of Stock', 'uxd-elementor-extension'); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($atts['show_add_to_cart'] === 'yes' && $product->is_in_stock()): ?>
                        <div class="uxd-wishlist-item-cart">
                            <?php
                            if ($product->is_type('simple')) {
                                ?>
                                <button type="button" class="uxd-add-to-cart-from-wishlist button" data-product-id="<?php echo esc_attr($product_id); ?>">
                                    <?php esc_html_e('Add to Cart', 'uxd-elementor-extension'); ?>
                                </button>
                                <?php
                            } else {
                                ?>
                                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="button">
                                    <?php esc_html_e('Select Options', 'uxd-elementor-extension'); ?>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (!$shared_data): ?>
        <div class="uxd-wishlist-footer">
            <div class="uxd-wishlist-share">
                <h4><?php esc_html_e('Share Your Wishlist', 'uxd-elementor-extension'); ?></h4>
                <div class="uxd-share-url-container" style="display: none;">
                    <input type="text" class="uxd-share-url" readonly value="<?php echo esc_attr($wishlist_instance->get_wishlist_share_url()); ?>">
                    <button type="button" class="uxd-copy-share-url button"><?php esc_html_e('Copy Link', 'uxd-elementor-extension'); ?></button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.uxd-wishlist-container {
    margin: 20px 0;
}

.uxd-wishlist-empty {
    text-align: center;
    padding: 60px 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.uxd-empty-icon {
    color: #ccc;
    margin-bottom: 20px;
}

.uxd-wishlist-empty h3 {
    color: #666;
    margin-bottom: 15px;
    font-size: 24px;
}

.uxd-wishlist-empty p {
    color: #999;
    margin-bottom: 25px;
    font-size: 16px;
}

.uxd-wishlist-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
}

.uxd-wishlist-header h2 {
    margin: 0;
    color: #333;
    font-size: 28px;
}

.uxd-wishlist-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.uxd-wishlist-count {
    color: #666;
    font-size: 14px;
}

.uxd-wishlist-grid {
    display: grid;
    gap: 30px;
    margin-bottom: 40px;
}

.uxd-wishlist-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.uxd-wishlist-item:hover {
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.uxd-wishlist-item-image {
    position: relative;
    overflow: hidden;
    background: #f8f9fa;
}

.uxd-wishlist-item-image img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.uxd-wishlist-item:hover .uxd-wishlist-item-image img {
    transform: scale(1.05);
}

.uxd-sale-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #e74c3c;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    z-index: 2;
}

.uxd-remove-from-wishlist {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 35px;
    height: 35px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    opacity: 0;
    z-index: 2;
}

.uxd-wishlist-item:hover .uxd-remove-from-wishlist {
    opacity: 1;
}

.uxd-remove-from-wishlist:hover {
    background: #dc3545;
    transform: scale(1.1);
}

.uxd-wishlist-item-content {
    padding: 20px;
}

.uxd-wishlist-item-title {
    margin: 0 0 10px 0;
    font-size: 16px;
    font-weight: 600;
}

.uxd-wishlist-item-title a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.uxd-wishlist-item-title a:hover {
    color: #007cba;
}

.uxd-wishlist-item-rating {
    margin-bottom: 10px;
}

.uxd-wishlist-item-price {
    margin-bottom: 15px;
    font-weight: bold;
    color: #007cba;
    font-size: 18px;
}

.uxd-wishlist-item-stock {
    margin-bottom: 15px;
    font-size: 14px;
}

.uxd-in-stock {
    color: #28a745;
    font-weight: 500;
}

.uxd-out-of-stock {
    color: #dc3545;
    font-weight: 500;
}

.uxd-wishlist-item-cart .button {
    width: 100%;
    background: #007cba;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.uxd-wishlist-item-cart .button:hover {
    background: #005a87;
    transform: translateY(-1px);
}

.uxd-wishlist-footer {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.uxd-wishlist-share h4 {
    margin-bottom: 15px;
    color: #333;
}

.uxd-share-url-container {
    display: flex;
    gap: 10px;
    align-items: center;
}

.uxd-share-url {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-family: monospace;
    font-size: 14px;
    background: white;
}

.uxd-copy-share-url {
    padding: 10px 20px;
    background: #007cba;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.uxd-copy-share-url:hover {
    background: #005a87;
}

/* Responsive */
@media (max-width: 768px) {
    .uxd-wishlist-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .uxd-wishlist-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .uxd-wishlist-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px;
    }
    
    .uxd-share-url-container {
        flex-direction: column;
    }
    
    .uxd-share-url {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .uxd-wishlist-grid {
        grid-template-columns: 1fr !important;
        gap: 15px;
    }
    
    .uxd-wishlist-actions {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Remove from wishlist
    $(document).on('click', '.uxd-remove-from-wishlist', function(e) {
        e.preventDefault();
        var $button = $(this);
        var productId = $button.data('product-id');
        var $item = $button.closest('.uxd-wishlist-item');
        
        $.ajax({
            url: uxd_wishlist_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'uxd_remove_from_wishlist',
                product_id: productId,
                nonce: uxd_wishlist_ajax.nonce
            },
            beforeSend: function() {
                $button.prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Update count
                        $('.uxd-wishlist-count').text(response.data.count + ' products');
                        
                        // Check if wishlist is empty
                        if ($('.uxd-wishlist-item').length === 0) {
                            location.reload();
                        }
                    });
                }
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    });
    
    // Clear wishlist
    $(document).on('click', '.uxd-clear-wishlist', function(e) {
        e.preventDefault();
        var confirmText = $(this).data('confirm');
        
        if (confirm(confirmText)) {
            location.reload();
        }
    });
    
    // Share wishlist
    $(document).on('click', '.uxd-share-wishlist', function(e) {
        e.preventDefault();
        $('.uxd-share-url-container').slideToggle();
    });
    
    // Copy share URL
    $(document).on('click', '.uxd-copy-share-url', function(e) {
        e.preventDefault();
        var $input = $('.uxd-share-url');
        $input.select();
        document.execCommand('copy');
        
        var $button = $(this);
        var originalText = $button.text();
        $button.text('Copied!');
        setTimeout(function() {
            $button.text(originalText);
        }, 2000);
    });
});
</script>