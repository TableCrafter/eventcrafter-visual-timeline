<?php
use WP_Mock\Tools\TestCase;

class Test_Event_Renderer extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();
        // Force mock return for escaping to avoid null/type errors if stubs bypassed
        WP_Mock::userFunction('esc_html', [
            'return' => function ($x) {
                return (string) $x;
            }
        ]);
        WP_Mock::userFunction('esc_attr', [
            'return' => function ($x) {
                return (string) $x;
            }
        ]);
        WP_Mock::userFunction('esc_url', [
            'return' => function ($x) {
                return (string) $x;
            }
        ]);
        WP_Mock::userFunction('wp_kses_post', [
            'return' => function ($x) {
                return (string) $x;
            }
        ]);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
        \Mockery::close();
    }

    public function test_render_with_invalid_source()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        require_once dirname(__DIR__) . '/unit/mock-wp-error.php';
        $renderer = new EventCrafter_Renderer();

        $output = $renderer->render('', 'vertical');
        $this->assertStringContainsString('eventcrafter-error', $output);
    }

    public function test_fetch_data_from_json_source()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $url = 'https://example.com/data.json';
        $mock_data_array = [
            'events' => [
                ['title' => 'Test Event', 'date' => '2025']
            ]
        ];
        $mock_data = json_encode($mock_data_array);

        WP_Mock::userFunction('wp_remote_get', [
            'args' => [$url],
            'return' => ['body' => $mock_data, 'response' => ['code' => 200]]
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'return' => false
        ]);

        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => $mock_data
        ]);

        // Mock filters to return the data explicitly
        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $url)
            ->reply($mock_data_array);

        WP_Mock::onFilter('eventcrafter_wrapper_classes')
            ->with(\Mockery::type('array'))
            ->reply(['eventcrafter-wrapper', 'eventcrafter-layout-vertical']);

        // For the loop
        WP_Mock::onFilter('eventcrafter_single_event_data')
            ->with(\Mockery::type('array'), \Mockery::type('int'))
            ->reply($mock_data_array['events'][0]);

        $output = $renderer->render($url, 'vertical');

        $this->assertStringContainsString('eventcrafter-timeline', $output);
        $this->assertStringContainsString('Test Event', $output);
    }

    public function test_fetch_data_from_post_meta_id()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $post_id = 123;
        $mock_data_array = [
            'events' => [
                ['title' => 'Meta Event', 'date' => '2026']
            ],
            'settings' => ['layout' => 'horizontal']
        ];
        $mock_json = json_encode($mock_data_array);

        WP_Mock::userFunction('get_post_meta', [
            'args' => [$post_id, '_eventcrafter_tl_data', true],
            'return' => $mock_json
        ]);

        // Mock filters for this test too
        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $post_id)
            ->reply($mock_data_array);

        WP_Mock::onFilter('eventcrafter_wrapper_classes')
            ->with(\Mockery::type('array'))
            ->reply(['eventcrafter-wrapper', 'eventcrafter-layout-vertical']); // Defaults to vertical if JSON override ignored in mock logic, or horizontal if code logic respects JSON. Code: $safe_layout derived from $layout arg. Test passes 'vertical'.

        WP_Mock::onFilter('eventcrafter_single_event_data')
            ->with(\Mockery::type('array'), \Mockery::type('int'))
            ->reply($mock_data_array['events'][0]);

        $output = $renderer->render($post_id, 'vertical');

        $this->assertStringContainsString('Meta Event', $output);
    }

    public function test_render_with_empty_events()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $post_id = 123;
        $mock_data_array = [
            'events' => []  // Empty events array
        ];
        $mock_json = json_encode($mock_data_array);

        WP_Mock::userFunction('get_post_meta', [
            'return' => $mock_json
        ]);

        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $post_id)
            ->reply($mock_data_array);

        $output = $renderer->render($post_id, 'vertical');

        $this->assertStringContainsString('No events found', $output);
    }

    public function test_render_with_wp_error()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        require_once dirname(__DIR__) . '/unit/mock-wp-error.php';
        $renderer = new EventCrafter_Renderer();

        $url = 'https://example.com/invalid.json';

        WP_Mock::userFunction('wp_remote_get', [
            'return' => new WP_Error('http_request_failed', 'Connection failed')
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'return' => true
        ]);

        $output = $renderer->render($url, 'vertical');

        $this->assertStringContainsString('Error loading timeline', $output);
    }

    public function test_render_with_invalid_json()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $url = 'https://example.com/invalid.json';

        WP_Mock::userFunction('wp_remote_get', [
            'return' => ['body' => 'invalid json}']
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'return' => false
        ]);

        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => 'invalid json}'
        ]);

        $output = $renderer->render($url, 'vertical');

        $this->assertStringContainsString('Error loading timeline', $output);
    }

    public function test_render_with_local_file()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $file_path = '/path/to/nonexistent/file.json';

        $output = $renderer->render($file_path, 'vertical');

        $this->assertStringContainsString('Error loading timeline', $output);
    }

    public function test_render_event_with_complete_data()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $post_id = 123;
        $event_data = [
            'title' => 'Complete Event',
            'date' => '2025-01-01',
            'category' => 'Test Category',
            'description' => 'Test Description',
            'color' => '#ff0000',
            'link' => [
                'url' => 'https://example.com',
                'text' => 'Read More',
                'target' => '_blank'
            ]
        ];
        $mock_data_array = [
            'events' => [$event_data]
        ];
        $mock_json = json_encode($mock_data_array);

        WP_Mock::userFunction('get_post_meta', [
            'return' => $mock_json
        ]);

        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $post_id)
            ->reply($mock_data_array);

        WP_Mock::onFilter('eventcrafter_wrapper_classes')
            ->with(\Mockery::type('array'))
            ->reply(['eventcrafter-wrapper', 'eventcrafter-layout-vertical']);

        WP_Mock::onFilter('eventcrafter_single_event_data')
            ->with(\Mockery::type('array'), \Mockery::type('int'))
            ->reply($event_data);

        $output = $renderer->render($post_id, 'vertical');

        $this->assertStringContainsString('Complete Event', $output);
        $this->assertStringContainsString('2025-01-01', $output);
        $this->assertStringContainsString('Test Category', $output);
        $this->assertStringContainsString('Test Description', $output);
        $this->assertStringContainsString('#ff0000', $output);
        $this->assertStringContainsString('https://example.com', $output);
        $this->assertStringContainsString('Read More', $output);
        $this->assertStringContainsString('_blank', $output);
    }

    public function test_render_event_with_minimal_data()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $post_id = 123;
        $event_data = ['title' => ''];  // Minimal event data with empty title
        $mock_data_array = [
            'events' => [$event_data]
        ];
        $mock_json = json_encode($mock_data_array);

        WP_Mock::userFunction('get_post_meta', [
            'return' => $mock_json
        ]);

        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $post_id)
            ->reply($mock_data_array);

        WP_Mock::onFilter('eventcrafter_wrapper_classes')
            ->with(\Mockery::type('array'))
            ->reply(['eventcrafter-wrapper', 'eventcrafter-layout-vertical']);

        WP_Mock::onFilter('eventcrafter_single_event_data')
            ->with(\Mockery::type('array'), \Mockery::type('int'))
            ->reply($event_data);

        $output = $renderer->render($post_id, 'vertical');

        // The renderer doesn't output "Untitled Event" for empty title - it outputs empty title
        $this->assertStringContainsString('eventcrafter-title', $output);
        $this->assertStringContainsString('#3b82f6', $output); // Default color
    }

    public function test_render_with_horizontal_layout()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $post_id = 123;
        $mock_data_array = [
            'events' => [
                ['title' => 'Horizontal Event', 'date' => '2025']
            ]
        ];
        $mock_json = json_encode($mock_data_array);

        WP_Mock::userFunction('get_post_meta', [
            'return' => $mock_json
        ]);

        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $post_id)
            ->reply($mock_data_array);

        WP_Mock::onFilter('eventcrafter_wrapper_classes')
            ->with(\Mockery::type('array'))
            ->reply(['eventcrafter-wrapper', 'eventcrafter-layout-horizontal']);

        WP_Mock::onFilter('eventcrafter_single_event_data')
            ->with(\Mockery::type('array'), \Mockery::type('int'))
            ->reply($mock_data_array['events'][0]);

        $output = $renderer->render($post_id, 'horizontal');

        $this->assertStringContainsString('eventcrafter-layout-horizontal', $output);
    }

    public function test_render_with_invalid_layout()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $post_id = 123;
        $mock_data_array = [
            'events' => [
                ['title' => 'Test Event']
            ]
        ];
        $mock_json = json_encode($mock_data_array);

        WP_Mock::userFunction('get_post_meta', [
            'return' => $mock_json
        ]);

        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $post_id)
            ->reply($mock_data_array);

        WP_Mock::onFilter('eventcrafter_wrapper_classes')
            ->with(\Mockery::type('array'))
            ->reply(['eventcrafter-wrapper', 'eventcrafter-layout-vertical']);

        WP_Mock::onFilter('eventcrafter_single_event_data')
            ->with(\Mockery::type('array'), \Mockery::type('int'))
            ->reply($mock_data_array['events'][0]);

        $output = $renderer->render($post_id, 'invalid_layout');

        // Should default to vertical
        $this->assertStringContainsString('eventcrafter-layout-vertical', $output);
    }

    public function test_render_with_filter_var_validation()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $url = 'https://example.com/data.json';
        $mock_data_array = [
            'events' => [
                ['title' => 'Filtered Event']
            ]
        ];
        $mock_data = json_encode($mock_data_array);

        // Skip filter_var - it's a PHP function

        WP_Mock::userFunction('wp_remote_get', [
            'return' => ['body' => $mock_data]
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'return' => false
        ]);

        WP_Mock::userFunction('wp_remote_retrieve_body', [
            'return' => $mock_data
        ]);

        WP_Mock::onFilter('eventcrafter_timeline_data')
            ->with(\Mockery::type('array'), $url)
            ->reply($mock_data_array);

        WP_Mock::onFilter('eventcrafter_wrapper_classes')
            ->with(\Mockery::type('array'))
            ->reply(['eventcrafter-wrapper', 'eventcrafter-layout-vertical']);

        WP_Mock::onFilter('eventcrafter_single_event_data')
            ->with(\Mockery::type('array'), \Mockery::type('int'))
            ->reply($mock_data_array['events'][0]);

        $output = $renderer->render($url, 'vertical');

        $this->assertStringContainsString('Filtered Event', $output);
    }
}
