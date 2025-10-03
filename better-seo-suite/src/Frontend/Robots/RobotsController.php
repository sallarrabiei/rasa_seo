<?php
namespace BSS\Frontend\Robots;

use BSS\Settings\Options;

class RobotsController
{
    /** @var Options */
    private $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_filter('robots_txt', [$this, 'filter_robots'], 10, 2);
        add_action('wp_head', [$this, 'add_verification_tags']);
    }

    public function filter_robots(string $output, bool $public): string
    {
        $extra = (string) $this->options->get('robots.extra_rules', '');
        if (!empty($extra)) {
            $output .= "\n" . trim($extra) . "\n";
        }
        return $output;
    }

    public function add_verification_tags(): void
    {
        if (is_admin()) {
            return;
        }
        $google = (string) $this->options->get('general.verify.google', '');
        $bing = (string) $this->options->get('general.verify.bing', '');
        $yandex = (string) $this->options->get('general.verify.yandex', '');
        $baidu = (string) $this->options->get('general.verify.baidu', '');
        if ($google) echo '<meta name="google-site-verification" content="' . esc_attr($google) . '" />' . "\n";
        if ($bing) echo '<meta name="msvalidate.01" content="' . esc_attr($bing) . '" />' . "\n";
        if ($yandex) echo '<meta name="yandex-verification" content="' . esc_attr($yandex) . '" />' . "\n";
        if ($baidu) echo '<meta name="baidu-site-verification" content="' . esc_attr($baidu) . '" />' . "\n";
    }
}

