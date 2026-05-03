<?php require_once '../views/admin/layout/header.php';
$isEdit   = isset($tableData);
$formData = $isEdit ? $tableData : ['table_number' => '', 'capacity' => 4, 'status' => 'available'];
$action   = $isEdit ? "admin_table_edit&id={$tableData['id']}" : 'admin_table_create';
?>

<div class="admin-card" style="max-width: 500px;">
    <h3 style="margin-bottom:25px;"><?= $isEdit ? 'Edit Meja' : 'Tambah Meja Baru' ?></h3>
    <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form action="/dreserve/public/index.php?action=<?= $action ?>" method="POST">
        <div class="form-group">
            <label>Nomor / Nama Meja</label>
            <input type="text" name="table_number" required value="<?= htmlspecialchars($formData['table_number']) ?>" placeholder="cth. T03 atau VIP-1">
        </div>
        <div class="form-group">
            <label>Kapasitas (Orang)</label>
            <input type="number" name="capacity" required min="1" max="50" value="<?= $formData['capacity'] ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="available" <?= $formData['status'] === 'available' ? 'selected' : '' ?>>Tersedia</option>
                <option value="occupied"  <?= $formData['status'] === 'occupied'  ? 'selected' : '' ?>>Terisi</option>
                <option value="reserved"  <?= $formData['status'] === 'reserved'  ? 'selected' : '' ?>>Dipesan</option>
            </select>
        </div>
        <div style="display:flex; gap:12px; margin-top:10px;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Perbarui Meja' : 'Tambah Meja' ?></button>
            <a href="/dreserve/public/index.php?action=admin_tables" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php require_once '../views/admin/layout/footer.php'; ?>
