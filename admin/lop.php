<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_lop_page() {
    
    global $wpdb;
    $message = '';
    
    $edit_data = null;
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['malop'])) {
        $edit_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tblLop WHERE malop = %d", $_GET['malop']));
    }
    
    if (isset($_POST['qlsv_save_lop'])) {
        $errors = array();
        if (empty($_POST['ten_lop'])) $errors[] = 'Tên lớp không được để trống';
        if (empty($_POST['makhoa'])) $errors[] = 'Vui lòng chọn khoa';
        
        if (empty($errors)) {
            $data = array(
                'makhoa' => intval($_POST['makhoa']),
                'ten_lop' => sanitize_text_field($_POST['ten_lop'])
            );
            
            if ($edit_data) {
                $result = $wpdb->update("{$wpdb->prefix}tblLop", $data, array('malop' => $edit_data->malop));
                $message = $result !== false ? '<div class="message success">Cập nhật lớp thành công</div>' : '<div class="message error">Cập nhật thất bại</div>';
            } else {
                $result = $wpdb->insert("{$wpdb->prefix}tblLop", $data);
                $message = $result ? '<div class="message success">Thêm lớp thành công</div>' : '<div class="message error">Thêm thất bại</div>';
            }
        } else {
            $message = '<div class="message error">' . implode('<br>', $errors) . '</div>';
        }
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['malop'])) {
        $result = $wpdb->delete("{$wpdb->prefix}tblLop", array('malop' => intval($_GET['malop'])));
        $message = $result ? '<div class="message success">Xóa lớp thành công</div>' : '<div class="message error">Xóa thất bại</div>';
    }
    
    $per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tblLop");
    
    $lop = $wpdb->get_results("SELECT l.*, k.tenkhoa FROM {$wpdb->prefix}tblLop l LEFT JOIN {$wpdb->prefix}tblKhoa k ON l.makhoa = k.makhoa LIMIT $offset, $per_page");
    $khoa = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}tblKhoa");
    ?>
    <div class="wrap qlsv-wrap">
        <h1>Quản lý Lớp học</h1>
        <?php echo $message; ?>
        <form method="post">
            <?php if ($edit_data) { ?>
                <input type="hidden" name="malop" value="<?php echo esc_attr($edit_data->malop); ?>">
            <?php } ?>
            <table class="form-table">
                <tr><th>Tên lớp</th><td><input type="text" name="ten_lop" value="<?php echo $edit_data ? esc_attr($edit_data->ten_lop) : ''; ?>" required></td></tr>
                <tr><th>Khoa</th><td>
                    <select name="makhoa" required>
                        <?php foreach ($khoa as $k) { ?>
                            <option value="<?php echo esc_attr($k->makhoa); ?>" <?php echo $edit_data && $edit_data->makhoa == $k->makhoa ? 'selected' : ''; ?>><?php echo esc_html($k->tenkhoa); ?></option>
                        <?php } ?>
                    </select>
                </td></tr>
            </table>
            <input type="submit" name="qlsv_save_lop" value="Lưu" class="button-primary">
        </form>
        
        <h2>Danh sách lớp học</h2>
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th>Mã lớp</th><th>Tên lớp</th><th>Khoa</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lop as $l) { ?>
                    <tr>
                        <td><?php echo esc_html($l->malop); ?></td>
                        <td><?php echo esc_html($l->ten_lop); ?></td>
                        <td><?php echo esc_html($l->tenkhoa); ?></td>
                        <td class="action-links">
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit', 'malop' => $l->malop))); ?>">Sửa</a>
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete', 'malop' => $l->malop))); ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
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
        wp_redirect(admin_url('admin.php?page=qlsv_lop'));
        exit;
    }
}