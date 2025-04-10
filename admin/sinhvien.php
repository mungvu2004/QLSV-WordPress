<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_sinhvien_page() {
    
    global $wpdb;
    $message = '';
    
    $edit_data = null;
    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['masv'])) {
        $edit_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}tblSinhVien WHERE masv = %d", $_GET['masv']));
    }
    
    if (isset($_POST['qlsv_save_sv'])) {
        $errors = array();
        if (empty($_POST['ho_ten'])) $errors[] = 'Họ tên không được để trống';
        if (empty($_POST['ngay_sinh'])) $errors[] = 'Ngày sinh không được để trống';
        if (!in_array($_POST['gioi_tinh'], array('Nam', 'Nữ'))) $errors[] = 'Giới tính không hợp lệ';
        if (empty($_POST['que_quan'])) $errors[] = 'Quê quán không được để trống';
        if (!is_email($_POST['email'])) $errors[] = 'Email không hợp lệ';
        if (!preg_match('/^[0-9]{10,11}$/', $_POST['so_dien_thoai'])) $errors[] = 'Số điện thoại không hợp lệ';
        
        if (empty($errors)) {
            $data = array(
                'ho_ten' => sanitize_text_field($_POST['ho_ten']),
                'ngay_sinh' => $_POST['ngay_sinh'],
                'gioi_tinh' => $_POST['gioi_tinh'],
                'que_quan' => sanitize_textarea_field($_POST['que_quan']),
                'email' => sanitize_email($_POST['email']),
                'so_dien_thoai' => sanitize_text_field($_POST['so_dien_thoai']),
                'malop' => intval($_POST['malop'])
            );
            
            if (isset($_POST['masv']) && !empty($_POST['masv'])) {
                $result = $wpdb->update("{$wpdb->prefix}tblSinhVien", $data, array('masv' => intval($_POST['masv'])));
                $message = $result !== false ? '<div class="message success">Cập nhật sinh viên thành công</div>' : '<div class="message error">Cập nhật thất bại</div>';
            } else {
                $result = $wpdb->insert("{$wpdb->prefix}tblSinhVien", $data);
                $message = $result ? '<div class="message success">Thêm sinh viên thành công</div>' : '<div class="message error">Thêm thất bại</div>';
            }
        } else {
            $message = '<div class="message error">' . implode('<br>', $errors) . '</div>';
        }
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['masv'])) {
        $result = $wpdb->delete("{$wpdb->prefix}tblSinhVien", array('masv' => intval($_GET['masv'])));
        $message = $result ? '<div class="message success">Xóa sinh viên thành công</div>' : '<div class="message error">Xóa thất bại</div>';
    }
    
    $per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}tblSinhVien");
    
    $sinhvien = $wpdb->get_results("SELECT sv.*, l.ten_lop FROM {$wpdb->prefix}tblSinhVien sv LEFT JOIN {$wpdb->prefix}tblLop l ON sv.malop = l.malop LIMIT $offset, $per_page");
    $lop = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}tblLop");
    ?>
    <div class="wrap qlsv-wrap">
        <h1>Quản lý Sinh viên</h1>
        <?php echo $message; ?>
        <form method="post">
            <?php if ($edit_data) { ?>
                <input type="hidden" name="masv" value="<?php echo esc_attr($edit_data->masv); ?>">
            <?php } ?>
            <table class="form-table">
                <tr><th>Họ tên</th><td><input type="text" name="ho_ten" value="<?php echo $edit_data ? esc_attr($edit_data->ho_ten) : ''; ?>" required></td></tr>
                <tr><th>Ngày sinh</th><td><input type="date" name="ngay_sinh" value="<?php echo $edit_data ? esc_attr($edit_data->ngay_sinh) : ''; ?>" required></td></tr>
                <tr><th>Giới tính</th><td>
                    <select name="gioi_t используетсяinh">
                        <option value="Nam" <?php echo $edit_data && $edit_data->gioi_tinh == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo $edit_data && $edit_data->gioi_tinh == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </td></tr>
                <tr><th>Quê quán</th><td><textarea name="que_quan" required><?php echo $edit_data ? esc_textarea($edit_data->que_quan) : ''; ?></textarea></td></tr>
                <tr><th>Email</th><td><input type="email" name="email" value="<?php echo $edit_data ? esc_attr($edit_data->email) : ''; ?>" required></td></tr>
                <tr><th>Số điện thoại</th><td><input type="text" name="so_dien_thoai" value="<?php echo $edit_data ? esc_attr($edit_data->so_dien_thoai) : ''; ?>" required></td></tr>
                <tr><th>Lớp</th><td>
                    <select name="malop">
                        <?php foreach ($lop as $l) { ?>
                            <option value="<?php echo esc_attr($l->malop); ?>" <?php echo $edit_data && $edit_data->malop == $l->malop ? 'selected' : ''; ?>><?php echo esc_html($l->ten_lop); ?></option>
                        <?php } ?>
                    </select>
                </td></tr>
            </table>
            <input type="submit" name="qlsv_save_sv" value="Lưu" class="button-primary">
        </form>
        
        <h2>Danh sách sinh viên</h2>
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th>Mã SV</th><th>Họ tên</th><th>Ngày sinh</th><th>Giới tính</th><th>Lớp</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sinhvien as $sv) { ?>
                    <tr>
                        <td><?php echo esc_html($sv->masv); ?></td>
                        <td><?php echo esc_html($sv->ho_ten); ?></td>
                        <td><?php echo esc_html($sv->ngay_sinh); ?></td>
                        <td><?php echo esc_html($sv->gioi_tinh); ?></td>
                        <td><?php echo esc_html($sv->ten_lop); ?></td>
                        <td class="action-links">
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'edit', 'masv' => $sv->masv))); ?>">Sửa</a>
                            <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete', 'masv' => $sv->masv))); ?>" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
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
        if (!session_id()) {
            session_start();
        }
        session_destroy();
        wp_redirect(admin_url('admin.php?page=qlsv_sinhvien'));
        exit;
    }
}