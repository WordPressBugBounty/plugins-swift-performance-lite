<?php

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

class Swift_Performance_Upgrader_Skin extends WP_Upgrader_Skin {
    public function header() {}
    public function footer() {}
    public function feedback($string, ...$args) {
        // If you need to capture feedback you can implement logging here
        // error_log($string);
    }
    public function error($errors) {
        if (is_wp_error($errors)) {
            // Handle errors silently or log them
            error_log($errors->get_error_message());
        }
    }
}