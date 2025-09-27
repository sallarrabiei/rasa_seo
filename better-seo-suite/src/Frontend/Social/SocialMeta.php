<?php
namespace BSS\Frontend\Social;

use BSS\Settings\Options;

class SocialMeta
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('wp_head', [$this, 'render'], 5);
    }

    public function render(): void
    {
        if (is_admin()) {
            return;
        }

        $ogEnabled = (bool) $this->options->get('social.opengraph.enabled', true);
        $twEnabled = (bool) $this->options->get('social.twitter.enabled', true);

        $title = wp_get_document_title();
        $desc = '';
        if (is_singular()) {
            $pid = get_queried_object_id();
            $desc = get_post_meta($pid, '_bss_desc', true);
            if (empty($desc)) {
                $desc = has_excerpt($pid) ? get_the_excerpt($pid) : wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $pid), true), 30, '…');
            }
        } elseif (is_home() || is_front_page()) {
            $desc = (string) $this->options->get('general.home_description', '');
        }

        $image = '';
        if (is_singular()) {
            $pid = get_queried_object_id();
            if (has_post_thumbnail($pid)) {
                $img = wp_get_attachment_image_src(get_post_thumbnail_id($pid), 'full');
                if ($img) {
                    $image = $img[0];
                }
            }
        }
        if (!$image) {
            $image = (string) $this->options->get('social.opengraph.default_image', '');
        }

        if ($ogEnabled) {
            echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
            echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
            if (!empty($desc)) {
                echo '<meta property="og:description" content="' . esc_attr(wp_strip_all_tags($desc, true)) . '" />' . "\n";
            }
            echo '<meta property="og:type" content="' . esc_attr(is_singular() ? 'article' : 'website') . '" />' . "\n";
            echo '<meta property="og:url" content="' . esc_url(home_url(add_query_arg([]))) . '" />' . "\n";
            if (!empty($image)) {
                echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n";
            }
        }

        if ($twEnabled) {
            $card = (string) $this->options->get('social.twitter.card_type', 'summary_large_image');
            $site = (string) $this->options->get('social.twitter.site', '');
            echo '<meta name="twitter:card" content="' . esc_attr($card) . '" />' . "\n";
            if (!empty($site)) {
                echo '<meta name="twitter:site" content="' . esc_attr($site) . '" />' . "\n";
            }
            echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
            if (!empty($desc)) {
                echo '<meta name="twitter:description" content="' . esc_attr(wp_strip_all_tags($desc, true)) . '" />' . "\n";
            }
            if (!empty($image)) {
                echo '<meta name="twitter:image" content="' . esc_url($image) . '" />' . "\n";
            }
        }
    }
}

