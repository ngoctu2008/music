<?php

/**
 * @Project NUKEVIET MUSIC 4.X
 * @Author PHAN TAN DUNG <phantandung92@gmail.com>
 * @Copyright (C) 2016 PHAN TAN DUNG. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 26 Feb 2017 14:04:32 GMT
 */

if (!defined('NV_IS_MUSIC_ADMIN')) {
    die('Stop!!!');
}

use NukeViet\Music\AjaxRespon;
use NukeViet\Music\Utils;
use NukeViet\Music\Config;

$page_title = $lang_module['video_list'];

$ajaction = $nv_Request->get_title('ajaction', 'post', '');

// Xóa
if ($ajaction == 'delete') {
    AjaxRespon::reset();
    if (!defined('NV_IS_AJAX')) {
        AjaxRespon::setMessage('Wrong URL!!!')->respon();
    }

    $video_ids = $nv_Request->get_title('id', 'post', '');
    $video_ids = array_filter(array_unique(array_map('intval', explode(',', $video_ids))));
    if (empty($video_ids)) {
        AjaxRespon::setMessage('Wrong ID!!!')->respon();
    }

    $array_select_fields = nv_get_video_select_fields();

    foreach ($video_ids as $video_id) {
        $video = $db->query("SELECT " . implode(', ', $array_select_fields[0]) . " FROM " . NV_MOD_TABLE . "_videos WHERE video_id=" . $video_id)->fetch();
        if (!empty($video)) {
            foreach ($array_select_fields[1] as $f) {
                if (empty($video[$f]) and !empty($video['default_' . $f])) {
                    $video[$f] = $video['default_' . $f];
                }
                unset($video['default_' . $f]);
            }

            // Xóa video
            $sql = "DELETE FROM " . NV_MOD_TABLE . "_videos WHERE video_id=" . $video_id;
            $db->query($sql);

            // Xóa file video
            $sql = "DELETE FROM " . NV_MOD_TABLE . "_videos_data WHERE video_id=" . $video_id;
            $db->query($sql);

            // Cập nhật lại thống kê thể loại
            $video['cat_ids'] = Utils::arrayIntFromStrList($video['cat_ids']);
            foreach ($video['cat_ids'] as $cat_id) {
                msUpdateCatStat($cat_id);
            }

            // Cập nhật ca sĩ, nhạc sĩ
            $video['singer_ids'] = Utils::arrayIntFromStrList($video['singer_ids']);
            foreach ($video['singer_ids'] as $singer_id) {
                msUpdateArtistStat($singer_id, true);
            }
            $video['author_ids'] = Utils::arrayIntFromStrList($video['author_ids']);
            foreach ($video['author_ids'] as $author_id) {
                msUpdateArtistStat($author_id, false);
            }

            // Cập nhật bài hát liên quan
            if ($video['song_id']) {
                $sql = "UPDATE " . NV_MOD_TABLE . "_songs SET video_id=0 WHERE song_id=" . $video['song_id'];
                $db->query($sql);
            }

            // Ghi nhật ký hệ thống
            nv_insert_logs(NV_LANG_DATA, $module_name, 'LOG_DELETE_VIDEO', $video_id . ':' . $video['video_name'], $admin_info['userid']);
        }
    }

    $nv_Cache->delMod($module_name);

    AjaxRespon::setSuccess()->respon();
}

