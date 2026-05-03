<?php require_once '../views/layout/header.php'; ?>

<div class="glass-panel">
    <h2>Reservasi Saya</h2>
    
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">Pembayaran berhasil! Reservasi Anda telah dikonfirmasi.</div>
    <?php endif; ?>

    <?php if($reservations->rowCount() > 0): ?>
        <table>
            <thead>
                <tr>
                    <th style="padding: 15px;">Tanggal</th>
                    <th style="padding: 15px;">Waktu</th>
                    <th style="padding: 15px;">Meja</th>
                    <th style="padding: 15px; text-align: center;">Status</th>
                    <th style="padding: 15px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $reservations->fetch(PDO::FETCH_ASSOC)): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <td style="padding: 20px 15px;"><?= date('d M Y', strtotime($row['reservation_date'])) ?></td>
                    <td style="padding: 20px 15px; font-weight: 500;"><?= date('H:i', strtotime($row['start_time'])) ?> - <?= date('H:i', strtotime($row['end_time'])) ?></td>
                    <td style="padding: 20px 15px;"><span style="color: var(--primary-color); font-weight: 600;">Meja <?= $row['table_number'] ?></span></td>
                    <td style="padding: 20px 15px; text-align: center;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <span class="badge badge-<?= strtolower($row['status']) ?>" style="min-width: 90px;">
                                <?= ucfirst($row['status']) ?>
                            </span>
                            <?php if ($row['status'] === 'cancelled' && ($row['payment_status'] ?? '') === 'refunded'): ?>
                                <span class="badge" style="background:rgba(46,204,113,0.1); color:var(--success); border:1px solid rgba(46,204,113,0.2); font-size:0.65rem; padding: 2px 8px; text-transform: uppercase; letter-spacing: 0.5px;">Refunded</span>
                            <?php elseif ($row['status'] === 'cancelled' && ($row['payment_status'] ?? '') === 'success'): ?>
                                <span class="badge" style="background:rgba(212,175,55,0.1); color:var(--primary-color); border:1px solid rgba(212,175,55,0.2); font-size:0.65rem; padding: 2px 8px; text-transform: uppercase; letter-spacing: 0.5px;">Refund Pending</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="padding: 20px 15px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end; align-items: center;">
                            <button onclick="viewMyReservation(<?= $row['id'] ?>)" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85em; min-width: 70px;">
                                Detail
                            </button>
                            <?php if (in_array($row['status'], ['pending', 'confirmed'])): ?>
                                <form action="index.php?action=cancel_reservation" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini?')">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85em; color: var(--danger); border-color: rgba(231,76,60,0.4); min-width: 80px;">
                                        Batalkan
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Anda belum memiliki reservasi.</p>
        <a href="index.php?action=book" class="btn btn-primary" style="margin-top: 15px;">Pesan Meja</a>
    <?php endif; ?>
</div>

<!-- Modal Detail Reservasi -->
<div id="myReservationModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; backdrop-filter:blur(5px); justify-content:center; align-items:center; opacity:0; transition:opacity 0.3s ease;">
    <div style="background:#15181e; border:1px solid rgba(255,255,255,0.1); border-radius:12px; width:100%; max-width:500px; padding:25px; box-shadow:0 10px 40px rgba(0,0,0,0.8); transform:translateY(-20px); transition:transform 0.3s ease;" id="myModalContent">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:15px;">
            <h3 style="margin:0; font-size:1.2rem; color:var(--primary-color);">Invoice & Details</h3>
            <button onclick="closeMyModal()" style="background:none; border:none; color:var(--text-muted); cursor:pointer; font-size:1.5rem; line-height:1;">&times;</button>
        </div>
        
        <div id="myModalBody">
            <p style="text-align:center; color:var(--text-muted);">Loading...</p>
        </div>
        
        <div style="margin-top:25px; text-align:right;">
            <button onclick="closeMyModal()" class="btn btn-outline" style="padding:8px 16px; font-size:0.9rem;">Tutup</button>
        </div>
    </div>
</div>

