<?php 
require_once '../views/layout/header.php'; 
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

$stmtFood = $conn->prepare("SELECT * FROM menus WHERE is_available = 1 AND category = 'makanan' LIMIT 3");
$stmtFood->execute();
$foodMenus = $stmtFood->fetchAll(PDO::FETCH_ASSOC);

$stmtDrink = $conn->prepare("SELECT * FROM menus WHERE is_available = 1 AND category = 'minuman' LIMIT 3");
$stmtDrink->execute();
$drinkMenus = $stmtDrink->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="hero glass-panel">
    <p class="section-sub" style="margin-bottom:12px;">✦ Premium Dining Experience ✦</p>
    <h1>Rasakan Kelezatan yang Tak Terlupakan</h1>
    <p>Pesan meja Anda hari ini dan hindari antrian panjang. Pre-order makanan Anda untuk pengalaman bersantap yang sempurna.</p>
    <?php if(!isset($_SESSION['user_id'])): ?>
        <a href="index.php?action=login" class="btn btn-primary">Masuk untuk Reservasi</a>
    <?php else: ?>
        <a href="index.php?action=book" class="btn btn-primary">Pesan Meja Sekarang</a>
    <?php endif; ?>
</section>

<!-- Gradient divider -->
<div style="width:80px; height:2px; background:linear-gradient(90deg, transparent, var(--primary-color), transparent); margin:50px auto 0; border-radius:2px;"></div>

<section style="margin-top: 50px;">
    <p class="section-sub">Pilihan Terbaik</p>
    <h2 style="text-align: center; margin-bottom: 30px;">Menu Makanan</h2>
    <div class="grid">
        <?php foreach($foodMenus as $menu): ?>
        <?php
            $imgSrc = !empty($menu['image_url']) ? '/D-Reserve/public/' . ltrim($menu['image_url'], '/') : null;
            if (!$imgSrc || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imgSrc)) {
                $nameLower = strtolower($menu['name']);
                if (str_contains($nameLower, 'wagyu') || str_contains($nameLower, 'steak') || str_contains($nameLower, 'foie') || str_contains($nameLower, 'duck') || str_contains($nameLower, 'lobster')) $imgSrc = '/D-Reserve/public/img/wagyua5.webp';
                elseif (str_contains($nameLower, 'pasta') || str_contains($nameLower, 'truffle') || str_contains($nameLower, 'risotto')) $imgSrc = '/D-Reserve/public/img/trufflepasta.webp';
                else $imgSrc = null;
            }
        ?>
        <div class="item-card">
            <?php if($imgSrc): ?>
            <div class="img-zoom-wrap">
                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($menu['name']) ?>">
            </div>
            <?php endif; ?>
            <h3><?= htmlspecialchars($menu['name']) ?></h3>
            <p><?= htmlspecialchars($menu['description']) ?></p>
            <p style="color: var(--primary-color); font-weight: bold;">Rp <?= number_format($menu['price'], 0, ',', '.') ?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <h2 style="text-align: center; margin-top: 50px; margin-bottom: 30px;">Menu Minuman</h2>
    <div class="grid">
        <?php foreach($drinkMenus as $menu): ?>
        <?php
            $imgSrc = !empty($menu['image_url']) ? '/D-Reserve/public/' . ltrim($menu['image_url'], '/') : null;
            if (!$imgSrc || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imgSrc)) {
                $nameLower = strtolower($menu['name']);
                if (str_contains($nameLower, 'mojito') || str_contains($nameLower, 'drink') || str_contains($nameLower, 'martini') || str_contains($nameLower, 'kombucha') || str_contains($nameLower, 'matcha') || str_contains($nameLower, 'old fashioned')) $imgSrc = '/D-Reserve/public/img/Mojito.jpg';
                else $imgSrc = null;
            }
        ?>
        <div class="item-card">
            <?php if($imgSrc): ?>
            <div class="img-zoom-wrap">
                <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($menu['name']) ?>">
            </div>
            <?php endif; ?>
            <h3><?= htmlspecialchars($menu['name']) ?></h3>
            <p><?= htmlspecialchars($menu['description']) ?></p>
            <p style="color: var(--primary-color); font-weight: bold;">Rp <?= number_format($menu['price'], 0, ',', '.') ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="index.php?action=menus" class="btn btn-outline" style="padding: 12px 35px;">Lihat Menu Lainnya</a>
    </div>
</section>

<!-- Gradient divider -->
<div style="width:80px; height:2px; background:linear-gradient(90deg, transparent, var(--primary-color), transparent); margin:50px auto 0; border-radius:2px;"></div>

<!-- ── Contact & Map Section ───────────────── -->
<section style="margin-top: 50px;">
    <p class="section-sub">Lokasi & Kontak</p>
    <h2 style="text-align:center; margin-bottom: 8px;">Temukan Kami</h2>
    <p style="text-align:center; margin-bottom:40px; max-width:500px; margin-left:auto; margin-right:auto;">
        Kami berlokasi di pusat kota, siap menyambut Anda dengan pengalaman kuliner yang tak terlupakan.
    </p>

    <div class="contact-layout">

        <!-- Contact Info -->
        <div class="glass-panel contact-info">
            <h3 style="font-size:1.3rem; margin-bottom:24px;">Hubungi Kami</h3>

            <div class="contact-item">
                <div class="contact-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div>
                    <p class="contact-label">Alamat</p>
                    <p class="contact-value">Jl. Ahmad Yani No. 88, Sambas<br>Kalimantan Barat, 79462</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 7 7l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div>
                    <p class="contact-label">Telepon</p>
                    <p class="contact-value">+62 21 8888 7777</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                    <p class="contact-label">Email</p>
                    <p class="contact-value">dreserve@gmail.com</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <p class="contact-label">Jam Operasional</p>
                    <p class="contact-value">Setiap hari: 11:00 – 23:00 WIB</p>
                </div>
            </div>
        </div>

        <!-- Mini Map -->
        <div class="contact-map glass-panel" style="padding:0; overflow:hidden;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.4!2d106.8227!3d-6.2088!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sJl.%20Jend.%20Sudirman%2C%20Jakarta%20Pusat!5e0!3m2!1sid!2sid!4v1"
                width="100%" height="100%"
                style="border:0; display:block; min-height:380px;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>
</section>

<?php require_once '../views/layout/footer.php'; ?>
