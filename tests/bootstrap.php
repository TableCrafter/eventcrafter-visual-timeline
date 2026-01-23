<?php
/**
 * PHPUnit Bootstrap
 */

// Load Composer Autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define WordPress constants
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

// Define Plugin constants
define('EVENTCRAFTER_PATH', dirname(__DIR__) . '/');
define('EVENTCRAFTER_URL', 'http://example.com/wp-content/plugins/eventcrafter-visual-timeline/');
define('EVENTCRAFTER_VERSION', '1.1.3');

// Initialize WP_Mock
require_once __DIR__ . '/unit/stubs.php';
WP_Mock::bootstrap();
