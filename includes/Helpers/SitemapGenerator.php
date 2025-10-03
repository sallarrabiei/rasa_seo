<?php

namespace WP_SEO_Pro\Helpers;

/**
 * XML Sitemap generator
 */
class SitemapGenerator {
    
    /**
     * Generate XML sitemap
     */
    public function generate_sitemap() {
        $options = get_option('wp_seo_pro_sitemap', array());
        
        if (empty($options['enable_xml_sitemap'])) {
            return false;
        }
        
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Add homepage
        $sitemap .= $this->get_homepage_url();
        
        // Add posts and pages
        $sitemap .= $this->get_posts_urls();
        
        // Add custom post types
        $sitemap .= $this->get_custom_post_types_urls();
        
        // Add taxonomies
        $sitemap .= $this->get_taxonomies_urls();
        
        $sitemap .= '</urlset>';
        
        // Save sitemap
        $this->save_sitemap($sitemap);
        
        return true;
    }
    
    /**
     * Get homepage URL
     */
    private function get_homepage_url() {
        $url = home_url('/');
        $lastmod = get_lastpostmodified('GMT');
        
        return $this->format_url($url, $lastmod, '1.0', 'daily');
    }
    
    /**
     * Get posts and pages URLs
     */
    private function get_posts_urls() {
        $urls = '';
        $options = get_option('wp_seo_pro_sitemap', array());
        $excluded_types = isset($options['exclude_post_types']) ? $options['exclude_post_types'] : array();
        
        $post_types = get_post_types(array('public' => true), 'names');
        $post_types = array_diff($post_types, $excluded_types);
        
        $posts_per_page = isset($options['sitemap_posts_per_page']) ? $options['sitemap_posts_per_page'] : 1000;
        
        $posts = get_posts(array(
            'post_type' => $post_types,
            'post_status' => 'publish',
            'numberposts' => $posts_per_page,
            'orderby' => 'modified',
            'order' => 'DESC'
        ));
        
        foreach ($posts as $post) {
            $url = get_permalink($post->ID);
            $lastmod = $post->post_modified_gmt;
            $priority = $this->get_post_priority($post);
            $changefreq = $this->get_post_changefreq($post);
            
            $urls .= $this->format_url($url, $lastmod, $priority, $changefreq);
        }
        
        return $urls;
    }
    
    /**
     * Get custom post types URLs
     */
    private function get_custom_post_types_urls() {
        $urls = '';
        $options = get_option('wp_seo_pro_sitemap', array());
        $excluded_types = isset($options['exclude_post_types']) ? $options['exclude_post_types'] : array();
        
        $custom_post_types = get_post_types(array(
            'public' => true,
            '_builtin' => false
        ), 'names');
        
        $custom_post_types = array_diff($custom_post_types, $excluded_types);
        
        foreach ($custom_post_types as $post_type) {
            $posts = get_posts(array(
                'post_type' => $post_type,
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby' => 'modified',
                'order' => 'DESC'
            ));
            
            foreach ($posts as $post) {
                $url = get_permalink($post->ID);
                $lastmod = $post->post_modified_gmt;
                $priority = $this->get_post_priority($post);
                $changefreq = $this->get_post_changefreq($post);
                
                $urls .= $this->format_url($url, $lastmod, $priority, $changefreq);
            }
        }
        
        return $urls;
    }
    
    /**
     * Get taxonomies URLs
     */
    private function get_taxonomies_urls() {
        $urls = '';
        $options = get_option('wp_seo_pro_sitemap', array());
        $excluded_taxonomies = isset($options['exclude_taxonomies']) ? $options['exclude_taxonomies'] : array();
        
        $taxonomies = get_taxonomies(array('public' => true), 'names');
        $taxonomies = array_diff($taxonomies, $excluded_taxonomies);
        
        foreach ($taxonomies as $taxonomy) {
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ));
            
            foreach ($terms as $term) {
                $url = get_term_link($term);
                if (!is_wp_error($url)) {
                    $lastmod = $this->get_term_lastmod($term);
                    $priority = '0.6';
                    $changefreq = 'weekly';
                    
                    $urls .= $this->format_url($url, $lastmod, $priority, $changefreq);
                }
            }
        }
        
        return $urls;
    }
    
    /**
     * Format URL for sitemap
     */
    private function format_url($url, $lastmod, $priority, $changefreq) {
        $lastmod = date('Y-m-d\TH:i:s\Z', strtotime($lastmod));
        
        return sprintf(
            '  <url>' . "\n" .
            '    <loc>%s</loc>' . "\n" .
            '    <lastmod>%s</lastmod>' . "\n" .
            '    <changefreq>%s</changefreq>' . "\n" .
            '    <priority>%s</priority>' . "\n" .
            '  </url>' . "\n",
            esc_url($url),
            $lastmod,
            $changefreq,
            $priority
        );
    }
    
    /**
     * Get post priority
     */
    private function get_post_priority($post) {
        if (is_front_page()) {
            return '1.0';
        } elseif (is_page()) {
            return '0.8';
        } elseif (is_single()) {
            return '0.7';
        } else {
            return '0.5';
        }
    }
    
    /**
     * Get post changefreq
     */
    private function get_post_changefreq($post) {
        $age = time() - strtotime($post->post_date);
        $days = $age / DAY_IN_SECONDS;
        
        if ($days < 1) {
            return 'hourly';
        } elseif ($days < 7) {
            return 'daily';
        } elseif ($days < 30) {
            return 'weekly';
        } else {
            return 'monthly';
        }
    }
    
    /**
     * Get term last modified date
     */
    private function get_term_lastmod($term) {
        $posts = get_posts(array(
            'post_type' => 'any',
            'tax_query' => array(
                array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ),
            ),
            'numberposts' => 1,
            'orderby' => 'modified',
            'order' => 'DESC'
        ));
        
        if (!empty($posts)) {
            return $posts[0]->post_modified_gmt;
        }
        
        return current_time('mysql', true);
    }
    
    /**
     * Save sitemap to file
     */
    private function save_sitemap($sitemap) {
        $upload_dir = wp_upload_dir();
        $sitemap_dir = $upload_dir['basedir'] . '/wp-seo-pro';
        
        if (!file_exists($sitemap_dir)) {
            wp_mkdir_p($sitemap_dir);
        }
        
        $sitemap_file = $sitemap_dir . '/sitemap.xml';
        file_put_contents($sitemap_file, $sitemap);
        
        // Update rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Get sitemap URL
     */
    public function get_sitemap_url() {
        $upload_dir = wp_upload_dir();
        return $upload_dir['baseurl'] . '/wp-seo-pro/sitemap.xml';
    }
}