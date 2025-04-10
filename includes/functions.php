<?php
if (!defined('ABSPATH')) {
    exit;
}

function qlsv_pagination($total_items, $per_page, $current_page) {
    $total_pages = ceil($total_items / $per_page);
    $output = '<div class="pagination">';
    
    if ($current_page > 1) {
        $output .= '<a href="' . esc_url(add_query_arg('paged', $current_page - 1)) . '">Trước</a>';
    }
    
    for ($i = 1; $i <= $total_pages; $i++) {
        $class = ($i == $current_page) ? 'current' : '';
        $output .= '<a href="' . esc_url(add_query_arg('paged', $i)) . '" class="' . $class . '">' . $i . '</a>';
    }
    
    if ($current_page < $total_pages) {
        $output .= '<a href="' . esc_url(add_query_arg('paged', $current_page + 1)) . '">Sau</a>';
    }
    
    $output .= '</div>';
    return $output;
}