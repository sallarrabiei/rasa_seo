<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class Integrations
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
        echo '<h1>' . esc_html__('Integrations', 'better-seo-suite') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('bss_options_group');
        $woo = !empty($opts['integrations']['woocommerce']['enabled']);
        echo '<p><label><input type="checkbox" name="bss_options[integrations][woocommerce][enabled]" value="1" ' . checked($woo, true, false) . ' /> ' . esc_html__('Enable WooCommerce integration', 'better-seo-suite') . '</label></p>';
        $edd = !empty($opts['integrations']['edd']['enabled']);
        echo '<p><label><input type="checkbox" name="bss_options[integrations][edd][enabled]" value="1" ' . checked($edd, true, false) . ' /> ' . esc_html__('Enable Easy Digital Downloads integration', 'better-seo-suite') . '</label></p>';
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

