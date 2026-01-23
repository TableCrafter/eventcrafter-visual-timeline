<?php
if (!defined('ABSPATH')) {
    exit;
}

class EventCrafter_CPT
{
    public function __construct()
    {
        add_action('init', array($this, 'register_post_type'));
        add_filter('manage_ec_timeline_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_ec_timeline_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
    }

    public function register_post_type()
    {
        $labels = array(
            'name' => _x('Timelines', 'Post Type General Name', 'eventcrafter-visual-timeline'),
            'singular_name' => _x('Timeline', 'Post Type Singular Name', 'eventcrafter-visual-timeline'),
            'menu_name' => __('Timelines', 'eventcrafter-visual-timeline'),
            'name_admin_bar' => __('Timeline', 'eventcrafter-visual-timeline'),
            'archives' => __('Timeline Archives', 'eventcrafter-visual-timeline'),
            'attributes' => __('Timeline Attributes', 'eventcrafter-visual-timeline'),
            'parent_item_colon' => __('Parent Timeline:', 'eventcrafter-visual-timeline'),
            'all_items' => __('All Timelines', 'eventcrafter-visual-timeline'),
            'add_new_item' => __('Add New Timeline', 'eventcrafter-visual-timeline'),
            'add_new' => __('Add New', 'eventcrafter-visual-timeline'),
            'new_item' => __('New Timeline', 'eventcrafter-visual-timeline'),
            'edit_item' => __('Edit Timeline', 'eventcrafter-visual-timeline'),
            'update_item' => __('Update Timeline', 'eventcrafter-visual-timeline'),
            'view_item' => __('View Timeline', 'eventcrafter-visual-timeline'),
            'view_items' => __('View Timelines', 'eventcrafter-visual-timeline'),
            'search_items' => __('Search Timeline', 'eventcrafter-visual-timeline'),
            'not_found' => __('Not found', 'eventcrafter-visual-timeline'),
            'not_found_in_trash' => __('Not found in Trash', 'eventcrafter-visual-timeline'),
            'featured_image' => __('Featured Image', 'eventcrafter-visual-timeline'),
            'set_featured_image' => __('Set featured image', 'eventcrafter-visual-timeline'),
            'remove_featured_image' => __('Remove featured image', 'eventcrafter-visual-timeline'),
            'use_featured_image' => __('Use as featured image', 'eventcrafter-visual-timeline'),
            'insert_into_item' => __('Insert into timeline', 'eventcrafter-visual-timeline'),
            'uploaded_to_this_item' => __('Uploaded to this timeline', 'eventcrafter-visual-timeline'),
            'items_list' => __('Timelines list', 'eventcrafter-visual-timeline'),
            'items_list_navigation' => __('Timelines list navigation', 'eventcrafter-visual-timeline'),
            'filter_items_list' => __('Filter timelines list', 'eventcrafter-visual-timeline'),
        );
        $args = array(
            'label' => __('Timeline', 'eventcrafter-visual-timeline'),
            'description' => __('EventCrafter Timelines', 'eventcrafter-visual-timeline'),
            'labels' => $labels,
            'supports' => array('title'), // We don't need 'editor' as we use custom metabox
            'hierarchical' => false,
            'public' => false, // Not public on frontend directly (uses shortcode)
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-excerpt-view',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
        );
        register_post_type('ec_timeline', $args);
    }

    public function set_custom_columns($columns)
    {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['shortcode'] = __('Shortcode', 'eventcrafter-visual-timeline');
        $new_columns['date'] = $columns['date'];
        return $new_columns;
    }

    public function custom_column_content($column, $post_id)
    {
        switch ($column) {
            case 'shortcode':
                echo '<input type="text" readonly="readonly" value="[eventcrafter id=\'' . $post_id . '\']" class="large-text code" style="width: 100%; max-width: 250px;" />';
                break;
        }
    }
}
