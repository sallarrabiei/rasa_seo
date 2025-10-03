<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class Sitemaps
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
        echo '<h1>' . esc_html__('Sitemaps', 'better-seo-suite') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('bss_options_group');
        echo '<table class="form-table" role="presentation">';
        $enabled = !empty($opts['sitemap']['enabled']);
        echo '<tr><th>' . esc_html__('Enable Sitemaps', 'better-seo-suite') . '</th><td>';
        echo '<label><input type="checkbox" name="bss_options[sitemap][enabled]" value="1" ' . checked($enabled, true, false) . ' /> ' . esc_html__('Enable XML sitemaps', 'better-seo-suite') . '</label>';
        echo '</td></tr>';

        $incImg = !empty($opts['sitemap']['include_images']);
        echo '<tr><th>' . esc_html__('Include Images', 'better-seo-suite') . '</th><td>';
        echo '<label><input type="checkbox" name="bss_options[sitemap][include_images]" value="1" ' . checked($incImg, true, false) . ' /> ' . esc_html__('Include images in post sitemaps', 'better-seo-suite') . '</label>';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__('Exclude Post IDs', 'better-seo-suite') . '</th><td>';
        echo '<input type="text" class="regular-text" name="bss_options[sitemap][exclude_ids]" value="' . esc_attr($opts['sitemap']['exclude_ids'] ?? '') . '" />';
        echo '<p class="description">' . esc_html__('Comma-separated list of post IDs to exclude.', 'better-seo-suite') . '</p>';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__('Change Frequency', 'better-seo-suite') . '</th><td>';
        $cf = $opts['sitemap']['changefreq'] ?? 'weekly';
        echo '<select name="bss_options[sitemap][changefreq]">';
        foreach (['always','hourly','daily','weekly','monthly','yearly','never'] as $freq) {
            echo '<option value="' . esc_attr($freq) . '" ' . selected($cf, $freq, false) . '>' . esc_html($freq) . '</option>';
        }
        echo '</select>';
        echo '</td></tr>';

        echo '<tr><th>' . esc_html__('Default Priority', 'better-seo-suite') . '</th><td>';
        $pr = $opts['sitemap']['priority'] ?? '0.5';
        echo '<input type="number" step="0.1" min="0" max="1" name="bss_options[sitemap][priority]" value="' . esc_attr($pr) . '" />';
        echo '</td></tr>';

        echo '</table>';
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

