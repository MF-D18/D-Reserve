<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D-Reserve | Premium Restaurant</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Page Transition Overlay -->
    <div id="page-transition-overlay"></div>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php?action=home" class="logo">D-Reserve<span>.</span></a>
            <ul class="nav-links">
                <li><a href="index.php?action=home">Beranda</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="index.php?action=admin_dashboard" class="btn btn-outline" style="border-color: var(--primary-color); font-size:0.85rem; padding:8px 16px;">⚙ Panel Admin</a></li>
                    <?php else: ?>
                        <li><a href="index.php?action=book">Pesan Meja</a></li>
                        <li><a href="index.php?action=my_reservations">Reservasi Saya</a></li>
                    <?php endif; ?>
                    <li class="nav-dropdown">
                        <a href="javascript:void(0)" class="nav-dropdown-toggle">
                            Hai, <?= htmlspecialchars(explode(' ', trim($_SESSION['user_name'] ?? 'User'))[0]) ?> ▾
                        </a>
                        <ul class="dropdown-menu">
                            <?php if($_SESSION['user_role'] !== 'admin'): ?>
                                <li><a href="index.php?action=profile">Profil Saya</a></li>
                            <?php endif; ?>
                            <li><a href="index.php?action=logout" class="dropdown-item-danger">Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="index.php?action=register" class="btn btn-outline">Daftar</a></li>
                    <li><a href="index.php?action=login" class="btn btn-primary">Masuk</a></li>
                <?php endif; ?>
            </ul>

        </div>
    </nav>
    <main class="container">
