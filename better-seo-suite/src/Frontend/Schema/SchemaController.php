<?php
namespace BSS\Frontend\Schema;

use BSS\Settings\Options;

class SchemaController
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('wp_head', [$this, 'render'], 20);
    }

    public function render(): void
    {
        if (is_admin() || !(bool) $this->options->get('schema.enabled', true)) {
            return;
        }

        $graphs = [];

        // Site-wide Organization/Person
        $kgType = (string) $this->options->get('general.knowledge_graph_type', 'Organization');
        $kgName = (string) $this->options->get('general.knowledge_graph_name', get_bloginfo('name'));
        $kgLogo = (string) $this->options->get('general.org_logo', '');
        $kg = [
            '@context' => 'https://schema.org',
            '@type' => $kgType,
            'name' => $kgName,
            'url' => home_url('/'),
        ];
        if ($kgLogo) {
            $kg['logo'] = [
                '@type' => 'ImageObject',
                'url' => $kgLogo,
            ];
        }
        $graphs[] = $kg;

        if (is_singular() && (bool) $this->options->get('schema.article.enabled', true)) {
            $pid = get_queried_object_id();
            $article = [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => get_the_title($pid),
                'datePublished' => get_post_time('c', true, $pid),
                'dateModified' => get_post_modified_time('c', true, $pid),
                'mainEntityOfPage' => get_permalink($pid),
                'author' => [
                    '@type' => 'Person',
                    'name' => get_the_author_meta('display_name', get_post_field('post_author', $pid)),
                ],
                'publisher' => [
                    '@type' => $kgType,
                    'name' => $kgName,
                ],
            ];
            if (has_post_thumbnail($pid)) {
                $img = wp_get_attachment_image_src(get_post_thumbnail_id($pid), 'full');
                if ($img) {
                    $article['image'] = $img[0];
                }
            }
            $graphs[] = $article;
        }

        // Product schema hook for integrations to extend
        $graphs = apply_filters('bss/schema/graphs', $graphs);

        if (!empty($graphs)) {
            echo '<script type="application/ld+json">' . wp_json_encode($graphs) . '</script>' . "\n";
        }
    }
}

