    </main>
    <footer>
        <div class="container footer-content">
            <p>&copy; <?= date('Y') ?> D-Reserve. Hak Cipta Dilindungi.</p>
            <p>Sistem Otomasi Integritas &bull; Anti Tidak Datang (No-Show)</p>
        </div>
    </footer>

    <script>
    (function() {
        const overlay = document.getElementById('page-transition-overlay');
        if (!overlay) return;

        // Trigger fade-in on page load (overlay goes from active to hidden)
        overlay.classList.add('is-active');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                overlay.classList.remove('is-active');
            });
        });

        // Intercept all internal link clicks for fade-out before navigating
        document.querySelectorAll('a[href]').forEach(link => {
            const href = link.getAttribute('href');
            // Only intercept internal links; skip blank targets, modals, anchors
            if (!href || href.startsWith('#') || href.startsWith('javascript') || 
                link.target === '_blank' || link.getAttribute('onclick')) return;

            link.addEventListener('click', function(e) {
                const destination = this.getAttribute('href');
                e.preventDefault();
                overlay.classList.add('is-active');
                setTimeout(() => {
                    window.location.href = destination;
                }, 320); // matches transition duration
            });
        });
    })();
    </script>
</body>
</html>
