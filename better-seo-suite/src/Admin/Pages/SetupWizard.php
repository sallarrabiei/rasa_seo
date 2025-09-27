<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class SetupWizard
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('admin_menu', function(){
            add_submenu_page(null, __('Better SEO Setup', 'better-seo-suite'), __('Better SEO Setup', 'better-seo-suite'), 'manage_options', 'bss-setup', [$this, 'render']);
        });
    }

    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $step = isset($_GET['step']) ? sanitize_key((string) $_GET['step']) : 'welcome';
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Better SEO - Setup Wizard', 'better-seo-suite') . '</h1>';
        echo '<ol class="bss-steps"><li>Welcome</li><li>Site Basics</li><li>Search Appearance</li><li>Social</li><li>Finish</li></ol>';
        switch ($step) {
            case 'welcome':
                echo '<p>' . esc_html__('This wizard will help you configure essential SEO settings.', 'better-seo-suite') . '</p>';
                echo '<p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=bss-setup&step=basics')) . '">' . esc_html__('Get Started', 'better-seo-suite') . '</a></p>';
                break;
            case 'basics':
                $this->render_basics();
                break;
            case 'appearance':
                $this->render_appearance();
                break;
            case 'social':
                $this->render_social();
                break;
            case 'finish':
                echo '<p>' . esc_html__('You are all set! Visit the dashboard to explore more options.', 'better-seo-suite') . '</p>';
                echo '<p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=bss-dashboard')) . '">' . esc_html__('Go to Dashboard', 'better-seo-suite') . '</a></p>';
                break;
        }
        echo '</div>';
    }

    private function render_basics(): void
    {
        $opts = $this->options->all();
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bss_setup_basics" />';
        wp_nonce_field('bss_setup_basics');
        echo '<table class="form-table" role="presentation">';
        echo '<tr><th>' . esc_html__('Knowledge Graph Type', 'better-seo-suite') . '</th><td>';
        $kgType = $opts['general']['knowledge_graph_type'] ?? 'Organization';
        echo '<select name="general[knowledge_graph_type]">';
        foreach (['Organization','Person'] as $type) {
            echo '<option value="' . esc_attr($type) . '" ' . selected($kgType, $type, false) . '>' . esc_html($type) . '</option>';
        }
        echo '</select>';
        echo '</td></tr>';
        echo '<tr><th>' . esc_html__('Knowledge Graph Name', 'better-seo-suite') . '</th><td>';
        echo '<input type="text" class="regular-text" name="general[knowledge_graph_name]" value="' . esc_attr($opts['general']['knowledge_graph_name'] ?? get_bloginfo('name')) . '" />';
        echo '</td></tr>';
        echo '</table>';
        submit_button(__('Continue', 'better-seo-suite'));
        echo '</form>';
    }

    private function render_appearance(): void
    {
        $opts = $this->options->all();
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bss_setup_appearance" />';
        wp_nonce_field('bss_setup_appearance');
        echo '<table class="form-table" role="presentation">';
        echo '<tr><th>' . esc_html__('Title Separator', 'better-seo-suite') . '</th><td>';
        echo '<input type="text" name="general[separator]" value="' . esc_attr($opts['general']['separator'] ?? '—') . '" />';
        echo '</td></tr>';
        echo '</table>';
        submit_button(__('Continue', 'better-seo-suite'));
        echo '</form>';
    }

    private function render_social(): void
    {
        $opts = $this->options->all();
        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="bss_setup_social" />';
        wp_nonce_field('bss_setup_social');
        echo '<table class="form-table" role="presentation">';
        echo '<tr><th>' . esc_html__('Default OG Image URL', 'better-seo-suite') . '</th><td>';
        echo '<input type="url" class="regular-text" name="social[opengraph][default_image]" value="' . esc_attr($opts['social']['opengraph']['default_image'] ?? '') . '" />';
        echo '</td></tr>';
        echo '</table>';
        submit_button(__('Finish', 'better-seo-suite'));
        echo '</form>';
    }
}

