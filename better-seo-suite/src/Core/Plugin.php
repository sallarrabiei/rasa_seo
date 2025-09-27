<?php
namespace BSS\Core;

use BSS\Admin\Admin;
use BSS\Admin\Assets;
use BSS\Admin\Pages\SetupWizard;
use BSS\Admin\SetupHandlers;
use BSS\Settings\Options;
use BSS\Frontend\MetaRenderer;
use BSS\Frontend\Social\SocialMeta;
use BSS\Frontend\Schema\SchemaController;
use BSS\Frontend\Sitemap\SitemapController;
use BSS\Frontend\Robots\RobotsController;
use BSS\Integrations\WooCommerce as WooIntegration;
use BSS\Integrations\EDD as EddIntegration;
use BSS\Breadcrumbs\Breadcrumbs;

class Plugin
{
    /** @var Plugin|null */
    private static $instance;

    /** @var Options */
    private $options;

    /** @var Admin */
    private $admin;

    public static function get_instance(): Plugin
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function boot(): void
    {
        $this->options = new Options();

        if (is_admin()) {
            $this->admin = new Admin($this->options);
            new Assets();
            new SetupWizard($this->options);
            new SetupHandlers($this->options);
        }

        // Frontend features
        new MetaRenderer($this->options);
        new SocialMeta($this->options);
        new SchemaController($this->options);
        new SitemapController($this->options);
        new RobotsController($this->options);
        new Breadcrumbs();

        // Integrations and importer
        add_action('plugins_loaded', function () {
            if (class_exists('WooCommerce')) {
                new WooIntegration($this->options);
            }
            if (class_exists('Easy_Digital_Downloads')) {
                new EddIntegration($this->options);
            }
            // Importer bootstraps hooks for tools page
            if (class_exists('BSS\\Import\\Importer') === false) {
                // class autoloaded when referenced
            }
            new \BSS\Import\Importer();
        }, 20);
    }

    public function on_activate(): void
    {
        // Ensure default options exist
        $this->get_options()->ensure_defaults();

        // Register rewrites used by sitemaps
        do_action('bss_register_rewrites');
        flush_rewrite_rules();
    }

    public function on_deactivate(): void
    {
        flush_rewrite_rules();
    }

    public static function on_uninstall(): void
    {
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            return;
        }
        // Delete plugin options. Post meta is left intact by default.
        delete_option('bss_options');
    }

    public function get_options(): Options
    {
        return $this->options;
    }
}

