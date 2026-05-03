<?php require_once '../views/admin/layout/header.php'; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>All Reservations</h3>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Perbarui Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $reservations->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['user_name']) ?></strong><br>
                        <small style="color:var(--text-muted)"><?= htmlspecialchars($row['email']) ?></small>
                    </td>
                    <td><?= $row['table_number'] ?></td>
                    <td><?= date('d M Y', strtotime($row['reservation_date'])) ?></td>
                    <td><?= date('H:i', strtotime($row['start_time'])) ?> – <?= date('H:i', strtotime($row['end_time'])) ?></td>
                    <td><span class="badge badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                    <td>
                        <form action="index.php?action=admin_reservation_status" method="POST" style="display:flex; gap:8px; align-items:center;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <select name="status" style="padding:5px 8px; font-size:0.85rem; background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.1); color:var(--text-main); border-radius:4px;">
                                <option value="pending"   <?= $row['status'] === 'pending'   ? 'selected' : '' ?>>Menunggu</option>
                                <option value="confirmed" <?= $row['status'] === 'confirmed' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                <option value="completed" <?= $row['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                                <option value="cancelled" <?= $row['status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                            <button type="submit" class="btn-action btn-edit">Simpan</button>
                        </form>
                    </td>
                    <td style="display: flex; gap: 6px; flex-wrap: wrap;">
                        <button onclick="viewPreOrder(<?= $row['id'] ?>, '<?= htmlspecialchars($row['user_name']) ?>')" class="btn-action btn-edit" style="background:rgba(212,175,55,0.15); color:var(--primary-color); border-color:rgba(212,175,55,0.25);">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:text-bottom; margin-right:4px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Pesanan
                        </button>
                        
                        <?php if ($row['status'] === 'cancelled' && isset($row['payment_status']) && in_array($row['payment_status'], ['success', 'refunded'])): ?>
                            <button onclick="openRefundModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['user_name']) ?>', '<?= $row['payment_status'] ?>')" class="btn-action btn-edit" style="background:rgba(231,76,60,0.15); color:var(--danger); border-color:rgba(231,76,60,0.25);">
                                <?= $row['payment_status'] === 'success' ? 'Proses Refund' : 'Detail Refund' ?>
                            </button>
                        <?php endif; ?>

                        <?php if ($row['status'] === 'confirmed'): ?>
                            <button onclick="openPelunasanModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['user_name']) ?>')" class="btn-action btn-edit" style="background:rgba(46,204,113,0.15); color:var(--success); border-color:rgba(46,204,113,0.25);">
                                Pelunasan
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Pre-order -->
<div id="preorderModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; backdrop-filter:blur(5px); justify-content:center; align-items:center; opacity:0; transition:opacity 0.3s ease;">
    <div style="background:#15181e; border:1px solid rgba(255,255,255,0.1); border-radius:12px; width:100%; max-width:500px; padding:25px; box-shadow:0 10px 40px rgba(0,0,0,0.8); transform:translateY(-20px); transition:transform 0.3s ease;" id="modalContent">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:15px;">
            <h3 style="margin:0; font-size:1.2rem; color:var(--primary-color);">Detail Pre-Order</h3>
            <button onclick="closeModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.5rem; line-height:1;">&times;</button>
        </div>
        
        <p style="margin-bottom:15px; font-size:0.9rem;">Pelanggan: <strong id="modalCustomer" style="color:var(--text-main);"></strong></p>
        
        <div id="modalBody">
            <p style="text-align:center; color:var(--text-muted);">Loading...</p>
        </div>
        
        <div style="margin-top:25px; text-align:right;">
            <button onclick="closeModal()" class="btn btn-outline" style="padding:8px 16px; font-size:0.9rem;">Tutup</button>
        </div>
    </div>
</div>

<script>
async function viewPreOrder(resId, customerName) {
    const modal = document.getElementById('preorderModal');
    const modalContent = document.getElementById('modalContent');
    const modalBody = document.getElementById('modalBody');
    document.getElementById('modalCustomer').textContent = customerName;
    
    // Reset and show modal
    modalBody.innerHTML = '<p style="text-align:center; color:var(--text-muted);">Memuat detail pesanan...</p>';
    modal.style.display = 'flex';
    // Trigger reflow
    void modal.offsetWidth;
    modal.style.opacity = '1';
    modalContent.style.transform = 'translateY(0)';
    
    try {
        const response = await fetch(`index.php?action=api_get_preorder&id=${resId}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            if (data.items.length === 0) {
                modalBody.innerHTML = '<div class="alert alert-danger" style="margin:0;">Tidak ada menu pre-order untuk reservasi ini.</div>';
            } else {
                let html = `
                    <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.1);">
                            <th style="padding:8px 4px; text-align:left; color:var(--text-muted);">Menu</th>
                            <th style="padding:8px 4px; text-align:center; color:var(--text-muted);">Qty</th>
                            <th style="padding:8px 4px; text-align:right; color:var(--text-muted);">Subtotal</th>
                        </tr>
                `;
                
                data.items.forEach(item => {
                    const priceFmt = new Intl.NumberFormat('id-ID').format(item.price);
                    const subFmt = new Intl.NumberFormat('id-ID').format(item.subtotal);
                    html += `
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                            <td style="padding:10px 4px;">
                                <span style="color:var(--text-main);">${item.name}</span><br>
                                <small style="color:var(--text-muted);">Rp ${priceFmt}</small>
                            </td>
                            <td style="padding:10px 4px; text-align:center;">${item.quantity}</td>
                            <td style="padding:10px 4px; text-align:right; font-weight:600;">Rp ${subFmt}</td>
                        </tr>
                    `;
                });
                
                const totalFmt = new Intl.NumberFormat('id-ID').format(data.total);
                html += `
                        <tr>
                            <td colspan="2" style="padding:15px 4px; text-align:right; color:var(--text-muted);"><strong>Total:</strong></td>
                            <td style="padding:15px 4px; text-align:right; color:var(--primary-color); font-size:1.1rem;"><strong>Rp ${totalFmt}</strong></td>
                        </tr>
                    </table>
                `;
                modalBody.innerHTML = html;
            }
        } else {
            modalBody.innerHTML = `<div class="alert alert-danger" style="margin:0;">${data.message}</div>`;
        }
    } catch (err) {
        modalBody.innerHTML = '<div class="alert alert-danger" style="margin:0;">Terjadi kesalahan sistem. Coba lagi.</div>';
        console.error(err);
    }
}

function closeModal() {
    const modal = document.getElementById('preorderModal');
    const modalContent = document.getElementById('modalContent');
    
    modal.style.opacity = '0';
    modalContent.style.transform = 'translateY(-20px)';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('preorderModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>

<!-- Modal Pelunasan -->
<div id="pelunasanModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; backdrop-filter:blur(5px); justify-content:center; align-items:center; opacity:0; transition:opacity 0.3s ease;">
    <div style="background:#15181e; border:1px solid rgba(255,255,255,0.1); border-radius:12px; width:100%; max-width:500px; padding:25px; box-shadow:0 10px 40px rgba(0,0,0,0.8); transform:translateY(-20px); transition:transform 0.3s ease;" id="pelunasanContent">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:15px;">
            <h3 style="margin:0; font-size:1.2rem; color:var(--success);">Pelunasan Reservasi</h3>
            <button onclick="closePelunasanModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.5rem; line-height:1;">&times;</button>
        </div>
        
        <p style="margin-bottom:15px; font-size:0.9rem;">Pelanggan: <strong id="pelunasanCustomer" style="color:var(--text-main);"></strong></p>
        
        <form action="index.php?action=admin_settle_reservation" method="POST" id="pelunasanForm">
            <input type="hidden" name="id" id="pelunasanResId">
            <div id="pelunasanBody">
                <p style="text-align:center; color:var(--text-muted);">Memuat detail...</p>
            </div>
            
            <div style="margin-top:20px; background:rgba(0,0,0,0.2); padding:15px; border-radius:8px; border:1px solid rgba(255,255,255,0.05);">
                <div class="form-group" style="margin-bottom: 10px;">
                    <label>Total Tagihan Akhir (Rp)</label>
                    <input type="number" id="total_tagihan" name="total_tagihan" class="form-control" required oninput="calculateSisa()">
                    <small style="color:var(--text-muted);">Masukkan total tagihan akhir termasuk pesanan tambahan di tempat.</small>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span style="color:var(--text-muted);">Sudah Dibayar:</span>
                    <strong id="sudah_dibayar_text" style="color:var(--primary-color);">Rp 0</strong>
                </div>
                <div style="display:flex; justify-content:space-between; border-top:1px dashed rgba(255,255,255,0.2); padding-top:10px; margin-top:5px;">
                    <span>Sisa / Kembalian:</span>
                    <strong id="sisa_pembayaran" style="font-size:1.1rem;">Rp 0</strong>
                </div>
            </div>
            
            <div style="margin-top:25px; display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closePelunasanModal()" class="btn btn-outline" style="padding:8px 16px; font-size:0.9rem;">Batal</button>
                <button type="submit" class="btn btn-primary" style="background:var(--success); border-color:var(--success); padding:8px 16px; font-size:0.9rem;">Selesaikan Reservasi</button>
            </div>
        </form>
    </div>
</div>

<script>
// ... existing script for pre-order modal
let paidAmount = 0;

async function openPelunasanModal(resId, customerName) {
    const modal = document.getElementById('pelunasanModal');
    const modalContent = document.getElementById('pelunasanContent');
    const modalBody = document.getElementById('pelunasanBody');
    
    document.getElementById('pelunasanCustomer').textContent = customerName;
    document.getElementById('pelunasanResId').value = resId;
    
    // Reset values
    document.getElementById('total_tagihan').value = '';
    document.getElementById('sudah_dibayar_text').textContent = 'Rp 0';
    document.getElementById('sisa_pembayaran').textContent = 'Rp 0';
    document.getElementById('sisa_pembayaran').style.color = 'var(--text-main)';
    paidAmount = 0;
    
    modalBody.innerHTML = '<p style="text-align:center; color:var(--text-muted);">Memuat detail...</p>';
    modal.style.display = 'flex';
    void modal.offsetWidth;
    modal.style.opacity = '1';
    modalContent.style.transform = 'translateY(0)';
    
    try {
        const response = await fetch(`index.php?action=api_get_reservation_details&id=${resId}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            paidAmount = data.total_paid;
            
            const paidFmt = new Intl.NumberFormat('id-ID').format(paidAmount);
            document.getElementById('sudah_dibayar_text').textContent = `Rp ${paidFmt}`;
            document.getElementById('total_tagihan').value = paidAmount; // Default to what they paid
            
            let html = `
                <table style="width:100%; border-collapse:collapse; font-size:0.9rem; margin-bottom:15px;">
                    <tr>
                        <td style="padding:4px 0; color:var(--text-muted);">Deposit:</td>
                        <td style="padding:4px 0; text-align:right;">Rp ${new Intl.NumberFormat('id-ID').format(data.deposit)}</td>
                    </tr>
            `;
            
            if (data.items && data.items.length > 0) {
                html += `<tr><td colspan="2" style="padding-top:8px; font-weight:600; font-size:0.85rem;">Pre-Order:</td></tr>`;
                data.items.forEach(item => {
                    html += `
                        <tr>
                            <td style="padding:2px 0 2px 10px; color:var(--text-muted);">- ${item.name} (x${item.quantity})</td>
                            <td style="padding:2px 0; text-align:right;">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                        </tr>
                    `;
                });
            }
            html += `</table>`;
            modalBody.innerHTML = html;
            calculateSisa();
        } else {
            modalBody.innerHTML = `<div class="alert alert-danger" style="margin:0;">${data.message}</div>`;
        }
    } catch (err) {
        modalBody.innerHTML = '<div class="alert alert-danger" style="margin:0;">Terjadi kesalahan sistem. Coba lagi.</div>';
        console.error(err);
    }
}

