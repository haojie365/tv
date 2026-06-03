<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_t = isset($_GET['t']) ? intval($_GET['t']) : 0;
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>6b影视TV - <?php echo isset($page_title) ? $page_title : '免费沉浸式影视'; ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest/dist/hls.min.js"></script>
    <script src="https://unpkg.com/gsap@3/dist/gsap.min.js"></script>
    <script src="https://unpkg.com/gsap@3/dist/ScrollTrigger.min.js"></script>
    <?php if (isset($extra_style)) echo $extra_style; ?>
</head>
<body>
    <div id="app">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="ri-movie-2-line"></i> 6b影视TV
            </a>
            <div class="nav-links">
                <a href="index.php" class="<?php echo $current_page === 'index' ? 'active' : ''; ?>">首页</a>
                <a href="list.php?t=1" class="<?php echo ($current_page === 'list' && $current_t === 1) ? 'active' : ''; ?>">电影片</a>
                <a href="list.php?t=2" class="<?php echo ($current_page === 'list' && $current_t === 2) ? 'active' : ''; ?>">连续剧</a>
                <a href="list.php?t=3" class="<?php echo ($current_page === 'list' && $current_t === 3) ? 'active' : ''; ?>">综艺片</a>
                <a href="list.php?t=4" class="<?php echo ($current_page === 'list' && $current_t === 4) ? 'active' : ''; ?>">动漫片</a>
            </div>
            <div class="search-box">
                <i class="ri-search-line"></i>
                <input type="text" placeholder="搜索影视..." onkeydown="if(event.key==='Enter')window.location='search.php?q='+encodeURIComponent(this.value)">
            </div>
        </nav>

        <!-- Mobile Bottom Tab Bar -->
        <div class="mobile-tabbar">
            <div class="tabbar-inner">
                <a href="index.php" class="tab-item <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                    <i class="ri-compass-3-line"></i><span>首页</span>
                </a>
                <a href="list.php?t=1" class="tab-item <?php echo ($current_page === 'list' && $current_t === 1) ? 'active' : ''; ?>">
                    <i class="ri-film-line"></i><span>电影</span>
                </a>
                <a href="list.php?t=2" class="tab-item <?php echo ($current_page === 'list' && $current_t === 2) ? 'active' : ''; ?>">
                    <i class="ri-tv-2-line"></i><span>剧集</span>
                </a>
                <a href="list.php?t=3" class="tab-item <?php echo ($current_page === 'list' && $current_t === 3) ? 'active' : ''; ?>">
                    <i class="ri-disc-line"></i><span>综艺</span>
                </a>
                <a href="list.php?t=4" class="tab-item <?php echo ($current_page === 'list' && $current_t === 4) ? 'active' : ''; ?>">
                    <i class="ri-ghost-line"></i><span>动漫</span>
                </a>
            </div>
        </div>

        <!-- Back to Top -->
        <div class="back-to-top" id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
            <div class="btt-ring"></div>
            <i class="ri-arrow-up-line"></i>
        </div>
