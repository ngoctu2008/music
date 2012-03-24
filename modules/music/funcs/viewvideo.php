<?php

/* *
* @Project NUKEVIET-MUSIC
* @Author Phan Tan Dung (phantandung92@gmail.com)
* @Copyright (C) 2011 Freeware
* @Createdate 26/01/2011 10:12 AM
*/

if( ! defined( 'NV_IS_MOD_MUSIC' ) ) die( 'Stop!!!' );

$category = get_videocategory();
$setting = setting_music();

$g_array = array();
$g_array['name'] = defined( 'NV_IS_USER' ) ? $user_info['username'] : '';
$g_array['user_login'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=login&amp;nv_redirect=" . nv_base64_encode( $client_info['selfurl'] );
$g_array['user_register'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=users&amp;" . NV_OP_VARIABLE . "=register";

// Lay video
$id = isset( $array_op[1] ) ? intval( $array_op[1] ) : 0;

if( empty( $id ) ) module_info_die();

$sql = "SELECT a.*, b.ten AS singeralias, b.tenthat AS singername FROM `" . NV_PREFIXLANG . "_" . $module_data . "_video` AS a LEFT JOIN `" . NV_PREFIXLANG . "_" . $module_data . "_singer` AS b ON a.casi=b.id WHERE a.id=" . $id . " AND a.active=1";
$result = $db->sql_query( $sql );
$row = $db->sql_fetchrow( $result );

if( empty( $row ) ) module_info_die();

updateHIT_VIEW( $id, '_video', false );

$row['listcat'] = empty( $row['listcat'] ) ? array() : array_diff( array_filter( array_unique( explode( ",", $row['listcat'] ) ) ), array( $row['theloai'] ) );
if( ! empty( $row['listcat'] ) )
{
	$list_cat = $row['listcat'];
	$row['listcat'] = array();
	foreach( $list_cat as $cat )
	{
		$catname = isset( $category[$cat] ) ? $category[$cat]['title'] : $category[0]['title'];
	
		$row['listcat'][] = array( "name" => $catname, "url" => $mainURL . "=search&amp;where=video&amp;q=" . urlencode( $catname ) . "&amp;id=" . $cat . "&amp;type=category" );
	}
}

$singername = $row['singername'] ? $row['singername'] : $lang_module['unknow'];

$array = array(
	"URL_SENDMAIL" => $mainURL . "=videosendmail&amp;id=" . $id, //
	"TITLE" => $lang_module['sendtomail'], //
	"ID" => $id, //
	"name" => $row['tname'], //
	"thumb" => $row['thumb'], //
	"listcat" => $row['listcat'], //
	"sname" => $row['name'], //
	"singer" => $singername, //
	"category" => $category[$row['theloai']]['title'], //
	"view" => $row['view'], //
	"creat_link_url" => NV_MY_DOMAIN . nv_url_rewrite( $main_header_URL . '=creatlinksong/video/' . $row['id'] . '/' . $row['name'], true ), //
	"url_search_singer" => $mainURL . "=search&amp;where=video&amp;q=" . urlencode( $singername ) . "&amp;id=" . $row['casi'] . "&amp;type=singer", //
	"url_search_category" => $mainURL . "=search&amp;where=video&amp;q=" . urlencode( $category[$row['theloai']]['title'] ) . "&amp;id=" . $row['theloai'] . "&amp;type=category", //
	"link" => nv_url_rewrite( $main_header_URL . "=creatlinksong/video/" . $row['id'] . "/" . $row['name'], true ), //
	"URL_SONG" => NV_MY_DOMAIN . nv_url_rewrite( $main_header_URL . '=viewvideo/' . $row['id'] . '/' . $row['name'], true ) //
);

// tieu de trang
$page_title = $row['tname'] . " - " . $array['singer'];
$key_words = $row['tname'] . " - " . $array['singer'];

$contents = nv_music_viewvideo( $g_array, $array );

include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_site_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>