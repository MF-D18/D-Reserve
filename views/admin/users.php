<?php require_once '../views/admin/layout/header.php'; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Semua Pengguna</h3>
        <button type="button" class="btn btn-primary" style="font-size:0.85rem; padding:8px 16px;" onclick="openModal('addUserModal')">+ Tambah Pengguna</button>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Peran</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?? '-') ?></td>
                    <td><span class="badge <?= $row['role'] === 'admin' ? 'badge-confirmed' : 'badge-pending' ?>"><?= ucfirst($row['role']) ?></span></td>
                    <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                    <td class="action-buttons">
                        <button type="button" class="btn-action btn-edit" onclick='openEditUserModal(<?= htmlspecialchars(json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)'>Edit</button>
                        <a href="index.php?action=admin_user_delete&id=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add User -->
<div class="modal-overlay" id="addUserModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
        <div class="modal-header">
            <h3>Tambah Pengguna Baru</h3>
        </div>
        <form action="index.php?action=admin_user_create" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama lengkap">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="user@example.com">
            </div>
            <div class="form-group">
                <label>Nomor Telepon</label>
                <input type="tel" name="phone" placeholder="Contoh: 081234567890">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Atur password awal">
            </div>
            <div class="form-group">
                <label>Peran</label>
                <select name="role" required>
                    <option value="customer">Pelanggan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display:flex; gap:12px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('addUserModal')">Batal</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Edit User -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('editUserModal')">&times;</button>
        <div class="modal-header">
            <h3>Edit Pengguna</h3>
        </div>
        <form action="index.php?action=admin_user_edit" method="POST" id="editUserForm">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Nama lengkap">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="user@example.com">
            </div>
            <div class="form-group">
                <label>Nomor Telepon</label>
                <input type="tel" name="phone" placeholder="Contoh: 081234567890">
            </div>
            <div class="form-group">
                <label>Peran</label>
                <select name="role" required>
                    <option value="customer">Pelanggan</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display:flex; gap:12px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">Perbarui Pengguna</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('editUserModal')">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('is-active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('is-active');
}
function openEditUserModal(data) {
    const form = document.getElementById('editUserForm');
    form.action = `index.php?action=admin_user_edit&id=${data.id}`;
    form.elements['name'].value = data.name;
    form.elements['email'].value = data.email;
    form.elements['phone'].value = data.phone || '';
    form.elements['role'].value = data.role;
    openModal('editUserModal');
}
</script>

<?php require_once '../views/admin/layout/footer.php'; ?>
