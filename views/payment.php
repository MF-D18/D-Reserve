<?php require_once '../views/layout/header.php'; ?>

<div style="max-width: 1000px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: start;">
    
    <!-- Left Column: Invoice Details -->
    <div class="glass-panel" style="padding: 30px;">
        <h3 style="margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Ringkasan Pesanan</h3>
        
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="color: var(--text-muted);">Deposit Reservasi</span>
                <strong>Rp 100.000</strong>
            </div>
            
            <?php if(!empty($preOrderItems)): ?>
                <div style="margin: 15px 0; border-top: 1px dashed rgba(255,255,255,0.2); padding-top: 15px;">
                    <span style="color: var(--text-muted); display: block; margin-bottom: 10px;">Item Pre-Order:</span>
                    <?php foreach($preOrderItems as $item): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9em;">
                            <span><?= htmlspecialchars($item['name']) ?> <small>(x<?= $item['quantity'] ?>)</small></span>
                            <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div style="border-top: 2px solid rgba(212, 175, 55, 0.3); padding-top: 15px; display: flex; justify-content: space-between; font-size: 1.2em;">
            <strong>Total Tagihan</strong>
            <strong style="color: var(--primary-color);">Rp <?= number_format($totalAmount, 0, ',', '.') ?></strong>
        </div>
        <p style="font-size: 0.8em; color: var(--danger); margin-top: 10px; text-align: right;">
            *Deposit wajib dibayar untuk mencegah No-Show
        </p>
    </div>

    <!-- Right Column: Payment Methods -->
    <div class="glass-panel" style="padding: 30px;">
        <h3 style="margin-bottom: 20px;">Pilih Metode Pembayaran</h3>
        
        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div id="paymentMethods">
            <!-- Bank Transfer -->
            <label class="item-card" style="display: block; cursor: pointer; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px; border-radius: 8px; padding: 20px;" onclick="selectMethod('bank_transfer')">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="font-size: 1.1rem;">Bank Transfer (Virtual Account)</strong>
                    <input type="radio" name="pay_method" value="bank_transfer" style="transform: scale(1.3); margin:0;">
                </div>
            </label>

            <!-- E-Wallet -->
            <label class="item-card" style="display: block; cursor: pointer; border: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px; border-radius: 8px; padding: 20px;" onclick="selectMethod('e_wallet')">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong style="font-size: 1.1rem;">E-Wallet (QRIS)</strong>
                    <input type="radio" name="pay_method" value="e_wallet" style="transform: scale(1.3); margin:0;">
                </div>
            </label>
        </div>

        <!-- Payment Simulation Area (Hidden Initially) -->
        <div id="paymentSimulation" style="display: none; margin-top: 25px; padding: 20px; background: rgba(0,0,0,0.3); border-radius: 8px; border: 1px dashed var(--primary-color);">
            <div id="simBank" style="display:none; text-align: center;">
                <p style="color: var(--text-muted); margin-bottom: 10px;">BCA Virtual Account</p>
                <h2 style="letter-spacing: 2px; margin-bottom: 10px;">3901 0293 8472 901</h2>
                <p style="font-size: 0.85em; color: var(--text-muted);">Harap transfer jumlah yang tepat sebelum timer habis.</p>
            </div>
            
            <div id="simQR" style="display:none; text-align: center;">
                <p style="color: var(--text-muted); margin-bottom: 10px;">Scan QRIS untuk Membayar</p>
                <!-- Fake QR Code Placeholder -->
                <div style="width: 150px; height: 150px; background: #fff; margin: 0 auto 10px auto; padding: 10px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
                    <div style="background:#000; width:100%; height:100%;"></div><div style="background:#000;"></div><div style="background:#000;"></div>
                    <div style="background:#000;"></div><div style="background:#fff;"></div><div style="background:#000;"></div>
                    <div style="background:#000;"></div><div style="background:#000;"></div><div style="background:#000;"></div>
                </div>
                <p style="font-size: 0.85em; color: var(--text-muted);">Supported by GoPay, OVO, Dana, ShopeePay</p>
            </div>

            <!-- Actual Form to Submit -->
            <form action="index.php?action=payment&res_id=<?= htmlspecialchars($_GET['res_id']) ?>" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="payment_method" id="hiddenMethod">
                <button type="submit" class="btn btn-primary btn-block" style="background: #28a745; border-color: #28a745;">
                    Simulasikan Pembayaran Berhasil
                </button>
            </form>
        </div>
    </div>
</div>

<style>
/* Add media query for responsive stacked layout */
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
function selectMethod(method) {
    document.getElementById('paymentSimulation').style.display = 'block';
    document.getElementById('hiddenMethod').value = method;
    
    // Highlight selected card
    const radios = document.getElementsByName('pay_method');
    radios.forEach(radio => {
        if(radio.value === method) {
            radio.checked = true;
            radio.closest('label').style.borderColor = 'var(--primary-color)';
            radio.closest('label').style.background = 'rgba(212, 175, 55, 0.1)';
        } else {
            radio.closest('label').style.borderColor = 'rgba(255,255,255,0.1)';
            radio.closest('label').style.background = 'transparent';
        }
    });

    if (method === 'bank_transfer') {
        document.getElementById('simBank').style.display = 'block';
        document.getElementById('simQR').style.display = 'none';
    } else {
        document.getElementById('simBank').style.display = 'none';
        document.getElementById('simQR').style.display = 'block';
    }
}
</script>

<?php require_once '../views/layout/footer.php'; ?>
