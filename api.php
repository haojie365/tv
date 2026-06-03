<?php
/**
 * NewCloud TV API Helper
 * 资源站接口封装
 */

define('API_BASE', 'https://cj.ffzyapi.com/api.php/provide/vod/at/json/');

/**
 * 发起 GET 请求并返回解码后的 JSON
 */
function api_get($params = []) {
    $url = API_BASE . '?' . http_build_query($params);
    
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 6,
            'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ],
    ]);
    
    $json = @file_get_contents($url, false, $ctx);
    if ($json === false) return null;
    
    return json_decode($json, true);
}

/**
 * 获取影片列表（含 vod_pic）
 * 注意：此 API 分页参数为 pg（非 page）
 * ac=detail 直接返回带封面图的完整数据
 * 一级分类(1-4)API不支持，自动取第一个子分类
 */
function api_list($tid = 0, $pg = 1, $wd = '') {
    // 搜索走 ac=detail + wd
    if ($wd !== '') {
        return api_get(['ac' => 'detail', 'pg' => $pg, 'wd' => $wd, 'limit' => 24]);
    }

    // 无分类筛选
    if ($tid <= 0) {
        return api_get(['ac' => 'detail', 'pg' => $pg, 'limit' => 24]);
    }

    // 一级分类(1-4): API不支持，取第一个子分类
    $primary_ids = [1, 2, 3, 4];
    $query_tid = $tid;
    if (in_array($tid, $primary_ids)) {
        $cls_data = api_get(['ac' => 'list']);
        if ($cls_data && !empty($cls_data['class'])) {
            foreach ($cls_data['class'] as $c) {
                if (intval($c['type_pid'] ?? 0) === $tid) {
                    $query_tid = intval($c['type_id']);
                    break;
                }
            }
        }
    }

    // ac=detail 直接支持分页(pg)且包含封面图
    $data = api_get(['ac' => 'detail', 't' => $query_tid, 'pg' => $pg, 'limit' => 24]);
    if ($data && !empty($data['list'])) {
        return $data;
    }

    return ['list' => [], 'total' => 0, 'pagecount' => 0];
}

/**
 * 仅获取分类列表（轻量，用 ac=list 速度快）
 */
function api_classes_only() {
    $data = api_get(['ac' => 'list']);
    return $data ? api_classes($data) : [];
}

/**
 * 根据 class 列表构建 type_id → 一级分类ID 的映射表
 * 一级分类(1,2,3,4)的 type_pid=0，二级分类的 type_pid 指向一级
 */
function build_type_map($classes) {
    $map = [1 => 1, 2 => 2, 3 => 3, 4 => 4];
    foreach ($classes as $cls) {
        $id  = intval($cls['type_id']);
        $pid = isset($cls['type_pid']) ? intval($cls['type_pid']) : 0;
        if ($pid > 0) {
            $map[$id] = $pid;
        }
    }
    return $map;
}

/**
 * 获取影片详情
 * @param int $id vod_id
 * @return array|null
 */
function api_detail($id) {
    $data = api_get(['ac' => 'detail', 'ids' => intval($id)]);
    if (!$data || empty($data['list'])) return null;
    return $data['list'][0];
}

/**
 * 从接口数据中提取分类列表（一级+二级）
 * @param array $data 接口返回
 * @return array
 */
function api_classes($data) {
    if (!$data || empty($data['class'])) return [];
    return $data['class'];
}

/**
 * 解析播放地址串
 * 格式: "第01集$url#第02集$url" 多线路用 "$$$" 分隔
 * @param string $play_url vod_play_url
 * @return array [ ['name'=>'线路1', 'episodes'=>[['name'=>'第01集','url'=>'...'], ...]], ... ]
 */
function parse_play_url($play_url) {
    if (empty($play_url)) return [];
    
    $sources = explode('$$$', $play_url);
    $result = [];
    
    foreach ($sources as $idx => $source) {
        $episodes = [];
        $parts = explode('#', trim($source));
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;
            $seg = explode('$', $part, 2);
            if (count($seg) === 2) {
                $episodes[] = ['name' => $seg[0], 'url' => $seg[1]];
            }
        }
        if (!empty($episodes)) {
            $result[] = [
                'name'     => '线路' . ($idx + 1),
                'episodes' => $episodes,
            ];
        }
    }
    
    return $result;
}

/**
 * 将分类列表按一级/二级分组
 * @param array $classes 接口返回的 class 数组
 * @return array ['primary' => [...], 'sub' => [pid => [...], ...]]
 */
function group_classes($classes) {
    $primary = [];
    $sub = [];
    $primary_ids = [1, 2, 3, 4];
    
    foreach ($classes as $cls) {
        $id = intval($cls['type_id']);
        $pid = isset($cls['type_pid']) ? intval($cls['type_pid']) : 0;
        
        if (in_array($id, $primary_ids) || $pid === 0) {
            $primary[] = $cls;
        } else {
            if (!isset($sub[$pid])) $sub[$pid] = [];
            $sub[$pid][] = $cls;
        }
    }
    
    return ['primary' => $primary, 'sub' => $sub];
}

/**
 * 安全输出为 JS 变量
 */
function to_json($data) {
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}