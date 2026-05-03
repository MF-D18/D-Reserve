<?php 
require_once '../views/layout/header.php'; 
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT * FROM menus WHERE is_available = 1 ORDER BY name ASC");
$stmt->execute();
$allMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="glass-panel" style="text-align: center; padding: 60px 20px; margin-bottom: 40px;">
    <p class="section-sub" style="margin-bottom:12px;">✦ Koleksi Lengkap ✦</p>
    <h1 style="margin-bottom: 10px;">Daftar Menu Kami</h1>
    <p style="max-width: 600px; margin: 0 auto;">Jelajahi seluruh koleksi hidangan dan minuman premium kami yang disiapkan khusus oleh chef profesional.</p>
</section>

<section>
    <div class="menu-filters" style="text-align: center; margin-bottom: 30px;">
        <button class="btn btn-primary filter-btn is-active" data-filter="all" style="margin: 0 5px; min-width: 100px;">Semua</button>
        <button class="btn btn-outline filter-btn" data-filter="makanan" style="margin: 0 5px; min-width: 100px;">Makanan</button>
        <button class="btn btn-outline filter-btn" data-filter="minuman" style="margin: 0 5px; min-width: 100px;">Minuman</button>
    </div>

    <div class="grid">
        <?php foreach($allMenus as $menu): ?>
        <?php
            $imgSrc = !empty($menu['image_url']) ? '/D-Reserve/public/' . ltrim($menu['image_url'], '/') : null;
            if (!$imgSrc || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imgSrc)) {
                $nameLower = strtolower($menu['name']);
                if (str_contains($nameLower, 'wagyu') || str_contains($nameLower, 'steak')) $imgSrc = '/D-Reserve/public/img/wagyua5.webp';
                elseif (str_contains($nameLower, 'pasta') || str_contains($nameLower, 'truffle')) $imgSrc = '/D-Reserve/public/img/trufflepasta.webp';
                elseif (str_contains($nameLower, 'mojito') || str_contains($nameLower, 'drink')) $imgSrc = '/D-Reserve/public/img/Mojito.jpg';
                else $imgSrc = null;
            }
        ?>
        <div class="item-card" data-category="<?= htmlspecialchars($menu['category'] ?? 'makanan') ?>">
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
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const menuItems = document.querySelectorAll('.item-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Reset all buttons
            filterBtns.forEach(b => {
                b.classList.remove('btn-primary', 'is-active');
                b.classList.add('btn-outline');
            });
            
            // Set active button
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary', 'is-active');

            const filterValue = btn.getAttribute('data-filter');

            // Filter items
            menuItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php require_once '../views/layout/footer.php'; ?>
