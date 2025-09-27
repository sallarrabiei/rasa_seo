<?php
namespace BSS\Frontend\Sitemap;

use BSS\Settings\Options;

class SitemapController
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('init', [$this, 'register_rewrites']);
        add_action('parse_request', [$this, 'handle_request']);
        add_action('bss_register_rewrites', [$this, 'register_rewrites']);
    }

    public function register_rewrites(): void
    {
        add_rewrite_rule('^sitemap\.xml$', 'index.php?bss_sitemap=index', 'top');
        add_rewrite_rule('^sitemap-(post|taxonomy)-([a-z0-9_-]+)\.xml$', 'index.php?bss_sitemap=$matches[1]&bss_sitemap_var=$matches[2]', 'top');
        add_rewrite_tag('bss_sitemap', '([^&]+)');
        add_rewrite_tag('bss_sitemap_var', '([^&]+)');
    }

    public function handle_request($wp): void
    {
        if (get_query_var('bss_sitemap')) {
            $type = get_query_var('bss_sitemap');
            $var = get_query_var('bss_sitemap_var');
            $this->render($type, $var);
            exit;
        }
    }

    private function render(string $type, ?string $var): void
    {
        if (!(bool) $this->options->get('sitemap.enabled', true)) {
            status_header(404);
            exit;
        }
        header('Content-Type: application/xml; charset=' . get_option('blog_charset'));
        echo '<?xml version="1.0" encoding="' . esc_attr(get_option('blog_charset')) . '"?>';

        if ($type === 'index') {
            echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach (get_post_types(['public' => true], 'objects') as $pt) {
                $loc = home_url('sitemap-post-' . $pt->name . '.xml');
                echo '<sitemap><loc>' . esc_url($loc) . '</loc></sitemap>';
            }
            foreach (get_taxonomies(['public' => true], 'objects') as $tx) {
                $loc = home_url('sitemap-taxonomy-' . $tx->name . '.xml');
                echo '<sitemap><loc>' . esc_url($loc) . '</loc></sitemap>';
            }
            echo '</sitemapindex>';
            return;
        }

        $include_images = (bool) $this->options->get('sitemap.include_images', true);
        $changefreq = (string) $this->options->get('sitemap.changefreq', 'weekly');
        $priority = (string) $this->options->get('sitemap.priority', '0.5');

        $xmlns_image = $include_images ? ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"' : '';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . $xmlns_image . '>';

        if ($type === 'post' && $var) {
            $query = new \WP_Query([
                'post_type' => $var,
                'post_status' => 'publish',
                'posts_per_page' => 1000,
                'no_found_rows' => true,
                'fields' => 'ids',
            ]);
            foreach ($query->posts as $pid) {
                echo '<url>';
                echo '<loc>' . esc_url(get_permalink($pid)) . '</loc>';
                echo '<lastmod>' . esc_html(get_post_modified_time('c', true, $pid)) . '</lastmod>';
                echo '<changefreq>' . esc_html($changefreq) . '</changefreq>';
                echo '<priority>' . esc_html($priority) . '</priority>';
                if ($include_images && has_post_thumbnail($pid)) {
                    $img = wp_get_attachment_image_src(get_post_thumbnail_id($pid), 'full');
                    if ($img) {
                        echo '<image:image><image:loc>' . esc_url($img[0]) . '</image:loc></image:image>';
                    }
                }
                echo '</url>';
            }
        } elseif ($type === 'taxonomy' && $var) {
            $terms = get_terms(['taxonomy' => $var, 'hide_empty' => true]);
            if (!is_wp_error($terms)) {
                foreach ($terms as $term) {
                    echo '<url>';
                    echo '<loc>' . esc_url(get_term_link($term)) . '</loc>';
                    echo '<changefreq>' . esc_html($changefreq) . '</changefreq>';
                    echo '<priority>' . esc_html($priority) . '</priority>';
                    echo '</url>';
                }
            }
        }

        echo '</urlset>';
    }
}

