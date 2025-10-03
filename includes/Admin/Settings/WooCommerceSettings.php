<?php

namespace WP_SEO_Pro\Admin\Settings;

/**
 * WooCommerce settings
 */
class WooCommerceSettings {
    
    /**
     * Constructor
     */
    public function __construct() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        add_settings_section(
            'wp_seo_pro_woocommerce_section',
            __('WooCommerce SEO Settings', 'wp-seo-pro'),
            array($this, 'section_callback'),
            'wp_seo_pro_woocommerce'
        );
        
        // Enable product schema
        add_settings_field(
            'enable_product_schema',
            __('Enable Product Schema', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_woocommerce',
            'wp_seo_pro_woocommerce_section',
            array('field' => 'enable_product_schema', 'description' => __('Add structured data for products', 'wp-seo-pro'))
        );
        
        // Enable review schema
        add_settings_field(
            'enable_review_schema',
            __('Enable Review Schema', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_woocommerce',
            'wp_seo_pro_woocommerce_section',
            array('field' => 'enable_review_schema', 'description' => __('Add structured data for product reviews', 'wp-seo-pro'))
        );
        
        // Product title template
        add_settings_field(
            'product_title_template',
            __('Product Title Template', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_woocommerce',
            'wp_seo_pro_woocommerce_section',
            array('field' => 'product_title_template', 'description' => __('Template for product titles. Use {product_name} and {site_name}', 'wp-seo-pro'))
        );
        
        // Product description template
        add_settings_field(
            'product_description_template',
            __('Product Description Template', 'wp-seo-pro'),
            array($this, 'textarea_callback'),
            'wp_seo_pro_woocommerce',
            'wp_seo_pro_woocommerce_section',
            array('field' => 'product_description_template', 'description' => __('Template for product descriptions. Use {product_excerpt}', 'wp-seo-pro'))
        );
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>' . __('Configure SEO settings specifically for WooCommerce products.', 'wp-seo-pro') . '</p>';
    }
    
    /**
     * Checkbox callback
     */
    public function checkbox_callback($args) {
        $options = get_option('wp_seo_pro_woocommerce');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : true;
        
        echo '<input type="checkbox" id="' . $args['field'] . '" name="wp_seo_pro_woocommerce[' . $args['field'] . ']" value="1" ' . checked(1, $value, false) . ' />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Text callback
     */
    public function text_callback($args) {
        $options = get_option('wp_seo_pro_woocommerce');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        
        echo '<input type="text" id="' . $args['field'] . '" name="wp_seo_pro_woocommerce[' . $args['field'] . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Textarea callback
     */
    public function textarea_callback($args) {
        $options = get_option('wp_seo_pro_woocommerce');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        
        echo '<textarea id="' . $args['field'] . '" name="wp_seo_pro_woocommerce[' . $args['field'] . ']" rows="3" cols="50" class="large-text">' . esc_textarea($value) . '</textarea>';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
}