<?php require_once '../views/admin/layout/header.php'; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Semua Meja</h3>
        <button type="button" class="btn btn-primary" style="font-size:0.85rem; padding:8px 16px;" onclick="openModal('addTableModal')">+ Tambah Meja</button>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Meja</th>
                    <th>Kapasitas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $tables->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><strong><?= htmlspecialchars($row['table_number']) ?></strong></td>
                    <td><?= $row['capacity'] ?> Orang</td>
                    <td>
                        <?php $st = $row['status'];
                        $cls = ['available' => 'badge-confirmed', 'occupied' => 'badge-cancelled', 'reserved' => 'badge-pending'];
                        ?>
                        <span class="badge <?= $cls[$st] ?? 'badge-pending' ?>"><?= ucfirst($st) ?></span>
                    </td>
                    <td class="action-buttons">
                        <button type="button" class="btn-action btn-edit" onclick='openEditTableModal(<?= htmlspecialchars(json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)'>Edit</button>
                        <a href="index.php?action=admin_table_delete&id=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus meja ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Modal Add Table -->
<div class="modal-overlay" id="addTableModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('addTableModal')">&times;</button>
        <div class="modal-header">
            <h3>Tambah Meja Baru</h3>
        </div>
        <form action="index.php?action=admin_table_create" method="POST">
            <div class="form-group">
                <label>Nomor / Nama Meja</label>
                <input type="text" name="table_number" required placeholder="cth. T03 atau VIP-1">
            </div>
            <div class="form-group">
                <label>Kapasitas (Orang)</label>
                <input type="number" name="capacity" required min="1" max="50" value="4">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="available" selected>Tersedia</option>
                    <option value="occupied">Terisi</option>
                    <option value="reserved">Dipesan</option>
                </select>
            </div>
            <div style="display:flex; gap:12px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">Tambah Meja</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('addTableModal')">Batal</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Edit Table -->
<div class="modal-overlay" id="editTableModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('editTableModal')">&times;</button>
        <div class="modal-header">
            <h3>Edit Meja</h3>
        </div>
        <form action="index.php?action=admin_table_edit" method="POST" id="editTableForm">
            <div class="form-group">
                <label>Nomor / Nama Meja</label>
                <input type="text" name="table_number" required placeholder="cth. T03 atau VIP-1">
            </div>
            <div class="form-group">
                <label>Kapasitas (Orang)</label>
                <input type="number" name="capacity" required min="1" max="50">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="available">Tersedia</option>
                    <option value="occupied">Terisi</option>
                    <option value="reserved">Dipesan</option>
                </select>
            </div>
            <div style="display:flex; gap:12px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">Perbarui Meja</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('editTableModal')">Batal</button>
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
function openEditTableModal(data) {
    const form = document.getElementById('editTableForm');
    form.action = `index.php?action=admin_table_edit&id=${data.id}`;
    form.elements['table_number'].value = data.table_number;
    form.elements['capacity'].value = data.capacity;
    form.elements['status'].value = data.status;
    openModal('editTableModal');
}
</script>

<?php require_once '../views/admin/layout/footer.php'; ?>
