<?php
namespace BSS\Settings;

class Options
{
    /** @var array<string,mixed> */
    private $options;

    public function __construct()
    {
        $this->options = get_option('bss_options', []);
        if (!is_array($this->options)) {
            $this->options = [];
        }
    }

    public function ensure_defaults(): void
    {
        $defaults = $this->get_default_options();
        $merged = array_replace_recursive($defaults, $this->options);
        update_option('bss_options', $merged);
        $this->options = $merged;
    }

    /**
     * @return array<string,mixed>
     */
    public function all(): array
    {
        return $this->options;
    }

    /**
     * Get option using dotted path, e.g. general.separator
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        $segments = explode('.', $path);
        $value = $this->options;
        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }
        return $value;
    }

    /**
     * @param string $path
     * @param mixed $value
     */
    public function set(string $path, $value): void
    {
        $segments = explode('.', $path);
        $ref =& $this->options;
        foreach ($segments as $segment) {
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref =& $ref[$segment];
        }
        $ref = $value;
        update_option('bss_options', $this->options);
    }

    /**
     * Sanitize options on save
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    public function sanitize(array $input): array
    {
        $defaults = $this->get_default_options();
        $sanitized = array_replace_recursive($defaults, $this->recursive_sanitize($input));
        return $sanitized;
    }

    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function recursive_sanitize(array $input): array
    {
        $output = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $output[$key] = $this->recursive_sanitize($value);
            } elseif (is_bool($value)) {
                $output[$key] = (bool) $value;
            } elseif (is_numeric($value)) {
                $output[$key] = $value + 0;
            } else {
                $output[$key] = sanitize_text_field((string) $value);
            }
        }
        return $output;
    }

    /**
     * @return array<string,mixed>
     */
    private function get_default_options(): array
    {
        return [
            'general' => [
                'separator' => '—',
                'home_title' => '',
                'home_description' => '',
                'org_name' => '',
                'org_logo' => '',
                'knowledge_graph_type' => 'Organization', // Organization | Person
                'knowledge_graph_name' => '',
                'verify' => [
                    'google' => '',
                    'bing' => '',
                    'yandex' => '',
                    'baidu' => '',
                ],
            ],
            'search_appearance' => [
                'post_types' => [
                    'post' => [
                        'title_pattern' => '%title% %sep% %sitename%',
                        'description_pattern' => '%excerpt%',
                        'noindex' => false,
                    ],
                    'page' => [
                        'title_pattern' => '%title% %sep% %sitename%',
                        'description_pattern' => '%excerpt%',
                        'noindex' => false,
                    ],
                ],
                'taxonomies' => [
                    'category' => [
                        'title_pattern' => '%term% %sep% %sitename%',
                        'description_pattern' => '%term_description%',
                        'noindex' => false,
                    ],
                ],
                'archives' => [
                    'author' => ['noindex' => true],
                    'date' => ['noindex' => true],
                    'search' => ['noindex' => true],
                    '404' => ['noindex' => true],
                ],
            ],
            'sitemap' => [
                'enabled' => true,
                'include_images' => true,
                'post_types' => [], // default all public
                'taxonomies' => [], // default all public
                'exclude_ids' => '',
                'changefreq' => 'weekly',
                'priority' => '0.5',
            ],
            'robots' => [
                'extra_rules' => "",
            ],
            'social' => [
                'opengraph' => [
                    'enabled' => true,
                    'default_image' => '',
                ],
                'twitter' => [
                    'enabled' => true,
                    'card_type' => 'summary_large_image',
                    'site' => '',
                ],
            ],
            'schema' => [
                'enabled' => true,
                'article' => ['enabled' => true],
                'product' => ['enabled' => true],
            ],
            'integrations' => [
                'woocommerce' => ['enabled' => true],
                'edd' => ['enabled' => true],
            ],
        ];
    }
}

