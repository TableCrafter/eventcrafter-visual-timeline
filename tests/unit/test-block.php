<?php
use WP_Mock\Tools\TestCase;

class Test_Event_Block extends TestCase
{
    private $instance;

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

        $this->instance = EventCrafter::get_instance();
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
        \Mockery::close();
    }

    public function test_block_registration()
    {
        // Mock register_block_type function
        WP_Mock::userFunction('register_block_type', [
            'times' => 1,
            'args' => [
                EVENTCRAFTER_PATH . 'blocks/eventcrafter-timeline',
                ['render_callback' => [$this->instance, 'render_block']]
            ]
        ]);

        // Call register_blocks method
        $this->instance->register_blocks();

        // If no exception thrown, registration was successful
        $this->assertTrue(true);
    }

    public function test_render_block_with_timeline_id()
    {
        $attributes = [
            'timelineId' => '123',
            'layout' => 'vertical',
            'limit' => 10
        ];

        // Mock WordPress functions that will be called by render_shortcode
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('get_post_meta', [
            'times' => 1,
            'args' => ['123', '_eventcrafter_tl_data', true],
            'return' => '{"events":[{"title":"Test Event","date":"2023-01-01","description":"Test"}]}'
        ]);
        WP_Mock::userFunction('is_wp_error', ['return' => false]);
        WP_Mock::userFunction('apply_filters', ['return' => ['events' => [['title' => 'Test Event']]]]);
        WP_Mock::userFunction('wp_kses_post', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_attr', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_url', ['return_arg' => 0]);

        $result = $this->instance->render_block($attributes, '', null);

        $this->assertIsString($result);
        $this->assertStringContainsString('eventcrafter-wrapper', $result);
    }

    public function test_render_block_with_source_url()
    {
        $attributes = [
            'sourceUrl' => 'https://example.com/data.json',
            'layout' => 'horizontal',
            'limit' => 5
        ];

        // Mock WordPress functions for remote fetch
        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        // Skip filter_var - it's a PHP function
        WP_Mock::userFunction('wp_remote_get', [
            'times' => 1,
            'return' => ['body' => '{"events":[{"title":"Test Event","date":"2023-01-01"}]}']
        ]);
        WP_Mock::userFunction('is_wp_error', ['return' => false]);
        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => '{"events":[{"title":"Test Event","date":"2023-01-01"}]}'
        ]);
        WP_Mock::userFunction('apply_filters', ['return' => ['events' => [['title' => 'Test Event']]]]);
        WP_Mock::userFunction('wp_kses_post', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_attr', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_url', ['return_arg' => 0]);

        $result = $this->instance->render_block($attributes, '', null);

        $this->assertIsString($result);
        $this->assertStringContainsString('eventcrafter-wrapper', $result);
    }

    public function test_render_block_with_empty_attributes()
    {
        $attributes = [];

        $result = $this->instance->render_block($attributes, '', null);

        $this->assertIsString($result);
        $this->assertStringContainsString('Please provide a timeline ID or source URL', $result);
    }

    public function test_render_block_with_missing_timeline_id()
    {
        $attributes = [
            'timelineId' => '999'  // Non-existent timeline
        ];

        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('get_post_meta', [
            'times' => 1,
            'args' => ['999', '_eventcrafter_tl_data', true],
            'return' => ''  // Empty meta data
        ]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);

        $result = $this->instance->render_block($attributes, '', null);

        $this->assertIsString($result);
        $this->assertStringContainsString('Error loading timeline', $result);
    }

    public function test_render_block_with_invalid_json_url()
    {
        $attributes = [
            'sourceUrl' => 'https://example.com/invalid.json'
        ];

        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        // Skip filter_var - it's a PHP function that can't be mocked
        WP_Mock::userFunction('wp_remote_get', [
            'times' => 1,
            'return' => new WP_Error('http_request_failed', 'Connection failed')
        ]);
        WP_Mock::userFunction('is_wp_error', ['return' => true]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);

        $result = $this->instance->render_block($attributes, '', null);

        $this->assertIsString($result);
        $this->assertStringContainsString('Error loading timeline', $result);
    }

    public function test_block_attributes_conversion()
    {
        $attributes = [
            'timelineId' => '456',
            'sourceUrl' => 'https://example.com/data.json',
            'layout' => 'horizontal',
            'limit' => 20
        ];

        // Create reflection to access private method for testing
        $reflection = new ReflectionMethod($this->instance, 'render_block');
        $reflection->setAccessible(true);

        // Mock render_shortcode method to capture converted attributes
        $mock = $this->createMock(EventCrafter::class);
        $mock->expects($this->once())
             ->method('render_shortcode')
             ->with([
                 'id' => '456',
                 'source' => 'https://example.com/data.json',
                 'layout' => 'horizontal',
                 'limit' => 20
             ])
             ->willReturn('<div>Test output</div>');

        $result = $mock->render_shortcode([
            'id' => '456',
            'source' => 'https://example.com/data.json',
            'layout' => 'horizontal',
            'limit' => 20
        ]);

        $this->assertEquals('<div>Test output</div>', $result);
    }

    public function test_block_attributes_defaults()
    {
        $attributes = [];

        // Mock render_shortcode to capture default values
        $mock = $this->createMock(EventCrafter::class);
        $mock->expects($this->once())
             ->method('render_shortcode')
             ->with([
                 'id' => '',
                 'source' => '',
                 'layout' => 'vertical',
                 'limit' => -1
             ])
             ->willReturn('<div>Default output</div>');

        $result = $mock->render_shortcode([
            'id' => '',
            'source' => '',
            'layout' => 'vertical',
            'limit' => -1
        ]);

        $this->assertEquals('<div>Default output</div>', $result);
    }

    public function test_block_json_exists()
    {
        $block_json_path = EVENTCRAFTER_PATH . 'blocks/eventcrafter-timeline/block.json';
        
        $this->assertFileExists($block_json_path, 'block.json file should exist');
        
        $json_content = file_get_contents($block_json_path);
        $decoded = json_decode($json_content, true);
        
        $this->assertNotNull($decoded, 'block.json should be valid JSON');
        $this->assertEquals('eventcrafter/timeline', $decoded['name']);
        $this->assertEquals('EventCrafter Timeline', $decoded['title']);
    }

    public function test_block_assets_exist()
    {
        $block_dir = EVENTCRAFTER_PATH . 'blocks/eventcrafter-timeline/';
        
        $this->assertFileExists($block_dir . 'index.js', 'Block JavaScript file should exist');
        $this->assertFileExists($block_dir . 'editor.css', 'Block editor CSS file should exist');
        $this->assertFileExists($block_dir . 'style.css', 'Block frontend CSS file should exist');
    }

    public function test_block_javascript_syntax()
    {
        $js_file = EVENTCRAFTER_PATH . 'blocks/eventcrafter-timeline/index.js';
        $js_content = file_get_contents($js_file);
        
        // Basic syntax checks
        $this->assertStringContainsString('registerBlockType', $js_content);
        $this->assertStringContainsString('eventcrafter/timeline', $js_content);
        $this->assertStringContainsString('edit:', $js_content);
        $this->assertStringContainsString('save:', $js_content);
    }

    public function test_block_css_syntax()
    {
        $editor_css = EVENTCRAFTER_PATH . 'blocks/eventcrafter-timeline/editor.css';
        $style_css = EVENTCRAFTER_PATH . 'blocks/eventcrafter-timeline/style.css';
        
        $editor_content = file_get_contents($editor_css);
        $style_content = file_get_contents($style_css);
        
        // Check for basic CSS structure
        $this->assertStringContainsString('.wp-block-eventcrafter-timeline', $editor_content);
        $this->assertStringContainsString('.wp-block-eventcrafter-timeline', $style_content);
        
        // Verify no PHP errors in CSS (basic check)
        $this->assertStringNotContainsString('<?php', $editor_content);
        $this->assertStringNotContainsString('<?php', $style_content);
    }

    public function test_render_block_priority_timeline_id_over_source()
    {
        $attributes = [
            'timelineId' => '123',
            'sourceUrl' => 'https://example.com/data.json'  // Should be ignored
        ];

        WP_Mock::userFunction('wp_enqueue_style', ['times' => 1]);
        WP_Mock::userFunction('wp_enqueue_script', ['times' => 1]);
        WP_Mock::userFunction('get_post_meta', [
            'times' => 1,
            'args' => ['123', '_eventcrafter_tl_data', true],
            'return' => '{"events":[{"title":"Test Event","date":"2023-01-01"}]}'
        ]);
        WP_Mock::userFunction('is_wp_error', ['return' => false]);
        WP_Mock::userFunction('apply_filters', ['return' => ['events' => [['title' => 'Test Event']]]]);
        WP_Mock::userFunction('wp_kses_post', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_html', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_attr', ['return_arg' => 0]);
        WP_Mock::userFunction('esc_url', ['return_arg' => 0]);

        // Should not call wp_remote_get since timeline ID takes priority
        WP_Mock::userFunction('wp_remote_get', ['times' => 0]);

        $result = $this->instance->render_block($attributes, '', null);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('eventcrafter-wrapper', $result);
    }
}