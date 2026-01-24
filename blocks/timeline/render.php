<?php
/**
 * Server-side render callback for the EventCrafter Timeline block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content (empty for dynamic blocks).
 * @param WP_Block $block      Block instance.
 * @return string Rendered HTML.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load renderer if needed
if (!class_exists('EventCrafter_Renderer')) {
    require_once EVENTCRAFTER_PATH . 'includes/class-event-renderer.php';
}

$timeline_id = isset($attributes['timelineId']) ? absint($attributes['timelineId']) : 0;
$source = isset($attributes['source']) ? esc_url_raw($attributes['source']) : '';
$layout = isset($attributes['layout']) ? sanitize_key($attributes['layout']) : 'vertical';
$limit = isset($attributes['limit']) ? intval($attributes['limit']) : -1;
$show_animation = isset($attributes['showAnimation']) ? (bool) $attributes['showAnimation'] : true;

// Determine source
$render_source = $timeline_id > 0 ? $timeline_id : $source;

if (empty($render_source)) {
    echo '<div class="eventcrafter-block-placeholder">';
    echo esc_html__('Please select a timeline or provide a source URL.', 'eventcrafter-visual-timeline');
    echo '</div>';
    return;
}

// Enqueue frontend assets
wp_enqueue_style('eventcrafter-style');
wp_enqueue_script('eventcrafter-script');

// Build wrapper classes
$wrapper_classes = array('wp-block-eventcrafter-timeline');
if (!empty($attributes['className'])) {
    $wrapper_classes[] = esc_attr($attributes['className']);
}
if (!$show_animation) {
    $wrapper_classes[] = 'no-animation';
}
if (!empty($attributes['align'])) {
    $wrapper_classes[] = 'align' . esc_attr($attributes['align']);
}

$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => implode(' ', $wrapper_classes),
));

// Render using existing renderer
$renderer = new EventCrafter_Renderer();
$output = $renderer->render($render_source, $layout);

printf(
    '<div %1$s>%2$s</div>',
    $wrapper_attributes,
    $output
);
