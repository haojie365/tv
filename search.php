<?php
$q    = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$page_title = $q ? "搜索: {$q}" : '搜索';
include 'includes/header.php';
?>

        <div class="container">
            <!-- 搜索区域 -->
            <div class="search-hero">
                <h1>搜索影视</h1>
                <div class="search-big-input">
                    <i class="ri-search-line"></i>
                    <input type="text" v-model="keyword" placeholder="输入电影名、演员、导演..." @keydown.enter="doSearch">
                </div>
            </div>

            <!-- Loading -->
            <div class="loading-spinner" v-if="isLoading">加载中...</div>

            <!-- 搜索结果 -->
            <template v-if="searched && !isLoading">
                <div class="search-stats" v-if="items.length">
                    搜索 "<em>{{ keyword }}</em>" 共找到 <em>{{ total }}</em> 个结果
                </div>
                <div class="grid fade-in" v-if="items.length">
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
                <div class="pagination" v-if="pagecount > 1">
                    <a class="page-btn" v-if="page > 1" :href="searchUrl(page - 1)"><i class="ri-arrow-left-s-line"></i></a>
                    <a class="page-btn" v-if="pageStart > 1" :href="searchUrl(1)">1</a>
                    <span class="page-btn dots" v-if="pageStart > 2">...</span>
                    <a class="page-btn" v-for="p in pageRange" :key="p" :class="{ active: p === page }" :href="searchUrl(p)">{{ p }}</a>
                    <span class="page-btn dots" v-if="pageEnd < pagecount - 1">...</span>
                    <a class="page-btn" v-if="pageEnd < pagecount" :href="searchUrl(pagecount)">{{ pagecount }}</a>
                    <a class="page-btn" v-if="page < pagecount" :href="searchUrl(page + 1)"><i class="ri-arrow-right-s-line"></i></a>
                </div>

                <!-- 空结果 -->
                <div class="empty-state" v-if="!items.length">
                    <i class="ri-search-eye-line"></i>
                    <p>没有找到 "{{ keyword }}" 相关影视，换个关键词试试吧</p>
                </div>
            </template>

            <!-- 未搜索时：最新更新 -->
            <template v-if="!searched && !isLoading">
                <div class="section" v-if="trending.length">
                    <div class="section-header">
                        <h2 class="section-title">最新更新</h2>
                    </div>
                    <div class="grid fade-in">
                        <a class="card" v-for="item in trending" :key="item.vod_id" :href="'detail.php?id=' + item.vod_id">
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
                </div>
            </template>

            <!-- 未搜索骨架 -->
            <div class="grid" v-if="!searched && isLoading">
                <div class="skeleton skeleton-card" v-for="n in 12" :key="n"></div>
            </div>
        </div>

<?php
$q_js = addslashes($q);
$vue_script = <<<SCRIPT
<script>
const { createApp } = Vue
createApp({
    data() {
        return {
            keyword: '{$q_js}',
            searched: !!'{$q_js}',
            page: {$page},
            items: [],
            trending: [],
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
        if (this.searched) {
            this.fetchSearch()
        } else {
            this.fetchTrending()
        }
    },
    methods: {
        doSearch() {
            if (this.keyword.trim()) {
                window.location = 'search.php?q=' + encodeURIComponent(this.keyword.trim())
            }
        },
        async fetchSearch() {
            try {
                const res = await fetch('api_proxy.php?do=search&wd=' + encodeURIComponent(this.keyword) + '&page=' + this.page)
                const data = await res.json()
                this.items     = data.list || []
                this.total     = parseInt(data.total) || 0
                this.pagecount = parseInt(data.pagecount) || 1
            } catch(e) {}
            this.isLoading = false
        },
        async fetchTrending() {
            try {
                const res = await fetch('api_proxy.php?do=list&page=1')
                const data = await res.json()
                this.trending = (data.list || []).slice(0, 12)
            } catch(e) {}
            this.isLoading = false
        },
        searchUrl(p) {
            return 'search.php?q=' + encodeURIComponent(this.keyword) + (p > 1 ? '&page=' + p : '')
        }
    }
}).mount('#app')
</script>
SCRIPT;
include 'includes/footer.php';
?>