function calculateSisa() {
    const total = parseFloat(document.getElementById('total_tagihan').value) || 0;
    const sisa = total - paidAmount;
    const sisaEl = document.getElementById('sisa_pembayaran');
    
    sisaEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(sisa));
    
    if (sisa > 0) {
        sisaEl.textContent = 'Kurang: ' + sisaEl.textContent;
        sisaEl.style.color = 'var(--danger)';
    } else if (sisa < 0) {
        sisaEl.textContent = 'Kembali: ' + sisaEl.textContent;
        sisaEl.style.color = 'var(--success)';
    } else {
        sisaEl.textContent = 'Pas: Rp 0';
        sisaEl.style.color = 'var(--text-main)';
    }
}

function closePelunasanModal() {
    const modal = document.getElementById('pelunasanModal');
    const modalContent = document.getElementById('pelunasanContent');
    
    modal.style.opacity = '0';
    modalContent.style.transform = 'translateY(-20px)';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Update window.onclick to handle both modals
const oldWindowOnClick = window.onclick;
window.onclick = function(event) {
    if (oldWindowOnClick) oldWindowOnClick(event);
    
    const pelunasanModal = document.getElementById('pelunasanModal');
    if (event.target == pelunasanModal) {
        closePelunasanModal();
    }
}
</script>

<!-- Modal Refund -->
<div id="refundModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; backdrop-filter:blur(5px); justify-content:center; align-items:center; opacity:0; transition:opacity 0.3s ease;">
    <div style="background:#15181e; border:1px solid rgba(255,255,255,0.1); border-radius:12px; width:100%; max-width:500px; padding:25px; box-shadow:0 10px 40px rgba(0,0,0,0.8); transform:translateY(-20px); transition:transform 0.3s ease;" id="refundContent">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:15px;">
            <h3 style="margin:0; font-size:1.2rem; color:var(--danger);">Detail Refund</h3>
            <button onclick="closeRefundModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.5rem; line-height:1;">&times;</button>
        </div>
        
        <p style="margin-bottom:15px; font-size:0.9rem;">Pelanggan: <strong id="refundCustomer" style="color:var(--text-main);"></strong></p>
        
        <form action="index.php?action=admin_process_refund" method="POST" id="refundForm">
            <input type="hidden" name="id" id="refundResId">
            <div id="refundBody">
                <p style="text-align:center; color:var(--text-muted);">Memuat detail...</p>
            </div>
            
            <div style="margin-top:25px; display:flex; gap:10px; justify-content:flex-end;" id="refundActionButtons">
                <!-- Action buttons will be injected here -->
            </div>
        </form>
    </div>
</div>

<script>
async function openRefundModal(resId, customerName, paymentStatus) {
    const modal = document.getElementById('refundModal');
    const modalContent = document.getElementById('refundContent');
    const modalBody = document.getElementById('refundBody');
    const actionButtons = document.getElementById('refundActionButtons');
    
    document.getElementById('refundCustomer').textContent = customerName;
    document.getElementById('refundResId').value = resId;
    
    modalBody.innerHTML = '<p style="text-align:center; color:var(--text-muted);">Memuat detail...</p>';
    actionButtons.innerHTML = '';
    
    modal.style.display = 'flex';
    void modal.offsetWidth;
    modal.style.opacity = '1';
    modalContent.style.transform = 'translateY(0)';
    
    try {
        const response = await fetch(`index.php?action=api_get_reservation_details&id=${resId}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            let html = `
                <div style="background:rgba(0,0,0,0.2); padding:15px; border-radius:8px; border:1px solid rgba(255,255,255,0.05);">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span style="color:var(--text-muted);">Deposit:</span>
                        <span>Rp ${new Intl.NumberFormat('id-ID').format(data.deposit)}</span>
                    </div>
            `;
            
            let preOrderTotal = 0;
            if (data.items && data.items.length > 0) {
                html += `<div style="border-top:1px dashed rgba(255,255,255,0.1); margin:10px 0; padding-top:10px;"><span style="color:var(--text-muted); font-size:0.85rem;">Pre-Order:</span></div>`;
                data.items.forEach(item => {
                    html += `
                        <div style="display:flex; justify-content:space-between; margin-bottom:4px; font-size:0.9rem;">
                            <span style="color:var(--text-muted); margin-left:10px;">- ${item.name} (x${item.quantity})</span>
                            <span>Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</span>
                        </div>
                    `;
                    preOrderTotal += item.subtotal;
                });
            }
            
            const totalRefund = data.deposit + preOrderTotal;
            html += `
                    <div style="display:flex; justify-content:space-between; border-top:1px dashed rgba(255,255,255,0.2); padding-top:10px; margin-top:10px;">
                        <strong>Total yang harus di-refund:</strong>
                        <strong style="color:var(--danger); font-size:1.1rem;">Rp ${new Intl.NumberFormat('id-ID').format(totalRefund)}</strong>
                    </div>
                </div>
            `;
            
            if (paymentStatus === 'refunded') {
                html += `<div style="margin-top:15px; text-align:center;"><span class="badge" style="background:rgba(46,204,113,0.15); color:var(--success); border:1px solid rgba(46,204,113,0.25); padding:8px 12px; font-size:0.9rem;">✅ Uang telah dikembalikan</span></div>`;
            }
            
            modalBody.innerHTML = html;
            
            // Render buttons based on status
            if (paymentStatus === 'success') {
                actionButtons.innerHTML = `
                    <button type="button" onclick="closeRefundModal()" class="btn btn-outline" style="padding:8px 16px; font-size:0.9rem;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background:var(--danger); border-color:var(--danger); padding:8px 16px; font-size:0.9rem;">Konfirmasi & Proses Refund</button>
                `;
            } else {
                actionButtons.innerHTML = `
                    <button type="button" onclick="closeRefundModal()" class="btn btn-outline" style="padding:8px 16px; font-size:0.9rem;">Tutup</button>
                `;
            }
            
        } else {
            modalBody.innerHTML = `<div class="alert alert-danger" style="margin:0;">${data.message}</div>`;
            actionButtons.innerHTML = `<button type="button" onclick="closeRefundModal()" class="btn btn-outline">Tutup</button>`;
        }
    } catch (err) {
        modalBody.innerHTML = '<div class="alert alert-danger" style="margin:0;">Terjadi kesalahan sistem. Coba lagi.</div>';
        actionButtons.innerHTML = `<button type="button" onclick="closeRefundModal()" class="btn btn-outline">Tutup</button>`;
        console.error(err);
    }
}

function closeRefundModal() {
    const modal = document.getElementById('refundModal');
    const modalContent = document.getElementById('refundContent');
    
    modal.style.opacity = '0';
    modalContent.style.transform = 'translateY(-20px)';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Ensure the old window.onclick covers this new modal as well
const originalWindowOnClick2 = window.onclick;
window.onclick = function(event) {
    if (originalWindowOnClick2) originalWindowOnClick2(event);
    
    const refundModal = document.getElementById('refundModal');
    if (event.target == refundModal) {
        closeRefundModal();
    }
}
</script>

<?php require_once '../views/admin/layout/footer.php'; ?>
