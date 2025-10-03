<?php
namespace BSS\Admin\MetaBoxes;

class MetaBox
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_box']);
        add_action('save_post', [$this, 'save_meta'], 10, 2);
    }

    public function add_meta_box(): void
    {
        $post_types = get_post_types(['public' => true]);
        foreach ($post_types as $pt) {
            add_meta_box('bss-seo', __('SEO', 'better-seo-suite'), [$this, 'render_meta_box'], $pt, 'normal', 'high');
        }
    }

    public function render_meta_box($post): void
    {
        wp_nonce_field('bss_seo_meta', 'bss_seo_meta_nonce');
        $title = get_post_meta($post->ID, '_bss_title', true);
        $desc = get_post_meta($post->ID, '_bss_desc', true);
        $noindex = (bool) get_post_meta($post->ID, '_bss_noindex', true);
        echo '<p><label>' . esc_html__('SEO Title', 'better-seo-suite') . '<br/><input type="text" class="widefat" name="bss_title" value="' . esc_attr($title) . '" /></label></p>';
        echo '<p><label>' . esc_html__('Meta Description', 'better-seo-suite') . '<br/><textarea class="widefat" rows="3" name="bss_desc">' . esc_textarea($desc) . '</textarea></label></p>';
        echo '<p><label><input type="checkbox" name="bss_noindex" value="1" ' . checked($noindex, true, false) . ' /> ' . esc_html__('Noindex', 'better-seo-suite') . '</label></p>';
    }

    public function save_meta(int $post_id, $post): void
    {
        if (!isset($_POST['bss_seo_meta_nonce']) || !wp_verify_nonce($_POST['bss_seo_meta_nonce'], 'bss_seo_meta')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        $title = isset($_POST['bss_title']) ? sanitize_text_field((string) $_POST['bss_title']) : '';
        $desc = isset($_POST['bss_desc']) ? sanitize_textarea_field((string) $_POST['bss_desc']) : '';
        $noindex = !empty($_POST['bss_noindex']) ? '1' : '';
        update_post_meta($post_id, '_bss_title', $title);
        update_post_meta($post_id, '_bss_desc', $desc);
        update_post_meta($post_id, '_bss_noindex', $noindex);
    }
}

