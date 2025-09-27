<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WooCommerce')) {
    echo '<div class="notice notice-error"><p>' . __('WooCommerce is not installed or activated.', 'wp-seo-pro') . '</p></div>';
    return;
}

$options = get_option('wp_seo_pro_woocommerce', array());
?>

<div class="wrap">
    <h1><?php _e('WooCommerce SEO Settings', 'wp-seo-pro'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_seo_pro_woocommerce');
        do_settings_sections('wp_seo_pro_woocommerce');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Product Schema', 'wp-seo-pro'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="wp_seo_pro_woocommerce[enable_product_schema]" value="1" <?php checked(isset($options['enable_product_schema']) ? $options['enable_product_schema'] : true, 1); ?> />
                            <?php _e('Enable Product Schema', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Add structured data for products to help search engines understand your products', 'wp-seo-pro'); ?></p>
                        
                        <br>
                        
                        <label>
                            <input type="checkbox" name="wp_seo_pro_woocommerce[enable_review_schema]" value="1" <?php checked(isset($options['enable_review_schema']) ? $options['enable_review_schema'] : true, 1); ?> />
                            <?php _e('Enable Review Schema', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Add structured data for product reviews', 'wp-seo-pro'); ?></p>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="product_title_template"><?php _e('Product Title Template', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="product_title_template" name="wp_seo_pro_woocommerce[product_title_template]" value="<?php echo esc_attr(isset($options['product_title_template']) ? $options['product_title_template'] : '{product_name} | {site_name}'); ?>" class="regular-text" />
                    <p class="description"><?php _e('Template for product titles. Available variables: {product_name}, {site_name}, {category_name}', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="product_description_template"><?php _e('Product Description Template', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <textarea id="product_description_template" name="wp_seo_pro_woocommerce[product_description_template]" rows="3" cols="50" class="large-text"><?php echo esc_textarea(isset($options['product_description_template']) ? $options['product_description_template'] : '{product_excerpt}'); ?></textarea>
                    <p class="description"><?php _e('Template for product descriptions. Available variables: {product_excerpt}, {product_description}', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="wp-seo-pro-woocommerce-info">
        <h3><?php _e('WooCommerce SEO Tips', 'wp-seo-pro'); ?></h3>
        <ul>
            <li><?php _e('Use descriptive product titles with relevant keywords', 'wp-seo-pro'); ?></li>
            <li><?php _e('Write detailed product descriptions that answer customer questions', 'wp-seo-pro'); ?></li>
            <li><?php _e('Optimize product images with descriptive alt text', 'wp-seo-pro'); ?></li>
            <li><?php _e('Use product categories and tags effectively', 'wp-seo-pro'); ?></li>
            <li><?php _e('Encourage customer reviews to improve SEO and trust', 'wp-seo-pro'); ?></li>
            <li><?php _e('Use structured data to help search engines understand your products', 'wp-seo-pro'); ?></li>
        </ul>
    </div>
</div>

<style>
.wp-seo-pro-woocommerce-info {
    background: #f0f6fc;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.wp-seo-pro-woocommerce-info h3 {
    margin-top: 0;
    color: #23282d;
}

.wp-seo-pro-woocommerce-info ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.wp-seo-pro-woocommerce-info li {
    margin-bottom: 8px;
    color: #666;
}
</style>