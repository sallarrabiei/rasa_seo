<?php

namespace WP_SEO_Pro\Admin\Settings;

/**
 * Social media settings
 */
class SocialSettings {
    
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
            'wp_seo_pro_social_section',
            __('Social Media Settings', 'wp-seo-pro'),
            array($this, 'section_callback'),
            'wp_seo_pro_social'
        );
        
        // Facebook App ID
        add_settings_field(
            'facebook_app_id',
            __('Facebook App ID', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_social',
            'wp_seo_pro_social_section',
            array('field' => 'facebook_app_id', 'description' => __('Your Facebook App ID for Open Graph', 'wp-seo-pro'))
        );
        
        // Twitter Username
        add_settings_field(
            'twitter_username',
            __('Twitter Username', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_social',
            'wp_seo_pro_social_section',
            array('field' => 'twitter_username', 'description' => __('Your Twitter username (without @)', 'wp-seo-pro'))
        );
        
        // LinkedIn Username
        add_settings_field(
            'linkedin_username',
            __('LinkedIn Username', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_social',
            'wp_seo_pro_social_section',
            array('field' => 'linkedin_username', 'description' => __('Your LinkedIn username', 'wp-seo-pro'))
        );
        
        // Instagram Username
        add_settings_field(
            'instagram_username',
            __('Instagram Username', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_social',
            'wp_seo_pro_social_section',
            array('field' => 'instagram_username', 'description' => __('Your Instagram username', 'wp-seo-pro'))
        );
        
        // YouTube Username
        add_settings_field(
            'youtube_username',
            __('YouTube Username', 'wp-seo-pro'),
            array($this, 'text_callback'),
            'wp_seo_pro_social',
            'wp_seo_pro_social_section',
            array('field' => 'youtube_username', 'description' => __('Your YouTube username or channel ID', 'wp-seo-pro'))
        );
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>' . __('Configure your social media profiles for better social sharing and SEO.', 'wp-seo-pro') . '</p>';
    }
    
    /**
     * Text callback
     */
    public function text_callback($args) {
        $options = get_option('wp_seo_pro_social');
        $value = isset($options[$args['field']]) ? $options[$args['field']] : '';
        
        echo '<input type="text" id="' . $args['field'] . '" name="wp_seo_pro_social[' . $args['field'] . ']" value="' . esc_attr($value) . '" class="regular-text" />';
        
        if (isset($args['description'])) {
            echo '<p class="description">' . $args['description'] . '</p>';
        }
    }
}