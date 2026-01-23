<?php
use WP_Mock\Tools\TestCase;

class Test_Event_Renderer extends TestCase
{
    public function setUp(): void
    {
        WP_Mock::setUp();
        // Force mock return for escaping to avoid null/type errors if stubs bypassed
        WP_Mock::userFunction('esc_html', ['return' => function ($x) {
            return (string) $x; }]);
        WP_Mock::userFunction('esc_attr', ['return' => function ($x) {
            return (string) $x; }]);
        WP_Mock::userFunction('esc_url', ['return' => function ($x) {
            return (string) $x; }]);
        WP_Mock::userFunction('wp_kses_post', ['return' => function ($x) {
            return (string) $x; }]);
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

        // Reflection to access private method if needed, or simply test public render() traversing to fetch_data
        // Let's test render which calls fetch_data

        $url = 'https://example.com/data.json';
        $mock_data = json_encode([
            'events' => [
                ['title' => 'Test Event', 'date' => '2025']
            ]
        ]);

        // Removed internal filter_var mock. Real filter_var will pass this URL.

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

        // Mock escaping functions used in render_event
        // Mock escaping functions used in render_event (handled by stubs)
        // WP_Mock::userFunction('esc_html', ...);

        $output = $renderer->render($url, 'vertical');

        $this->assertStringContainsString('eventcrafter-timeline', $output);
        $this->assertStringContainsString('Test Event', $output);
    }

    public function test_fetch_data_from_post_meta_id()
    {
        require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
        $renderer = new EventCrafter_Renderer();

        $post_id = 123;
        $mock_json = json_encode([
            'events' => [
                ['title' => 'Meta Event', 'date' => '2026']
            ],
            'settings' => ['layout' => 'horizontal']
        ]);

        // When source is NOT a URL, logic assumes file or ID.
        // If it's numeric, it treats as ID.

        // Native filter_var will return false for int, so we don't need to mock it.

        WP_Mock::userFunction('get_post_meta', [
            'args' => [$post_id, '_ec_timeline_data', true],
            'return' => $mock_json
        ]);

        // Mocks handled by stubs

        $output = $renderer->render($post_id, 'vertical');

        $this->assertStringContainsString('Meta Event', $output);
    }
}
