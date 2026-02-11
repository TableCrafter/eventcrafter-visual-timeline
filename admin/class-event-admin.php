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
        add_action('save_post_eventcrafter_tl', array($this, 'save_timeline_data'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function add_builder_metabox()
    {
        add_meta_box(
            'eventcrafter_tl_builder',
            __('EventCrafter Visual Builder', 'eventcrafter-visual-timeline'),
            array($this, 'render_builder_metabox'),
            'eventcrafter_tl',
            'normal',
            'high'
        );
    }

    public function enqueue_admin_assets($hook)
    {
        global $post;

        if ($hook === 'post-new.php' || $hook === 'post.php') {
            if ($post && 'eventcrafter_tl' === $post->post_type) {
                wp_enqueue_media();
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');

                wp_enqueue_style(
                    'eventcrafter-admin-css',
                    EVENTCRAFTER_URL . 'admin/css/builder.css',
                    array(),
                    $this->version
                );

                wp_enqueue_style(
                    'eventcrafter-frontend-css',
                    EVENTCRAFTER_URL . 'assets/css/eventcrafter.css',
                    array(),
                    $this->version
                );

                wp_enqueue_script(
                    'eventcrafter-admin-js',
                    EVENTCRAFTER_URL . 'admin/js/builder.js',
                    array('jquery', 'wp-color-picker', 'jquery-ui-sortable'),
                    $this->version,
                    true
                );
            }
        }

        // Enqueue on List Table for Copy Button
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Just checking param for enqueue, no action taken.
        if ($hook === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'eventcrafter_tl') {
            wp_enqueue_script(
                'eventcrafter-admin-list-js',
                EVENTCRAFTER_URL . 'assets/js/admin-script.js',
                array('jquery'),
                $this->version,
                true
            );
        }
    }

    public function render_builder_metabox($post)
    {
        // Retrieve existing data
        $json_data = get_post_meta($post->ID, '_eventcrafter_tl_data', true);

        // Default structure if empty
        if (empty($json_data)) {
            $json_data = json_encode(array(
                'settings' => array('layout' => 'vertical'),
                'events' => array()
            ));
        }

        // Hidden input to store the JSON
        echo '<input type="hidden" id="eventcrafter_tl_data" name="eventcrafter_tl_data" value="' . esc_attr($json_data) . '">';

        // Nonce for security
        wp_nonce_field('eventcrafter_save_tl_data', 'eventcrafter_tl_nonce');

        // The Builder Interface Container
        ?>
        <div id="ec-builder-app">
            <div class="ec-usage-box"
                style="background: #f0f0f1; padding: 10px 15px; border-left: 4px solid #007cba; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <strong>Usage:</strong> <span class="description">Copy this shortcode to use this timeline anywhere.</span>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <code style="background: #fff; padding: 5px 10px; border: 1px solid #ddd; border-radius: 3px;"
                        id="ec-shortcode-preview">[eventcrafter id="<?php echo esc_attr($post->ID); ?>"]</code>
                    <button type="button" class="button button-small" id="ec-copy-shortcode">Copy</button>
                </div>
            </div>

            <div class="ec-builder-columns" style="display: flex; gap: 20px;">
                <div class="ec-editor-column" style="flex: 1;">
                    <h3 style="margin-top:0;">Edit Events</h3>
                    <div class="ec-toolbar">
                        <button type="button" class="button button-primary" id="ec-add-event">+ Add Event</button>
                    </div>
                    <div id="ec-events-list">
                        <!-- Events will be rendered here via JS -->
                        <p class="ec-empty-state">No events yet. Click "Add Event" to start building your timeline.</p>
                    </div>
                </div>
                <div class="ec-preview-column"
                    style="flex: 1; min-width: 300px; background: #fff; padding: 20px; border: 1px solid #ddd; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin-top:0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">Live
                        Preview</h3>
                    <div id="ec-live-preview">
                        <!-- Live Preview Rendered Here -->
                    </div>
                </div>
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
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Verify Nonce
        if (
            !isset($_POST['eventcrafter_tl_nonce']) ||
            !wp_verify_nonce(sanitize_key(wp_unslash($_POST['eventcrafter_tl_nonce'])), 'eventcrafter_save_tl_data')
        ) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['eventcrafter_tl_data'])) {
            // Sanitize text area input first
            $raw_json = sanitize_textarea_field(wp_unslash($_POST['eventcrafter_tl_data']));

            // Validate: Decode to ensure it's valid JSON
            $decoded = json_decode($raw_json, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Recursively sanitize the decoded data
                $sanitized_data = $this->sanitize_timeline_data($decoded);
                
                // Re-encode to ensure clean JSON string storage
                update_post_meta($post_id, '_eventcrafter_tl_data', wp_json_encode($sanitized_data));
            }
        }
    }

    /**
     * Recursively sanitize timeline data
     */
    private function sanitize_timeline_data($data)
    {
        if (is_array($data)) {
            $sanitized = array();
            foreach ($data as $key => $value) {
                $sanitized_key = sanitize_key($key);
                if (is_array($value)) {
                    $sanitized[$sanitized_key] = $this->sanitize_timeline_data($value);
                } elseif (is_string($value)) {
                    // For HTML content, use wp_kses_post, otherwise sanitize_text_field
                    if (in_array($sanitized_key, array('description', 'content'), true)) {
                        $sanitized[$sanitized_key] = wp_kses_post($value);
                    } else {
                        $sanitized[$sanitized_key] = sanitize_text_field($value);
                    }
                } else {
                    // For other types (numbers, booleans), validate appropriately
                    $sanitized[$sanitized_key] = $value;
                }
            }
            return $sanitized;
        }
        return sanitize_text_field($data);
    }
}
