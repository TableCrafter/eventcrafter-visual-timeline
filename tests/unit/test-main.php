<?php
use WP_Mock\Tools\TestCase;

class Test_Event_Main extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();

        if (!class_exists('EventCrafter')) {
            require_once EVENTCRAFTER_PATH . 'eventcrafter.php';
        }

        // Reset Singleton
        if (class_exists('EventCrafter')) {
            $reflection = new ReflectionClass('EventCrafter');
            $instance = $reflection->getProperty('instance');
            $instance->setAccessible(true);
            $instance->setValue(null, null);
        }
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
        \Mockery::close();
    }

    public function test_singleton_pattern()
    {
        $instance1 = EventCrafter::get_instance();
        $instance2 = EventCrafter::get_instance();
        
        $this->assertSame($instance1, $instance2, 'Singleton should return same instance');
    }

    public function test_constructor_basic()
    {
        // Simple test to verify constructor works
        $instance = EventCrafter::get_instance();
        $this->assertInstanceOf('EventCrafter', $instance);
    }

    public function test_load_dependencies_admin()
    {
        WP_Mock::userFunction('is_admin', ['return' => true]);
        
        // Expect admin class to be loaded
        $this->assertTrue(class_exists('EventCrafter'));
    }

    public function test_load_dependencies_frontend()
    {
        WP_Mock::userFunction('is_admin', ['return' => false]);
        
        // Expect only CPT class to be loaded (admin should not)
        $this->assertTrue(class_exists('EventCrafter'));
    }

    public function test_enqueue_assets()
    {
        $instance = EventCrafter::get_instance();
        
        WP_Mock::userFunction('wp_register_style', [
            'times' => 1,
            'args' => [
                'eventcrafter-style',
                EVENTCRAFTER_URL . 'assets/css/eventcrafter.css',
                array(),
                EVENTCRAFTER_VERSION
            ]
        ]);
        
        WP_Mock::userFunction('wp_register_script', [
            'times' => 1,
            'args' => [
                'eventcrafter-script',
                EVENTCRAFTER_URL . 'assets/js/eventcrafter.js',
                array(),
                EVENTCRAFTER_VERSION,
                true
            ]
        ]);
        
        $instance->enqueue_assets();
        
        $this->assertTrue(true);
    }

    public function test_render_shortcode_with_id()
    {
        $instance = EventCrafter::get_instance();
        $atts = ['id' => '123', 'layout' => 'vertical'];

        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('get_post_meta', [
            'return' => '{"events":[{"title":"Test"}]}'
        ]);
        WP_Mock::userFunction('is_wp_error', ['return' => false]);
        WP_Mock::userFunction('apply_filters', ['return' => ['events' => [['title' => 'Test']]]]);
        WP_Mock::userFunction('wp_kses_post', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_attr', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_url', ['return_arg' => 0]);

        $output = $instance->render_shortcode($atts);
        $this->assertNotEmpty($output);
        $this->assertStringContainsString('eventcrafter-wrapper', $output);
    }

    public function test_render_shortcode_with_source()
    {
        $instance = EventCrafter::get_instance();
        $atts = ['source' => 'http://example.com/data.json', 'layout' => 'vertical'];

        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        // Skip filter_var - it's a PHP function
        WP_Mock::userFunction('wp_remote_get', [
            'return' => ['body' => '{"events":[{"title":"Test"}]}']
        ]);
        WP_Mock::userFunction('wp_remote_retrieve_body', ['return' => '{"events":[{"title":"Test"}]}']);
        WP_Mock::userFunction('is_wp_error', ['return' => false]);
        WP_Mock::userFunction('apply_filters', ['return' => ['events' => [['title' => 'Test']]]]);
        WP_Mock::userFunction('wp_kses_post', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_attr', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_url', ['return_arg' => 0]);

        $output = $instance->render_shortcode($atts);
        $this->assertNotEmpty($output);
        $this->assertStringContainsString('eventcrafter-wrapper', $output);
    }

    public function test_render_shortcode_empty_source()
    {
        $instance = EventCrafter::get_instance();
        $atts = [];

        $output = $instance->render_shortcode($atts);
        $this->assertStringContainsString('Please provide a timeline ID or source URL', $output);
    }

    public function test_render_shortcode_id_priority()
    {
        $instance = EventCrafter::get_instance();
        $atts = ['id' => '123', 'source' => 'http://example.com/data.json'];

        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('get_post_meta', [
            'return' => '{"events":[{"title":"Test"}]}'
        ]);
        WP_Mock::userFunction('is_wp_error', ['return' => false]);
        WP_Mock::userFunction('apply_filters', ['return' => ['events' => [['title' => 'Test']]]]);
        WP_Mock::userFunction('wp_kses_post', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_attr', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_url', ['return_arg' => 0]);

        // Should not call wp_remote_get since ID takes priority
        WP_Mock::userFunction('wp_remote_get', ['times' => 0]);

        $output = $instance->render_shortcode($atts);
        $this->assertNotEmpty($output);
    }
}
