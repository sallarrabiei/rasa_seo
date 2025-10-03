<?php
namespace BSS\Admin\Pages;

use BSS\Settings\Options;

class SearchAppearance
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $opts = $this->options->all();
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Search Appearance', 'better-seo-suite') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('bss_options_group');

        $post_types = get_post_types(['public' => true], 'objects');
        echo '<h2>' . esc_html__('Post Types', 'better-seo-suite') . '</h2>';
        echo '<table class="form-table" role="presentation">';
        foreach ($post_types as $pt) {
            $key = $pt->name;
            $ptOpts = $opts['search_appearance']['post_types'][$key] ?? $opts['search_appearance']['post_types']['post'];
            echo '<tr><th>' . esc_html($pt->labels->name) . '</th><td>';
            echo '<p><label>' . esc_html__('Title Pattern', 'better-seo-suite') . ': <input type="text" class="regular-text" name="bss_options[search_appearance][post_types][' . esc_attr($key) . '][title_pattern]" value="' . esc_attr($ptOpts['title_pattern']) . '" /></label></p>';
            echo '<p><label>' . esc_html__('Description Pattern', 'better-seo-suite') . ': <input type="text" class="regular-text" name="bss_options[search_appearance][post_types][' . esc_attr($key) . '][description_pattern]" value="' . esc_attr($ptOpts['description_pattern']) . '" /></label></p>';
            $ni = !empty($ptOpts['noindex']);
            echo '<p><label><input type="checkbox" name="bss_options[search_appearance][post_types][' . esc_attr($key) . '][noindex]" value="1" ' . checked($ni, true, false) . ' /> ' . esc_html__('Noindex this post type', 'better-seo-suite') . '</label></p>';
            echo '</td></tr>';
        }
        echo '</table>';

        $taxonomies = get_taxonomies(['public' => true], 'objects');
        echo '<h2>' . esc_html__('Taxonomies', 'better-seo-suite') . '</h2>';
        echo '<table class="form-table" role="presentation">';
        foreach ($taxonomies as $tax) {
            $key = $tax->name;
            $txOpts = $opts['search_appearance']['taxonomies'][$key] ?? $opts['search_appearance']['taxonomies']['category'];
            echo '<tr><th>' . esc_html($tax->labels->name) . '</th><td>';
            echo '<p><label>' . esc_html__('Title Pattern', 'better-seo-suite') . ': <input type="text" class="regular-text" name="bss_options[search_appearance][taxonomies][' . esc_attr($key) . '][title_pattern]" value="' . esc_attr($txOpts['title_pattern']) . '" /></label></p>';
            echo '<p><label>' . esc_html__('Description Pattern', 'better-seo-suite') . ': <input type="text" class="regular-text" name="bss_options[search_appearance][taxonomies][' . esc_attr($key) . '][description_pattern]" value="' . esc_attr($txOpts['description_pattern']) . '" /></label></p>';
            $ni = !empty($txOpts['noindex']);
            echo '<p><label><input type="checkbox" name="bss_options[search_appearance][taxonomies][' . esc_attr($key) . '][noindex]" value="1" ' . checked($ni, true, false) . ' /> ' . esc_html__('Noindex this taxonomy', 'better-seo-suite') . '</label></p>';
            echo '</td></tr>';
        }
        echo '</table>';

        echo '<h2>' . esc_html__('Archives', 'better-seo-suite') . '</h2>';
        $arch = $opts['search_appearance']['archives'] ?? [];
        echo '<table class="form-table" role="presentation">';
        foreach ([
            'author' => __('Author archives', 'better-seo-suite'),
            'date' => __('Date archives', 'better-seo-suite'),
            'search' => __('Search results', 'better-seo-suite'),
            '404' => __('404 page', 'better-seo-suite'),
        ] as $key => $label) {
            $ni = !empty($arch[$key]['noindex']);
            echo '<tr><th>' . esc_html($label) . '</th><td>';
            echo '<label><input type="checkbox" name="bss_options[search_appearance][archives][' . esc_attr($key) . '][noindex]" value="1" ' . checked($ni, true, false) . ' /> ' . esc_html__('Noindex', 'better-seo-suite') . '</label>';
            echo '</td></tr>';
        }
        echo '</table>';

        submit_button();
        echo '</form>';
        echo '</div>';
    }
}

