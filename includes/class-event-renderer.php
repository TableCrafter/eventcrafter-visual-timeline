<?php
if (!defined('ABSPATH')) {
    exit;
}

class EventCrafter_Renderer
{
    /**
     * Render the timeline from a JSON source.
     *
     * @param string $source URL or path to JSON file.
     * @param string $layout Layout type (vertical, horizontal).
     * @return string HTML output.
     */
    public function render($source, $layout = 'vertical')
    {
        $data = $this->fetch_data($source);

        if (is_wp_error($data)) {
            return '<div class="eventcrafter-error">Error loading timeline: ' . esc_html($data->get_error_message()) . '</div>';
        }

        if (empty($data['events'])) {
            return '<div class="eventcrafter-info">No events found in timeline data.</div>';
        }

        $settings = isset($data['settings']) ? $data['settings'] : array();
        $safe_layout = in_array($layout, ['vertical', 'horizontal']) ? $layout : 'vertical';

        // Allow JSON to override layout if not forced
        if (isset($settings['layout']) && $layout === 'vertical') {
            // Keep user preference if specified in shortcode, otherwise fallback to JSON
            // For now, we trust shortcode over JSON unless shortcode is default
        }

        ob_start();
        ?>
        <div class="eventcrafter-wrapper eventcrafter-layout-<?php echo esc_attr($safe_layout); ?>">
            <div class="eventcrafter-timeline">
                <?php foreach ($data['events'] as $event): ?>
                    <?php $this->render_event($event); ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Fetch and decode JSON data.
     */
    private function fetch_data($source)
    {
        // Check if it's a valid URL
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            $response = wp_remote_get($source);
            if (is_wp_error($response)) {
                return $response;
            }
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
        } else {
            // Assume local file path relative to WP upload dir or plugin dir?
            // For MVP, let's support absolute path or URL. 
            // Better yet, just try reading it if file exists.
            if (file_exists($source)) {
                $data = json_decode(file_get_contents($source), true);
            } else {
                return new WP_Error('not_found', 'Source file not found.');
            }
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', 'Invalid JSON data.');
        }

        return $data;
    }

    /**
     * Render a single event item.
     */
    private function render_event($event)
    {
        $date = isset($event['date']) ? esc_html($event['date']) : '';
        $title = isset($event['title']) ? esc_html($event['title']) : 'Untitled Event';
        $category = isset($event['category']) ? esc_html($event['category']) : '';
        $description = isset($event['description']) ? wp_kses_post($event['description']) : '';
        $color = isset($event['color']) ? esc_attr($event['color']) : '#3b82f6';

        ?>
        <div class="eventcrafter-item" style="--event-color: <?php echo esc_attr($color); ?>;">
            <div class="eventcrafter-marker"></div>
            <div class="eventcrafter-content">
                <div class="eventcrafter-header">
                    <span class="eventcrafter-date">
                        <?php echo esc_html($date); ?>
                    </span>
                    <?php if ($category): ?>
                        <span class="eventcrafter-category">
                            <?php echo esc_html($category); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="eventcrafter-title">
                    <?php echo esc_html($title); ?>
                </h3>
                <?php if ($description): ?>
                    <div class="eventcrafter-description">
                        <?php echo wp_kses_post($description); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($event['link']) && !empty($event['link']['url'])): ?>
                    <a href="<?php echo esc_url($event['link']['url']); ?>"
                        target="<?php echo esc_attr(isset($event['link']['target']) ? $event['link']['target'] : '_self'); ?>"
                        class="eventcrafter-link">
                        <?php echo esc_html(isset($event['link']['text']) ? $event['link']['text'] : 'Read More'); ?> &rarr;
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
