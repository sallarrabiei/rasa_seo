<?php
namespace BSS\Integrations;

use BSS\Settings\Options;

class EDD
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_filter('bss/schema/graphs', [$this, 'add_product_schema']);
        add_action('add_meta_boxes', [$this, 'add_download_meta_box']);
        add_action('save_post_download', [$this, 'save_download_meta'], 10, 2);
    }

    public function add_product_schema(array $graphs): array
    {
        if (!is_singular('download') || !(bool) $this->options->get('schema.product.enabled', true)) {
            return $graphs;
        }
        $pid = get_queried_object_id();
        $price = function_exists('edd_get_download_price') ? edd_get_download_price($pid) : '';
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => get_the_title($pid),
            'image' => get_the_post_thumbnail_url($pid, 'full'),
            'description' => wp_strip_all_tags(get_post_field('post_excerpt', $pid) ?: get_post_field('post_content', $pid), true),
            'offers' => [
                '@type' => 'Offer',
                'url' => get_permalink($pid),
                'priceCurrency' => get_option('woocommerce_currency', 'USD'),
                'price' => $price,
                'availability' => 'https://schema.org/InStock',
            ],
        ];
        $graphs[] = $schema;
        return $graphs;
    }

    public function add_download_meta_box(): void
    {
        add_meta_box('bss-edd-seo', __('Download SEO', 'better-seo-suite'), [$this, 'render_download_meta_box'], 'download', 'normal', 'high');
    }

    public function render_download_meta_box($post): void
    {
        wp_nonce_field('bss_edd_meta', 'bss_edd_meta_nonce');
        $sku = get_post_meta($post->ID, '_bss_sku', true);
        echo '<p><label>SKU <input type="text" name="bss_sku" value="' . esc_attr($sku) . '" /></label></p>';
    }

    public function save_download_meta(int $post_id, $post): void
    {
        if (!isset($_POST['bss_edd_meta_nonce']) || !wp_verify_nonce($_POST['bss_edd_meta_nonce'], 'bss_edd_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        update_post_meta($post_id, '_bss_sku', sanitize_text_field((string) ($_POST['bss_sku'] ?? '')));
    }
}

