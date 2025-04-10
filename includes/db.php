<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_install() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Tạo bảng tblUser
    $table_user = $wpdb->prefix . 'tblUser';
    $sql1 = "CREATE TABLE $table_user (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        fullname varchar(255) NOT NULL,
        username varchar(100) NOT NULL,
        password varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Tạo bảng tblSinhVien
    $table_sinhvien = $wpdb->prefix . 'tblSinhVien';
    $sql2 = "CREATE TABLE $table_sinhvien (
        masv mediumint(9) NOT NULL AUTO_INCREMENT,
        ho_ten varchar(255) NOT NULL,
        ngay_sinh date NOT NULL,
        gioi_tinh varchar(10) NOT NULL,
        que_quan text NOT NULL,
        email varchar(100) NOT NULL,
        so_dien_thoai varchar(15) NOT NULL,
        malop mediumint(9),
        PRIMARY KEY  (masv)
    ) $charset_collate;";

    // Tạo bảng tblLop
    $table_lop = $wpdb->prefix . 'tblLop';
    $sql3 = "CREATE TABLE $table_lop (
        makhoa mediumint(9) NOT NULL,
        malop mediumint(9) NOT NULL AUTO_INCREMENT,
        ten_lop varchar(255) NOT NULL,
        PRIMARY KEY  (malop)
    ) $charset_collate;";

    // Tạo bảng tblKhoa
    $table_khoa = $wpdb->prefix . 'tblKhoa';
    $sql4 = "CREATE TABLE $table_khoa (
        makhoa mediumint(9) NOT NULL AUTO_INCREMENT,
        tenkhoa varchar(255) NOT NULL,
        PRIMARY KEY  (makhoa)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql1);
    dbDelta($sql2);
    dbDelta($sql3);
    dbDelta($sql4);

    // Tạo tài khoản mặc định nếu bảng tblUser trống
    $user_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_user");
    if ($user_count == 0) {
        $default_user = array(
            'fullname' => 'Administrator',
            'username' => 'admin',
            'password' => wp_hash_password('admin') // Mật khẩu mã hóa
        );
        $wpdb->insert($table_user, $default_user);
    }
}