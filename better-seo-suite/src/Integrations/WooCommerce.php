<?php
namespace BSS\Integrations;

use BSS\Settings\Options;

class WooCommerce
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_filter('bss/schema/graphs', [$this, 'add_product_schema']);
        add_action('add_meta_boxes', [$this, 'add_product_meta_box']);
        add_action('save_post_product', [$this, 'save_product_meta'], 10, 2);
    }

    public function add_product_schema(array $graphs): array
    {
        if (!is_singular('product') || !(bool) $this->options->get('schema.product.enabled', true)) {
            return $graphs;
        }
        $pid = get_queried_object_id();
        if (!$pid) return $graphs;
        $product = function_exists('wc_get_product') ? wc_get_product($pid) : null;
        if (!$product) return $graphs;
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => get_the_title($pid),
            'image' => get_the_post_thumbnail_url($pid, 'full'),
            'description' => wp_strip_all_tags(get_post_field('post_excerpt', $pid) ?: get_post_field('post_content', $pid), true),
        ];
        $offers = [
            '@type' => 'Offer',
            'url' => get_permalink($pid),
            'priceCurrency' => get_woocommerce_currency(),
            'price' => $product->get_price(),
            'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        ];
        $schema['offers'] = $offers;
        $graphs[] = $schema;
        return $graphs;
    }

    public function add_product_meta_box(): void
    {
        add_meta_box('bss-woo-seo', __('Product SEO', 'better-seo-suite'), [$this, 'render_product_meta_box'], 'product', 'normal', 'high');
    }

    public function render_product_meta_box($post): void
    {
        wp_nonce_field('bss_woo_meta', 'bss_woo_meta_nonce');
        $gtin = get_post_meta($post->ID, '_bss_gtin', true);
        $brand = get_post_meta($post->ID, '_bss_brand', true);
        echo '<p><label>GTIN <input type="text" name="bss_gtin" value="' . esc_attr($gtin) . '" /></label></p>';
        echo '<p><label>Brand <input type="text" name="bss_brand" value="' . esc_attr($brand) . '" /></label></p>';
    }

    public function save_product_meta(int $post_id, $post): void
    {
        if (!isset($_POST['bss_woo_meta_nonce']) || !wp_verify_nonce($_POST['bss_woo_meta_nonce'], 'bss_woo_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        update_post_meta($post_id, '_bss_gtin', sanitize_text_field((string) ($_POST['bss_gtin'] ?? '')));
        update_post_meta($post_id, '_bss_brand', sanitize_text_field((string) ($_POST['bss_brand'] ?? '')));
    }
}

