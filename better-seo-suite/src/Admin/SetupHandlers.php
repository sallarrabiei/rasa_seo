<?php
namespace BSS\Admin;

use BSS\Settings\Options;

class SetupHandlers
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('admin_post_bss_setup_basics', [$this, 'save_basics']);
        add_action('admin_post_bss_setup_appearance', [$this, 'save_appearance']);
        add_action('admin_post_bss_setup_social', [$this, 'save_social']);
    }

    public function save_basics(): void
    {
        check_admin_referer('bss_setup_basics');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        $input = [
            'general' => [
                'knowledge_graph_type' => sanitize_text_field((string) ($_POST['general']['knowledge_graph_type'] ?? 'Organization')),
                'knowledge_graph_name' => sanitize_text_field((string) ($_POST['general']['knowledge_graph_name'] ?? '')),
            ],
        ];
        $san = $this->options->sanitize($input);
        foreach ($san as $k => $v) {
            // merge into existing
        }
        $current = $this->options->all();
        $merged = array_replace_recursive($current, $san);
        update_option('bss_options', $merged);
        wp_safe_redirect(admin_url('admin.php?page=bss-setup&step=appearance'));
        exit;
    }

    public function save_appearance(): void
    {
        check_admin_referer('bss_setup_appearance');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        $input = [
            'general' => [
                'separator' => sanitize_text_field((string) ($_POST['general']['separator'] ?? '—')),
            ],
        ];
        $san = $this->options->sanitize($input);
        $current = $this->options->all();
        $merged = array_replace_recursive($current, $san);
        update_option('bss_options', $merged);
        wp_safe_redirect(admin_url('admin.php?page=bss-setup&step=social'));
        exit;
    }

    public function save_social(): void
    {
        check_admin_referer('bss_setup_social');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        $input = [
            'social' => [
                'opengraph' => [
                    'default_image' => esc_url_raw((string) ($_POST['social']['opengraph']['default_image'] ?? '')),
                ],
            ],
        ];
        $san = $this->options->sanitize($input);
        $current = $this->options->all();
        $merged = array_replace_recursive($current, $san);
        update_option('bss_options', $merged);
        wp_safe_redirect(admin_url('admin.php?page=bss-setup&step=finish'));
        exit;
    }
}

