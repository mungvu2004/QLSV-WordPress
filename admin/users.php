<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_users_page() {
    
    global $wpdb;
    $message = '';
    
    $edit_data = null;
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $edit_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tblUser WHERE id = %d", $_GET['id']));
    }
    
    if (isset($_POST['qlsv_save_user'])) {
        $errors = array();
        if (empty($_POST['fullname'])) $errors[] = 'Họ tên không được để trống';
        if (empty($_POST['username'])) $errors[] = 'Tên đăng nhập không được để trống';
        if (!$edit_data && empty($_POST['password'])) $errors[] = 'Mật khẩu không được để trống';
        
        if (empty($errors)) {
            $data = array(
                'fullname' => sanitize_text_field($_POST['fullname']),
                'username' => sanitize_text_field($_POST['username'])
            );
            
            if (!empty($_POST['password'])) {
                $data['password'] = wp_hash_password($_POST['password']);
            }
            
            if ($edit_data) {
                $result = $wpdb->update("{$wpdb->prefix}tblUser", $data, array('id' => $edit_data->id));
                $message = $result !== false ? '<div class="message success">Cập nhật người dùng thành công</div>' : '<div class="message error">Cập nhật thất bại</div>';
            } else {
                $result = $wpdb->insert("{$wpdb->prefix}tblUser", $data);
                $message = $result ? '<div class="message success">Thêm người dùng thành công</div>' : '<div class="message error">Thêm thất bại</div>';
            }
        } else {
            $message = '<div class="message error">' . implode('<br>', $errors) . '</div>';
        }
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $result = $wpdb->delete("{$wpdb->prefix}tblUser", array('id' => intval($_GET['id'])));
        $message = $result ? '<div class="message success">Xóa người dùng thành công</div>' : '<div class="message error">Xóa thất bại</div>';
    }
    
    $per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tblUser");
    
    $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}tblUser LIMIT $offset, $per_page");
    ?>
    <div class="wrap qlsv-wrap">
        <h1>Quản lý Người dùng</h1>
        <?php echo $message; ?>
        <form method="post">
            <?php if ($edit_data) { ?>
                <input type="hidden" name="id" value="<?php echo esc_attr($edit_data->id); ?>">
            <?php } ?>
            <table class="form-table">
                <tr><th>Họ tên</th><td><input type="text" name="fullname" value="<?php echo $edit_data ? esc_attr($edit_data->fullname) : ''; ?>" required></td></tr>
                <tr><th>Tên đăng nhập</th><td><input type="text" name="username" value="<?php echo $edit_data ? esc_attr($edit_data->username) : ''; ?>" required></td></tr>
                <tr><th>Mật khẩu</th><td><input type="password" name="password" <?php echo !$edit_data ? 'required' : ''; ?> placeholder="<?php echo $edit_data ? 'Để trống nếu không đổi' : ''; ?>"></td></tr>
            </table>
            <input type="submit" name="qlsv_save_user" value="Lưu" class="button-primary">
        </form>
        
        <h2>Danh sách người dùng</h2>
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th>ID</th><th>Họ tên</th><th>Tên đăng nhập</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td><?php echo esc_html($user->id); ?></td>
                        <td><?php echo esc_html($user->fullname); ?></td>
                        <td><?php echo esc_html($user->username); ?></td>
                        <td class="action-links">
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit', 'id' => $user->id))); ?>">Sửa</a>
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete', 'id' => $user->id))); ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php echo qlsv_pagination($total_items, $per_page, $current_page); ?>
        <a href="<?php echo esc_url(add_query_arg('qlsv_logout', '1')); ?>" class="button">Đăng xuất</a>
    </div>
    <?php
    if (isset($_GET['qlsv_logout'])) {
        session_start();
        session_destroy();
        wp_redirect(admin_url('admin.php?page=qlsv_users'));
        exit;
    }
}