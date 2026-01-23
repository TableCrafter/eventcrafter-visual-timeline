<?php
/**
 * Plugin Name: EventCrafter – Responsive Timelines, Roadmaps & Events Builder
 * Plugin URI: https://github.com/fahdi/eventcrafter-visual-timeline
 * Description: Transform JSON data into beautiful vertical timelines, product roadmaps, and event history. The API-native visual timeline builder.
 * Version: 1.1.1
 * Author: Fahad Murtaza
 * Author URI: https://github.com/fahdi
 * License: GPLv2 or later
 * Text Domain: eventcrafter-visual-timeline
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Global Constants
 */
if (!defined('EVENTCRAFTER_VERSION')) {
    define('EVENTCRAFTER_VERSION', '1.1.1');
}
if (!defined('EVENTCRAFTER_URL')) {
    define('EVENTCRAFTER_URL', plugin_dir_url(__FILE__));
}
if (!defined('EVENTCRAFTER_PATH')) {
    define('EVENTCRAFTER_PATH', plugin_dir_path(__FILE__));
}

/**
 * Main EventCrafter Class
 */
class EventCrafter
{
    private static $instance = null;

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->load_dependencies();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('eventcrafter', array($this, 'render_shortcode'));
    }

    private function load_dependencies()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-cpt.php';
        $cpt = new EventCrafter_CPT();

        if (is_admin()) {
            require_once EVENTCRAFTER_PATH . 'admin/class-event-admin.php';
            $admin = new EventCrafter_Admin(EVENTCRAFTER_VERSION);
        }
    }

    public function enqueue_assets()
    {
        wp_register_style(
            'eventcrafter-style',
            EVENTCRAFTER_URL . 'assets/css/eventcrafter.css',
            array(),
            EVENTCRAFTER_VERSION
        );

        wp_register_script(
            'eventcrafter-script',
            EVENTCRAFTER_URL . 'assets/js/eventcrafter.js',
            array(),
            EVENTCRAFTER_VERSION,
            true
        );
    }

    public function render_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'source' => '', // URL to JSON or path
            'id' => '', // Post ID of timeline
            'layout' => 'vertical',
            'limit' => -1
        ), $atts, 'eventcrafter');

        // Determine source: ID takes precedence over source URL
        $source = !empty($atts['id']) ? $atts['id'] : $atts['source'];

        if (empty($source)) {
            return '<div class="eventcrafter-error">Please provide a timeline ID or source URL.</div>';
        }

        wp_enqueue_style('eventcrafter-style');
        wp_enqueue_script('eventcrafter-script');

        if (!class_exists('EventCrafter_Renderer')) {
            require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        }

        $renderer = new EventCrafter_Renderer();
        return $renderer->render($source, $atts['layout']);
    }
}

// Initialize Plugin
function eventcrafter_init()
{
    EventCrafter::get_instance();
}
add_action('plugins_loaded', 'eventcrafter_init');
