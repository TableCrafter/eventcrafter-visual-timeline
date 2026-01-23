<?php
// Stubs for WP global functions not handled by WP_Mock default bootstrap or to avoid Patchwork complexity

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return false;
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback)
    {
        global $shortcode_tags;
        $shortcode_tags[$tag] = $callback;
    }
}

if (!function_exists('shortcode_atts')) {
    function shortcode_atts($pairs, $atts, $shortcode = '')
    {
        $atts = (array) $atts;
        $out = array();
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts))
                $out[$name] = $atts[$name];
            else
                $out[$name] = $default;
        }
        return $out;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text)
    {
        return (string) $text;
    }
}
if (!function_exists('esc_attr')) {
    function esc_attr($text)
    {
        return (string) $text;
    }
}
if (!function_exists('esc_url')) {
    function esc_url($text)
    {
        return (string) $text;
    }
}
if (!function_exists('wp_kses_post')) {
    function wp_kses_post($text)
    {
        return (string) $text;
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing)
    {
        return $thing instanceof WP_Error;
    }
}

if (!class_exists('WP_Error')) {
    class WP_Error
    {
        public function __construct($code = '', $message = '', $data = '')
        {
        }
        public function get_error_message()
        {
        }
    }
}
