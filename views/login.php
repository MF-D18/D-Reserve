<?php require_once '../views/layout/header.php'; ?>

<div class="auth-container glass-panel">
    <h2>Masuk ke D-Reserve</h2>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form action="index.php?action=login" method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="admin@dreserve.com">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="password">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>
    <div class="auth-hint" style="margin-top: 15px; text-align: center; font-size: 0.9em; color: var(--text-muted);">
        Akun Demo:<br>
        Admin: admin@dreserve.com / password<br>
        Pelanggan: budi@example.com / password
    </div>
</div>

<?php require_once '../views/layout/footer.php'; ?>

