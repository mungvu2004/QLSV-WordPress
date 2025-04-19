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
    $css_content = <<<CSS
/* --- RESET --- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
:root {
    --primary: #1e88e5;
    --primary-light: #90caf9;
    --primary-dark: #1565c0;
    --danger: #e53935;
    --success: #43a047;
    --text: #2e2e2e;
    --bg: #f9f9f9;
    --border: #e0e0e0;
    --radius: 8px;
}

/* --- BODY --- */
body {
    font-family: 'Segoe UI', 'Roboto', sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.6;
}

/* --- WRAPPER --- */
.qlsv-wrap {
    max-width: 1200px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: var(--radius);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    position: relative;
}

/* --- TITLES --- */
.qlsv-wrap h1, h2 {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 20px;
}
.qlsv-wrap h1 {
    font-size: 32px;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 10px;
}
.qlsv-wrap h2 {
    font-size: 24px;
}

/* --- FORM --- */
.form-table {
    width: 100%;
    border-spacing: 0 15px;
}
.form-table th {
    width: 200px;
    text-align: left;
    font-weight: 500;
    color: #555;
}
.form-table input[type="text"],
.form-table input[type="email"],
.form-table input[type="password"],
.form-table select,
.form-table textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: #fcfcfc;
    transition: 0.3s;
}
.form-table input:focus,
.form-table textarea:focus,
.form-table select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
    background: #fff;
}
.form-table textarea {
    resize: vertical;
}

/* --- BUTTONS --- */
.button-primary {
    background: var(--primary);
    color: #fff;
    padding: 12px 24px;
    border: none;
    border-radius: var(--radius);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}
.button-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}
.button-primary:active {
    transform: translateY(1px);
}

/* --- TABLE LIST --- */
.wp-list-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-radius: var(--radius);
    overflow: hidden;
}
.wp-list-table th {
    background: var(--primary-light);
    padding: 15px;
    text-align: left;
    color: var(--primary-dark);
    text-transform: uppercase;
    font-size: 14px;
}
.wp-list-table td {
    padding: 15px;
    border-top: 1px solid var(--border);
}
.wp-list-table tr:nth-child(even) {
    background: #fafafa;
}
.wp-list-table tr:hover {
    background: #e3f2fd;
}

/* --- LINKS --- */
.action-links a {
    margin-right: 10px;
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
}
.action-links a:hover {
    text-decoration: underline;
}

/* --- NOTICES --- */
.message {
    padding: 15px;
    margin: 20px 0;
    border-left: 5px solid;
    border-radius: var(--radius);
}
.message.success {
    background: #e8f5e9;
    color: var(--success);
    border-color: var(--success);
}
.message.error {
    background: #ffebee;
    color: var(--danger);
    border-color: var(--danger);
}

/* --- PAGINATION --- */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 30px;
    gap: 8px;
}
.pagination a {
    padding: 8px 14px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-decoration: none;
    color: #555;
    transition: 0.2s;
}
.pagination .current,
.pagination a:hover {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary);
}

/* --- DASHBOARD STATS --- */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.dashboard-stats div {
    background: #fff;
    padding: 20px;
    border-radius: var(--radius);
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-top: 4px solid var(--primary-light);
    transition: 0.3s;
}
.dashboard-stats div:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
}
.dashboard-stats span {
    display: block;
    font-size: 32px;
    font-weight: bold;
    color: var(--primary-dark);
    margin-bottom: 8px;
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
    .qlsv-wrap {
        padding: 20px;
    }
    .form-table th,
    .form-table td {
        display: block;
        width: 100%;
        padding: 5px 0;
    }
    .dashboard-stats {
        grid-template-columns: 1fr;
    }
    .wp-list-table {
        display: block;
        overflow-x: auto;
    }
}
CSS;

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
    remove_menu_page('tools.php'); // Tools
    remove_menu_page('options-general.php'); // Settings
}
add_action('admin_menu', 'custom_menu_page_removing');

add_action('admin_init', 'qlsv_require_login');

// Đăng ký hook kích hoạt plugin
register_activation_hook(__FILE__, 'qlsv_install');