<?php

/**
 * @Project NUKEVIET-MUSIC
 * @Author Phan Tan Dung (phantandung92@gmail.com)
 * @Copyright (C) 2011 Freeware
 * @Createdate 26/01/2011 12:01 PM
 */

if ( ! defined( 'NV_IS_MOD_MUSIC' ) ) die( 'Stop!!!' );

$id = 0;
$userid = 0;
$username = "";
$allsinger = getallsinger();
$allauthor = getallauthor();
$category = get_category();

if ( defined( 'NV_IS_USER' ) )
{
	$username = $user_info['username'];
	$userid = $user_info['userid'];
}

$g_array = array(
	"username" => $username,  //
	"userid" => $userid  //
);

// Thong tin trang
$page_title = $lang_module['mana_song'] . NV_TITLEBAR_DEFIS;
if( ! empty( $username ) ) $page_title .= $username . NV_TITLEBAR_DEFIS;
$page_title .=  $module_info['custom_title'];
$key_words = $module_info['keywords'];
$description = $setting['description'];

// Da dang nhap
$array_song = array();
$data_song = array();

if( ! empty( $userid ) )
{
	$id = isset( $array_op[1] ) ? intval( $array_op[1] ) : 0;
	if ( $id > 0 )
	{
		$song = getsongbyID( $id );
		
		// Thong tin trang
		$page_title = $lang_module['song_edit1'] . NV_TITLEBAR_DEFIS . $song['tenthat'] . NV_TITLEBAR_DEFIS . $username . NV_TITLEBAR_DEFIS . $module_info['custom_title'];

		$resuit = false;
		if ( $song['userid'] != $userid ) die ('Stop!!!');
		if ( $nv_Request->get_int( 'ok', 'post', 0 ) == 1 )
		{
			$song['tenthat'] = $songdata['tenthat'] = filter_text_input( 'name', 'post', '' );
			$song['ten'] = $songdata['ten'] = change_alias( $songdata['tenthat'] . "-" . $id );
			$song['casi'] = $songdata['casi'] = filter_text_input( 'singer', 'post', '' );
			$songdata['casimoi'] = filter_text_input( 'newsinger', 'post', '' );
			$song['nhacsi'] = $songdata['nhacsi'] = filter_text_input( 'nhacsi', 'post', '' );
			$songdata['nhacsimoi'] = filter_text_input( 'nhacsimoi', 'post', '' );
			$song['theloai'] = $songdata['theloai'] = $nv_Request->get_int( 'theloai', 'post', 0 );
			if ( $songdata['casimoi'] != '')
			{
				$song['casi'] = $songdata['casi'] = change_alias( $songdata['casimoi'] );
				newsinger( $songdata['casi'], $songdata['casimoi'] );
				updatesinger( $songdata['casi'], 'numsong', '+1' );
			}
			if ( $songdata['nhacsimoi'] != '')
			{
				$song['nhacsi'] = $songdata['nhacsi'] = change_alias( $songdata['nhacsimoi'] );
				newauthor( $songdata['nhacsi'], $songdata['nhacsimoi'] );
				updateauthor( $songdata['nhacsi'], 'numsong', '+1' );
			}
			
			$sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "` SET 
				`tenthat`=" . $db->dbescape( $song['tenthat'] ) . ", 
				`ten`=" . $db->dbescape( $song['ten'] ) . ", 
				`casi`=" . $db->dbescape( $song['casi'] ) . ", 
				`theloai`=" . $db->dbescape( $song['theloai'] ) . ", 
				`nhacsi`=" . $db->dbescape( $song['nhacsi'] ) . "
			WHERE `id`=" .  $id;
			$resuit = $db->sql_query( $sql );
			
			if( $resuit ) nv_del_moduleCache( $module_name );
		}
				
		$cate = '';
		$singer = '';
		$author = '';
		foreach( $category as $key => $data )
		{
			$cate .= "<option " . ( ( $key == $song['theloai'] )? ( "selected=\"selected\"" ):( "" ) ) . " value=\"" . $key . "\">" . $data['title'] . "</option>";
		}
		foreach( $allsinger as $key => $data )
		{
			$singer .= "<option " . ( ( $key == $song['casi'] )? ( "selected=\"selected\"" ):( "" ) ) . " value=\"" . $key . "\">" . $data . "</option>";
		}
		foreach( $allauthor as $key => $data )
		{
			$author .= "<option " . ( ( $key == $song['nhacsi'] )? ( "selected=\"selected\"" ):( "" ) ) . " value=\"" . $key . "\">" . $data . "</option>";
		}
		
		$data_song = array(
			"author" => $author,  //
			"singer" => $singer,  //
			"cate" => $cate,  //
			"song" => $song,  //
			"resuit" => $resuit  //
		);
	}
	else
	{
		$now_page = isset( $array_op[2] ) ?  intval( $array_op[2] ) : 1;
		$link = $mainURL . "=managersong/0" ;

		if ( $now_page == 1 ) 
		{
			$first_page = 0 ;
		}
		else 
		{
			$first_page = ( $now_page -1 ) * 20;
		}	
		$sql = "SELECT * FROM `" . NV_PREFIXLANG . "_" . $module_data . "` WHERE `userid`=" . $userid . " ORDER BY `id` DESC LIMIT " . $first_page . ",20";
		$sqlnum = "SELECT COUNT(*) FROM `" . NV_PREFIXLANG . "_" . $module_data . "` WHERE `userid` = " . $userid;

		$num = $db->sql_query( $sqlnum );
		list( $output ) = $db->sql_fetchrow( $num );
		$ts = 1;
		while ( $ts * 20 < $output ) {$ts ++ ;}

		$result = $db->sql_query( $sql );
		while ( $row = $db->sql_fetchrow( $result ) )
		{
			$array_song[] = array(
				"id" => $row['id'],  //
				"name" => $row['tenthat'],  //
				"singer" => $allsinger[$row['casi']],  //
				"view" => $row['numview'],  //
				"bitrate" => $row['bitrate'],  //
				"size" => $row['size'],  //
				"duration" => $row['duration'],  //
				"url_search_singer" => $mainURL . "=search/singer/" . $row['casi'],  //
				"url_search_category" => $mainURL . "=search/category/" . $row['theloai'],  //
				"category" => $category[$row['theloai']]['title'],  //
				"url_view" => $mainURL . "=listenone/" . $row['id'] . "/" . $row['ten'],  //
				"url_edit" => $mainURL . "=managersong/" . $row['id'],  //
				"active" => $row['active']  //
			);
		}
	}
}

$contents = nv_music_managersong( $g_array, $array_song, $data_song );
if ( $userid != 0 and $id == 0 ) $contents .= new_page( $ts, $now_page, $link );

include ( NV_ROOTDIR . "/includes/header.php" );
echo nv_site_theme( $contents );
include ( NV_ROOTDIR . "/includes/footer.php" );

?>