<?php
if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('wp_seo_pro_social', array());
?>

<div class="wrap">
    <h1><?php _e('Social Media Settings', 'wp-seo-pro'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_seo_pro_social');
        do_settings_sections('wp_seo_pro_social');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="facebook_app_id"><?php _e('Facebook App ID', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="facebook_app_id" name="wp_seo_pro_social[facebook_app_id]" value="<?php echo esc_attr(isset($options['facebook_app_id']) ? $options['facebook_app_id'] : ''); ?>" class="regular-text" />
                    <p class="description"><?php _e('Your Facebook App ID for Open Graph integration', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="twitter_username"><?php _e('Twitter Username', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="twitter_username" name="wp_seo_pro_social[twitter_username]" value="<?php echo esc_attr(isset($options['twitter_username']) ? $options['twitter_username'] : ''); ?>" class="regular-text" placeholder="@username" />
                    <p class="description"><?php _e('Your Twitter username (without @)', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="linkedin_username"><?php _e('LinkedIn Username', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="linkedin_username" name="wp_seo_pro_social[linkedin_username]" value="<?php echo esc_attr(isset($options['linkedin_username']) ? $options['linkedin_username'] : ''); ?>" class="regular-text" />
                    <p class="description"><?php _e('Your LinkedIn username or company page', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="instagram_username"><?php _e('Instagram Username', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="instagram_username" name="wp_seo_pro_social[instagram_username]" value="<?php echo esc_attr(isset($options['instagram_username']) ? $options['instagram_username'] : ''); ?>" class="regular-text" />
                    <p class="description"><?php _e('Your Instagram username', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="youtube_username"><?php _e('YouTube Username', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="youtube_username" name="wp_seo_pro_social[youtube_username]" value="<?php echo esc_attr(isset($options['youtube_username']) ? $options['youtube_username'] : ''); ?>" class="regular-text" />
                    <p class="description"><?php _e('Your YouTube username or channel ID', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="wp-seo-pro-settings-help">
        <h3><?php _e('Social Media Best Practices', 'wp-seo-pro'); ?></h3>
        <ul>
            <li><?php _e('Use consistent usernames across all social platforms', 'wp-seo-pro'); ?></li>
            <li><?php _e('Create high-quality, engaging content for social sharing', 'wp-seo-pro'); ?></li>
            <li><?php _e('Use relevant hashtags to increase visibility', 'wp-seo-pro'); ?></li>
            <li><?php _e('Optimize your social media profiles for SEO', 'wp-seo-pro'); ?></li>
            <li><?php _e('Engage with your audience regularly', 'wp-seo-pro'); ?></li>
        </ul>
    </div>
</div>