// Cho hoạt động/đình chỉ
if ($ajaction == 'active' or $ajaction == 'deactive') {
    AjaxRespon::reset();
    if (!defined('NV_IS_AJAX')) {
        AjaxRespon::setMessage('Wrong URL!!!')->respon();
    }

    $video_ids = $nv_Request->get_title('id', 'post', '');
    $video_ids = array_filter(array_unique(array_map('intval', explode(',', $video_ids))));
    if (empty($video_ids)) {
        AjaxRespon::setMessage('Wrong ID!!!')->respon();
    }

    // Xác định các bài hát
    $array_select_fields = nv_get_video_select_fields();
    $sql = "SELECT " . implode(', ', $array_select_fields[0]) . " FROM " . NV_MOD_TABLE . "_videos WHERE video_id IN(" . implode(',', $video_ids) . ")";
    $result = $db->query($sql);

    $array = [];
    while ($row = $result->fetch()) {
        foreach ($array_select_fields[1] as $f) {
            if (empty($row[$f]) and !empty($row['default_' . $f])) {
                $row[$f] = $row['default_' . $f];
            }
            unset($row['default_' . $f]);
        }
        $array[$row['video_id']] = $row;
    }
    if (sizeof($array) != sizeof($video_ids)) {
        AjaxRespon::setMessage('Wrong ID!!!')->respon();
    }

    $status = $ajaction == 'active' ? 1 : 0;

    foreach ($video_ids as $video_id) {
        // Cập nhật trạng thái
        $sql = "UPDATE " . NV_MOD_TABLE . "_videos SET status=" . $status . " WHERE video_id=" . $video_id;
        $db->query($sql);

        $video = $array[$video_id];

        // Cập nhật lại thống kê thể loại
        $video['cat_ids'] = Utils::arrayIntFromStrList($video['cat_ids']);
        foreach ($video['cat_ids'] as $cat_id) {
            msUpdateCatStat($cat_id);
        }

        // Cập nhật ca sĩ, nhạc sĩ
        $video['singer_ids'] = Utils::arrayIntFromStrList($video['singer_ids']);
        foreach ($video['singer_ids'] as $singer_id) {
            msUpdateArtistStat($singer_id, true);
        }
        $video['author_ids'] = Utils::arrayIntFromStrList($video['author_ids']);
        foreach ($video['author_ids'] as $author_id) {
            msUpdateArtistStat($author_id, false);
        }

        // Ghi nhật ký hệ thống
        nv_insert_logs(NV_LANG_DATA, $module_name, 'LOG_' . strtoupper($ajaction) . '_VIDEO', $video_id . ':' . $array[$video_id]['video_name'], $admin_info['userid']);
    }

    $nv_Cache->delMod($module_name);
    AjaxRespon::setSuccess()->respon();
}

$base_url = NV_ADMIN_MOD_FULLLINK_AMP . $op;
$per_page = 20;
$page = Utils::getValidPage($nv_Request->get_int('page', 'get', 1), $per_page);

// Dữ liệu tìm kiếm
$array_search = [];
$array_search['q'] = $nv_Request->get_title('q', 'get', ''); // Từ khóa
$array_search['c'] = $nv_Request->get_int('c', 'get', 0); // Thể loại
$array_search['f'] = $nv_Request->get_title('f', 'get', ''); // Từ
$array_search['t'] = $nv_Request->get_title('t', 'get', ''); // Đến

$db->sqlreset()->from(NV_MOD_TABLE . "_videos");

$where = [];
if (!empty($array_search['q'])) {
    $dblike = $db->dblikeescape($array_search['q']);
    $dblikekey = $db->dblikeescape(str_replace('-', ' ', strtolower(change_alias($array_search['q']))));
    $where[] = "(
        " . NV_LANG_DATA . "_video_name LIKE '%" . $dblike . "%' OR
        " . NV_LANG_DATA . "_video_searchkey LIKE '%" . $dblikekey . "%' OR
        " . NV_LANG_DATA . "_video_introtext LIKE '%" . $dblike . "%' OR
        " . NV_LANG_DATA . "_video_keywords LIKE '%" . $dblike . "%'
    )";
    $base_url .= '&amp;q=' . urlencode($array_search['q']);
}
if (!empty($array_search['c'])) {
    $where[] = "FIND_IN_SET(" . $array_search['c'] . ", cat_ids)";
    $base_url .= '&amp;c=' . $array_search['c'];
}
if (!empty($array_search['f'])) {
    $base_url .= '&amp;f=' . urlencode($array_search['f']);
    $stime = 0;
    if (preg_match('/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/', $array_search['f'], $m)) {
        $stime = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
    }
    if ($stime > 0) {
        $where[] = "time_add>=" . $stime;
    }
}
if (!empty($array_search['t'])) {
    $base_url .= '&amp;t=' . urlencode($array_search['t']);
    $stime = 0;
    if (preg_match('/^([0-9]{2})\-([0-9]{2})\-([0-9]{4})$/', $array_search['t'], $m)) {
        $stime = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
    }
    if ($stime > 0) {
        $where[] = "time_add<=" . ($stime + 86399);
    }
}
if (!empty($where)) {
    $db->where(implode(' AND ', $where));
}

