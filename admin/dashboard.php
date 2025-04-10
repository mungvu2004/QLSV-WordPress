<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_dashboard_page() {
    $login_result = qlsv_process_login();
    if (!qlsv_check_login()) {
        $message = $login_result === false ? '<div class="message error">Sai tên đăng nhập hoặc mật khẩu</div>' : '';
        qlsv_login_form($message);
        return;
    }
    global $wpdb;
    $total_sv = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tblSinhVien");
    $total_lop = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tblLop");
    $total_khoa = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tblKhoa");
    ?>
    <div class="wrap qlsv-wrap">
        <h1>Trang chủ QLSV</h1>
        <div class="dashboard-stats">
            <div>Tổng số sinh viên: <?php echo esc_html($total_sv); ?></div>
            <div>Tổng số lớp: <?php echo esc_html($total_lop); ?></div>
            <div>Tổng số khoa: <?php echo esc_html($total_khoa); ?></div>
        </div>
        <a href="<?php echo esc_url(add_query_arg('qlsv_logout', '1')); ?>" class="button">Đăng xuất</a>
    </div>
    <?php
    if (isset($_GET['qlsv_logout'])) {
        if (!session_id()) {
            session_start();
        }
        session_destroy();
        wp_redirect(admin_url('admin.php?page=qlsv_dashboard'));
        exit;
    }
}