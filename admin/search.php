<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_search_page() {
    
    global $wpdb;
    $results = array();
    $message = '';
    
    if (isset($_POST['qlsv_search'])) {
        $keyword = sanitize_text_field($_POST['keyword']);
        if (empty($keyword)) {
            $message = '<div class="message error">Vui lòng nhập từ khóa tìm kiếm</div>';
        } else {
            $per_page = 10;
            $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
            $offset = ($current_page - 1) * $per_page;
            
            $total_items = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}tblSinhVien WHERE ho_ten LIKE %s OR masv LIKE %s",
                "%$keyword%", "%$keyword%"
            ));
            
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT sv.*, l.ten_lop FROM {$wpdb->prefix}tblSinhVien sv 
                    LEFT JOIN {$wpdb->prefix}tblLop l ON sv.malop = l.malop 
                    WHERE sv.ho_ten LIKE %s OR sv.masv LIKE %s 
                    LIMIT $offset, $per_page",
                    "%$keyword%", "%$keyword%"
                )
            );
        }
    }
    ?>
    <div class="wrap qlsv-wrap">
        <h1>Tìm kiếm Sinh viên</h1>
        <?php echo $message; ?>
        <form method="post">
            <input type="text" name="keyword" placeholder="Nhập tên hoặc mã SV">
            <input type="submit" name="qlsv_search" value="Tìm kiếm" class="button-primary">
        </form>
        
        <?php if (!empty($results)) { ?>
            <h2>Kết quả tìm kiếm</h2>
            <table class="wp-list-table widefat">
                <thead>
                    <tr>
                        <th>Mã SV</th><th>Họ tên</th><th>Ngày sinh</th><th>Giới tính</th><th>Lớp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $sv) { ?>
                        <tr>
                            <td><?php echo esc_html($sv->masv); ?></td>
                            <td><?php echo esc_html($sv->ho_ten); ?></td>
                            <td><?php echo esc_html($sv->ngay_sinh); ?></td>
                            <td><?php echo esc_html($sv->gioi_tinh); ?></td>
                            <td><?php echo esc_html($sv->ten_lop); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php echo qlsv_pagination($total_items, $per_page, $current_page); ?>
        <?php } ?>
        <a href="<?php echo esc_url(add_query_arg('qlsv_logout', '1')); ?>" class="button">Đăng xuất</a>
    </div>
    <?php
    if (isset($_GET['qlsv_logout'])) {
        session_start();
        session_destroy();
        wp_redirect(admin_url('admin.php?page=qlsv_search'));
        exit;
    }
}