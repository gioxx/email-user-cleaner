<?php

// Basic WordPress function mocks for static analysis with PHPStan.

function add_action(...$args) {}
function add_menu_page(...$args) {}
function current_user_can(...$args) { return true; }
function get_users(...$args) { return []; }
function get_user_meta(...$args) { return ''; }
function get_user_by(...$args) { return false; }
function update_user_meta(...$args) {}
function delete_user_meta(...$args) {}
function wp_get_current_user() { return (object)['ID' => 1]; }
function wp_delete_user(...$args) {}
function check_admin_referer(...$args) {}
function wp_nonce_field(...$args) {}
function is_plugin_active(...$args) { return false; }
function current_time(...$args) { return time(); }
function get_plugin_data(...$args) { return []; }
function plugin_dir_path(...$args) { return __DIR__; }
function plugin_dir_url(...$args) { return 'http://example.com/'; }
function plugins_url(...$args) { return 'http://example.com/'; }
function admin_url(...$args) { return 'http://example.com/wp-admin/'; }

function esc_html($string) { return $string; }
function esc_html__($string) { return $string; }
function esc_html_e($string) { echo $string; }
function esc_attr($string) { return $string; }
function esc_attr_e($string) { echo $string; }
function esc_url($string) { return $string; }
function esc_url_raw($string) { return $string; }
function wp_kses($string, $allowed_html = [], $allowed_protocols = []) { return $string; }
function wp_kses_post($string) { return $string; }
function wp_die($message = '') { die($message); }

function __($string, $domain = 'default') { return $string; }
function _e($string, $domain = 'default') { echo $string; }

function sanitize_email($email) { return $email; }
function sanitize_text_field($text) { return $text; }
function sanitize_textarea_field($text) { return $text; }
function sanitize_user($username) { return $username; }
function is_email($email) { return (bool) filter_var($email, FILTER_VALIDATE_EMAIL); }


if (!function_exists('esc_html__')) {
    function esc_html__($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('esc_html_e')) {
    function esc_html_e($text, $domain = 'default') {
        echo $text;
    }
}

if (!function_exists('wp_die')) {
    function wp_die($message = '') {
        exit;
    }
}