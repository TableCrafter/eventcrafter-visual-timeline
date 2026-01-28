<?php
/**
 * Plugin Name: EventCrafter – Responsive Timelines, Roadmaps & Events Builder
 * Plugin URI: https://github.com/TableCrafter/eventcrafter-visual-timeline
 * Description: Create beautiful vertical timelines, product roadmaps, and event history. Manage your events using the intuitive Visual Builder.
 * Version: 1.2.0
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
    define('EVENTCRAFTER_VERSION', '1.2.0');
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
        add_action('init', array($this, 'register_blocks'));
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

    public function register_blocks()
    {
        register_block_type(
            EVENTCRAFTER_PATH . 'blocks/eventcrafter-timeline',
            array(
                'render_callback' => array($this, 'render_block'),
            )
        );
    }

    public function render_block($attributes, $content, $block)
    {
        $atts = array(
            'id' => isset($attributes['timelineId']) ? $attributes['timelineId'] : '',
            'source' => isset($attributes['sourceUrl']) ? $attributes['sourceUrl'] : '',
            'layout' => isset($attributes['layout']) ? $attributes['layout'] : 'vertical',
            'limit' => isset($attributes['limit']) ? $attributes['limit'] : -1
        );

        return $this->render_shortcode($atts);
    }
}

// Initialize Plugin
function eventcrafter_init()
{
    EventCrafter::get_instance();
}
add_action('init', 'eventcrafter_init');

// Also try registering the post type directly as a backup
add_action('init', function() {
    if (!post_type_exists('eventcrafter_tl')) {
        register_post_type('eventcrafter_tl', array(
            'label' => 'Timelines',
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-excerpt-view',
            'supports' => array('title'),
            'capability_type' => 'post',
        ));
    }
}, 20); // Lower priority to run after the class


// Activation Hook - Flush rewrite rules and handle data migration
function eventcrafter_activate()
{
    // Initialize the plugin to register post types
    EventCrafter::get_instance();
    
    // Handle migration from old post type name (ec_timeline) to new name (eventcrafter_timeline)
    eventcrafter_migrate_post_type();
    
    // Flush rewrite rules to ensure custom post types work
    flush_rewrite_rules();
}

// Migrate data from old post type to new post type
function eventcrafter_migrate_post_type()
{
    // Use WordPress functions instead of direct database queries
    $old_posts = get_posts(array(
        'post_type' => 'ec_timeline',
        'post_status' => 'any',
        'numberposts' => -1,
        'fields' => 'ids',
        'suppress_filters' => false
    ));
    
    if (!empty($old_posts)) {
        foreach ($old_posts as $post_id) {
            // Update post type using WordPress function
            wp_update_post(array(
                'ID' => $post_id,
                'post_type' => 'eventcrafter_tl'
            ));
            
            // Migrate meta data using WordPress functions
            $old_meta_value = get_post_meta($post_id, '_ec_timeline_data', true);
            if (!empty($old_meta_value)) {
                update_post_meta($post_id, '_eventcrafter_tl_data', $old_meta_value);
                delete_post_meta($post_id, '_ec_timeline_data');
            }
        }
        
        // Clear any object cache to ensure fresh data
        wp_cache_flush();
    }
}
register_activation_hook(__FILE__, 'eventcrafter_activate');

// Deactivation Hook - Clean up rewrite rules
function eventcrafter_deactivate()
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'eventcrafter_deactivate');
