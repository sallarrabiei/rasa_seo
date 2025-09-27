<?php

namespace WP_SEO_Pro\Helpers;

/**
 * Schema markup helper
 */
class SchemaMarkup {
    
    /**
     * Get website schema
     */
    public function get_website_schema() {
        if (!is_home() && !is_front_page()) {
            return null;
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'description' => get_bloginfo('description'),
        );
        
        // Add search action
        $schema['potentialAction'] = array(
            '@type' => 'SearchAction',
            'target' => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string'
        );
        
        return $schema;
    }
    
    /**
     * Get organization schema
     */
    public function get_organization_schema() {
        $options = get_option('wp_seo_pro_social', array());
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'description' => get_bloginfo('description'),
        );
        
        // Add social media profiles
        $same_as = array();
        
        if (!empty($options['facebook_app_id'])) {
            $same_as[] = 'https://facebook.com/' . $options['facebook_app_id'];
        }
        
        if (!empty($options['twitter_username'])) {
            $same_as[] = 'https://twitter.com/' . $options['twitter_username'];
        }
        
        if (!empty($options['linkedin_username'])) {
            $same_as[] = 'https://linkedin.com/in/' . $options['linkedin_username'];
        }
        
        if (!empty($options['instagram_username'])) {
            $same_as[] = 'https://instagram.com/' . $options['instagram_username'];
        }
        
        if (!empty($options['youtube_username'])) {
            $same_as[] = 'https://youtube.com/user/' . $options['youtube_username'];
        }
        
        if (!empty($same_as)) {
            $schema['sameAs'] = $same_as;
        }
        
        return $schema;
    }
    
    /**
     * Get post schema
     */
    public function get_post_schema() {
        if (!is_singular()) {
            return null;
        }
        
        $post = get_post();
        $author = get_userdata($post->post_author);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title(),
            'description' => wp_trim_words(strip_tags($post->post_content), 30),
            'url' => get_permalink(),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => $author->display_name,
                'url' => get_author_posts_url($author->ID)
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url('/'),
            )
        );
        
        // Add featured image
        if (has_post_thumbnail()) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($image) {
                $schema['image'] = array(
                    '@type' => 'ImageObject',
                    'url' => $image[0],
                    'width' => $image[1],
                    'height' => $image[2]
                );
            }
        }
        
        // Add categories and tags
        $categories = get_the_category();
        if (!empty($categories)) {
            $schema['articleSection'] = array();
            foreach ($categories as $category) {
                $schema['articleSection'][] = $category->name;
            }
        }
        
        $tags = get_the_tags();
        if (!empty($tags)) {
            $schema['keywords'] = array();
            foreach ($tags as $tag) {
                $schema['keywords'][] = $tag->name;
            }
        }
        
        return $schema;
    }
    
    /**
     * Get product schema (WooCommerce)
     */
    public function get_product_schema() {
        if (!class_exists('WooCommerce') || !is_product()) {
            return null;
        }
        
        global $product;
        
        if (!$product) {
            return null;
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->get_name(),
            'description' => wp_trim_words(strip_tags($product->get_description()), 30),
            'url' => get_permalink(),
            'sku' => $product->get_sku(),
        );
        
        // Price
        if ($product->get_price()) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $product->get_price(),
                'priceCurrency' => get_woocommerce_currency(),
                'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'url' => get_permalink(),
            );
        }
        
        // Images
        $image_ids = $product->get_gallery_image_ids();
        if (!empty($image_ids)) {
            $schema['image'] = array();
            foreach ($image_ids as $image_id) {
                $image = wp_get_attachment_image_src($image_id, 'large');
                if ($image) {
                    $schema['image'][] = $image[0];
                }
            }
        }
        
        // Reviews
        if ($product->get_review_count() > 0) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $product->get_average_rating(),
                'reviewCount' => $product->get_review_count()
            );
        }
        
        return $schema;
    }
    
    /**
     * Get download schema (EDD)
     */
    public function get_download_schema() {
        if (!class_exists('Easy_Digital_Downloads') || !is_singular('download')) {
            return null;
        }
        
        $post = get_post();
        $price = edd_get_download_price($post->ID);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => get_the_title(),
            'description' => wp_trim_words(strip_tags($post->post_content), 30),
            'url' => get_permalink(),
        );
        
        // Price
        if ($price) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => edd_get_currency(),
                'availability' => 'https://schema.org/InStock',
                'url' => get_permalink(),
            );
        }
        
        // Images
        if (has_post_thumbnail()) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($image) {
                $schema['image'] = array(
                    '@type' => 'ImageObject',
                    'url' => $image[0],
                    'width' => $image[1],
                    'height' => $image[2]
                );
            }
        }
        
        return $schema;
    }
}