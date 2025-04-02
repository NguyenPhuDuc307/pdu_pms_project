<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'PDU - PMS'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Thêm Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .header-gradient {
            background: linear-gradient(90deg, #4F46E5, #7C3AED);
        }
    </style>
</head>

<body class="bg-gray-100">
    <header class="header-gradient text-white fixed w-full top-0 z-20 shadow-lg">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <!-- Logo -->
            <a href="/pdu_pms_project/public/" class="text-xl font-semibold tracking-tight flex items-center space-x-2">
                <i class="fas fa-school"></i>
                <span>PDU - PMS</span>
            </a>

            <!-- Navigation -->
            <div class="flex items-center space-x-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User Info & Dropdown -->
                    <div class="relative">
                        <button data-dropdown-toggle="userDropdown" class="flex items-center space-x-2 cursor-pointer user-menu px-4 py-2 rounded-full">
                            <span class="text-sm font-medium capitalize"><?php echo ucfirst($_SESSION['role']); ?>: <?php echo $_SESSION['user_id']; ?> (<?php echo count(array_filter($data['users'] ?? [], fn($user) => $user['role'] === $_SESSION['role'])); ?>)</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div id="userDropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44">
                            <div class="px-4 py-3 text-sm text-gray-900">
                                <div><?php echo ucfirst($_SESSION['role']); ?>: <?php echo $_SESSION['full_name']; ?></div>
                            </div>
                            <ul class="py-2 text-sm text-gray-700">
                                <li>
                                    <a href="/pdu_pms_project/public/logout" class="flex items-center space-x-2 px-4 py-2 hover:bg-red-500 hover:text-white transition duration-300">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Đăng xuất</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/pdu_pms_project/public/about" class="nav-link text-white hover:text-gray-200 font-medium flex items-center space-x-1 mr-4">
                        <i class="fas fa-info-circle"></i>
                        <span>Giới thiệu</span>
                    </a>
                    <a href="/pdu_pms_project/public/guide" class="nav-link text-white hover:text-gray-200 font-medium flex items-center space-x-1 mr-4">
                        <i class="fas fa-book"></i>
                        <span>Hướng dẫn</span>
                    </a>
                    <a href="/pdu_pms_project/public/login" class="nav-link text-white hover:text-gray-200 font-medium flex items-center space-x-1">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Đăng nhập</span>
                    </a>
                    <a href="/pdu_pms_project/public/register" class="nav-link bg-white text-indigo-600 px-4 py-2 rounded-full font-semibold hover:bg-indigo-100 transition duration-300 flex items-center space-x-1">
                        <i class="fas fa-user-plus"></i>
                        <span>Đăng ký</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="flex pt-16">