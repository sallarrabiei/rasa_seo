<?php
namespace BSS\Inc;

function get_option_value(string $path, $default = null)
{
    $options = get_option('bss_options', []);
    if (!is_array($options)) {
        return $default;
    }
    $segments = explode('.', $path);
    $value = $options;
    foreach ($segments as $segment) {
        if (is_array($value) && array_key_exists($segment, $value)) {
            $value = $value[$segment];
        } else {
            return $default;
        }
    }
    return $value;
}

function bool_to_string(bool $value): string
{
    return $value ? 'true' : 'false';
}

function truncate(string $text, int $limit = 160): string
{
    $clean = wp_strip_all_tags($text, true);
    if (mb_strlen($clean) <= $limit) {
        return $clean;
    }
    return rtrim(mb_substr($clean, 0, $limit - 1)) . '…';
}

