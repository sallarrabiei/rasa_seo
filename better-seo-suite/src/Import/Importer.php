<?php
namespace BSS\Import;

class Importer
{
    public function __construct()
    {
        add_action('bss/tools/run_import', [$this, 'run'], 10, 1);
    }

    /**
     * @param array<int,string> $sources
     */
    public function run(array $sources): void
    {
        global $wpdb;
        if (in_array('yoast', $sources, true)) {
            $this->import_yoast($wpdb);
        }
        if (in_array('aioseo', $sources, true)) {
            $this->import_aioseo($wpdb);
        }
        if (in_array('rankmath', $sources, true)) {
            $this->import_rankmath($wpdb);
        }
    }

    private function import_yoast($wpdb): void
    {
        $posts = get_posts(['post_type' => 'any', 'posts_per_page' => -1, 'meta_query' => [
            ['key' => '_yoast_wpseo_title', 'compare' => 'EXISTS'],
        ]]);
        foreach ($posts as $p) {
            $title = get_post_meta($p->ID, '_yoast_wpseo_title', true);
            $desc = get_post_meta($p->ID, '_yoast_wpseo_metadesc', true);
            if ($title) update_post_meta($p->ID, '_bss_title', $title);
            if ($desc) update_post_meta($p->ID, '_bss_desc', $desc);
        }
    }

    private function import_aioseo($wpdb): void
    {
        $posts = get_posts(['post_type' => 'any', 'posts_per_page' => -1, 'meta_query' => [
            ['key' => '_aioseop_title', 'compare' => 'EXISTS'],
        ]]);
        foreach ($posts as $p) {
            $title = get_post_meta($p->ID, '_aioseop_title', true);
            $desc = get_post_meta($p->ID, '_aioseop_description', true);
            if ($title) update_post_meta($p->ID, '_bss_title', $title);
            if ($desc) update_post_meta($p->ID, '_bss_desc', $desc);
        }
    }

    private function import_rankmath($wpdb): void
    {
        $posts = get_posts(['post_type' => 'any', 'posts_per_page' => -1, 'meta_query' => [
            ['key' => 'rank_math_title', 'compare' => 'EXISTS'],
        ]]);
        foreach ($posts as $p) {
            $title = get_post_meta($p->ID, 'rank_math_title', true);
            $desc = get_post_meta($p->ID, 'rank_math_description', true);
            if ($title) update_post_meta($p->ID, '_bss_title', $title);
            if ($desc) update_post_meta($p->ID, '_bss_desc', $desc);
        }
    }
}

