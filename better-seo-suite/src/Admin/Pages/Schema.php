<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class Schema
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
        echo '<h1>' . esc_html__('Schema', 'better-seo-suite') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('bss_options_group');

        $enabled = !empty($opts['schema']['enabled']);
        echo '<p><label><input type="checkbox" name="bss_options[schema][enabled]" value="1" ' . checked($enabled, true, false) . ' /> ' . esc_html__('Enable JSON-LD schema output', 'better-seo-suite') . '</label></p>';

        $article = !empty($opts['schema']['article']['enabled']);
        echo '<p><label><input type="checkbox" name="bss_options[schema][article][enabled]" value="1" ' . checked($article, true, false) . ' /> ' . esc_html__('Article schema for posts', 'better-seo-suite') . '</label></p>';

        $product = !empty($opts['schema']['product']['enabled']);
        echo '<p><label><input type="checkbox" name="bss_options[schema][product][enabled]" value="1" ' . checked($product, true, false) . ' /> ' . esc_html__('Product schema for products (Woo/EDD)', 'better-seo-suite') . '</label></p>';

        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

