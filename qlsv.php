<?php
/*
Plugin Name: QLSV - Hệ thống quản lý sinh viên
Plugin URI: http://example.com/
Description: Plugin quản lý sinh viên, người dùng, khoa, lớp học dựa trên nền tảng WordPress.
Version: 1.4
Author: Tên của bạn
Author URI: http://example.com/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

// Include các file từ thư mục includes
require_once plugin_dir_path(__FILE__) . 'includes/db.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/auth.php';

// Include các file admin
require_once plugin_dir_path(__FILE__) . 'admin/dashboard.php';
require_once plugin_dir_path(__FILE__) . 'admin/sinhvien.php';
require_once plugin_dir_path(__FILE__) . 'admin/users.php';
require_once plugin_dir_path(__FILE__) . 'admin/search.php';
require_once plugin_dir_path(__FILE__) . 'admin/khoa.php';
require_once plugin_dir_path(__FILE__) . 'admin/lop.php';

// Thêm CSS
function qlsv_enqueue_styles() {
    if (isset($_GET['page']) && strpos($_GET['page'], 'qlsv_') === 0) {
        wp_enqueue_style('qlsv-style', plugin_dir_url(__FILE__) . 'assets/css/qlsv-style.css');
    }
}
add_action('admin_enqueue_scripts', 'qlsv_enqueue_styles');

// Tạo file CSS nếu chưa tồn tại
$css_file = plugin_dir_path(__FILE__) . 'assets/css/qlsv-style.css';
if (!file_exists($css_file)) {
    $css_content = "
    .qlsv-wrap_snipet-wrap { max-width: 1200px; margin: 20px; }
    .form-table th { width: 150px; }
    .message { padding: 10px; margin: 10px 0; border-radius: 3px; }
    .success { background: #dff0d8; color: #3c763d; }
    .error { background: #f2dede; color: #a94442; }
    .wp-list-table { margin-top: 20px; }
    .action-links a { margin-right: 10px; }
    .pagination { margin-top: 20px; }
    .pagination a { padding: 5px 10px; margin: 0 5px; text-decoration: none; }
    .pagination .current { background: #0073aa; color: white; }
    .login-form { max-width: 400px; margin: 20px auto; padding: 20px; background: #f5f5f5; border-radius: 5px; }
    .login-form input[type='text'], .login-form input[type='password'] { width: 100%; margin-bottom: 10px; }
    ";
    @file_put_contents($css_file, $css_content);
}

// Đăng ký menu
function qlsv_menu() {
    $capability = 'read';
    add_menu_page('Trang chủ QLSV', 'QLSV', $capability, 'qlsv_dashboard', 'qlsv_dashboard_page', 'dashicons-welcome-learn-more', 6);
    add_submenu_page('qlsv_dashboard', 'Quản lý Sinh viên', 'Quản lý Sinh viên', $capability, 'qlsv_sinhvien', 'qlsv_sinhvien_page');
    add_submenu_page('qlsv_dashboard', 'Quản lý Người dùng', 'Quản lý Người dùng', $capability, 'qlsv_users', 'qlsv_users_page');
    add_submenu_page('qlsv_dashboard', 'Tìm kiếm Sinh viên', 'Tìm kiếm Sinh viên', $capability, 'qlsv_search', 'qlsv_search_page');
    add_submenu_page('qlsv_dashboard', 'Quản lý Khoa', 'Quản lý Khoa', $capability, 'qlsv_khoa', 'qlsv_khoa_page');
    add_submenu_page('qlsv_dashboard', 'Quản lý Lớp học', 'Quản lý Lớp học', $capability, 'qlsv_lop', 'qlsv_lop_page');
}
add_action('admin_menu', 'qlsv_menu');

// Kiểm tra đăng nhập tập trung
function qlsv_require_login() {
    if (isset($_GET['page']) && strpos($_GET['page'], 'qlsv_') === 0) {
        $login_result = qlsv_process_login();
        if (!qlsv_check_login() && $_GET['page'] !== 'qlsv_dashboard') {
            wp_redirect(admin_url('admin.php?page=qlsv_dashboard'));
            exit;
        }
    }
}
function custom_menu_page_removing() {
    remove_menu_page('index.php'); // Dashboard
    remove_menu_page('edit.php'); // Posts
    remove_menu_page('upload.php'); // Media
    remove_menu_page('edit.php?post_type=page'); // Pages
    remove_menu_page('edit-comments.php'); // Comments
    remove_menu_page('themes.php'); // Appearance
    remove_menu_page('plugins.php'); // Plugins
    remove_menu_page('users.php'); // Users
    remove_menu_page('tools.php'); // Tools
    remove_menu_page('options-general.php'); // Settings
}
add_action('admin_menu', 'custom_menu_page_removing');

add_action('admin_init', 'qlsv_require_login');

// Đăng ký hook kích hoạt plugin
register_activation_hook(__FILE__, 'qlsv_install');