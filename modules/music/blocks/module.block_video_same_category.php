<?php
/**
 * @Project NUKEVIET 3.0
 * @Author VINADES., JSC (contact@vinades.vn)
 * @Copyright (C) 2010 VINADES ., JSC. All rights reserved
 * @Createdate Dec 3, 2010  12:57:52 PM 
 */

if ( ! defined( 'NV_IS_MOD_MUSIC' ) ) die( 'Stop!!!' );
global $lang_module, $module_data, $module_file, $module_info, $mainURL, $db, $array_op;
$xtpl = new XTemplate( "block_video_same_category.tpl", NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$allsinger = getallsinger();

$videoid = isset( $array_op[1] ) ? intval( $array_op[1] ) : 0;

$data = getvideobyID( $videoid );
$theloai = $data['theloai'];

$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_video WHERE `active` = 1 AND theloai =\"".$theloai."\" ORDER BY id DESC LIMIT 0,6";
$query = $db->sql_query( $sql );
while( $video =  $db->sql_fetchrow( $query ) )
{
	if ( $data['id'] == $video['id'] ) continue;
	$xtpl->assign( 'url_view', $mainURL . "=viewvideo/" .$video['id']. "/" . $video['name'] );
	$xtpl->assign( 'video_name', $video['tname'] );
	$xtpl->assign( 'thumb', $video['thumb'] );
	$xtpl->assign( 'view', $video['view'] );
	$xtpl->assign( 'url_search_singer', $mainURL . "=searchvideo/singer/" . $video['casi']);
	$xtpl->assign( 'singer', $allsinger[$video['casi']] );
	$xtpl->parse( 'main.loop' );
}

$xtpl->parse( 'main' );
$content = $xtpl->text( 'main' );
?>