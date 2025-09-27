<?php
if (!defined('ABSPATH')) {
    exit;
}

$analytics = new WP_SEO_Pro\Analytics\Analytics();
$analytics_data = $analytics->get_analytics_data(30);
?>

<div class="wrap">
    <h1><?php _e('SEO Analytics', 'wp-seo-pro'); ?></h1>
    
    <div class="wp-seo-pro-analytics-overview">
        <div class="analytics-cards">
            <div class="analytics-card">
                <h3><?php _e('Page Views (30 days)', 'wp-seo-pro'); ?></h3>
                <div class="analytics-number">
                    <?php
                    $total_views = 0;
                    foreach ($analytics_data as $data) {
                        if ($data->metric === 'page_view') {
                            $total_views += $data->total;
                        }
                    }
                    echo number_format($total_views);
                    ?>
                </div>
            </div>
            
            <div class="analytics-card">
                <h3><?php _e('Total Posts', 'wp-seo-pro'); ?></h3>
                <div class="analytics-number">
                    <?php
                    $posts = wp_count_posts('post');
                    echo number_format($posts->publish);
                    ?>
                </div>
            </div>
            
            <div class="analytics-card">
                <h3><?php _e('Total Pages', 'wp-seo-pro'); ?></h3>
                <div class="analytics-number">
                    <?php
                    $pages = wp_count_posts('page');
                    echo number_format($pages->publish);
                    ?>
                </div>
            </div>
            
            <div class="analytics-card">
                <h3><?php _e('SEO Score', 'wp-seo-pro'); ?></h3>
                <div class="analytics-number">
                    <?php
                    // Calculate basic SEO score based on content with SEO data
                    $posts_with_seo = 0;
                    $total_posts = $posts->publish + $pages->publish;
                    
                    if ($total_posts > 0) {
                        global $wpdb;
                        $posts_with_seo = $wpdb->get_var("
                            SELECT COUNT(DISTINCT post_id) 
                            FROM {$wpdb->postmeta} 
                            WHERE meta_key IN ('_wp_seo_pro_title', '_wp_seo_pro_description')
                        ");
                        
                        $seo_score = round(($posts_with_seo / $total_posts) * 100);
                        echo $seo_score . '%';
                    } else {
                        echo '0%';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="wp-seo-pro-analytics-charts">
        <h2><?php _e('Analytics Charts', 'wp-seo-pro'); ?></h2>
        <div class="chart-container">
            <canvas id="analytics-chart" width="400" height="200"></canvas>
        </div>
    </div>
    
    <div class="wp-seo-pro-analytics-recommendations">
        <h2><?php _e('SEO Recommendations', 'wp-seo-pro'); ?></h2>
        <div class="recommendations-list">
            <?php
            $recommendations = array();
            
            // Check for posts without SEO titles
            $posts_without_title = $wpdb->get_var("
                SELECT COUNT(*) 
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_seo_pro_title'
                WHERE p.post_status = 'publish' 
                AND p.post_type IN ('post', 'page')
                AND pm.meta_value IS NULL
            ");
            
            if ($posts_without_title > 0) {
                $recommendations[] = sprintf(
                    __('%d posts/pages need SEO titles', 'wp-seo-pro'),
                    $posts_without_title
                );
            }
            
            // Check for posts without meta descriptions
            $posts_without_desc = $wpdb->get_var("
                SELECT COUNT(*) 
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_seo_pro_description'
                WHERE p.post_status = 'publish' 
                AND p.post_type IN ('post', 'page')
                AND pm.meta_value IS NULL
            ");
            
            if ($posts_without_desc > 0) {
                $recommendations[] = sprintf(
                    __('%d posts/pages need meta descriptions', 'wp-seo-pro'),
                    $posts_without_desc
                );
            }
            
            // Check if sitemap is enabled
            $sitemap_options = get_option('wp_seo_pro_sitemap', array());
            if (empty($sitemap_options['enable_xml_sitemap'])) {
                $recommendations[] = __('Enable XML sitemap for better search engine indexing', 'wp-seo-pro');
            }
            
            // Check if social media is configured
            $social_options = get_option('wp_seo_pro_social', array());
            if (empty($social_options['facebook_app_id']) && empty($social_options['twitter_username'])) {
                $recommendations[] = __('Configure social media profiles for better social sharing', 'wp-seo-pro');
            }
            
            if (empty($recommendations)) {
                echo '<p class="no-recommendations">' . __('Great job! Your SEO is well optimized.', 'wp-seo-pro') . '</p>';
            } else {
                echo '<ul>';
                foreach ($recommendations as $recommendation) {
                    echo '<li>' . esc_html($recommendation) . '</li>';
                }
                echo '</ul>';
            }
            ?>
        </div>
    </div>
</div>

<style>
.wp-seo-pro-analytics-overview {
    margin: 20px 0;
}

.analytics-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.analytics-card {
    background: #fff;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.analytics-card h3 {
    margin: 0 0 15px 0;
    color: #666;
    font-size: 14px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.analytics-number {
    font-size: 32px;
    font-weight: 700;
    color: #0073aa;
    line-height: 1;
}

.wp-seo-pro-analytics-charts {
    background: #fff;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.wp-seo-pro-analytics-charts h2 {
    margin-top: 0;
    color: #23282d;
}

.chart-container {
    margin-top: 20px;
}

.wp-seo-pro-analytics-recommendations {
    background: #fff;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.wp-seo-pro-analytics-recommendations h2 {
    margin-top: 0;
    color: #23282d;
}

.recommendations-list ul {
    margin: 15px 0 0 0;
    padding-left: 20px;
}

.recommendations-list li {
    margin-bottom: 10px;
    color: #666;
    line-height: 1.5;
}

.no-recommendations {
    color: #00a32a;
    font-weight: 500;
    margin: 15px 0 0 0;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Simple analytics chart using Chart.js (if available)
    if (typeof Chart !== 'undefined') {
        var ctx = document.getElementById('analytics-chart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Page Views',
                    data: [12, 19, 3, 5],
                    borderColor: '#0073aa',
                    backgroundColor: 'rgba(0, 115, 170, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>