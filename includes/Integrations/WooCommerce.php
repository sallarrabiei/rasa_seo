<?php

namespace WP_SEO_Pro\Integrations;

/**
 * WooCommerce integration
 */
class WooCommerce {
    
    /**
     * Constructor
     */
    public function __construct() {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Add SEO meta box to products
        add_action('add_meta_boxes', array($this, 'add_product_seo_meta_box'));
        
        // Save product SEO data
        add_action('save_post', array($this, 'save_product_seo_data'));
        
        // Add product SEO fields to admin
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_product_seo_fields'));
        
        // Save product SEO fields
        add_action('woocommerce_process_product_meta', array($this, 'save_product_seo_fields'));
        
        // Add category SEO fields
        add_action('product_cat_add_form_fields', array($this, 'add_category_seo_fields'));
        add_action('product_cat_edit_form_fields', array($this, 'edit_category_seo_fields'));
        add_action('created_product_cat', array($this, 'save_category_seo_fields'));
        add_action('edited_product_cat', array($this, 'save_category_seo_fields'));
        
        // Add SEO columns to product list
        add_filter('manage_product_posts_columns', array($this, 'add_seo_columns'));
        add_action('manage_product_posts_custom_column', array($this, 'display_seo_columns'), 10, 2);
    }
    
    /**
     * Add SEO meta box to products
     */
    public function add_product_seo_meta_box() {
        add_meta_box(
            'wp_seo_pro_product_seo',
            __('SEO Settings', 'wp-seo-pro'),
            array($this, 'product_seo_meta_box_callback'),
            'product',
            'normal',
            'high'
        );
    }
    
