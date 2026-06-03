<?php
/**
 * API 代理端点 — 供前端 fetch 异步调用
 * 用法:
 *   /api_proxy.php?do=list&tid=1&page=1
 *   /api_proxy.php?do=detail&id=12345
 *   /api_proxy.php?do=search&wd=战狼&page=1
 *   /api_proxy.php?do=classes
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'includes/api.php';

$do = isset($_GET['do']) ? $_GET['do'] : '';

switch ($do) {
    case 'list':
    $tid  = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    
    // 请求两页数据（第1页20条 + 第2页20条）
    $page1_data = api_list($tid, $page);
    $page2_data = api_list($tid, $page + 1);
    
    // 合并数据，取前24条
    $merged_list = array_merge(
        $page1_data['list'] ?? [],
        $page2_data['list'] ?? []
    );
    $merged_list = array_slice($merged_list, 0, 24);
    
    // 重新计算总页数
    $total = $page1_data['total'] ?? 0;
    $new_pagecount = ceil($total / 24);
    
    echo to_json([
        'list' => $merged_list,
        'total' => $total,
        'pagecount' => $new_pagecount
    ]);
    break;

    case 'search':
    $wd   = isset($_GET['wd']) ? trim($_GET['wd']) : '';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    
    if (!$wd) {
        echo to_json(['list' => [], 'total' => 0, 'pagecount' => 0]);
        break;
    }
    
    // 请求两页数据（第1页 + 第2页）
    $page1_data = api_list(0, $page, $wd);
    $page2_data = api_list(0, $page + 1, $wd);
    
    // 合并数据，取前24条
    $merged_list = array_merge(
        $page1_data['list'] ?? [],
        $page2_data['list'] ?? []
    );
    $merged_list = array_slice($merged_list, 0, 24);
    
    // 重新计算总页数（按24条/页重新计算）
    $total = $page1_data['total'] ?? 0;
    $new_pagecount = ceil($total / 24);
    
    echo to_json([
        'list' => $merged_list,
        'total' => $total,
        'pagecount' => $new_pagecount
    ]);
    break;

    case 'detail':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $vod = $id > 0 ? api_detail($id) : null;
        echo to_json($vod ?: null);
        break;

    case 'home':
        // 1. 获取分类（含 type_pid）
        $cls_data = api_get(['ac' => 'list']);
        $classes  = ($cls_data && !empty($cls_data['class'])) ? $cls_data['class'] : [];

        // 2. 按一级分类归集子分类 ID
        $sub_ids = [1 => [], 2 => [], 3 => [], 4 => []];
        foreach ($classes as $c) {
            $pid = intval($c['type_pid']);
            if (isset($sub_ids[$pid])) {
                $sub_ids[$pid][] = intval($c['type_id']);
            }
        }

        // 3. 每个一级分类：从前2个子分类各取6条ID，合并后批量取详情（含封面图）
        function home_fetch($sub_list, $limit = 12) {
            if (empty($sub_list)) return [];
            $per_sub  = ceil($limit / min(2, count($sub_list)));
            $all_ids  = [];
            foreach (array_slice($sub_list, 0, 2) as $stid) {
                $ld = api_get(['ac' => 'list', 't' => $stid, 'page' => 1]);
                if ($ld && !empty($ld['list'])) {
                    $ids = array_column(array_slice($ld['list'], 0, $per_sub), 'vod_id');
                    $all_ids = array_merge($all_ids, $ids);
                }
            }
            $all_ids = array_unique($all_ids);
            $all_ids = array_slice($all_ids, 0, $limit);
            if (empty($all_ids)) return [];
            $dd = api_get(['ac' => 'detail', 'ids' => implode(',', $all_ids)]);
            return ($dd && !empty($dd['list'])) ? $dd['list'] : [];
        }

        echo to_json([
            'classes' => $classes,
            'movies'  => home_fetch($sub_ids[1]),
            'series'  => home_fetch($sub_ids[2]),
            'anime'   => home_fetch($sub_ids[4])
        ]);
        break;

    case 'classes':
        $classes = api_classes_only();
        echo to_json($classes);
        break;

    default:
        echo json_encode(['error' => 'invalid action']);
}
