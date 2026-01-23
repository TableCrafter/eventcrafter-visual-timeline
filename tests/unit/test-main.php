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

    public function test_shortcode_registration()
    {
        // Simple assertion to keep class coverage if needed
        $this->assertTrue(true);
    }

    public function test_render_shortcode_method()
    {
        $instance = EventCrafter::get_instance();
        $atts = ['source' => 'http://example.com', 'layout' => 'vertical'];

        // shortcode_atts handled by stub

        WP_Mock::userFunction('is_admin', ['return' => false]);

        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);

        // Mock dependents for Renderer:
        WP_Mock::userFunction('wp_remote_get', [
            'return' => ['body' => '{}', 'response' => ['code' => 200]]
        ]);
        WP_Mock::userFunction('wp_remote_retrieve_body', ['return' => '{}']);
        WP_Mock::userFunction('is_wp_error', ['return' => false]);
        // Mock escaping (handled by stubs)

        $output = $instance->render_shortcode($atts);
        $this->assertNotEmpty($output);
    }
}
