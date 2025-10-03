<?php
if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('wp_seo_pro_general', array());
?>

<div class="wrap">
    <h1><?php _e('General SEO Settings', 'wp-seo-pro'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_seo_pro_general');
        do_settings_sections('wp_seo_pro_general');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="enable_meta_tags"><?php _e('Enable Meta Tags', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="enable_meta_tags" name="wp_seo_pro_general[enable_meta_tags]" value="1" <?php checked(isset($options['enable_meta_tags']) ? $options['enable_meta_tags'] : true, 1); ?> />
                    <p class="description"><?php _e('Enable automatic meta tag generation for all pages and posts.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="enable_schema"><?php _e('Enable Schema Markup', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="enable_schema" name="wp_seo_pro_general[enable_schema]" value="1" <?php checked(isset($options['enable_schema']) ? $options['enable_schema'] : true, 1); ?> />
                    <p class="description"><?php _e('Enable structured data markup for better search engine understanding.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="enable_sitemap"><?php _e('Enable XML Sitemap', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="enable_sitemap" name="wp_seo_pro_general[enable_sitemap]" value="1" <?php checked(isset($options['enable_sitemap']) ? $options['enable_sitemap'] : true, 1); ?> />
                    <p class="description"><?php _e('Generate XML sitemap automatically for search engines.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="enable_analytics"><?php _e('Enable Analytics', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="enable_analytics" name="wp_seo_pro_general[enable_analytics]" value="1" <?php checked(isset($options['enable_analytics']) ? $options['enable_analytics'] : true, 1); ?> />
                    <p class="description"><?php _e('Enable SEO analytics and performance tracking.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="separator"><?php _e('Title Separator', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="separator" name="wp_seo_pro_general[separator]" value="<?php echo esc_attr(isset($options['separator']) ? $options['separator'] : '|'); ?>" class="regular-text" />
                    <p class="description"><?php _e('Character used to separate title parts (e.g., Page Title | Site Name).', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="home_title"><?php _e('Home Page Title', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="home_title" name="wp_seo_pro_general[home_title]" value="<?php echo esc_attr(isset($options['home_title']) ? $options['home_title'] : get_bloginfo('name')); ?>" class="regular-text" />
                    <p class="description"><?php _e('Title for your homepage. Leave empty to use site title.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="home_description"><?php _e('Home Page Description', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <textarea id="home_description" name="wp_seo_pro_general[home_description]" rows="3" cols="50" class="large-text"><?php echo esc_textarea(isset($options['home_description']) ? $options['home_description'] : get_bloginfo('description')); ?></textarea>
                    <p class="description"><?php _e('Meta description for your homepage. Leave empty to use site description.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="wp-seo-pro-settings-help">
        <h3><?php _e('SEO Best Practices', 'wp-seo-pro'); ?></h3>
        <ul>
            <li><?php _e('Keep your title under 60 characters for optimal display in search results.', 'wp-seo-pro'); ?></li>
            <li><?php _e('Write compelling meta descriptions between 150-160 characters.', 'wp-seo-pro'); ?></li>
            <li><?php _e('Use your target keywords naturally in titles and descriptions.', 'wp-seo-pro'); ?></li>
            <li><?php _e('Enable schema markup to help search engines understand your content.', 'wp-seo-pro'); ?></li>
            <li><?php _e('Submit your XML sitemap to Google Search Console.', 'wp-seo-pro'); ?></li>
        </ul>
    </div>
</div>

<style>
.wp-seo-pro-settings-help {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-top: 30px;
}

.wp-seo-pro-settings-help h3 {
    margin-top: 0;
    color: #23282d;
}

.wp-seo-pro-settings-help ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.wp-seo-pro-settings-help li {
    margin-bottom: 8px;
    color: #666;
}
</style>