<?php
if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('wp_seo_pro_advanced', array());
?>

<div class="wrap">
    <h1><?php _e('Advanced SEO Settings', 'wp-seo-pro'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_seo_pro_advanced');
        do_settings_sections('wp_seo_pro_advanced');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Indexing Control', 'wp-seo-pro'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="wp_seo_pro_advanced[noindex_archive]" value="1" <?php checked(isset($options['noindex_archive']) ? $options['noindex_archive'] : false, 1); ?> />
                            <?php _e('Noindex Archive Pages', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Add noindex to archive pages (categories, tags, etc.)', 'wp-seo-pro'); ?></p>
                        
                        <br>
                        
                        <label>
                            <input type="checkbox" name="wp_seo_pro_advanced[noindex_search]" value="1" <?php checked(isset($options['noindex_search']) ? $options['noindex_search'] : true, 1); ?> />
                            <?php _e('Noindex Search Pages', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Add noindex to search result pages', 'wp-seo-pro'); ?></p>
                        
                        <br>
                        
                        <label>
                            <input type="checkbox" name="wp_seo_pro_advanced[noindex_404]" value="1" <?php checked(isset($options['noindex_404']) ? $options['noindex_404'] : true, 1); ?> />
                            <?php _e('Noindex 404 Pages', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Add noindex to 404 error pages', 'wp-seo-pro'); ?></p>
                    </fieldset>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Clean Up WordPress Head', 'wp-seo-pro'); ?></th>
                <td>
                    <fieldset>
                        <label>
                            <input type="checkbox" name="wp_seo_pro_advanced[remove_wp_generator]" value="1" <?php checked(isset($options['remove_wp_generator']) ? $options['remove_wp_generator'] : true, 1); ?> />
                            <?php _e('Remove WordPress Generator', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Remove WordPress version from head section', 'wp-seo-pro'); ?></p>
                        
                        <br>
                        
                        <label>
                            <input type="checkbox" name="wp_seo_pro_advanced[remove_rsd_link]" value="1" <?php checked(isset($options['remove_rsd_link']) ? $options['remove_rsd_link'] : true, 1); ?> />
                            <?php _e('Remove RSD Link', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Remove Really Simple Discovery link', 'wp-seo-pro'); ?></p>
                        
                        <br>
                        
                        <label>
                            <input type="checkbox" name="wp_seo_pro_advanced[remove_wlwmanifest_link]" value="1" <?php checked(isset($options['remove_wlwmanifest_link']) ? $options['remove_wlwmanifest_link'] : true, 1); ?> />
                            <?php _e('Remove WLW Manifest', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Remove Windows Live Writer manifest link', 'wp-seo-pro'); ?></p>
                        
                        <br>
                        
                        <label>
                            <input type="checkbox" name="wp_seo_pro_advanced[remove_shortlink]" value="1" <?php checked(isset($options['remove_shortlink']) ? $options['remove_shortlink'] : true, 1); ?> />
                            <?php _e('Remove Shortlink', 'wp-seo-pro'); ?>
                        </label>
                        <p class="description"><?php _e('Remove WordPress shortlink', 'wp-seo-pro'); ?></p>
                    </fieldset>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="wp-seo-pro-settings-help">
        <h3><?php _e('Advanced SEO Tips', 'wp-seo-pro'); ?></h3>
        <ul>
            <li><?php _e('Use noindex for pages that don\'t add SEO value (archives, search results)', 'wp-seo-pro'); ?></li>
            <li><?php _e('Clean up unnecessary WordPress head elements to improve page speed', 'wp-seo-pro'); ?></li>
            <li><?php _e('Monitor your site\'s performance regularly', 'wp-seo-pro'); ?></li>
            <li><?php _e('Use Google Search Console to monitor indexing status', 'wp-seo-pro'); ?></li>
            <li><?php _e('Test your site\'s mobile-friendliness', 'wp-seo-pro'); ?></li>
        </ul>
    </div>
</div>