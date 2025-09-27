<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class Dashboard
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
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Better SEO - Dashboard', 'better-seo-suite') . '</h1>';
        echo '<p>' . esc_html__('Welcome! Use the sections on the left to configure your SEO settings. ', 'better-seo-suite') . '</p>';

        echo '<h2>' . esc_html__('Quick Links', 'better-seo-suite') . '</h2>';
        echo '<ul>';
        echo '<li><a href="' . esc_url(admin_url('admin.php?page=bss-settings')) . '">' . esc_html__('Global Settings', 'better-seo-suite') . '</a></li>';
        echo '<li><a href="' . esc_url(admin_url('admin.php?page=bss-search-appearance')) . '">' . esc_html__('Search Appearance', 'better-seo-suite') . '</a></li>';
        echo '<li><a href="' . esc_url(admin_url('admin.php?page=bss-sitemaps')) . '">' . esc_html__('Sitemaps', 'better-seo-suite') . '</a></li>';
        echo '<li><a href="' . esc_url(admin_url('admin.php?page=bss-social')) . '">' . esc_html__('Social Meta', 'better-seo-suite') . '</a></li>';
        echo '<li><a href="' . esc_url(admin_url('admin.php?page=bss-schema')) . '">' . esc_html__('Schema', 'better-seo-suite') . '</a></li>';
        echo '<li><a href="' . esc_url(admin_url('admin.php?page=bss-tools')) . '">' . esc_html__('Tools (Import)', 'better-seo-suite') . '</a></li>';
        echo '</ul>';

        echo '</div>';
    }
}

