<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class Settings
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $opts = $this->options->all();
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Better SEO - Global Settings', 'better-seo-suite') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('bss_options_group');
        echo '<table class="form-table" role="presentation">';
        echo '<tr><th><label>' . esc_html__('Title Separator', 'better-seo-suite') . '</label></th><td>';
        echo '<input type="text" name="bss_options[general][separator]" value="' . esc_attr($opts['general']['separator'] ?? '—') . '" />';
        echo '</td></tr>';

        echo '<tr><th><label>' . esc_html__('Homepage Title', 'better-seo-suite') . '</label></th><td>';
        echo '<input type="text" class="regular-text" name="bss_options[general][home_title]" value="' . esc_attr($opts['general']['home_title'] ?? '') . '" />';
        echo '</td></tr>';

        echo '<tr><th><label>' . esc_html__('Homepage Meta Description', 'better-seo-suite') . '</label></th><td>';
        echo '<textarea class="large-text" rows="3" name="bss_options[general][home_description]">' . esc_textarea($opts['general']['home_description'] ?? '') . '</textarea>';
        echo '</td></tr>';

        echo '<tr><th><label>' . esc_html__('Knowledge Graph Type', 'better-seo-suite') . '</label></th><td>';
        $kgType = $opts['general']['knowledge_graph_type'] ?? 'Organization';
        echo '<select name="bss_options[general][knowledge_graph_type]">';
        foreach (['Organization','Person'] as $type) {
            $sel = selected($kgType, $type, false);
            echo '<option value="' . esc_attr($type) . '" ' . $sel . '>' . esc_html($type) . '</option>';
        }
        echo '</select>';
        echo '</td></tr>';

        echo '<tr><th><label>' . esc_html__('Knowledge Graph Name', 'better-seo-suite') . '</label></th><td>';
        echo '<input type="text" class="regular-text" name="bss_options[general][knowledge_graph_name]" value="' . esc_attr($opts['general']['knowledge_graph_name'] ?? '') . '" />';
        echo '</td></tr>';

        echo '<tr><th><label>' . esc_html__('Organization Logo URL', 'better-seo-suite') . '</label></th><td>';
        echo '<input type="url" class="regular-text" name="bss_options[general][org_logo]" value="' . esc_attr($opts['general']['org_logo'] ?? '') . '" />';
        echo '</td></tr>';

        echo '<tr><th><label>' . esc_html__('Verification Codes', 'better-seo-suite') . '</label></th><td>';
        echo '<p><label>Google: <input type="text" name="bss_options[general][verify][google]" value="' . esc_attr($opts['general']['verify']['google'] ?? '') . '" /></label></p>';
        echo '<p><label>Bing: <input type="text" name="bss_options[general][verify][bing]" value="' . esc_attr($opts['general']['verify']['bing'] ?? '') . '" /></label></p>';
        echo '<p><label>Yandex: <input type="text" name="bss_options[general][verify][yandex]" value="' . esc_attr($opts['general']['verify']['yandex'] ?? '') . '" /></label></p>';
        echo '<p><label>Baidu: <input type="text" name="bss_options[general][verify][baidu]" value="' . esc_attr($opts['general']['verify']['baidu'] ?? '') . '" /></label></p>';
        echo '</td></tr>';

        echo '</table>';
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

