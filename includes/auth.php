<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_check_login() {
    // Kiểm tra cookie trước
    if (isset($_COOKIE['qlsv_user_id']) && !empty($_COOKIE['qlsv_user_id'])) {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['qlsv_user_id'] = $_COOKIE['qlsv_user_id'];
        return true;
    }

    // Nếu không có cookie, kiểm tra session
    if (!session_id()) {
        session_start();
    }
    return isset($_SESSION['qlsv_user_id']) && !empty($_SESSION['qlsv_user_id']);
}

function qlsv_login_form($message = '') {
    ?>
    <div class="wrap qlsv-wrap">
        <h1>Đăng nhập</h1>
        <?php if ($message) echo $message; ?>
        <form method="post" class="login-form">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <input type="submit" name="qlsv_login" value="Đăng nhập" class="button-primary">
        </form>
    </div>
    <?php
}

function qlsv_process_login() {
    if (isset($_POST['qlsv_login'])) {
        global $wpdb;
        $username = sanitize_text_field($_POST['username']);
        $password = $_POST['password'];
        
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}tblUser WHERE username = %s",
            $username
        ));
        
        if ($user && wp_check_password($password, $user->password)) {
            if (!session_id()) {
                session_start();
            }
            $_SESSION['qlsv_user_id'] = $user->id;
            setcookie('qlsv_user_id', $user->id, time() + (30 * 24 * 60 * 60), '/'); // Cookie tồn tại 30 ngày
            wp_redirect(admin_url('admin.php?page=qlsv_dashboard')); // Chuyển hướng sau khi đăng nhập
            exit;
        }
        return false;
    }
    return null;
}

// Hàm đăng xuất
function qlsv_logout() {
    if (!session_id()) {
        session_start();
    }
    session_destroy();
    setcookie('qlsv_user_id', '', time() - 3600, '/');
    wp_redirect(admin_url('admin.php?page=qlsv_dashboard'));
    exit;
}