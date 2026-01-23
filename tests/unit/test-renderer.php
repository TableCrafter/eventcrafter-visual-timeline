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
            'args' => [$post_id, '_ec_timeline_data', true],
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
}
