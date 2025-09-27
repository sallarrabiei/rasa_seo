<?php
namespace BSS\Frontend;

use BSS\Settings\Options;

class MetaRenderer
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_filter('pre_get_document_title', [$this, 'filter_title'], 99);
        add_action('wp_head', [$this, 'render_meta'], 1);
    }

    public function filter_title($title)
    {
        if (is_admin()) {
            return $title;
        }
        $pattern = '';
        if (is_home() || is_front_page()) {
            $pattern = (string) $this->options->get('general.home_title', '');
            if ($pattern === '') {
                $pattern = '%sitename%';
            }
        } elseif (is_singular()) {
            $post_type = get_post_type();
            $pattern = (string) ($this->options->get('search_appearance.post_types.' . $post_type . '.title_pattern') ?? '%title% %sep% %sitename%');
        } elseif (is_category() || is_tag() || is_tax()) {
            $tax = get_queried_object();
            $tax_name = $tax && isset($tax->taxonomy) ? $tax->taxonomy : 'category';
            $pattern = (string) ($this->options->get('search_appearance.taxonomies.' . $tax_name . '.title_pattern') ?? '%term% %sep% %sitename%');
        } else {
            $pattern = get_bloginfo('name');
        }
        return $this->replace_pattern($pattern);
    }

    public function render_meta(): void
    {
        if (is_admin()) {
            return;
        }
        // Canonical
        $canonical = '';
        if (is_singular()) {
            $canonical = get_permalink(get_queried_object_id());
        } elseif (is_category() || is_tag() || is_tax()) {
            $canonical = get_term_link(get_queried_object());
        } elseif (is_home() || is_front_page()) {
            $canonical = home_url('/');
        }
        if ($canonical && !is_wp_error($canonical)) {
            echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . "\n";
        }
        $desc = '';
        if (is_home() || is_front_page()) {
            $desc = (string) $this->options->get('general.home_description', '');
        } elseif (is_singular()) {
            $post_id = get_queried_object_id();
            $custom = get_post_meta($post_id, '_bss_desc', true);
            if (!empty($custom)) {
                $desc = (string) $custom;
            } else {
                $pt = get_post_type($post_id);
                $pattern = (string) ($this->options->get('search_appearance.post_types.' . $pt . '.description_pattern') ?? '%excerpt%');
                $desc = $this->replace_pattern($pattern, $post_id);
            }
        } elseif (is_category() || is_tag() || is_tax()) {
            $tax = get_queried_object();
            $tax_name = $tax && isset($tax->taxonomy) ? $tax->taxonomy : 'category';
            $pattern = (string) ($this->options->get('search_appearance.taxonomies.' . $tax_name . '.description_pattern') ?? '%term_description%');
            $desc = $this->replace_pattern($pattern);
        }
        if ($desc) {
            echo '<meta name="description" content="' . esc_attr(wp_strip_all_tags($desc, true)) . '" />' . "\n";
        }

        // Robots
        $noindex = false;
        if (is_singular()) {
            $post_id = get_queried_object_id();
            $noindex = (bool) get_post_meta($post_id, '_bss_noindex', true);
            $pt = get_post_type($post_id);
            $noindex = $noindex || (bool) $this->options->get('search_appearance.post_types.' . $pt . '.noindex', false);
        } elseif (is_category() || is_tag() || is_tax()) {
            $tax = get_queried_object();
            $tax_name = $tax && isset($tax->taxonomy) ? $tax->taxonomy : 'category';
            $noindex = (bool) $this->options->get('search_appearance.taxonomies.' . $tax_name . '.noindex', false);
        } elseif (is_author()) {
            $noindex = (bool) $this->options->get('search_appearance.archives.author.noindex', true);
        } elseif (is_date()) {
            $noindex = (bool) $this->options->get('search_appearance.archives.date.noindex', true);
        } elseif (is_search()) {
            $noindex = (bool) $this->options->get('search_appearance.archives.search.noindex', true);
        } elseif (is_404()) {
            $noindex = (bool) $this->options->get('search_appearance.archives.404.noindex', true);
        }
        echo '<meta name="robots" content="' . esc_attr($noindex ? 'noindex, follow' : 'index, follow') . '" />' . "\n";
        // Locale
        $locale = get_locale();
        echo '<meta property="og:locale" content="' . esc_attr(str_replace('_','-', $locale)) . '" />' . "\n";
    }

    private function replace_pattern(string $pattern, ?int $post_id = null): string
    {
        $replacements = [
            '%sitename%' => get_bloginfo('name'),
            '%sep%' => (string) $this->options->get('general.separator', '—'),
        ];
        if ($post_id) {
            $replacements['%title%'] = get_the_title($post_id);
            $replacements['%excerpt%'] = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id), true), 30, '…');
        } else {
            if (is_singular()) {
                $pid = get_queried_object_id();
                $replacements['%title%'] = get_the_title($pid);
                $replacements['%excerpt%'] = has_excerpt($pid) ? get_the_excerpt($pid) : wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $pid), true), 30, '…');
            }
            if (is_category() || is_tag() || is_tax()) {
                $term = get_queried_object();
                if ($term) {
                    $replacements['%term%'] = $term->name ?? '';
                    $replacements['%term_description%'] = term_description($term->term_id) ?: '';
                }
            }
        }
        $customTitle = null;
        if (is_singular()) {
            $pid = $post_id ?: get_queried_object_id();
            $ct = get_post_meta($pid, '_bss_title', true);
            if (!empty($ct)) {
                $customTitle = (string) $ct;
            }
        }
        $out = $customTitle ?: $pattern;
        return strtr($out, $replacements);
    }
}

