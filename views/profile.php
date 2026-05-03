<?php require_once '../views/layout/header.php'; ?>

<div style="max-width: 680px; margin: 0 auto;">

    <!-- Profile Header Card -->
    <div class="glass-panel" style="text-align:center; margin-bottom: 24px; padding: 35px 30px;">
        <div style="width:90px; height:90px; border-radius:50%; background:linear-gradient(135deg, var(--primary-color) 0%, #8B6914 100%); display:flex; align-items:center; justify-content:center; margin: 0 auto 16px; font-size:2.2rem; font-family:var(--font-serif); color:var(--bg-color); font-weight:700; box-shadow: 0 0 0 4px rgba(212,175,55,0.2);">
            <?= strtoupper(substr($userData['name'], 0, 1)) ?>
        </div>
        <h2 style="margin-bottom:4px; font-size:1.6rem;"><?= htmlspecialchars($userData['name']) ?></h2>
        <p style="margin:0; font-size:0.9rem; color:var(--text-muted);"><?= htmlspecialchars($userData['email']) ?></p>
        <span style="display:inline-block; margin-top:10px; background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.3); color:var(--primary-color); font-size:0.75rem; font-weight:600; padding:3px 12px; border-radius:20px; text-transform:uppercase; letter-spacing:0.05em;">
            <?= ucfirst($userData['role']) ?>
        </span>
    </div>

    <!-- Edit Form Card -->
    <div class="glass-panel">
        <h3 style="margin-bottom:6px; font-size:1.2rem;">Edit Profil</h3>
        <p style="font-size:0.85rem; margin-bottom:24px;">Perbarui informasi pribadi Anda di bawah ini.</p>

        <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
                <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if($success): ?>
        <div class="alert alert-success" style="margin-bottom:20px;">✓ Profil berhasil diperbarui!</div>
        <?php endif; ?>

        <form action="index.php?action=profile" method="POST" id="profileForm">

            <!-- Section: Personal Info -->
            <p style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--primary-color); margin-bottom:14px; font-weight:600;">Informasi Pribadi</p>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="form-group" style="margin:0;">
                    <label for="p-name">Nama Lengkap</label>
                    <input type="text" id="p-name" name="name" required
                           value="<?= htmlspecialchars($userData['name']) ?>"
                           placeholder="Nama lengkap Anda">
                </div>
                <div class="form-group" style="margin:0;">
                    <label for="p-phone">Nomor HP</label>
                    <input type="tel" id="p-phone" name="phone"
                           value="<?= htmlspecialchars($userData['phone'] ?? '') ?>"
                           placeholder="Contoh: 081234567890">
                </div>
            </div>

            <div class="form-group">
                <label for="p-email">Email</label>
                <input type="email" id="p-email" name="email" required
                       value="<?= htmlspecialchars($userData['email']) ?>"
                       placeholder="email@example.com">
            </div>

            <!-- Section: Change Password -->
            <p style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--primary-color); margin: 24px 0 14px; font-weight:600;">Ubah Password <span style="text-transform:none; font-weight:400; color:var(--text-muted); font-size:0.75rem;">(kosongkan jika tidak ingin diubah)</span></p>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div class="form-group" style="margin:0; position:relative;">
                    <label for="p-new-pass">Password Baru</label>
                    <div style="position:relative;">
                        <input type="password" id="p-new-pass" name="new_password"
                               placeholder="Min. 6 karakter"
                               style="padding-right:50px;"
                               autocomplete="new-password">
                        <button type="button" onclick="toggleVis('p-new-pass', this)"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:0.78rem;">Lihat</button>
                    </div>
                </div>
                <div class="form-group" style="margin:0;">
                    <label for="p-confirm-pass">Konfirmasi Password</label>
                    <div style="position:relative;">
                        <input type="password" id="p-confirm-pass" name="confirm_password"
                               placeholder="Ulangi password baru"
                               style="padding-right:50px;"
                               autocomplete="new-password">
                        <button type="button" onclick="toggleVis('p-confirm-pass', this)"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:0.78rem;">Lihat</button>
                    </div>
                    <div id="passMatchHint" style="font-size:0.78rem; margin-top:5px;"></div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display:flex; gap:12px; margin-top:28px;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="index.php?action=home" class="btn btn-outline">Batal</a>
            </div>
        </form>
    </div>

</div>

<script>
function toggleVis(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Sembunyikan';
        btn.style.color = 'var(--primary-color)';
    } else {
        input.type = 'password';
        btn.textContent = 'Lihat';
        btn.style.color = 'var(--text-muted)';
    }
}

// Password match indicator
const newPass  = document.getElementById('p-new-pass');
const confPass = document.getElementById('p-confirm-pass');
const hint     = document.getElementById('passMatchHint');

function checkMatch() {
    if (!confPass.value) { hint.textContent = ''; return; }
    if (newPass.value === confPass.value) {
        hint.textContent = '✓ Password cocok';
        hint.style.color = '#2ecc71';
    } else {
        hint.textContent = '✗ Password tidak cocok';
        hint.style.color = '#e74c3c';
    }
}
newPass.addEventListener('input', checkMatch);
confPass.addEventListener('input', checkMatch);
</script>

<?php require_once '../views/layout/footer.php'; ?>
