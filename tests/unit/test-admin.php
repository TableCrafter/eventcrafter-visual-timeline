<?php
use WP_Mock\Tools\TestCase;

class Test_Event_Admin extends TestCase
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

    public function test_add_metabox()
    {
        require_once EVENTCRAFTER_PATH . 'admin/class-event-admin.php';
        $admin = new EventCrafter_Admin('1.0.0');

        WP_Mock::userFunction('add_meta_box', [
            'times' => 1,
            'args' => [
                'ec_timeline_builder',
                'EventCrafter Visual Builder',
                [$admin, 'render_builder_metabox'],
                'ec_timeline',
                'normal',
                'high'
            ]
        ]);

        $admin->add_builder_metabox();

        // Verifying expectations met
        $this->assertTrue(true);
    }

    public function test_save_timeline_data_security()
    {
        require_once EVENTCRAFTER_PATH . 'admin/class-event-admin.php';
        $admin = new EventCrafter_Admin('1.0.0');
        $post_id = 456;

        WP_Mock::userFunction('current_user_can', [
            'args' => ['edit_post', $post_id],
            'return' => false // Simulate no permission
        ]);

        // Should NOT call update_post_meta
        WP_Mock::userFunction('update_post_meta', [
            'times' => 0
        ]);

        $admin->save_timeline_data($post_id);

        // Verifying expectations met
        $this->assertTrue(true);
    }

    public function test_save_timeline_data_success()
    {
        require_once EVENTCRAFTER_PATH . 'admin/class-event-admin.php';
        $admin = new EventCrafter_Admin('1.0.0');
        $post_id = 456;
        $_POST['ec_timeline_data'] = '{"events":[]}';

        WP_Mock::userFunction('current_user_can', [
            'return' => true
        ]);

        // Should call update_post_meta
        WP_Mock::userFunction('update_post_meta', [
            'times' => 1,
            'args' => [$post_id, '_ec_timeline_data', $_POST['ec_timeline_data']]
        ]);

        $admin->save_timeline_data($post_id);

        // Verifying expectations met
        $this->assertTrue(true);
    }
}