$db->select("COUNT(*)");
$all_pages = $db->query($db->sql())->fetchColumn();

$db->order("video_id DESC")->offset(($page - 1) * $per_page)->limit($per_page);

$array_select_fields = nv_get_video_select_fields(true);
$db->select(implode(', ', $array_select_fields[0]));

$result = $db->query($db->sql());
$array = $array_singer_ids = [];
while ($row = $result->fetch()) {
    foreach ($array_select_fields[1] as $f) {
        if (empty($row[$f]) and !empty($row['default_' . $f])) {
            $row[$f] = $row['default_' . $f];
        }
        unset($row['default_' . $f]);
    }

    $row['authors'] = [];
    $row['author_ids'] = explode(',', $row['author_ids']);
    $row['singers'] = [];
    $row['singer_ids'] = explode(',', $row['singer_ids']);
    $row['cats'] = [];
    $row['cat_ids'] = explode(',', $row['cat_ids']);
    $row['video_link'] = '';

    if (!empty($row['author_ids'])) {
        $array_singer_ids = array_merge_recursive($array_singer_ids, $row['author_ids']);
    }
    if (!empty($row['singer_ids'])) {
        $array_singer_ids = array_merge_recursive($array_singer_ids, $row['singer_ids']);
    }

    $array[$row['video_id']] = $row;
}

// Xác định ca sĩ, chủ đề, đường dẫn bài hát
$array_singers = nv_get_artists($array_singer_ids);

foreach ($array as $id => $row) {
    if (!empty($row['singer_ids'])) {
        foreach ($row['singer_ids'] as $singer_id) {
            if (isset($array_singers[$singer_id])) {
                $row['singers'][$singer_id] = $array_singers[$singer_id];
            }
        }
    }
    if (!empty($row['author_ids'])) {
        foreach ($row['author_ids'] as $author_id) {
            if (isset($array_singers[$author_id])) {
                $row['authors'][$author_id] = $array_singers[$author_id];
            }
        }
    }
    foreach ($row['cat_ids'] as $cid) {
        if (isset($global_array_cat[$cid])) {
            $row['cats'][$cid] = $global_array_cat[$cid];
        }
    }
    $row['video_link'] = nv_get_detail_video_link($row, $row['singers']);
    $array[$id] = $row;
}

$xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('UNIQUEID', nv_genpass(6));
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('MODULE_FILE', $module_file);
$xtpl->assign('NV_LANG_INTERFACE', NV_LANG_INTERFACE);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php');
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('OP', $op);
$xtpl->assign('SEARCH', $array_search);
$xtpl->assign('LINK_ADD', NV_ADMIN_MOD_FULLLINK_AMP . 'video-content');

