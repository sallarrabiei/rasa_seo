<?php
namespace BSS\Admin;

use BSS\Settings\Options;
use BSS\Admin\Pages\Dashboard;
use BSS\Admin\Pages\Settings as SettingsPage;
use BSS\Admin\Pages\SearchAppearance;
use BSS\Admin\Pages\Sitemaps;
use BSS\Admin\Pages\Social;
use BSS\Admin\Pages\Schema as SchemaPage;
use BSS\Admin\Pages\Integrations;
use BSS\Admin\Pages\Tools;
use BSS\Admin\MetaBoxes\MetaBox;

class Admin
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('admin_menu', [$this, 'register_menus']);
        add_action('admin_init', [$this, 'register_settings']);
        new MetaBox();
    }

    public function register_settings(): void
    {
        register_setting('bss_options_group', 'bss_options', [
            'type' => 'array',
            'sanitize_callback' => function ($input) {
                if (!is_array($input)) {
                    $input = [];
                }
                $sanitized = $this->options->sanitize($input);
                return $sanitized;
            },
            'default' => $this->options->all(),
        ]);
    }

    public function register_menus(): void
    {
        add_menu_page(
            __('Better SEO', 'better-seo-suite'),
            __('Better SEO', 'better-seo-suite'),
            'manage_options',
            'bss-dashboard',
            [new Dashboard($this->options), 'render'],
            'dashicons-chart-line',
            56
        );

        add_submenu_page('bss-dashboard', __('Dashboard', 'better-seo-suite'), __('Dashboard', 'better-seo-suite'), 'manage_options', 'bss-dashboard', [new Dashboard($this->options), 'render']);
        add_submenu_page('bss-dashboard', __('Global Settings', 'better-seo-suite'), __('Global Settings', 'better-seo-suite'), 'manage_options', 'bss-settings', [new SettingsPage($this->options), 'render']);
        add_submenu_page('bss-dashboard', __('Search Appearance', 'better-seo-suite'), __('Search Appearance', 'better-seo-suite'), 'manage_options', 'bss-search-appearance', [new SearchAppearance($this->options), 'render']);
        add_submenu_page('bss-dashboard', __('Sitemaps', 'better-seo-suite'), __('Sitemaps', 'better-seo-suite'), 'manage_options', 'bss-sitemaps', [new Sitemaps($this->options), 'render']);
        add_submenu_page('bss-dashboard', __('Social Meta', 'better-seo-suite'), __('Social Meta', 'better-seo-suite'), 'manage_options', 'bss-social', [new Social($this->options), 'render']);
        add_submenu_page('bss-dashboard', __('Schema', 'better-seo-suite'), __('Schema', 'better-seo-suite'), 'manage_options', 'bss-schema', [new SchemaPage($this->options), 'render']);
        add_submenu_page('bss-dashboard', __('Integrations', 'better-seo-suite'), __('Integrations', 'better-seo-suite'), 'manage_options', 'bss-integrations', [new Integrations($this->options), 'render']);
        add_submenu_page('bss-dashboard', __('Tools', 'better-seo-suite'), __('Tools', 'better-seo-suite'), 'manage_options', 'bss-tools', [new Tools($this->options), 'render']);
    }
}

