<?php

namespace WP_SEO_Pro\Frontend;

use WP_SEO_Pro\Helpers\MetaTags;
use WP_SEO_Pro\Helpers\SchemaMarkup;

/**
 * Frontend functionality
 */
class Frontend {
    
    /**
     * Meta tags helper
     */
    private $meta_tags;
    
    /**
     * Schema markup helper
     */
    private $schema_markup;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->meta_tags = new MetaTags();
        $this->schema_markup = new SchemaMarkup();
    }
    
    /**
     * Get meta tags for current page
     */
    public function get_meta_tags() {
        if (is_admin()) {
            return '';
        }
        
        $meta_tags = array();
        
        // Title tag
        $title = $this->get_page_title();
        if ($title) {
            $meta_tags[] = '<title>' . esc_html($title) . '</title>';
        }
        
        // Meta description
        $description = $this->get_page_description();
        if ($description) {
            $meta_tags[] = '<meta name="description" content="' . esc_attr($description) . '">';
        }
        
        // Meta keywords
        $keywords = $this->get_page_keywords();
        if ($keywords) {
            $meta_tags[] = '<meta name="keywords" content="' . esc_attr($keywords) . '">';
        }
        
        // Canonical URL
        $canonical = $this->get_canonical_url();
        if ($canonical) {
            $meta_tags[] = '<link rel="canonical" href="' . esc_url($canonical) . '">';
        }
        
        // Robots meta
        $robots = $this->get_robots_meta();
        if ($robots) {
            $meta_tags[] = '<meta name="robots" content="' . esc_attr($robots) . '">';
        }
        
        // Open Graph tags
        $og_tags = $this->get_og_tags();
        $meta_tags = array_merge($meta_tags, $og_tags);
        
        // Twitter Card tags
        $twitter_tags = $this->get_twitter_tags();
        $meta_tags = array_merge($meta_tags, $twitter_tags);
        
        return implode("\n", $meta_tags);
    }
    
    /**
     * Get schema markup for current page
     */
    public function get_schema_markup() {
        if (is_admin()) {
            return '';
        }
        
        $schema = array();
        
        // Website schema
        $website_schema = $this->schema_markup->get_website_schema();
        if ($website_schema) {
            $schema[] = $website_schema;
        }
        
        // Organization schema
        $organization_schema = $this->schema_markup->get_organization_schema();
        if ($organization_schema) {
            $schema[] = $organization_schema;
        }
        
        // Post/Page schema
        if (is_singular()) {
            $post_schema = $this->schema_markup->get_post_schema();
            if ($post_schema) {
                $schema[] = $post_schema;
            }
        }
        
        // WooCommerce product schema
        if (class_exists('WooCommerce') && is_product()) {
            $product_schema = $this->schema_markup->get_product_schema();
            if ($product_schema) {
                $schema[] = $product_schema;
            }
        }
        
        // EDD download schema
        if (class_exists('Easy_Digital_Downloads') && is_singular('download')) {
            $download_schema = $this->schema_markup->get_download_schema();
            if ($download_schema) {
                $schema[] = $download_schema;
            }
        }
        
        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Get page title
     */
    private function get_page_title() {
        $title = '';
        
        if (is_home() || is_front_page()) {
            $options = get_option('wp_seo_pro_general', array());
            $title = isset($options['home_title']) ? $options['home_title'] : get_bloginfo('name');
        } elseif (is_singular()) {
            $custom_title = get_post_meta(get_the_ID(), '_wp_seo_pro_title', true);
            if ($custom_title) {
                $title = $custom_title;
            } else {
                $title = get_the_title();
                $options = get_option('wp_seo_pro_general', array());
                $separator = isset($options['separator']) ? $options['separator'] : '|';
                $title .= ' ' . $separator . ' ' . get_bloginfo('name');
            }
        } elseif (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            $title = $term->name;
            $options = get_option('wp_seo_pro_general', array());
            $separator = isset($options['separator']) ? $options['separator'] : '|';
            $title .= ' ' . $separator . ' ' . get_bloginfo('name');
        } elseif (is_archive()) {
            $title = get_the_archive_title();
        } elseif (is_search()) {
            $title = sprintf(__('Search Results for: %s', 'wp-seo-pro'), get_search_query());
        } elseif (is_404()) {
            $title = __('Page Not Found', 'wp-seo-pro');
        }
        
        return apply_filters('wp_seo_pro_title', $title);
    }
    
    /**
     * Get page description
     */
    private function get_page_description() {
        $description = '';
        
        if (is_home() || is_front_page()) {
            $options = get_option('wp_seo_pro_general', array());
            $description = isset($options['home_description']) ? $options['home_description'] : get_bloginfo('description');
        } elseif (is_singular()) {
            $custom_desc = get_post_meta(get_the_ID(), '_wp_seo_pro_description', true);
            if ($custom_desc) {
                $description = $custom_desc;
            } else {
                $post = get_post();
                if ($post && $post->post_excerpt) {
                    $description = $post->post_excerpt;
                } else {
                    $description = wp_trim_words(strip_tags($post->post_content), 30);
                }
            }
        } elseif (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            $description = $term->description;
        }
        
        return apply_filters('wp_seo_pro_description', $description);
    }
    
    /**
     * Get page keywords
     */
    private function get_page_keywords() {
        $keywords = '';
        
        if (is_singular()) {
            $custom_keywords = get_post_meta(get_the_ID(), '_wp_seo_pro_keywords', true);
            if ($custom_keywords) {
                $keywords = $custom_keywords;
            }
        }
        
        return apply_filters('wp_seo_pro_keywords', $keywords);
    }
    
    /**
     * Get canonical URL
     */
    private function get_canonical_url() {
        $canonical = '';
        
        if (is_singular()) {
            $custom_canonical = get_post_meta(get_the_ID(), '_wp_seo_pro_canonical', true);
            if ($custom_canonical) {
                $canonical = $custom_canonical;
            } else {
                $canonical = get_permalink();
            }
        } elseif (is_home() || is_front_page()) {
            $canonical = home_url('/');
        } elseif (is_category() || is_tag() || is_tax()) {
            $canonical = get_term_link(get_queried_object());
        }
        
        return apply_filters('wp_seo_pro_canonical', $canonical);
    }
    
    /**
     * Get robots meta
     */
    private function get_robots_meta() {
        $robots = array();
        
        if (is_singular()) {
            $noindex = get_post_meta(get_the_ID(), '_wp_seo_pro_noindex', true);
            $nofollow = get_post_meta(get_the_ID(), '_wp_seo_pro_nofollow', true);
            
            if ($noindex) {
                $robots[] = 'noindex';
            }
            if ($nofollow) {
                $robots[] = 'nofollow';
            }
        }
        
        // Default robots
        if (empty($robots)) {
            $robots[] = 'index';
            $robots[] = 'follow';
        }
        
        return implode(', ', $robots);
    }
    
    /**
     * Get Open Graph tags
     */
    private function get_og_tags() {
        $og_tags = array();
        
        // Basic OG tags
        $og_tags[] = '<meta property="og:type" content="' . $this->get_og_type() . '">';
        $og_tags[] = '<meta property="og:title" content="' . esc_attr($this->get_og_title()) . '">';
        $og_tags[] = '<meta property="og:description" content="' . esc_attr($this->get_og_description()) . '">';
        $og_tags[] = '<meta property="og:url" content="' . esc_url($this->get_canonical_url()) . '">';
        $og_tags[] = '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">';
        
        // OG Image
        $og_image = $this->get_og_image();
        if ($og_image) {
            $og_tags[] = '<meta property="og:image" content="' . esc_url($og_image) . '">';
        }
        
        return $og_tags;
    }
    
    /**
     * Get Twitter Card tags
     */
    private function get_twitter_tags() {
        $twitter_tags = array();
        
        $options = get_option('wp_seo_pro_social', array());
        $twitter_username = isset($options['twitter_username']) ? $options['twitter_username'] : '';
        
        if ($twitter_username) {
            $twitter_tags[] = '<meta name="twitter:card" content="summary_large_image">';
            $twitter_tags[] = '<meta name="twitter:site" content="@' . esc_attr($twitter_username) . '">';
            $twitter_tags[] = '<meta name="twitter:title" content="' . esc_attr($this->get_og_title()) . '">';
            $twitter_tags[] = '<meta name="twitter:description" content="' . esc_attr($this->get_og_description()) . '">';
            
            $og_image = $this->get_og_image();
            if ($og_image) {
                $twitter_tags[] = '<meta name="twitter:image" content="' . esc_url($og_image) . '">';
            }
        }
        
        return $twitter_tags;
    }
    
    /**
     * Get OG type
     */
    private function get_og_type() {
        if (is_home() || is_front_page()) {
            return 'website';
        } elseif (is_singular()) {
            return 'article';
        }
        return 'website';
    }
    
    /**
     * Get OG title
     */
    private function get_og_title() {
        if (is_singular()) {
            $custom_og_title = get_post_meta(get_the_ID(), '_wp_seo_pro_og_title', true);
            if ($custom_og_title) {
                return $custom_og_title;
            }
        }
        return $this->get_page_title();
    }
    
    /**
     * Get OG description
     */
    private function get_og_description() {
        if (is_singular()) {
            $custom_og_desc = get_post_meta(get_the_ID(), '_wp_seo_pro_og_description', true);
            if ($custom_og_desc) {
                return $custom_og_desc;
            }
        }
        return $this->get_page_description();
    }
    
    /**
     * Get OG image
     */
    private function get_og_image() {
        if (is_singular()) {
            $custom_og_image = get_post_meta(get_the_ID(), '_wp_seo_pro_og_image', true);
            if ($custom_og_image) {
                return $custom_og_image;
            }
            
            if (has_post_thumbnail()) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
                if ($image) {
                    return $image[0];
                }
            }
        }
        
        return '';
    }
}