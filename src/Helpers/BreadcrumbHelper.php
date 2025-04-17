<?php

class BreadcrumbHelper
{
    public static function generate()
    {
        // Lấy URL hiện tại và tách thành các phần
        $uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $base_path = 'pdu_pms_project/public';
        if (strpos($uri, $base_path) === 0) {
            $uri = substr($uri, strlen($base_path));
            $uri = trim($uri, '/');
        }
        $segments = array_filter(explode('/', $uri));

        // Tạo mảng Breadcrumb
        $breadcrumb = [];
        $path = '/pdu_pms_project/public';
        $breadcrumb[] = ['name' => 'Trang chủ', 'url' => $path];

        // Xây dựng Breadcrumb dựa trên URL
        if (!empty($segments)) {
            $current_path = $path;
            foreach ($segments as $index => $segment) {
                $current_path .= '/' . $segment;
                $name = ucfirst(str_replace('_', ' ', $segment));
                if ($segment === 'admin' && count($segments) > 1) {
                    $name = 'Admin';
                } elseif ($segment === 'teacher' && count($segments) > 1) {
                    $name = 'Giáo viên';
                } elseif ($segment === 'student' && count($segments) > 1) {
                    $name = 'Sinh viên';
                } elseif ($segment === 'manage_users') {
                    $name = 'Quản lý người dùng';
                } elseif ($segment === 'manage_rooms') {
                    $name = 'Quản lý phòng';
                } elseif ($segment === 'manage_timetable') {
                    $name = 'Quản lý lịch dạy';
                } elseif ($segment === 'manage_bookings') {
                    $name = 'Quản lý đặt phòng';
                } elseif ($segment === 'auto_schedule') {
                    $name = 'Xếp lịch tự động';
                } elseif ($segment === 'book_room') {
                    $name = 'Đặt phòng';
                } elseif ($segment === 'add_user') {
                    $name = 'Thêm người dùng';
                } elseif ($segment === 'edit_user') {
                    $name = 'Sửa người dùng';
                } elseif ($segment === 'add_room') {
                    $name = 'Thêm phòng';
                } elseif ($segment === 'edit_room') {
                    $name = 'Sửa phòng';
                } elseif ($segment === 'add_timetable') {
                    $name = 'Thêm lịch dạy';
                } elseif ($segment === 'edit_timetable') {
                    $name = 'Sửa lịch dạy';
                } elseif ($segment === 'add_booking') {
                    $name = 'Thêm đặt phòng';
                } elseif ($segment === 'edit_booking') {
                    $name = 'Sửa đặt phòng';
                }
                $breadcrumb[] = ['name' => $name, 'url' => $current_path];
            }
        }

        return $breadcrumb;
    }

    public static function render()
    {
        $breadcrumb = self::generate();
        $output = '<nav class="flex items-center space-x-2 text-sm text-gray-600">';
        foreach ($breadcrumb as $index => $item) {
            $is_last = $index === count($breadcrumb) - 1;
            if (!$is_last) {
                $output .= '<a href="' . $item['url'] . '" class="hover:text-indigo-600 transition duration-300">' . $item['name'] . '</a>';
                $output .= '<span class="text-gray-400 mx-1">/</span>';
            } else {
                $output .= '<span class="text-indigo-600 font-medium">' . $item['name'] . '</span>';
            }
        }
        $output .= '</nav>';
        return $output;
    }
}
