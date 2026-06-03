        <footer class="site-footer">
            <div class="footer-brand"><i class="ri-movie-2-line"></i> 6b影视</div>
            <div class="footer-nav">
                <a href="index.php">首页</a>
                <a href="list.php?t=1">电影片</a>
                <a href="list.php?t=2">连续剧</a>
                <a href="list.php?t=3">综艺片</a>
                <a href="list.php?t=4">动漫片</a>
            </div>
            <div class="footer-copy">&copy; 2026 6b影视 &middot; 免费影视在线观看 &middot; 仅供学习交流</div>
        </footer>
    </div>

    <?php if (isset($vue_script)) echo $vue_script; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // GSAP entrance animations
            if (typeof gsap !== 'undefined') {
                gsap.from('.card', {
                    y: 40,
                    opacity: 0,
                    duration: 0.6,
                    stagger: 0.08,
                    ease: 'power3.out',
                    clearProps: 'all'
                });
                gsap.from('.section-title', {
                    x: -30,
                    opacity: 0,
                    duration: 0.5,
                    stagger: 0.15,
                    ease: 'power2.out'
                });
            }

            // Back to top button visibility
            var btt = document.getElementById('backToTop');
            if (btt) {
                var lastScroll = 0;
                window.addEventListener('scroll', function() {
                    var y = window.pageYOffset || document.documentElement.scrollTop;
                    if (y > 400) {
                        btt.classList.add('visible');
                    } else {
                        btt.classList.remove('visible');
                    }
                    lastScroll = y;
                }, { passive: true });
            }
        });
    </script>
</body>
</html>
