<?php
/**
 * PHPUnit Bootstrap
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Initialize WP_Mock
WP_Mock::setUsePatchwork(true);
WP_Mock::bootstrap();

require_once __DIR__ . '/unit/stubs.php';

// Define Constants usually defined by WordPress
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}
if (!defined('EVENTCRAFTER_VERSION')) {
    define('EVENTCRAFTER_VERSION', '1.1.0');
}
if (!defined('EVENTCRAFTER_URL')) {
    define('EVENTCRAFTER_URL', 'http://example.org/wp-content/plugins/eventcrafter-visual-timeline/');
}
if (!defined('EVENTCRAFTER_PATH')) {
    define('EVENTCRAFTER_PATH', dirname(__DIR__) . '/');
}