// Xuất ra trình duyệt
foreach ($array as $row) {
    $row['time_add_time'] = nv_date('H:i', $row['time_add']);
    $row['time_update_time'] = $row['time_update'] ? nv_date('H:i', $row['time_update']) : '';
    $row['time_add'] = Utils::getFormatDateView($row['time_add']);
    $row['time_update'] = $row['time_update'] ? Utils::getFormatDateView($row['time_update']) : '';
    $row['stat_views'] = Utils::getFormatNumberView($row['stat_views']);
    $row['stat_comments'] = Utils::getFormatNumberView($row['stat_comments']);
    $row['state'] = $lang_module['status_' . $row['status']];
    $row['url_edit'] = NV_ADMIN_MOD_FULLLINK_AMP . 'video-content&amp;video_id=' . $row['video_id'];
    $row['resource_avatar_thumb'] = nv_get_resource_url($row['resource_avatar'], 'video', true);
    $row['resource_avatar'] = nv_get_resource_url($row['resource_avatar'], 'video');

    $xtpl->assign('ROW', $row);

    // Ca sĩ
    $num_singers = sizeof($row['singers']);
    if ($num_singers > Config::getLimitSingersDisplayed()) {
        $xtpl->assign('VA_SINGERS', Config::getVariousArtists());

        foreach ($row['singers'] as $singer) {
            $xtpl->assign('SINGER', $singer);
            $xtpl->parse('main.loop.va_singer.loop');
        }

        $xtpl->parse('main.loop.va_singer');
    } elseif (!empty($row['singers'])) {
        $i = 0;
        foreach ($row['singers'] as $singer) {
            $i++;
            $xtpl->assign('SINGER', $singer);

            if ($i > 1) {
                $xtpl->parse('main.loop.show_singer.loop.separate');
            }
            $xtpl->parse('main.loop.show_singer.loop');
        }
        $xtpl->parse('main.loop.show_singer');
    } else {
        $xtpl->assign('UNKNOW_SINGER', Config::getUnknowSinger());
        $xtpl->parse('main.loop.no_singer');
    }

    // Xuất nhạc sĩ
    $num_authors = sizeof($row['authors']);
    if ($num_authors > Config::getLimitAuthorsDisplayed()) {
        $xtpl->assign('VA_AUTHORS', Config::getVariousArtistsAuthors());

        foreach ($row['authors'] as $author) {
            $xtpl->assign('AUTHOR', $author);
            $xtpl->parse('main.loop.va_author.loop');
        }

        $xtpl->parse('main.loop.va_author');
    } elseif (!empty($row['authors'])) {
        $i = 0;
        foreach ($row['authors'] as $author) {
            $i++;
            $xtpl->assign('AUTHOR', $author);

            if ($i > 1) {
                $xtpl->parse('main.loop.show_author.loop.separate');
            }
            $xtpl->parse('main.loop.show_author.loop');
        }
        $xtpl->parse('main.loop.show_author');
    } else {
        $xtpl->assign('UNKNOW_AUTHOR', Config::getUnknowAuthor());
        $xtpl->parse('main.loop.no_author');
    }

    // Xuất thể loại
    $num_cats = sizeof($row['cats']);
    if ($num_cats > 0) {
        $i = 0;
        foreach ($row['cats'] as $cat) {
            $i++;
            $xtpl->assign('CAT', $cat);

            if ($i > 1) {
                $xtpl->parse('main.loop.show_cat.loop.separate');
            }
            $xtpl->parse('main.loop.show_cat.loop');
        }
        $xtpl->parse('main.loop.show_cat');
    } else {
        $xtpl->assign('UNKNOW_CAT', Config::getUnknowCat());
        $xtpl->parse('main.loop.no_cat');
    }

    if (empty($row['status'])) {
        $xtpl->assign('ACTION_STATUS', 'active');
        $xtpl->assign('LANG_STATUS', $lang_module['action_active']);
    } else {
        $xtpl->assign('ACTION_STATUS', 'deactive');
        $xtpl->assign('LANG_STATUS', $lang_module['action_deactive']);
    }

    $xtpl->parse('main.loop');
}

// Xuất thể loại
foreach ($global_array_cat as $cat) {
    $cat['selected'] = $cat['cat_id'] == $array_search['c'] ? ' selected="selected"' : '';
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.cat');
}

// Phân trang
$generate_page = nv_generate_page($base_url, $all_pages, $per_page, $page);
if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
