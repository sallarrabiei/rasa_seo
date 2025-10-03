<?php
namespace BSS\Admin;

class Assets
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    public function enqueue(): void
    {
        wp_enqueue_style('bss-admin', plugins_url('src/Admin/assets/admin.css', BSS_SEO_FILE), [], BSS_SEO_VERSION);
        wp_enqueue_script('bss-admin', plugins_url('src/Admin/assets/admin.js', BSS_SEO_FILE), [], BSS_SEO_VERSION, true);
    }
}