<script>
async function viewMyReservation(resId) {
    const modal = document.getElementById('myReservationModal');
    const modalContent = document.getElementById('myModalContent');
    const modalBody = document.getElementById('myModalBody');
    
    // Reset and show modal
    modalBody.innerHTML = '<p style="text-align:center; color:var(--text-muted);">Loading details...</p>';
    modal.style.display = 'flex';
    void modal.offsetWidth; // Trigger reflow
    modal.style.opacity = '1';
    modalContent.style.transform = 'translateY(0)';
    
    try {
        const response = await fetch(`index.php?action=api_get_my_reservation&id=${resId}`);
        const data = await response.json();
        
        if (data.status === 'success') {
            const r = data.reservation;
            let html = `
                <div style="margin-bottom: 20px; font-size: 0.9em;">
                    <p><strong>Tanggal:</strong> ${r.reservation_date}</p>
                    <p><strong>Waktu:</strong> ${r.start_time} - ${r.end_time}</p>
                    <p><strong>Meja:</strong> ${r.table_number}</p>
                    <p><strong>Status:</strong> <span style="color:var(--primary-color);">${r.status.toUpperCase()}</span></p>
                </div>
            `;
            
            html += `<h4 style="margin-bottom:10px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:5px;">Rincian Pembayaran</h4>`;
            html += `<table style="width:100%; font-size:0.9em; margin-bottom: 10px;">`;
            
            const depFmt = new Intl.NumberFormat('id-ID').format(data.deposit);
            html += `<tr>
                        <td style="padding:5px 0;">Deposit Reservasi</td>
                        <td style="text-align:right;">Rp ${depFmt}</td>
                     </tr>`;

            if (data.items.length > 0) {
                html += `<tr><td colspan="2" style="padding-top:10px; color:var(--text-muted);">Item Pre-Order:</td></tr>`;
                data.items.forEach(item => {
                    const subFmt = new Intl.NumberFormat('id-ID').format(item.subtotal);
                    html += `<tr>
                                <td style="padding:5px 0 5px 15px;">- ${item.name} (x${item.quantity})</td>
                                <td style="text-align:right;">Rp ${subFmt}</td>
                             </tr>`;
                });
            }
            
            const totFmt = new Intl.NumberFormat('id-ID').format(data.total);
            const isRefunded = r.status === 'cancelled' && r.payment_status === 'refunded';
            
            html += `<tr style="border-top:1px dashed rgba(255,255,255,0.2);">
                        <td style="padding:10px 0; font-weight:bold;">${isRefunded ? 'Total Direfund' : 'Total Dibayar'}</td>
                        <td style="text-align:right; font-weight:bold; color:${isRefunded ? 'var(--success)' : 'var(--primary-color)'};">Rp ${totFmt}</td>
                     </tr>`;
            
            if (isRefunded) {
                html += `<tr><td colspan="2" style="text-align:center; padding-top:15px; color:var(--success); font-size:0.85rem;">✅ Dana telah dikembalikan ke metode pembayaran Anda.</td></tr>`;
            } else if (r.status === 'cancelled' && r.payment_status === 'success') {
                html += `<tr><td colspan="2" style="text-align:center; padding-top:15px; color:var(--primary-color); font-size:0.85rem;">⏳ Pengembalian dana sedang diproses oleh admin.</td></tr>`;
            }
                     
            html += `</table>`;
            
            modalBody.innerHTML = html;
        } else {
            modalBody.innerHTML = `<div class="alert alert-danger" style="margin:0;">${data.message}</div>`;
        }
    } catch (err) {
        modalBody.innerHTML = '<div class="alert alert-danger" style="margin:0;">Gagal memuat detail. Silakan coba lagi.</div>';
        console.error(err);
    }
}

function closeMyModal() {
    const modal = document.getElementById('myReservationModal');
    const modalContent = document.getElementById('myModalContent');
    
    modal.style.opacity = '0';
    modalContent.style.transform = 'translateY(-20px)';
    
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

window.onclick = function(event) {
    const modal = document.getElementById('myReservationModal');
    if (event.target == modal) {
        closeMyModal();
    }
}
</script>

<?php require_once '../views/layout/footer.php'; ?>
