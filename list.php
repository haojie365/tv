<?php
$t    = isset($_GET['t']) ? intval($_GET['t']) : 1;
$c    = isset($_GET['c']) ? intval($_GET['c']) : 0;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$cat_names  = [1 => '电影片', 2 => '连续剧', 3 => '综艺片', 4 => '动漫片'];
$page_title = isset($cat_names[$t]) ? $cat_names[$t] : '影视列表';

include 'includes/header.php';
?>

        <div class="container">
            <div class="breadcrumb">
                <a href="index.php"><i class="ri-home-4-line"></i> 首页</a>
                <span class="sep">/</span>
                <span class="current"><?php echo $page_title; ?></span>
            </div>

            <!-- 二级分类筛选 -->
            <div class="filter-section glass-panel" style="padding:20px 24px; margin-bottom:28px;" v-if="subCats.length">
                <div class="filter-row">
                    <span class="filter-label">分类</span>
                    <div class="filter-scroll">
                        <a class="filter-item" :class="{ active: activeCat === 0 }" href="list.php?t=<?php echo $t; ?>">全部</a>
                        <a class="filter-item" v-for="cat in subCats" :key="cat.type_id" :class="{ active: activeCat === cat.type_id }" :href="'list.php?t=<?php echo $t; ?>&c=' + cat.type_id">{{ cat.type_name }}</a>
                    </div>
                </div>
            </div>

            <div class="result-info" v-if="!isLoading">共 <em>{{ total }}</em> 部 · 第 {{ page }}/{{ pagecount }} 页</div>

            <!-- 骨架屏 -->
            <div class="grid" v-if="isLoading">
                <div class="skeleton skeleton-card" v-for="n in 12" :key="n"></div>
            </div>

            <!-- 真实列表 -->
            <div class="grid fade-in" v-else>
                <a class="card" v-for="item in items" :key="item.vod_id" :href="'detail.php?id=' + item.vod_id">
                    <div class="card-poster">
                        <img v-if="item.vod_pic" :src="item.vod_pic" :alt="item.vod_name" loading="lazy">
                    </div>
                    <div class="badge-top-left" v-if="item.vod_remarks">{{ item.vod_remarks }}</div>
                    <div class="card-overlay">
                        <div class="play-btn"><i class="ri-play-fill"></i></div>
                        <div class="card-info">
                            <div class="card-title">{{ item.vod_name }}</div>
                            <div class="card-meta"><span>{{ item.type_name }}</span></div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 翻页 -->
            <div class="pagination" v-if="pagecount > 1 && !isLoading">
                <a class="page-btn" v-if="page > 1" :href="pageUrl(page - 1)"><i class="ri-arrow-left-s-line"></i></a>
                <a class="page-btn" v-if="pageStart > 1" :href="pageUrl(1)">1</a>
                <span class="page-btn dots" v-if="pageStart > 2">...</span>
                <a class="page-btn" v-for="p in pageRange" :key="p" :class="{ active: p === page }" :href="pageUrl(p)">{{ p }}</a>
                <span class="page-btn dots" v-if="pageEnd < pagecount - 1">...</span>
                <a class="page-btn" v-if="pageEnd < pagecount" :href="pageUrl(pagecount)">{{ pagecount }}</a>
                <a class="page-btn" v-if="page < pagecount" :href="pageUrl(page + 1)"><i class="ri-arrow-right-s-line"></i></a>
            </div>
        </div>

<?php
$vue_script = <<<SCRIPT
<script>
const { createApp } = Vue
createApp({
    data() {
        return {
            typeId: {$t},
            activeCat: {$c},
            page: {$page},
            subCats: [],
            items: [],
            total: 0,
            pagecount: 1,
            isLoading: true
        }
    },
    computed: {
        pageStart() { return Math.max(1, this.page - 2) },
        pageEnd()   { return Math.min(this.pagecount, this.page + 2) },
        pageRange() {
            const r = []
            for (let i = this.pageStart; i <= this.pageEnd; i++) r.push(i)
            return r
        }
    },
    mounted() {
        this.fetchData()
        this.fetchClasses()
    },
    methods: {
        async fetchData() {
            const tid = this.activeCat > 0 ? this.activeCat : this.typeId
            try {
                const res = await fetch('api_proxy.php?do=list&tid=' + tid + '&page=' + this.page)
                const data = await res.json()
                this.items     = data.list || []
                this.total     = parseInt(data.total) || 0
                this.pagecount = parseInt(data.pagecount) || 1
            } catch(e) {}
            this.isLoading = false
        },
        async fetchClasses() {
            try {
                const res = await fetch('api_proxy.php?do=classes')
                const all = await res.json()
                this.subCats = all.filter(c => parseInt(c.type_pid) === this.typeId)
            } catch(e) {}
        },
        pageUrl(p) {
            let url = 'list.php?t=' + this.typeId
            if (this.activeCat > 0) url += '&c=' + this.activeCat
            if (p > 1) url += '&page=' + p
            return url
        }
    }
}).mount('#app')
</script>
SCRIPT;
include 'includes/footer.php';
?>
