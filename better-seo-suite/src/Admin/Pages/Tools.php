<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class Tools
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('admin_post_bss_import', [$this, 'handle_import']);
    }

    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $nonce = wp_create_nonce('bss_import');
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Tools', 'better-seo-suite') . '</h1>';
        echo '<h2>' . esc_html__('Import from other SEO plugins', 'better-seo-suite') . '</h2>';
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bss_import" />';
        echo '<input type="hidden" name="_wpnonce" value="' . esc_attr($nonce) . '" />';
        echo '<p><label><input type="checkbox" name="import[yoast]" value="1" /> ' . esc_html__('Yoast SEO', 'better-seo-suite') . '</label></p>';
        echo '<p><label><input type="checkbox" name="import[aioseo]" value="1" /> ' . esc_html__('All in One SEO', 'better-seo-suite') . '</label></p>';
        echo '<p><label><input type="checkbox" name="import[rankmath]" value="1" /> ' . esc_html__('Rank Math', 'better-seo-suite') . '</label></p>';
        submit_button(__('Run Import', 'better-seo-suite'));
        echo '</form>';
        echo '</div>';
    }

    public function handle_import(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Unauthorized', 'better-seo-suite'));
        }
        check_admin_referer('bss_import');
        $sources = isset($_POST['import']) && is_array($_POST['import']) ? array_keys($_POST['import']) : [];
        do_action('bss/tools/run_import', $sources);
        wp_safe_redirect(admin_url('admin.php?page=bss-tools&import=done'));
        exit;
    }
}

