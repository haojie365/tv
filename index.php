<?php $page_title = '免费沉浸式影视'; include 'includes/header.php'; ?>

        <div class="container">
            <!-- 顶部分类快捷入口 -->
            <div class="categories">
                <a class="tag active" href="index.php">推荐</a>
                <template v-if="allClasses.length">
                    <a class="tag" v-for="cat in allClasses" :key="cat.type_id" :href="'list.php?t=' + cat.type_id">{{ cat.type_name }}</a>
                </template>
                <template v-else>
                    <span class="skeleton skeleton-tag" v-for="n in 6" :key="n" style="margin-right:8px"></span>
                </template>
            </div>

            <!-- 电影片 -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">电影片</h2>
                    <a href="list.php?t=1" class="view-more">全部 <i class="ri-arrow-right-s-line"></i></a>
                </div>
                <!-- 骨架屏 -->
                <div class="grid" v-if="loading.movies">
                    <div class="skeleton skeleton-card" v-for="n in 6" :key="n"></div>
                </div>
                <!-- 真实数据 -->
                <div class="grid fade-in" v-else>
                    <a class="card" v-for="item in movies" :key="item.vod_id" :href="'detail.php?id=' + item.vod_id">
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

            <!-- 连续剧 -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">连续剧</h2>
                    <a href="list.php?t=2" class="view-more">全部 <i class="ri-arrow-right-s-line"></i></a>
                </div>
                <div class="grid" v-if="loading.series">
                    <div class="skeleton skeleton-card" v-for="n in 6" :key="n"></div>
                </div>
                <div class="grid fade-in" v-else>
                    <a class="card" v-for="item in series" :key="item.vod_id" :href="'detail.php?id=' + item.vod_id">
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

            <!-- 动漫片 -->
            <div class="section">
                <div class="section-header">
                    <h2 class="section-title">动漫片</h2>
                    <a href="list.php?t=4" class="view-more">全部 <i class="ri-arrow-right-s-line"></i></a>
                </div>
                <div class="grid" v-if="loading.anime">
                    <div class="skeleton skeleton-card" v-for="n in 6" :key="n"></div>
                </div>
                <div class="grid fade-in" v-else>
                    <a class="card" v-for="item in anime" :key="item.vod_id" :href="'detail.php?id=' + item.vod_id">
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
        </div>

<?php
$vue_script = <<<'SCRIPT'
<script>
const { createApp } = Vue
createApp({
    data() {
        return {
            allClasses: [],
            movies: [],
            series: [],
            anime: [],
            loading: { movies: true, series: true, anime: true }
        }
    },
    mounted() {
        this.fetchHome()
    },
    methods: {
        async fetchHome() {
            try {
                const res = await fetch('api_proxy.php?do=home')
                const data = await res.json()
                this.allClasses = data.classes || []
                this.movies = data.movies || []
                this.series = data.series || []
                this.anime  = data.anime  || []
            } catch(e) {}
            this.loading.movies = false
            this.loading.series = false
            this.loading.anime  = false
        }
    }
}).mount('#app')
</script>
SCRIPT;
include 'includes/footer.php';
?>
