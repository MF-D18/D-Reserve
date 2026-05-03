<?php require_once '../views/admin/layout/header.php';
$isEdit   = isset($userData);
$formData = $isEdit ? $userData : ['name' => '', 'email' => '', 'role' => 'customer'];
$action   = $isEdit ? "admin_user_edit&id={$userData['id']}" : 'admin_user_create';
?>

<div class="admin-card" style="max-width: 600px;">
    <h3 style="margin-bottom:25px;"><?= $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru' ?></h3>
    <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

    <form action="/dreserve/public/index.php?action=<?= $action ?>" method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" required value="<?= htmlspecialchars($formData['name']) ?>" placeholder="Nama lengkap">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($formData['email']) ?>" placeholder="user@example.com">
        </div>
        <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($formData['phone'] ?? '') ?>" placeholder="Contoh: 081234567890">
        </div>
        <?php if(!$isEdit): ?>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Atur password awal">
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label>Peran</label>
            <select name="role" required>
                <option value="customer" <?= $formData['role'] === 'customer' ? 'selected' : '' ?>>Pelanggan</option>
                <option value="admin"    <?= $formData['role'] === 'admin'    ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div style="display:flex; gap:12px; margin-top:10px;">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Perbarui Pengguna' : 'Tambah Pengguna' ?></button>
            <a href="/dreserve/public/index.php?action=admin_users" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php require_once '../views/admin/layout/footer.php'; ?>
