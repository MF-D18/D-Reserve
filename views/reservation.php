<?php require_once '../views/layout/header.php'; ?>

<div class="glass-panel" style="max-width: 800px; margin: 0 auto;">
    <h2>Pesan Meja</h2>
    
    <!-- Step 1: Check Availability Form -->
    <form id="checkAvailabilityForm" onsubmit="checkAvailability(event)">
        <div class="grid" style="margin-top:0;">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" id="book_date" name="date" required min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Jam Mulai</label>
                <input type="time" id="book_start_time" name="start_time" required value="18:00">
            </div>
            <div class="form-group">
                <label>Durasi (Jam)</label>
                <select id="book_duration" name="duration" required>
                    <option value="1">1 Jam</option>
                    <option value="2" selected>2 Jam</option>
                    <option value="3">3 Jam</option>
                </select>
                <small style="color:var(--text-muted);">Untuk mencegah monopoli meja</small>
            </div>
            <div class="form-group">
                <label>Jumlah Tamu</label>
                <input type="number" id="book_capacity" name="capacity" min="1" max="20" required value="2">
            </div>
        </div>
        <button type="submit" id="checkBtn" class="btn btn-primary" style="margin-top: 20px;">
            <span id="btnText">Cek Ketersediaan</span>
        </button>
        <div id="availabilityMsg" style="margin-top: 15px;"></div>
    </form>

    <hr style="border-color: rgba(255,255,255,0.05); margin: 30px 0;">

    <!-- Step 2: Select Table & Pre-order (Hidden Initially) -->
    <div id="step2Container" style="display: none; opacity: 0; transition: opacity 0.5s ease;">
        <form action="index.php?action=book" method="POST" id="confirmForm">
            <h3 style="margin-bottom: 15px;">Meja Tersedia</h3>
            <div class="grid" id="tablesGrid" style="margin-top:0; gap: 15px;">
                <!-- Tables populated by JS -->
            </div>
            
            <h3 style="margin-top: 40px; margin-bottom: 8px;">Pre-Order Menu (Opsional)</h3>
            <p style="margin-bottom: 20px;">Pesan sekarang agar makanan Anda siap sesaat setelah Anda tiba!</p>

            <div class="menu-filters" style="text-align: center; margin-bottom: 30px;">
                <button type="button" class="btn btn-primary preorder-filter-btn is-active" data-filter="all" style="margin: 0 5px; min-width: 100px; padding: 8px 16px; font-size: 0.85rem;">Semua</button>
                <button type="button" class="btn btn-outline preorder-filter-btn" data-filter="makanan" style="margin: 0 5px; min-width: 100px; padding: 8px 16px; font-size: 0.85rem;">Makanan</button>
                <button type="button" class="btn btn-outline preorder-filter-btn" data-filter="minuman" style="margin: 0 5px; min-width: 100px; padding: 8px 16px; font-size: 0.85rem;">Minuman</button>
            </div>

            <div class="preorder-grid">
                <?php while($menu = $menus->fetch(PDO::FETCH_ASSOC)): ?>
                <?php
                    // Normalize image path
                    $imgSrc = !empty($menu['image_url'])
                        ? '/D-Reserve/public/' . ltrim($menu['image_url'], '/')
                        : null;
                    // Try to match known images by name if image_url is generic
                    if (!$imgSrc || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imgSrc)) {
                        $nameLower = strtolower($menu['name']);
                        if (str_contains($nameLower, 'wagyu'))   $imgSrc = '/D-Reserve/public/img/wagyua5.webp';
                        elseif (str_contains($nameLower, 'pasta') || str_contains($nameLower, 'truffle')) $imgSrc = '/D-Reserve/public/img/trufflepasta.webp';
                        elseif (str_contains($nameLower, 'mojito')) $imgSrc = '/D-Reserve/public/img/Mojito.jpg';
                        else $imgSrc = null;
                    }
                ?>
                <div class="preorder-card" data-category="<?= htmlspecialchars($menu['category'] ?? 'makanan') ?>">
                    <?php if($imgSrc): ?>
                    <div class="preorder-card__img-wrap">
                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($menu['name']) ?>">
                    </div>
                    <?php endif; ?>
                    <div class="preorder-card__body">
                        <h4><?= htmlspecialchars($menu['name']) ?></h4>
                        <?php if(!empty($menu['description'])): ?>
                        <p class="preorder-card__desc"><?= htmlspecialchars($menu['description']) ?></p>
                        <?php endif; ?>
                        <p class="preorder-card__price">Rp <?= number_format($menu['price'], 0, ',', '.') ?></p>
                        <div class="preorder-card__qty">
                            <button type="button" class="qty-btn" onclick="adjustQty(<?= $menu['id'] ?>, -1)">−</button>
                            <input type="number" id="qty_<?= $menu['id'] ?>" name="menus[<?= $menu['id'] ?>]" min="0" value="0" readonly class="qty-input">
                            <button type="button" class="qty-btn" onclick="adjustQty(<?= $menu['id'] ?>, 1)">+</button>
                            <input type="hidden" name="prices[<?= $menu['id'] ?>]" value="<?= $menu['price'] ?>">
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Confirm button -->
            <input type="hidden" name="confirm_booking" value="1">
            <button type="submit" class="btn btn-primary btn-block">Konfirmasi Pilihan & Lanjutkan ke Pembayaran</button>
        </form>
    </div>
