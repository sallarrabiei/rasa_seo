<?php

namespace WP_SEO_Pro\Analytics;

/**
 * Analytics functionality
 */
class Analytics {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('wp_head', array($this, 'track_page_view'));
        add_action('wp_ajax_wp_seo_pro_analytics', array($this, 'handle_analytics_request'));
        add_action('wp_ajax_nopriv_wp_seo_pro_analytics', array($this, 'handle_analytics_request'));
    }
    
    /**
     * Track page view
     */
    public function track_page_view() {
        if (is_admin()) {
            return;
        }
        
        $options = get_option('wp_seo_pro_general', array());
        if (empty($options['enable_analytics'])) {
            return;
        }
        
        ?>
        <script>
        if (typeof wpSeoPro !== 'undefined' && wpSeoPro.ajaxUrl) {
            jQuery(document).ready(function($) {
                $.post(wpSeoPro.ajaxUrl, {
                    action: 'wp_seo_pro_analytics',
                    event: 'page_view',
                    url: window.location.href,
                    title: document.title,
                    referrer: document.referrer
                });
            });
        }
        </script>
        <?php
    }
    
    /**
     * Handle analytics request
     */
    public function handle_analytics_request() {
        $event = sanitize_text_field($_POST['event']);
        $url = esc_url_raw($_POST['url']);
        $title = sanitize_text_field($_POST['title']);
        $referrer = esc_url_raw($_POST['referrer']);
        
        $this->log_event($event, $url, $title, $referrer);
        
        wp_die();
    }
    
    /**
     * Log analytics event
     */
    private function log_event($event, $url, $title, $referrer) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wp_seo_pro_analytics';
        
        $wpdb->insert(
            $table_name,
            array(
                'date' => current_time('Y-m-d'),
                'metric' => $event,
                'value' => 1,
                'post_id' => url_to_postid($url),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%d', '%d', '%s')
        );
    }
    
    /**
     * Get analytics data
     */
    public function get_analytics_data($days = 30) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'wp_seo_pro_analytics';
        $start_date = date('Y-m-d', strtotime("-{$days} days"));
        
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT date, metric, SUM(value) as total
            FROM {$table_name}
            WHERE date >= %s
            GROUP BY date, metric
            ORDER BY date DESC
        ", $start_date));
        
        return $results;
    }
}