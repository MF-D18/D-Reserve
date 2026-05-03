<?php require_once '../views/admin/layout/header.php';
$isEdit   = isset($menuData);
$formData = $isEdit ? $menuData : ['name' => '', 'description' => '', 'price' => '', 'image_url' => '', 'is_available' => 1];
$action   = $isEdit ? "admin_menu_edit&id={$menuData['id']}" : 'admin_menu_create';
?>

<div class="admin-card" style="max-width: 600px;">
    <h3 style="margin-bottom:25px;"><?= $isEdit ? 'Edit Item Menu' : 'Tambah Item Menu Baru' ?></h3>
    <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form action="/dreserve/public/index.php?action=<?= $action ?>" method="POST">
        <div class="form-group">
            <label>Nama Menu</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($formData['name']) ?>" placeholder="cth. Wagyu A5 Steak">
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" rows="3" placeholder="Deskripsi singkat item menu ini..." style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); border-radius:4px; padding:12px 15px; color:var(--text-main); font-family:var(--font-sans); resize:vertical;"><?= htmlspecialchars($formData['description']) ?></textarea>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="price" required min="0" step="500" value="<?= $formData['price'] ?>" placeholder="cth. 120000">
            </div>
            <div class="form-group">
                <label>URL Gambar</label>
                <input type="text" name="image_url" value="<?= htmlspecialchars($formData['image_url']) ?>" placeholder="img/namafile.jpg">
            </div>
        </div>
        <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:5px;">
            <input type="checkbox" name="is_available" id="is_available" value="1" <?= $formData['is_available'] ? 'checked' : '' ?> style="width:auto; accent-color: var(--primary-color);">
            <label for="is_available" style="margin:0; cursor:pointer;">Tampilkan di Menu</label>
        </div>
        <div style="display:flex; gap:12px; margin-top:15px;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Perbarui Menu' : 'Tambah Item Menu' ?></button>
            <a href="/dreserve/public/index.php?action=admin_menus" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php require_once '../views/admin/layout/footer.php'; ?>
