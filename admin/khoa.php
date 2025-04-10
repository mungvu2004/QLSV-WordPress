<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_khoa_page() {
    
    global $wpdb;
    $message = '';
    
    $edit_data = null;
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['makhoa'])) {
        $edit_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tblKhoa WHERE makhoa = %d", $_GET['makhoa']));
    }
    
    if (isset($_POST['qlsv_save_khoa'])) {
        if (empty($_POST['tenkhoa'])) {
            $message = '<div class="message error">Tên khoa không được để trống</div>';
        } else {
            $data = array('tenkhoa' => sanitize_text_field($_POST['tenkhoa']));
            
            if ($edit_data) {
                $result = $wpdb->update("{$wpdb->prefix}tblKhoa", $data, array('makhoa' => $edit_data->makhoa));
                $message = $result !== false ? '<div class="message success">Cập nhật khoa thành công</div>' : '<div class="message error">Cập nhật thất bại</div>';
            } else {
                $result = $wpdb->insert("{$wpdb->prefix}tblKhoa", $data);
                $message = $result ? '<div class="message success">Thêm khoa thành công</div>' : '<div class="message error">Thêm thất bại</div>';
            }
        }
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['makhoa'])) {
        $result = $wpdb->delete("{$wpdb->prefix}tblKhoa", array('makhoa' => intval($_GET['makhoa'])));
        $message = $result ? '<div class="message success">Xóa khoa thành công</div>' : '<div class="message error">Xóa thất bại</div>';
    }
    
    $per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tblKhoa");
    
    $khoa = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}tblKhoa LIMIT $offset, $per_page");
    ?>
    <div class="wrap qlsv-wrap">
        <h1>Quản lý Khoa</h1>
        <?php echo $message; ?>
        <form method="post">
            <?php if ($edit_data) { ?>
                <input type="hidden" name="makhoa" value="<?php echo esc_attr($edit_data->makhoa); ?>">
            <?php } ?>
            <table class="form-table">
                <tr><th>Tên khoa</th><td><input type="text" name="tenkhoa" value="<?php echo $edit_data ? esc_attr($edit_data->tenkhoa) : ''; ?>" required></td></tr>
            </table>
            <input type="submit" name="qlsv_save_khoa" value="Lưu" class="button-primary">
        </form>
        
        <h2>Danh sách khoa</h2>
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th>Mã khoa</th><th>Tên khoa</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($khoa as $k) { ?>
                    <tr>
                        <td><?php echo esc_html($k->makhoa); ?></td>
                        <td><?php echo esc_html($k->tenkhoa); ?></td>
                        <td class="action-links">
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit', 'makhoa' => $k->makhoa))); ?>">Sửa</a>
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete', 'makhoa' => $k->makhoa))); ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
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
        wp_redirect(admin_url('admin.php?page=qlsv_khoa'));
        exit;
    }
}