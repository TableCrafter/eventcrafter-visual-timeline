<?php
/**
 * Gutenberg Block Registration for EventCrafter
 *
 * @package EventCrafter
 */

if (!defined('ABSPATH')) {
    exit;
}

class EventCrafter_Block
{
    /**
     * Constructor - register hooks.
     */
    public function __construct()
    {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
    }

    /**
     * Register the Gutenberg block.
     */
    public function register_block()
    {
        // Only register if build exists
        $block_path = EVENTCRAFTER_PATH . 'build/blocks/timeline';

        if (!file_exists($block_path . '/block.json')) {
            // Fallback: try source directory for development
            $block_path = EVENTCRAFTER_PATH . 'blocks/timeline';
            if (!file_exists($block_path . '/block.json')) {
                return;
            }
        }

        register_block_type($block_path, array(
            'render_callback' => array($this, 'render_callback'),
        ));
    }

    /**
     * Render callback for the block.
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block content.
     * @param WP_Block $block      Block instance.
     * @return string Rendered HTML.
     */
    public function render_callback($attributes, $content, $block)
    {
        ob_start();

        // Include render template
        $render_file = EVENTCRAFTER_PATH . 'blocks/timeline/render.php';
        if (file_exists($render_file)) {
            include $render_file;
        }

        return ob_get_clean();
    }

    /**
     * Enqueue editor-only assets and localize data.
     */
    public function enqueue_editor_assets()
    {
        // Only on block editor screens
        if (!function_exists('get_current_screen')) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || !$screen->is_block_editor()) {
            return;
        }

        // Pass data to JavaScript for the block
        wp_localize_script(
            'eventcrafter-timeline-editor-script',
            'ecBlockData',
            array(
                'adminUrl' => admin_url(),
                'restUrl' => rest_url('wp/v2/'),
                'nonce' => wp_create_nonce('wp_rest'),
            )
        );
    }
}
