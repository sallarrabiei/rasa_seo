<?php
if (!defined('ABSPATH')) {
    exit;
}

$general_options = get_option('wp_seo_pro_general', array());
$social_options = get_option('wp_seo_pro_social', array());
$sitemap_options = get_option('wp_seo_pro_sitemap', array());
?>

<div class="wrap wp-seo-pro-dashboard">
    <h1><?php _e('WP SEO Pro Dashboard', 'wp-seo-pro'); ?></h1>
    
    <div class="wp-seo-pro-welcome">
        <div class="wp-seo-pro-welcome-content">
            <h2><?php _e('Welcome to WP SEO Pro!', 'wp-seo-pro'); ?></h2>
            <p><?php _e('Your comprehensive SEO solution for WordPress. Configure your settings below to get started.', 'wp-seo-pro'); ?></p>
        </div>
    </div>
    
    <div class="wp-seo-pro-dashboard-grid">
        <div class="wp-seo-pro-dashboard-card">
            <h3><?php _e('Quick Setup', 'wp-seo-pro'); ?></h3>
            <div class="wp-seo-pro-setup-status">
                <?php
                $setup_complete = 0;
                $total_setup = 4;
                
                // Check if basic settings are configured
                if (!empty($general_options['home_title']) && !empty($general_options['home_description'])) {
                    $setup_complete++;
                    echo '<div class="setup-item completed">✓ ' . __('Basic Settings', 'wp-seo-pro') . '</div>';
                } else {
                    echo '<div class="setup-item incomplete">○ ' . __('Basic Settings', 'wp-seo-pro') . '</div>';
                }
                
                // Check if social settings are configured
                if (!empty($social_options['facebook_app_id']) || !empty($social_options['twitter_username'])) {
                    $setup_complete++;
                    echo '<div class="setup-item completed">✓ ' . __('Social Media', 'wp-seo-pro') . '</div>';
                } else {
                    echo '<div class="setup-item incomplete">○ ' . __('Social Media', 'wp-seo-pro') . '</div>';
                }
                
                // Check if sitemap is enabled
                if (!empty($sitemap_options['enable_xml_sitemap'])) {
                    $setup_complete++;
                    echo '<div class="setup-item completed">✓ ' . __('XML Sitemap', 'wp-seo-pro') . '</div>';
                } else {
                    echo '<div class="setup-item incomplete">○ ' . __('XML Sitemap', 'wp-seo-pro') . '</div>';
                }
                
                // Check if migration is needed
                $migrated_yoast = get_option('wp_seo_pro_migrated_yoast', false);
                $migrated_aioseo = get_option('wp_seo_pro_migrated_aioseo', false);
                $migrated_rankmath = get_option('wp_seo_pro_migrated_rankmath', false);
                
                if ($migrated_yoast || $migrated_aioseo || $migrated_rankmath) {
                    $setup_complete++;
                    echo '<div class="setup-item completed">✓ ' . __('Data Migration', 'wp-seo-pro') . '</div>';
                } else {
                    echo '<div class="setup-item incomplete">○ ' . __('Data Migration', 'wp-seo-pro') . '</div>';
                }
                ?>
            </div>
            
            <div class="setup-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo ($setup_complete / $total_setup) * 100; ?>%"></div>
                </div>
                <p><?php printf(__('Setup Progress: %d/%d', 'wp-seo-pro'), $setup_complete, $total_setup); ?></p>
            </div>
        </div>
        
        <div class="wp-seo-pro-dashboard-card">
            <h3><?php _e('SEO Overview', 'wp-seo-pro'); ?></h3>
            <div class="seo-stats">
                <?php
                $total_posts = wp_count_posts('post');
                $total_pages = wp_count_posts('page');
                $total_products = class_exists('WooCommerce') ? wp_count_posts('product') : null;
                
                echo '<div class="stat-item">';
                echo '<span class="stat-number">' . $total_posts->publish . '</span>';
                echo '<span class="stat-label">' . __('Posts', 'wp-seo-pro') . '</span>';
                echo '</div>';
                
                echo '<div class="stat-item">';
                echo '<span class="stat-number">' . $total_pages->publish . '</span>';
                echo '<span class="stat-label">' . __('Pages', 'wp-seo-pro') . '</span>';
                echo '</div>';
                
                if ($total_products) {
                    echo '<div class="stat-item">';
                    echo '<span class="stat-number">' . $total_products->publish . '</span>';
                    echo '<span class="stat-label">' . __('Products', 'wp-seo-pro') . '</span>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <div class="wp-seo-pro-dashboard-card">
            <h3><?php _e('Quick Actions', 'wp-seo-pro'); ?></h3>
            <div class="quick-actions">
                <a href="<?php echo admin_url('admin.php?page=wp-seo-pro-general'); ?>" class="button button-primary">
                    <?php _e('Configure Settings', 'wp-seo-pro'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=wp-seo-pro-migration'); ?>" class="button">
                    <?php _e('Migrate Data', 'wp-seo-pro'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=wp-seo-pro-sitemap'); ?>" class="button">
                    <?php _e('Sitemap Settings', 'wp-seo-pro'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=wp-seo-pro-analytics'); ?>" class="button">
                    <?php _e('View Analytics', 'wp-seo-pro'); ?>
                </a>
            </div>
        </div>
        
        <div class="wp-seo-pro-dashboard-card">
            <h3><?php _e('Recent Activity', 'wp-seo-pro'); ?></h3>
            <div class="recent-activity">
                <?php
                $recent_posts = get_posts(array(
                    'numberposts' => 5,
                    'post_status' => 'publish',
                    'orderby' => 'modified',
                    'order' => 'DESC'
                ));
                
                if ($recent_posts) {
                    echo '<ul>';
                    foreach ($recent_posts as $post) {
                        $seo_title = get_post_meta($post->ID, '_wp_seo_pro_title', true);
                        $seo_desc = get_post_meta($post->ID, '_wp_seo_pro_description', true);
                        
                        echo '<li>';
                        echo '<strong>' . esc_html($post->post_title) . '</strong>';
                        echo '<br>';
                        echo '<small>';
                        if ($seo_title) {
                            echo '✓ ' . __('SEO Title', 'wp-seo-pro');
                        } else {
                            echo '○ ' . __('SEO Title', 'wp-seo-pro');
                        }
                        echo ' | ';
                        if ($seo_desc) {
                            echo '✓ ' . __('Meta Description', 'wp-seo-pro');
                        } else {
                            echo '○ ' . __('Meta Description', 'wp-seo-pro');
                        }
                        echo '</small>';
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>' . __('No recent activity.', 'wp-seo-pro') . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <div class="wp-seo-pro-dashboard-footer">
        <div class="wp-seo-pro-dashboard-card">
            <h3><?php _e('Need Help?', 'wp-seo-pro'); ?></h3>
            <p><?php _e('Check out our documentation and support resources:', 'wp-seo-pro'); ?></p>
            <ul>
                <li><a href="#" target="_blank"><?php _e('Documentation', 'wp-seo-pro'); ?></a></li>
                <li><a href="#" target="_blank"><?php _e('Video Tutorials', 'wp-seo-pro'); ?></a></li>
                <li><a href="#" target="_blank"><?php _e('Support Forum', 'wp-seo-pro'); ?></a></li>
            </ul>
        </div>
    </div>
</div>

<style>
.wp-seo-pro-dashboard {
    max-width: 1200px;
}

.wp-seo-pro-welcome {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.wp-seo-pro-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.wp-seo-pro-dashboard-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.wp-seo-pro-dashboard-card h3 {
    margin-top: 0;
    color: #23282d;
}

.setup-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f1;
}

.setup-item:last-child {
    border-bottom: none;
}

.setup-item.completed {
    color: #00a32a;
}

.setup-item.incomplete {
    color: #d63638;
}

.setup-progress {
    margin-top: 15px;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background: #f0f0f1;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00a32a, #00b32a);
    transition: width 0.3s ease;
}

.seo-stats {
    display: flex;
    justify-content: space-around;
    text-align: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

.stat-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.quick-actions .button {
    text-align: center;
}

.recent-activity ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.recent-activity li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f1;
}

.recent-activity li:last-child {
    border-bottom: none;
}

.wp-seo-pro-dashboard-footer {
    margin-top: 20px;
}

.wp-seo-pro-dashboard-footer ul {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
}

.wp-seo-pro-dashboard-footer li {
    display: inline-block;
    margin-right: 20px;
}
</style>