<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Easy_Digital_Downloads')) {
    echo '<div class="notice notice-error"><p>' . __('Easy Digital Downloads is not installed or activated.', 'wp-seo-pro') . '</p></div>';
    return;
}

$options = get_option('wp_seo_pro_edd', array());
?>

<div class="wrap">
    <h1><?php _e('Easy Digital Downloads SEO Settings', 'wp-seo-pro'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_seo_pro_edd');
        do_settings_sections('wp_seo_pro_edd');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="enable_download_schema"><?php _e('Enable Download Schema', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="enable_download_schema" name="wp_seo_pro_edd[enable_download_schema]" value="1" <?php checked(isset($options['enable_download_schema']) ? $options['enable_download_schema'] : true, 1); ?> />
                    <p class="description"><?php _e('Add structured data for downloads to help search engines understand your products', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="download_title_template"><?php _e('Download Title Template', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="download_title_template" name="wp_seo_pro_edd[download_title_template]" value="<?php echo esc_attr(isset($options['download_title_template']) ? $options['download_title_template'] : '{download_name} | {site_name}'); ?>" class="regular-text" />
                    <p class="description"><?php _e('Template for download titles. Available variables: {download_name}, {site_name}, {category_name}', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="download_description_template"><?php _e('Download Description Template', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <textarea id="download_description_template" name="wp_seo_pro_edd[download_description_template]" rows="3" cols="50" class="large-text"><?php echo esc_textarea(isset($options['download_description_template']) ? $options['download_description_template'] : '{download_excerpt}'); ?></textarea>
                    <p class="description"><?php _e('Template for download descriptions. Available variables: {download_excerpt}, {download_description}', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="wp-seo-pro-edd-info">
        <h3><?php _e('EDD SEO Tips', 'wp-seo-pro'); ?></h3>
        <ul>
            <li><?php _e('Use descriptive download titles with relevant keywords', 'wp-seo-pro'); ?></li>
            <li><?php _e('Write detailed download descriptions that explain the value', 'wp-seo-pro'); ?></li>
            <li><?php _e('Optimize download images with descriptive alt text', 'wp-seo-pro'); ?></li>
            <li><?php _e('Use download categories and tags effectively', 'wp-seo-pro'); ?></li>
            <li><?php _e('Create compelling download pages that convert visitors', 'wp-seo-pro'); ?></li>
            <li><?php _e('Use structured data to help search engines understand your downloads', 'wp-seo-pro'); ?></li>
        </ul>
    </div>
</div>

<style>
.wp-seo-pro-edd-info {
    background: #f0f6fc;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.wp-seo-pro-edd-info h3 {
    margin-top: 0;
    color: #23282d;
}

.wp-seo-pro-edd-info ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.wp-seo-pro-edd-info li {
    margin-bottom: 8px;
    color: #666;
}
</style>