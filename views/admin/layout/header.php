<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | D-Reserve</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">

</head>
<body class="admin-body">

<!-- Page Transition Overlay -->
<div id="page-transition-overlay"></div>

<aside class="admin-sidebar">
    <div class="sidebar-logo">
        <a href="index.php?action=admin_dashboard">D-Reserve<span>.</span></a>
        <small>Panel Admin</small>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php?action=admin_dashboard"  class="<?= (strpos($_GET['action'] ?? '','dashboard') !== false)  ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="index.php?action=admin_users"       class="<?= (strpos($_GET['action'] ?? '','user') !== false)        ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Pengguna
        </a>
        <a href="index.php?action=admin_tables"      class="<?= (strpos($_GET['action'] ?? '','table') !== false)        ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
            Meja
        </a>
        <a href="index.php?action=admin_menus"       class="<?= (strpos($_GET['action'] ?? '','menu') !== false)         ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 0 20A10 10 0 0 0 12 2z"/><path d="M12 8v4l3 3"/></svg>
            Menu
        </a>
        <a href="index.php?action=admin_reservations" class="<?= (strpos($_GET['action'] ?? '','reservation') !== false) ? 'active' : '' ?>">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Reservasi
        </a>
        <hr style="border-color: rgba(255,255,255,0.05); margin: 15px 0;">
        <a href="index.php?action=home">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Kembali ke Situs
        </a>
        <a href="index.php?action=logout">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
        </a>
    </nav>
</aside>


<div class="admin-main">
    <header class="admin-topbar">
        <span class="admin-topbar-title">
            <?php
            $titles = [
                'admin_dashboard'       => 'Dashboard',
                'admin_users'           => 'Kelola Pengguna',
                'admin_user_create'     => 'Tambah Pengguna',
                'admin_user_edit'       => 'Edit Pengguna',
                'admin_tables'          => 'Kelola Meja',
                'admin_table_create'    => 'Tambah Meja',
                'admin_table_edit'      => 'Edit Meja',
                'admin_menus'           => 'Kelola Menu',
                'admin_menu_create'     => 'Tambah Item Menu',
                'admin_menu_edit'       => 'Edit Item Menu',
                'admin_reservations'    => 'Semua Reservasi',
            ];
            echo $titles[$_GET['action'] ?? 'admin_dashboard'] ?? 'Admin';
            ?>
        </span>
        <span class="admin-user-badge">
            <?= htmlspecialchars($_SESSION['user_name']) ?> &bull; Admin
        </span>
    </header>
    <div class="admin-content">
        <?php
        $msgMap = [
            'created'     => ['success', 'Data berhasil ditambahkan.'],
            'updated'     => ['success', 'Data berhasil diperbarui.'],
            'deleted'     => ['danger',  'Data berhasil dihapus.'],
            'not_found'   => ['danger',  'Data tidak ditemukan.'],
            'self_delete' => ['danger',  'Anda tidak dapat menghapus akun Anda sendiri.'],
        ];
        if (isset($_GET['msg']) && isset($msgMap[$_GET['msg']])) {
            [$type, $text] = $msgMap[$_GET['msg']];
            echo "<div class='alert alert-{$type}'>{$text}</div>";
        }
        ?>
