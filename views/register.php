<?php require_once '../views/layout/header.php'; ?>

<div class="auth-container glass-panel">

    <div style="text-align:center; margin-bottom: 30px;">
        <h2 style="margin-bottom: 6px;">Buat Akun Baru</h2>
        <p style="margin:0; font-size:0.9rem;">Bergabung dan nikmati kemudahan reservasi meja.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul style="margin:0; padding-left:18px;">
                <?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="index.php?action=register" method="POST" id="registerForm" novalidate>

        <div class="form-group">
            <label for="reg-name">Nama Lengkap</label>
            <input type="text" id="reg-name" name="name" required
                   placeholder="Masukkan nama lengkap Anda"
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="reg-email">Email</label>
            <input type="email" id="reg-email" name="email" required
                   placeholder="contoh@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="reg-phone">Nomor HP</label>
            <input type="tel" id="reg-phone" name="phone" required
                   placeholder="Misal: 081234567890"
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                   pattern="[0-9]{10,15}" title="Hanya angka, 10-15 digit">
        </div>

        <div class="form-group">
            <label for="reg-pass">Password</label>
            <div style="position:relative;">
                <input type="password" id="reg-pass" name="password" required
                       placeholder="Minimal 6 karakter"
                       style="padding-right: 45px;">
                <button type="button" onclick="togglePass('reg-pass', this)"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-muted); font-size:0.8rem; padding:0;">
                    Lihat
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="reg-pass-confirm">Konfirmasi Password</label>
            <div style="position:relative;">
                <input type="password" id="reg-pass-confirm" name="password_confirm" required
                       placeholder="Ulangi password Anda"
                       style="padding-right: 45px;">
                <button type="button" onclick="togglePass('reg-pass-confirm', this)"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--text-muted); font-size:0.8rem; padding:0;">
                    Lihat
                </button>
            </div>
            <div id="pass-match-hint" style="font-size:0.8rem; margin-top:5px;"></div>
        </div>

        <!-- Password strength bar -->
        <div style="margin-bottom:20px;">
            <div style="height:4px; background:rgba(255,255,255,0.08); border-radius:4px; overflow:hidden;">
                <div id="strength-bar" style="height:100%; width:0%; transition:all 0.4s ease; border-radius:4px;"></div>
            </div>
            <div id="strength-label" style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;"></div>
        </div>

        <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
            Daftar Sekarang
        </button>
    </form>

    <div style="text-align:center; margin-top:20px; font-size:0.9rem; color:var(--text-muted);">
        Sudah punya akun?
        <a href="index.php?action=login" style="color:var(--primary-color); text-decoration:none; font-weight:600;">Masuk di sini</a>
    </div>
</div>

<script>
// Toggle password visibility
function togglePass(id, btn) {
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

// Password strength indicator
const passInput    = document.getElementById('reg-pass');
const confirmInput = document.getElementById('reg-pass-confirm');
const strengthBar  = document.getElementById('strength-bar');
const strengthLbl  = document.getElementById('strength-label');
const matchHint    = document.getElementById('pass-match-hint');

passInput.addEventListener('input', function () {
    const val = this.value;
    let strength = 0;
    if (val.length >= 6)  strength++;
    if (val.length >= 10) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const levels = [
        { w: '0%',   color: 'transparent',         label: '' },
        { w: '25%',  color: '#e74c3c',              label: 'Lemah' },
        { w: '50%',  color: '#e67e22',              label: 'Cukup' },
        { w: '75%',  color: '#f1c40f',              label: 'Kuat' },
        { w: '100%', color: '#2ecc71',              label: 'Sangat Kuat' },
    ];
    const lvl = Math.min(strength, 4);
    strengthBar.style.width     = levels[lvl].w;
    strengthBar.style.background = levels[lvl].color;
    strengthLbl.textContent     = levels[lvl].label;
    strengthLbl.style.color     = levels[lvl].color;
});

// Password match check
confirmInput.addEventListener('input', function () {
    if (this.value === '') {
        matchHint.textContent = '';
        return;
    }
    if (this.value === passInput.value) {
        matchHint.textContent = '✓ Password cocok';
        matchHint.style.color = '#2ecc71';
    } else {
        matchHint.textContent = '✗ Password tidak cocok';
        matchHint.style.color = '#e74c3c';
    }
});
</script>

<?php require_once '../views/layout/footer.php'; ?>
