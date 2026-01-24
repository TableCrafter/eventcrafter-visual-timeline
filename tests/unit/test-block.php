<?php
/**
 * Tests for EventCrafter_Block class
 */

use WP_Mock\Tools\TestCase;

class Test_Event_Block extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
        \Mockery::close();
    }

    public function test_register_block_calls_register_block_type()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-block.php';

        WP_Mock::userFunction('register_block_type', [
            'times' => 1,
            'return' => true
        ]);

        $block = new EventCrafter_Block();
        $block->register_block();

        $this->assertTrue(true);
    }

    public function test_render_callback_with_timeline_id()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-block.php';

        WP_Mock::userFunction('register_block_type', [
            'return' => true
        ]);

        $block = new EventCrafter_Block();

        $attributes = [
            'timelineId' => 123,
            'source' => '',
            'layout' => 'vertical',
            'limit' => -1,
            'showAnimation' => true
        ];

        // Mock WordPress functions used in render
        WP_Mock::userFunction('absint', [
            'return' => function ($val) {
                return abs(intval($val));
            }
        ]);

        WP_Mock::userFunction('esc_url_raw', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('sanitize_key', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('wp_enqueue_style', [
            'return' => true
        ]);

        WP_Mock::userFunction('wp_enqueue_script', [
            'return' => true
        ]);

        WP_Mock::userFunction('esc_attr', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('get_block_wrapper_attributes', [
            'return' => 'class="wp-block-eventcrafter-timeline"'
        ]);

        WP_Mock::userFunction('get_post_meta', [
            'return' => '{"events":[]}'
        ]);

        WP_Mock::userFunction('esc_html', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('esc_url', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('wp_kses_post', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        // Test that render_callback returns string
        $result = $block->render_callback($attributes, '', null);

        $this->assertIsString($result);
    }

    public function test_render_callback_without_selection_shows_placeholder()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-block.php';

        WP_Mock::userFunction('register_block_type', [
            'return' => true
        ]);

        $block = new EventCrafter_Block();

        $attributes = [
            'timelineId' => 0,
            'source' => '',
            'layout' => 'vertical',
            'limit' => -1,
            'showAnimation' => true
        ];

        WP_Mock::userFunction('absint', [
            'return' => 0
        ]);

        WP_Mock::userFunction('esc_url_raw', [
            'return' => ''
        ]);

        WP_Mock::userFunction('sanitize_key', [
            'return' => 'vertical'
        ]);

        WP_Mock::userFunction('esc_html__', [
            'return' => 'Please select a timeline or provide a source URL.'
        ]);

        $result = $block->render_callback($attributes, '', null);

        $this->assertStringContainsString('placeholder', $result);
    }

    public function test_render_callback_with_external_source()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-block.php';

        WP_Mock::userFunction('register_block_type', [
            'return' => true
        ]);

        $block = new EventCrafter_Block();

        $attributes = [
            'timelineId' => 0,
            'source' => 'https://example.com/timeline.json',
            'layout' => 'horizontal',
            'limit' => 5,
            'showAnimation' => false,
            'className' => 'custom-class'
        ];

        WP_Mock::userFunction('absint', [
            'return' => 0
        ]);

        WP_Mock::userFunction('esc_url_raw', [
            'return' => 'https://example.com/timeline.json'
        ]);

        WP_Mock::userFunction('sanitize_key', [
            'return' => 'horizontal'
        ]);

        WP_Mock::userFunction('wp_enqueue_style', [
            'return' => true
        ]);

        WP_Mock::userFunction('wp_enqueue_script', [
            'return' => true
        ]);

        WP_Mock::userFunction('esc_attr', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('get_block_wrapper_attributes', [
            'return' => 'class="wp-block-eventcrafter-timeline custom-class no-animation"'
        ]);

        WP_Mock::userFunction('wp_remote_get', [
            'return' => ['body' => '{"events":[]}']
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'return' => false
        ]);

        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => '{"events":[]}'
        ]);

        WP_Mock::userFunction('esc_html', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('esc_url', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        WP_Mock::userFunction('wp_kses_post', [
            'return' => function ($val) {
                return $val;
            }
        ]);

        $result = $block->render_callback($attributes, '', null);

        $this->assertIsString($result);
        $this->assertStringContainsString('no-animation', $result);
    }

    public function test_enqueue_editor_assets_localizes_script()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-block.php';

        WP_Mock::userFunction('register_block_type', [
            'return' => true
        ]);

        $block = new EventCrafter_Block();

        // Mock get_current_screen
        $screen = \Mockery::mock('WP_Screen');
        $screen->shouldReceive('is_block_editor')->andReturn(true);

        WP_Mock::userFunction('get_current_screen', [
            'return' => $screen
        ]);

        WP_Mock::userFunction('admin_url', [
            'return' => 'http://example.com/wp-admin/'
        ]);

        WP_Mock::userFunction('rest_url', [
            'return' => 'http://example.com/wp-json/wp/v2/'
        ]);

        WP_Mock::userFunction('wp_create_nonce', [
            'return' => 'test_nonce'
        ]);

        WP_Mock::userFunction('wp_localize_script', [
            'times' => 1,
            'args' => [
                'eventcrafter-timeline-editor-script',
                'ecBlockData',
                [
                    'adminUrl' => 'http://example.com/wp-admin/',
                    'restUrl' => 'http://example.com/wp-json/wp/v2/',
                    'nonce' => 'test_nonce',
                ]
            ]
        ]);

        $block->enqueue_editor_assets();

        $this->assertTrue(true);
    }

    public function test_enqueue_editor_assets_skips_non_block_editor()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-block.php';

        WP_Mock::userFunction('register_block_type', [
            'return' => true
        ]);

        $block = new EventCrafter_Block();

        // Mock get_current_screen for non-block editor
        $screen = \Mockery::mock('WP_Screen');
        $screen->shouldReceive('is_block_editor')->andReturn(false);

        WP_Mock::userFunction('get_current_screen', [
            'return' => $screen
        ]);

        // wp_localize_script should NOT be called
        WP_Mock::userFunction('wp_localize_script', [
            'times' => 0
        ]);

        $block->enqueue_editor_assets();

        $this->assertTrue(true);
    }

    public function test_attributes_are_sanitized()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-block.php';

        WP_Mock::userFunction('register_block_type', [
            'return' => true
        ]);

        $block = new EventCrafter_Block();

        // Test with potentially malicious input
        $attributes = [
            'timelineId' => '<script>alert("xss")</script>',
            'source' => 'javascript:alert("xss")',
            'layout' => '<script>alert("xss")</script>',
            'limit' => 'not a number',
            'showAnimation' => 'not a boolean'
        ];

        WP_Mock::userFunction('absint', [
            'return' => 0  // Should sanitize to 0
        ]);

        WP_Mock::userFunction('esc_url_raw', [
            'return' => ''  // Should strip javascript: URL
        ]);

        WP_Mock::userFunction('sanitize_key', [
            'return' => 'scriptalertxssscript'  // Sanitized key
        ]);

        WP_Mock::userFunction('esc_html__', [
            'return' => 'Please select a timeline or provide a source URL.'
        ]);

        $result = $block->render_callback($attributes, '', null);

        // Should show placeholder since sanitized timelineId is 0 and source is empty
        $this->assertStringContainsString('placeholder', $result);
        $this->assertStringNotContainsString('<script>', $result);
    }
}
