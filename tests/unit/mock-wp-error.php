<?php
if (!class_exists('WP_Error')) {
    class WP_Error
    {
        public function __construct($code = '', $message = '', $data = '')
        {
        }
        public function get_error_message()
        {
        }
    }
}
