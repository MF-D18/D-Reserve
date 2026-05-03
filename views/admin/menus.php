<?php require_once '../views/admin/layout/header.php'; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Semua Item Menu</h3>
        <button type="button" class="btn btn-primary" style="font-size:0.85rem; padding:8px 16px;" onclick="openModal('addMenuModal')">+ Tambah Menu</button>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Tersedia</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $menus->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                    <td><span class="badge badge-pending"><?= ucfirst($row['category'] ?? 'Makanan') ?></span></td>
                    <td style="color:var(--text-muted); max-width:250px;"><?= htmlspecialchars(substr($row['description'], 0, 60)) ?>…</td>
                    <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                    <td>
                        <span class="badge <?= $row['is_available'] ? 'badge-confirmed' : 'badge-cancelled' ?>">
                            <?= $row['is_available'] ? 'Ya' : 'Tidak' ?>
                        </span>
                    </td>
                    <td class="action-buttons">
                        <button type="button" class="btn-action btn-edit" onclick='openEditMenuModal(<?= htmlspecialchars(json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT)) ?>)'>Edit</button>
                        <a href="index.php?action=admin_menu_delete&id=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Hapus item menu ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Modal Add Menu -->
<div class="modal-overlay" id="addMenuModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('addMenuModal')">&times;</button>
        <div class="modal-header">
            <h3>Tambah Item Menu Baru</h3>
        </div>
        <form action="index.php?action=admin_menu_create" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="name" required placeholder="cth. Wagyu A5 Steak">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="category" required>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat item menu ini..." style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); border-radius:4px; padding:12px 15px; color:var(--text-main); font-family:var(--font-sans); resize:vertical;"></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="price" required min="0" step="500" placeholder="cth. 120000">
                </div>
                <div class="form-group">
                    <label>Pilih Gambar</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event, 'add_preview')">
                    <div style="margin-top: 10px;">
                        <img id="add_preview" src="" style="max-width: 100%; height: auto; border-radius: 8px; display: none; border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
            </div>
            <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:5px;">
                <input type="checkbox" name="is_available" id="is_available_modal" value="1" checked style="width:auto; accent-color: var(--primary-color);">
                <label for="is_available_modal" style="margin:0; cursor:pointer;">Tampilkan di Menu</label>
            </div>
            <div style="display:flex; gap:12px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">Tambah Item Menu</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('addMenuModal')">Batal</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal Edit Menu -->
<div class="modal-overlay" id="editMenuModal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeModal('editMenuModal')">&times;</button>
        <div class="modal-header">
            <h3>Edit Item Menu</h3>
        </div>
        <form action="index.php?action=admin_menu_edit" method="POST" id="editMenuForm" enctype="multipart/form-data">
            <input type="hidden" name="current_image" id="edit_current_image">
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="name" required placeholder="cth. Wagyu A5 Steak">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="category" required>
                    <option value="makanan">Makanan</option>
                    <option value="minuman">Minuman</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="3" placeholder="Deskripsi singkat item menu ini..." style="width:100%; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); border-radius:4px; padding:12px 15px; color:var(--text-main); font-family:var(--font-sans); resize:vertical;"></textarea>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="price" required min="0" step="500" placeholder="cth. 120000">
                </div>
                <div class="form-group">
                    <label>Ganti Gambar (Opsional)</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event, 'edit_preview')">
                    <div style="margin-top: 10px;">
                        <img id="edit_preview" src="" style="max-width: 100%; height: auto; border-radius: 8px; display: none; border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                </div>
            </div>
            <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top:5px;">
                <input type="checkbox" name="is_available" id="is_available_edit_modal" value="1" style="width:auto; accent-color: var(--primary-color);">
                <label for="is_available_edit_modal" style="margin:0; cursor:pointer;">Tampilkan di Menu</label>
            </div>
            <div style="display:flex; gap:12px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">Perbarui Item Menu</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('editMenuModal')">Batal</button>
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
function openEditMenuModal(data) {
    const form = document.getElementById('editMenuForm');
    form.action = `index.php?action=admin_menu_edit&id=${data.id}`;
    form.elements['name'].value = data.name;
    form.elements['category'].value = data.category || 'makanan';
    form.elements['description'].value = data.description || '';
    form.elements['price'].value = data.price;
    document.getElementById('edit_current_image').value = data.image_url || '';
    
    // Show existing image in preview
    const preview = document.getElementById('edit_preview');
    if (data.image_url) {
        preview.src = data.image_url;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }

    form.elements['is_available'].checked = (data.is_available == 1);
    openModal('editMenuModal');
}

function previewImage(event, targetId) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(targetId);
        output.src = reader.result;
        output.style.display = 'block';
    };
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

<?php require_once '../views/admin/layout/footer.php'; ?>