</div>

<script>
let hideTimeout;

async function checkAvailability(e) {
    e.preventDefault();
    
    const btn = document.getElementById('checkBtn');
    const btnText = document.getElementById('btnText');
    const msgDiv = document.getElementById('availabilityMsg');
    const step2 = document.getElementById('step2Container');
    const tablesGrid = document.getElementById('tablesGrid');
    
    const data = {
        date: document.getElementById('book_date').value,
        start_time: document.getElementById('book_start_time').value,
        duration: document.getElementById('book_duration').value,
        capacity: document.getElementById('book_capacity').value
    };
    
    // Loading state
    btn.disabled = true;
    btnText.textContent = 'Checking...';
    msgDiv.innerHTML = '';
    
    // Clear any previous hide timeout
    if (hideTimeout) clearTimeout(hideTimeout);
    
    step2.style.opacity = '0';
    hideTimeout = setTimeout(() => step2.style.display = 'none', 300);

    try {
        const response = await fetch('index.php?action=api_check_availability', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            if (result.tables && result.tables.length > 0) {
                // Populate tables
                tablesGrid.innerHTML = '';
                result.tables.forEach(table => {
                    // Tambah batas tabel
    const html = `
                        <label class="item-card" style="cursor:pointer; display:block; padding: 20px;">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                <input type="radio" name="table_id" value="${table.id}" required style="width:18px; height:18px; margin:0; cursor:pointer;">
                                <strong style="font-size: 1.25rem; color: var(--primary-color); line-height:1;">Meja ${table.table_number}</strong>
                            </div>
                            <div style="color:var(--text-muted); font-size:0.95rem; margin-left: 28px;">
                                Kapasitas: ${table.capacity} Orang
                            </div>
                        </label>
                    `;
                    tablesGrid.insertAdjacentHTML('beforeend', html);
                });
                
                // Show Step 2
                if (hideTimeout) clearTimeout(hideTimeout); // Cancel the hiding if fetch was fast
                step2.style.display = 'block';
                setTimeout(() => step2.style.opacity = '1', 50);
                
                msgDiv.innerHTML = `<div class="alert alert-success" style="margin:0; padding:10px;">Found ${result.tables.length} available table(s). Please select below.</div>`;
            } else {
                msgDiv.innerHTML = `<div class="alert alert-danger" style="margin:0; padding:10px;">No tables available for the selected criteria. Please try different time/duration.</div>`;
            }
        } else {
            msgDiv.innerHTML = `<div class="alert alert-danger" style="margin:0; padding:10px;">${result.message}</div>`;
        }
    } catch (err) {
        msgDiv.innerHTML = `<div class="alert alert-danger" style="margin:0; padding:10px;">Failed to check availability. Please try again later.</div>`;
        console.error(err);
    } finally {
        btn.disabled = false;
        btnText.textContent = 'Cek Ketersediaan';
    }
}

function adjustQty(menuId, delta) {
    const input = document.getElementById('qty_' + menuId);
    const current = parseInt(input.value) || 0;
    const newVal = Math.max(0, current + delta);
    input.value = newVal;

    // Visual feedback: highlight the card when qty > 0
    const card = input.closest('.preorder-card');
    if (newVal > 0) {
        card.classList.add('preorder-card--selected');
    } else {
        card.classList.remove('preorder-card--selected');
    }
}

// Pre-order Filtering Logic
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.preorder-filter-btn');
    const menuItems = document.querySelectorAll('.preorder-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Reset all buttons
            filterBtns.forEach(b => {
                b.classList.remove('btn-primary', 'is-active');
                b.classList.add('btn-outline');
            });
            
            // Set active button
            btn.classList.remove('btn-outline');
            btn.classList.add('btn-primary', 'is-active');

            const filterValue = btn.getAttribute('data-filter');

            // Filter items
            menuItems.forEach(item => {
                if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                    item.style.display = 'flex'; // Preorder cards are flex
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php require_once '../views/layout/footer.php'; ?>

