<?php

namespace WP_SEO_Pro\Admin\Settings;

/**
 * Sitemap settings
 */
class SitemapSettings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        add_settings_section(
            'wp_seo_pro_sitemap_section',
            __('XML Sitemap Settings', 'wp-seo-pro'),
            array($this, 'section_callback'),
            'wp_seo_pro_sitemap'
        );
        
        // Enable XML sitemap
        add_settings_field(
            'enable_xml_sitemap',
            __('Enable XML Sitemap', 'wp-seo-pro'),
            array($this, 'checkbox_callback'),
            'wp_seo_pro_sitemap',
            'wp_seo_pro_sitemap_section',
            array('field' => 'enable_xml_sitemap', 'description' => __('Generate XML sitemap automatically', 'wp-seo-pro'))
        );
        
        // Posts per page
        add_settings_field(
            'sitemap_posts_per_page',
            __('Posts Per Page', 'wp-seo-pro'),
            array($this, 'number_callback'),
            'wp_seo_pro_sitemap',
            'wp_seo_pro_sitemap_section',
            array('field' => 'sitemap_posts_per_page', 'description' => __('Number of posts to include per sitemap page', 'wp-seo-pro'))
        );
        
        // Exclude post types
        add_settings_field(
            'exclude_post_types',
            __('Exclude Post Types', 'wp-seo-pro'),
            array($this, 'post_types_callback'),
            'wp_seo_pro_sitemap',
            'wp_seo_pro_sitemap_section',
            array('field' => 'exclude_post_types', 'description' => __('Select post types to exclude from sitemap', 'wp-seo-pro'))
        );
        
        // Exclude taxonomies
        add_settings_field(
            'exclude_taxonomies',
            __('Exclude Taxonomies', 'wp-seo-pro'),
            array($this, 'taxonomies_callback'),
            'wp_seo_pro_sitemap',
            'wp_seo_pro_sitemap_section',
            array('field' => 'exclude_taxonomies', 'description' => __('Select taxonomies to exclude from sitemap', 'wp-seo-pro'))
        );
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>' . __('Configure your XML sitemap settings for better search engine indexing.', 'wp-seo-pro') . '</p>';
    }
    
    /**
     * Checkbox callback
     */
    public function checkbox_callback($args) {
        $options = get_option('wp_seo_pro_sitemap');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : true;
        
        echo '<input type="checkbox" id="' . $args['field'] . '" name="wp_seo_pro_sitemap[' . $args['field'] . ']" value="1" ' . checked(1, $value, false) . ' />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Number callback
     */
    public function number_callback($args) {
        $options = get_option('wp_seo_pro_sitemap');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : 1000;
        
        echo '<input type="number" id="' . $args['field'] . '" name="wp_seo_pro_sitemap[' . $args['field'] . ']" value="' . esc_attr($value) . '" class="small-text" min="1" max="50000" />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Post types callback
     */
    public function post_types_callback($args) {
        $options = get_option('wp_seo_pro_sitemap');
        $excluded = isset($options[$args['field']]) ? $options[$args['field']] : array();
        
        $post_types = get_post_types(array('public' => true), 'objects');
        
        echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
        foreach ($post_types as $post_type) {
            $checked = in_array($post_type->name, $excluded) ? 'checked' : '';
            echo '<label style="display: block; margin-bottom: 5px;">';
            echo '<input type="checkbox" name="wp_seo_pro_sitemap[' . $args['field'] . '][]" value="' . esc_attr($post_type->name) . '" ' . $checked . ' /> ';
            echo esc_html($post_type->label);
            echo '</label>';
        }
        echo '</div>';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
    
    /**
     * Taxonomies callback
     */
    public function taxonomies_callback($args) {
        $options = get_option('wp_seo_pro_sitemap');
        $excluded = isset($options[$args['field']]) ? $options[$args['field']] : array();
        
        $taxonomies = get_taxonomies(array('public' => true), 'objects');
        
        echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">';
        foreach ($taxonomies as $taxonomy) {
            $checked = in_array($taxonomy->name, $excluded) ? 'checked' : '';
            echo '<label style="display: block; margin-bottom: 5px;">';
            echo '<input type="checkbox" name="wp_seo_pro_sitemap[' . $args['field'] . '][]" value="' . esc_attr($taxonomy->name) . '" ' . $checked . ' /> ';
            echo esc_html($taxonomy->label);
            echo '</label>';
        }
        echo '</div>';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
}