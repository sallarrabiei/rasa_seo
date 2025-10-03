<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class Social
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
        echo '<h1>' . esc_html__('Social Meta', 'better-seo-suite') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('bss_options_group');
        echo '<h2>' . esc_html__('Open Graph', 'better-seo-suite') . '</h2>';
        $ogEnabled = !empty($opts['social']['opengraph']['enabled']);
        echo '<p><label><input type="checkbox" name="bss_options[social][opengraph][enabled]" value="1" ' . checked($ogEnabled, true, false) . ' /> ' . esc_html__('Enable Open Graph', 'better-seo-suite') . '</label></p>';
        echo '<p><label>' . esc_html__('Default OG Image URL', 'better-seo-suite') . ': <input type="url" class="regular-text" name="bss_options[social][opengraph][default_image]" value="' . esc_attr($opts['social']['opengraph']['default_image'] ?? '') . '" /></label></p>';

        echo '<h2>' . esc_html__('Twitter', 'better-seo-suite') . '</h2>';
        $twEnabled = !empty($opts['social']['twitter']['enabled']);
        echo '<p><label><input type="checkbox" name="bss_options[social][twitter][enabled]" value="1" ' . checked($twEnabled, true, false) . ' /> ' . esc_html__('Enable Twitter Cards', 'better-seo-suite') . '</label></p>';
        $card = $opts['social']['twitter']['card_type'] ?? 'summary_large_image';
        echo '<p><label>' . esc_html__('Card Type', 'better-seo-suite') . ': <select name="bss_options[social][twitter][card_type]">';
        foreach (['summary','summary_large_image'] as $type) {
            echo '<option value="' . esc_attr($type) . '" ' . selected($card, $type, false) . '>' . esc_html($type) . '</option>';
        }
        echo '</select></label></p>';
        echo '<p><label>' . esc_html__('Twitter @site', 'better-seo-suite') . ': <input type="text" name="bss_options[social][twitter][site]" value="' . esc_attr($opts['social']['twitter']['site'] ?? '') . '" /></label></p>';

        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

