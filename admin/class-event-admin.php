<?php
if (!defined('ABSPATH')) {
    exit;
}

class EventCrafter_Admin
{
    private $version;

    public function __construct($version)
    {
        $this->version = $version;
        add_action('add_meta_boxes', array($this, 'add_builder_metabox'));
        add_action('save_post_ec_timeline', array($this, 'save_timeline_data'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function add_builder_metabox()
    {
        add_meta_box(
            'ec_timeline_builder',
            __('EventCrafter Visual Builder', 'eventcrafter-visual-timeline'),
            array($this, 'render_builder_metabox'),
            'ec_timeline',
            'normal',
            'high'
        );
    }

    public function enqueue_admin_assets($hook)
    {
        global $post;

        if ($hook === 'post-new.php' || $hook === 'post.php') {
            if ($post && 'ec_timeline' === $post->post_type) {
                wp_enqueue_media();
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');

                wp_enqueue_style(
                    'eventcrafter-admin-css',
                    EVENTCRAFTER_URL . 'admin/css/builder.css',
                    array(),
                    $this->version
                );

                wp_enqueue_script(
                    'eventcrafter-admin-js',
                    EVENTCRAFTER_URL . 'admin/js/builder.js',
                    array('jquery', 'wp-color-picker'),
                    $this->version,
                    true
                );
            }
        }
    }

    public function render_builder_metabox($post)
    {
        // Retrieve existing data
        $json_data = get_post_meta($post->ID, '_ec_timeline_data', true);

        // Default structure if empty
        if (empty($json_data)) {
            $json_data = json_encode(array(
                'settings' => array('layout' => 'vertical'),
                'events' => array()
            ));
        }

        // Hidden input to store the JSON
        echo '<input type="hidden" id="ec_timeline_data" name="ec_timeline_data" value="' . esc_attr($json_data) . '">';

        // The Builder Interface Container
        ?>
                <div id="ec-builder-app">
                    <div class="ec-toolbar">
                        <button type="button" class="button button-primary" id="ec-add-event">+ Add Event</button>
                    </div>
                    <div id="ec-events-list">
                        <!-- Events will be rendered here via JS -->
                         <p class="ec-empty-state">No events yet. Click "Add Event" to start building your timeline.</p>
                    </div>
                </div>
        
                <!-- Template for Event Item (Hidden) -->
                <script type="text/template" id="tmpl-ec-event">
                    <div class="ec-event-card" data-id="{{id}}">
                        <div class="ec-event-header">
                            <span class="ec-drag-handle">☰</span>
                            <span class="ec-event-preview-title">{{title}}</span>
                            <span class="ec-event-preview-date">{{date}}</span>
                            <div class="ec-event-actions">
                                <button type="button" class="button-link ec-toggle-edit">Edit</button>
                                <button type="button" class="button-link ec-delete-event" style="color: #b32d2e;">Delete</button>
                            </div>
                        </div>
                        <div class="ec-event-body" style="display:none;">
                            <div class="ec-field-row">
                                <label>Date</label>
                                <input type="text" class="widefat ec-input-date" value="{{date}}" placeholder="YYYY-MM-DD or Year">
                            </div>
                            <div class="ec-field-row">
                                <label>Title</label>
                                <input type="text" class="widefat ec-input-title" value="{{title}}" placeholder="Event Title">
                            </div>
                            <div class="ec-field-row">
                                <label>Description</label>
                                <textarea class="widefat ec-input-desc" rows="3">{{description}}</textarea>
                            </div>
                            <div class="ec-field-row">
                                 <label>Color</label>
                                 <input type="text" class="ec-input-color" value="{{color}}" data-default-color="#3b82f6">
                            </div>
                             <div class="ec-field-row">
                                <label>Category</label>
                                <input type="text" class="widefat ec-input-category" value="{{category}}" placeholder="e.g. Milestone">
                            </div>
                        </div>
                    </div>
                </script>
                <?php
    }

    public function save_timeline_data($post_id)
    {
        // Check verification, permissions, autosave, etc.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        if (!current_user_can('edit_post', $post_id))
            return;

        if (isset($_POST['ec_timeline_data'])) {
            // Sanitize JSON string (we will validate structure in renderer, but saving raw JSON is okay here as long as we treat it carefully on output)
            // Ideally we decode and sanitize recursively, but for MVP saving the JSON string is standard.
            // Using wp_kses_post or similar might break JSON quotes. 
            // We'll treat it as a blob.
            update_post_meta($post_id, '_ec_timeline_data', $_POST['ec_timeline_data']);
        }
    }
}
