<?php
if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('wp_seo_pro_sitemap', array());
$sitemap_generator = new WP_SEO_Pro\Helpers\SitemapGenerator();
$sitemap_url = $sitemap_generator->get_sitemap_url();
?>

<div class="wrap">
    <h1><?php _e('Sitemap Settings', 'wp-seo-pro'); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('wp_seo_pro_sitemap');
        do_settings_sections('wp_seo_pro_sitemap');
        ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="enable_xml_sitemap"><?php _e('Enable XML Sitemap', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="enable_xml_sitemap" name="wp_seo_pro_sitemap[enable_xml_sitemap]" value="1" <?php checked(isset($options['enable_xml_sitemap']) ? $options['enable_xml_sitemap'] : true, 1); ?> />
                    <p class="description"><?php _e('Generate XML sitemap automatically for search engines', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="sitemap_posts_per_page"><?php _e('Posts Per Page', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="number" id="sitemap_posts_per_page" name="wp_seo_pro_sitemap[sitemap_posts_per_page]" value="<?php echo esc_attr(isset($options['sitemap_posts_per_page']) ? $options['sitemap_posts_per_page'] : 1000); ?>" class="small-text" min="1" max="50000" />
                    <p class="description"><?php _e('Number of posts to include per sitemap page', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Exclude Post Types', 'wp-seo-pro'); ?></th>
                <td>
                    <?php
                    $excluded_types = isset($options['exclude_post_types']) ? $options['exclude_post_types'] : array();
                    $post_types = get_post_types(array('public' => true), 'objects');
                    ?>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                        <?php foreach ($post_types as $post_type): ?>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" name="wp_seo_pro_sitemap[exclude_post_types][]" value="<?php echo esc_attr($post_type->name); ?>" <?php checked(in_array($post_type->name, $excluded_types)); ?> />
                                <?php echo esc_html($post_type->label); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="description"><?php _e('Select post types to exclude from sitemap', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Exclude Taxonomies', 'wp-seo-pro'); ?></th>
                <td>
                    <?php
                    $excluded_taxonomies = isset($options['exclude_taxonomies']) ? $options['exclude_taxonomies'] : array();
                    $taxonomies = get_taxonomies(array('public' => true), 'objects');
                    ?>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #f9f9f9;">
                        <?php foreach ($taxonomies as $taxonomy): ?>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" name="wp_seo_pro_sitemap[exclude_taxonomies][]" value="<?php echo esc_attr($taxonomy->name); ?>" <?php checked(in_array($taxonomy->name, $excluded_taxonomies)); ?> />
                                <?php echo esc_html($taxonomy->label); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="description"><?php _e('Select taxonomies to exclude from sitemap', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
        </table>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="wp-seo-pro-sitemap-info">
        <h3><?php _e('Sitemap Information', 'wp-seo-pro'); ?></h3>
        <p><?php _e('Your XML sitemap is automatically generated and updated when you publish or update content.', 'wp-seo-pro'); ?></p>
        
        <?php if (!empty($options['enable_xml_sitemap'])): ?>
            <p><strong><?php _e('Sitemap URL:', 'wp-seo-pro'); ?></strong> <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank"><?php echo esc_url($sitemap_url); ?></a></p>
            <p><strong><?php _e('Google Search Console:', 'wp-seo-pro'); ?></strong> <a href="https://search.google.com/search-console" target="_blank"><?php _e('Submit your sitemap', 'wp-seo-pro'); ?></a></p>
            <p><strong><?php _e('Bing Webmaster Tools:', 'wp-seo-pro'); ?></strong> <a href="https://www.bing.com/webmasters" target="_blank"><?php _e('Submit your sitemap', 'wp-seo-pro'); ?></a></p>
        <?php endif; ?>
    </div>
    
    <div class="wp-seo-pro-settings-help">
        <h3><?php _e('Sitemap Best Practices', 'wp-seo-pro'); ?></h3>
        <ul>
            <li><?php _e('Submit your sitemap to Google Search Console and Bing Webmaster Tools', 'wp-seo-pro'); ?></li>
            <li><?php _e('Keep your sitemap under 50MB and 50,000 URLs per sitemap file', 'wp-seo-pro'); ?></li>
            <li><?php _e('Update your sitemap regularly by publishing fresh content', 'wp-seo-pro'); ?></li>
            <li><?php _e('Exclude duplicate or low-quality content from your sitemap', 'wp-seo-pro'); ?></li>
            <li><?php _e('Monitor your sitemap for errors in search console', 'wp-seo-pro'); ?></li>
        </ul>
    </div>
</div>

<style>
.wp-seo-pro-sitemap-info {
    background: #f0f6fc;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.wp-seo-pro-sitemap-info h3 {
    margin-top: 0;
    color: #23282d;
}

.wp-seo-pro-sitemap-info p {
    margin: 10px 0;
}

.wp-seo-pro-sitemap-info a {
    color: #0073aa;
    text-decoration: none;
}

.wp-seo-pro-sitemap-info a:hover {
    text-decoration: underline;
}
</style>