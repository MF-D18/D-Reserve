<?php require_once '../views/admin/layout/header.php'; ?>

<!-- Stat Cards -->
<div class="admin-stat-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-gold">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Reservasi</span>
            <span class="stat-value"><?= $stats['total'] ?? 0 ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-green">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Dikonfirmasi</span>
            <span class="stat-value"><?= $stats['confirmed'] ?? 0 ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Menunggu</span>
            <span class="stat-value"><?= $stats['pending'] ?? 0 ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Pengguna</span>
            <span class="stat-value"><?= $totalUsers ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-purple">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Total Meja</span>
            <span class="stat-value"><?= $totalTables ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-red">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
        </div>
        <div class="stat-info">
            <span class="stat-label">Item Menu</span>
            <span class="stat-value"><?= $totalMenus ?></span>
        </div>
    </div>
</div>

<!-- Recent Reservations Table -->
<div class="admin-card" style="margin-top: 30px;">
    <div class="admin-card-header">
        <h3>Reservasi Terbaru</h3>
        <a href="index.php?action=admin_reservations" class="btn btn-outline" style="font-size: 0.85rem; padding: 8px 16px;">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0; while($row = $recentRes->fetch(PDO::FETCH_ASSOC)): if($count++ >= 8) break; ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($row['user_name']) ?></strong><br>
                        <small style="color:var(--text-muted)"><?= htmlspecialchars($row['email']) ?></small>
                    </td>
                    <td><?= $row['table_number'] ?></td>
                    <td><?= date('d M Y', strtotime($row['reservation_date'])) ?></td>
                    <td><?= date('H:i', strtotime($row['start_time'])) ?> - <?= date('H:i', strtotime($row['end_time'])) ?></td>
                    <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Top Selling Menus Chart -->
<div class="admin-card" style="margin-top: 30px;">
    <div class="admin-card-header">
        <h3>Menu Paling Laris</h3>
    </div>
    <div style="padding: 20px;">
        <div style="height: 300px; position: relative;">
            <canvas id="topMenusChart"></canvas>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-quick-actions" style="margin-top: 30px;">
    <h3 style="margin-bottom: 15px;">Aksi Cepat</h3>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <button type="button" class="btn btn-primary" onclick="openModal('addUserModal')">+ Tambah Pengguna</button>
        <button type="button" class="btn btn-primary" onclick="openModal('addTableModal')">+ Tambah Meja</button>
        <button type="button" class="btn btn-primary" onclick="openModal('addMenuModal')">+ Tambah Menu</button>
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
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event, 'dash_add_preview')">
                    <div style="margin-top: 10px;">
                        <img id="dash_add_preview" src="" style="max-width: 100%; height: auto; border-radius: 8px; display: none; border: 1px solid rgba(255,255,255,0.1);">
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

<script>
function openModal(id) {
    document.getElementById(id).classList.add('is-active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('is-active');
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

// Chart initialization
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('topMenusChart').getContext('2d');
    const labels = <?= json_encode(array_column($topMenus, 'name')) ?>;
    const data = <?= json_encode(array_column($topMenus, 'total_quantity')) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Dipesan',
                data: data,
                backgroundColor: 'rgba(212, 175, 55, 0.6)',
                borderColor: 'rgba(212, 175, 55, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#888' }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: '#fff' }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php require_once '../views/admin/layout/footer.php'; ?>
