【NewCloud TV】
轻量级在线影视聚合播放系统


—— 项目简介 ——

NewCloud TV 是一个基于 PHP + Vue.js 的在线影视网站。对接公开采集API，支持电影、连续剧、综艺、动漫的浏览、搜索与播放。

无需数据库，部署简单，上传即用。


—— 核心功能 ——

【浏览】
- 首页推荐，瀑布流卡片展示
- 电影/连续剧/综艺/动漫分类
- 子分类筛选 + 完整分页
- 全局关键词搜索

【详情】
- 海报、评分、导演、演员
- 剧情简介、选集列表

【播放器】
- 自建 HLS 播放器(m3u8)
- 多码率画质切换
- 0.5x~3x 倍速播放
- 长按 3 倍速快进
- 进度条拖拽 + 时间预览
- 无刷新切集，播完自动下一集
- 全屏 + 画面比例调节
- 竖屏旋转(短剧适配)
- 键盘快捷键(空格/方向键/F/M)

【适配】
- 响应式布局，手机电脑通用
- 移动端触控优化
- 底部导航栏


—— 技术栈 ——

前端: Vue.js 3 + HLS.js + GSAP
图标: Remix Icon
样式: 原生CSS(变量+Grid+Flex)
后端: PHP 7.4+(纯原生无框架)
数据: 采集API(ffzyapi.com)


—— 项目结构 ——

index.php - 首页
list.php - 分类列表
detail.php - 影片详情
play.php - 播放页(HLS播放器)
search.php - 搜索页
api_proxy.php - API代理
includes/api.php - 接口封装
includes/header.php - 公共头部
includes/footer.php - 公共底部
assets/style.css - 全站样式


—— 部署说明 ——

环境要求:
- PHP 7.4+
- Apache/Nginx/宝塔
- 服务器能访问外网

部署步骤:
1. 上传文件到网站目录
2. 确保PHP启用file_get_contents
3. 访问 index.php 即可

无需数据库，无需Composer
相对路径，子目录也能用


—— 免责声明 ——

本项目仅供学习交流
不存储任何影视资源
内容来自第三方公开接口
请勿用于商业用途


© 2026 NewCloud TV