    /**
     * Product SEO meta box callback
     */
    public function product_seo_meta_box_callback($post) {
        wp_nonce_field('wp_seo_pro_product_seo', 'wp_seo_pro_product_seo_nonce');
        
        $title = get_post_meta($post->ID, '_wp_seo_pro_title', true);
        $description = get_post_meta($post->ID, '_wp_seo_pro_description', true);
        $keywords = get_post_meta($post->ID, '_wp_seo_pro_keywords', true);
        $noindex = get_post_meta($post->ID, '_wp_seo_pro_noindex', true);
        $nofollow = get_post_meta($post->ID, '_wp_seo_pro_nofollow', true);
        $canonical = get_post_meta($post->ID, '_wp_seo_pro_canonical', true);
        $og_title = get_post_meta($post->ID, '_wp_seo_pro_og_title', true);
        $og_description = get_post_meta($post->ID, '_wp_seo_pro_og_description', true);
        $og_image = get_post_meta($post->ID, '_wp_seo_pro_og_image', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="wp_seo_pro_title"><?php _e('SEO Title', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="wp_seo_pro_title" name="wp_seo_pro_title" value="<?php echo esc_attr($title); ?>" class="regular-text" />
                    <p class="description"><?php _e('Custom title for search engines. Leave empty to use product title.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp_seo_pro_description"><?php _e('Meta Description', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <textarea id="wp_seo_pro_description" name="wp_seo_pro_description" rows="3" cols="50" class="large-text"><?php echo esc_textarea($description); ?></textarea>
                    <p class="description"><?php _e('Description for search engines. Leave empty to use product description.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp_seo_pro_keywords"><?php _e('Keywords', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="wp_seo_pro_keywords" name="wp_seo_pro_keywords" value="<?php echo esc_attr($keywords); ?>" class="regular-text" />
                    <p class="description"><?php _e('Comma-separated keywords for this product.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp_seo_pro_canonical"><?php _e('Canonical URL', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="url" id="wp_seo_pro_canonical" name="wp_seo_pro_canonical" value="<?php echo esc_attr($canonical); ?>" class="regular-text" />
                    <p class="description"><?php _e('Canonical URL for this product. Leave empty to use product URL.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Robots', 'wp-seo-pro'); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="wp_seo_pro_noindex" value="1" <?php checked($noindex, 1); ?> />
                        <?php _e('No Index', 'wp-seo-pro'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" name="wp_seo_pro_nofollow" value="1" <?php checked($nofollow, 1); ?> />
                        <?php _e('No Follow', 'wp-seo-pro'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp_seo_pro_og_title"><?php _e('Open Graph Title', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="text" id="wp_seo_pro_og_title" name="wp_seo_pro_og_title" value="<?php echo esc_attr($og_title); ?>" class="regular-text" />
                    <p class="description"><?php _e('Title for social media sharing. Leave empty to use SEO title.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp_seo_pro_og_description"><?php _e('Open Graph Description', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <textarea id="wp_seo_pro_og_description" name="wp_seo_pro_og_description" rows="3" cols="50" class="large-text"><?php echo esc_textarea($og_description); ?></textarea>
                    <p class="description"><?php _e('Description for social media sharing. Leave empty to use meta description.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp_seo_pro_og_image"><?php _e('Open Graph Image', 'wp-seo-pro'); ?></label>
                </th>
                <td>
                    <input type="url" id="wp_seo_pro_og_image" name="wp_seo_pro_og_image" value="<?php echo esc_attr($og_image); ?>" class="regular-text" />
                    <button type="button" class="button" id="wp_seo_pro_og_image_button"><?php _e('Select Image', 'wp-seo-pro'); ?></button>
                    <p class="description"><?php _e('Image for social media sharing. Leave empty to use product image.', 'wp-seo-pro'); ?></p>
                </td>
            </tr>
        </table>
        
        <script>
        jQuery(document).ready(function($) {
            $('#wp_seo_pro_og_image_button').click(function(e) {
                e.preventDefault();
                
                var frame = wp.media({
                    title: '<?php _e('Select Open Graph Image', 'wp-seo-pro'); ?>',
                    button: {
                        text: '<?php _e('Use Image', 'wp-seo-pro'); ?>'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#wp_seo_pro_og_image').val(attachment.url);
                });
                
                frame.open();
            });
        });
        </script>
        <?php
    }
    
    /**
     * Save product SEO data
     */
    public function save_product_seo_data($post_id) {
        if (!isset($_POST['wp_seo_pro_product_seo_nonce']) || 
            !wp_verify_nonce($_POST['wp_seo_pro_product_seo_nonce'], 'wp_seo_pro_product_seo')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = array(
            'wp_seo_pro_title',
            'wp_seo_pro_description',
            'wp_seo_pro_keywords',
            'wp_seo_pro_canonical',
            'wp_seo_pro_og_title',
            'wp_seo_pro_og_description',
            'wp_seo_pro_og_image'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        $checkboxes = array(
            'wp_seo_pro_noindex',
            'wp_seo_pro_nofollow'
        );
        
        foreach ($checkboxes as $field) {
            update_post_meta($post_id, $field, isset($_POST[$field]) ? 1 : 0);
        }
    }
    
    /**
     * Add product SEO fields to WooCommerce product data
     */
    public function add_product_seo_fields() {
        global $post;
        
        $title = get_post_meta($post->ID, '_wp_seo_pro_title', true);
        $description = get_post_meta($post->ID, '_wp_seo_pro_description', true);
        
        echo '<div class="options_group">';
        echo '<h3>' . __('SEO Settings', 'wp-seo-pro') . '</h3>';
        
        woocommerce_wp_text_input(array(
            'id' => '_wp_seo_pro_title',
            'label' => __('SEO Title', 'wp-seo-pro'),
            'value' => $title,
            'desc_tip' => true,
            'description' => __('Custom title for search engines', 'wp-seo-pro')
        ));
        
        woocommerce_wp_textarea_input(array(
            'id' => '_wp_seo_pro_description',
            'label' => __('Meta Description', 'wp-seo-pro'),
            'value' => $description,
            'desc_tip' => true,
            'description' => __('Description for search engines', 'wp-seo-pro')
        ));
        
        echo '</div>';
    }
    
    /**
     * Save product SEO fields
     */
    public function save_product_seo_fields($post_id) {
        $fields = array(
            '_wp_seo_pro_title',
            '_wp_seo_pro_description'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    /**
     * Add category SEO fields
     */
    public function add_category_seo_fields() {
        ?>
        <div class="form-field">
            <label for="wp_seo_pro_cat_title"><?php _e('SEO Title', 'wp-seo-pro'); ?></label>
            <input type="text" name="wp_seo_pro_cat_title" id="wp_seo_pro_cat_title" value="" />
            <p class="description"><?php _e('Custom title for this category', 'wp-seo-pro'); ?></p>
        </div>
        
        <div class="form-field">
            <label for="wp_seo_pro_cat_description"><?php _e('Meta Description', 'wp-seo-pro'); ?></label>
            <textarea name="wp_seo_pro_cat_description" id="wp_seo_pro_cat_description" rows="3" cols="50"></textarea>
            <p class="description"><?php _e('Description for search engines', 'wp-seo-pro'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Edit category SEO fields
     */
    public function edit_category_seo_fields($term) {
        $title = get_term_meta($term->term_id, 'wp_seo_pro_cat_title', true);
        $description = get_term_meta($term->term_id, 'wp_seo_pro_cat_description', true);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="wp_seo_pro_cat_title"><?php _e('SEO Title', 'wp-seo-pro'); ?></label>
            </th>
            <td>
                <input type="text" name="wp_seo_pro_cat_title" id="wp_seo_pro_cat_title" value="<?php echo esc_attr($title); ?>" />
                <p class="description"><?php _e('Custom title for this category', 'wp-seo-pro'); ?></p>
            </td>
        </tr>
        
        <tr class="form-field">
            <th scope="row">
                <label for="wp_seo_pro_cat_description"><?php _e('Meta Description', 'wp-seo-pro'); ?></label>
            </th>
            <td>
                <textarea name="wp_seo_pro_cat_description" id="wp_seo_pro_cat_description" rows="3" cols="50"><?php echo esc_textarea($description); ?></textarea>
                <p class="description"><?php _e('Description for search engines', 'wp-seo-pro'); ?></p>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Save category SEO fields
     */
    public function save_category_seo_fields($term_id) {
        if (isset($_POST['wp_seo_pro_cat_title'])) {
            update_term_meta($term_id, 'wp_seo_pro_cat_title', sanitize_text_field($_POST['wp_seo_pro_cat_title']));
        }
        
        if (isset($_POST['wp_seo_pro_cat_description'])) {
            update_term_meta($term_id, 'wp_seo_pro_cat_description', sanitize_textarea_field($_POST['wp_seo_pro_cat_description']));
        }
    }
    
    /**
     * Add SEO columns to product list
     */
    public function add_seo_columns($columns) {
        $new_columns = array();
        
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            
            if ($key === 'product_tag') {
                $new_columns['seo_title'] = __('SEO Title', 'wp-seo-pro');
                $new_columns['seo_description'] = __('Meta Description', 'wp-seo-pro');
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Display SEO columns
     */
    public function display_seo_columns($column, $post_id) {
        switch ($column) {
            case 'seo_title':
                $title = get_post_meta($post_id, '_wp_seo_pro_title', true);
                echo $title ? esc_html($title) : '<span class="description">' . __('Not set', 'wp-seo-pro') . '</span>';
                break;
                
            case 'seo_description':
                $description = get_post_meta($post_id, '_wp_seo_pro_description', true);
                echo $description ? esc_html(wp_trim_words($description, 10)) : '<span class="description">' . __('Not set', 'wp-seo-pro') . '</span>';
                break;
        }
    }
}