    </div><!-- /.admin-content -->
</div><!-- /.admin-main -->

<script>
(function() {
    const overlay = document.getElementById('page-transition-overlay');
    if (!overlay) return;

    // Fade-in on page load
    overlay.classList.add('is-active');
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            overlay.classList.remove('is-active');
        });
    });

    // Intercept all internal link clicks for fade-out before navigating
    document.querySelectorAll('a[href]').forEach(link => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript') ||
            link.target === '_blank' || link.getAttribute('onclick')) return;

        link.addEventListener('click', function(e) {
            const destination = this.getAttribute('href');
            e.preventDefault();
            overlay.classList.add('is-active');
            setTimeout(() => {
                window.location.href = destination;
            }, 320);
        });
    });
})();
</script>

</body>
</html>
