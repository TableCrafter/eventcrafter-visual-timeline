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
        private $code;
        private $message;
        
        public function __construct($code = '', $message = '', $data = '')
        {
            $this->code = $code;
            $this->message = $message;
        }
        public function get_error_message()
        {
            return $this->message;
        }
    }
}

if (!function_exists('register_activation_hook')) {
    function register_activation_hook($file, $callback)
    {
        // Stub - do nothing in tests
    }
}

if (!function_exists('register_deactivation_hook')) {
    function register_deactivation_hook($file, $callback)
    {
        // Stub - do nothing in tests
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $accepted_args = 1)
    {
        // Stub - do nothing in tests
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file)
    {
        return 'http://example.com/wp-content/plugins/eventcrafter-visual-timeline/';
    }
}

if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file)
    {
        return EVENTCRAFTER_PATH;
    }
}

if (!function_exists('file_get_contents')) {
    // Don't override - this is a PHP function
}

if (!function_exists('json_decode')) {
    // Don't override - this is a PHP function
}

if (!function_exists('json_last_error')) {
    // Don't override - this is a PHP function
}
