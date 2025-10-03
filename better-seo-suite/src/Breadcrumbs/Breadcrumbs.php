<?php
namespace BSS\Breadcrumbs;

class Breadcrumbs
{
    public function __construct()
    {
        add_shortcode('bss_breadcrumbs', [$this, 'shortcode']);
    }

    public function shortcode(): string
    {
        $links = [];
        $links[] = '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'better-seo-suite') . '</a>';
        if (is_singular()) {
            $post = get_post();
            if ($post) {
                $parents = get_post_ancestors($post);
                $parents = array_reverse($parents);
                foreach ($parents as $pid) {
                    $links[] = '<a href="' . esc_url(get_permalink($pid)) . '">' . esc_html(get_the_title($pid)) . '</a>';
                }
                $links[] = '<span>' . esc_html(get_the_title($post)) . '</span>';
            }
        } elseif (is_category() || is_tag() || is_tax()) {
            $term = get_queried_object();
            if ($term) {
                $links[] = '<span>' . esc_html($term->name ?? '') . '</span>';
            }
        } elseif (is_search()) {
            $links[] = '<span>' . esc_html__('Search', 'better-seo-suite') . '</span>';
        } elseif (is_404()) {
            $links[] = '<span>' . esc_html__('Not Found', 'better-seo-suite') . '</span>';
        }

        return '<nav class="bss-breadcrumbs" aria-label="Breadcrumbs">' . implode(' &raquo; ', $links) . '</nav>';
    }
}

