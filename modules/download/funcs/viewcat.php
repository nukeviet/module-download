<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:30
 */

if( ! defined( 'NV_IS_MOD_DOWNLOAD' ) ) die( 'Stop!!!' );

$contents = '';
if( empty( $list_cats ) )
{
	$page_title = $module_info['custom_title'];
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_site_theme( '' );
	include NV_ROOTDIR . '/includes/footer.php';
	exit();
}

$download_config = nv_mod_down_config();

$today = mktime( 0, 0, 0, date( 'n' ), date( 'j' ), date( 'Y' ) );
$yesterday = $today - 86400;

// View cat
if( empty( $catid ) )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
	exit();
}

$array = array();
$subcats = array();
$contents = '';

$c = $list_cats[$catid];
$subcats = $c['subcats'];
$viewcat = $c['viewcat'];

$page_title = $mod_title = $c['title'];
$key_words = $module_info['keywords'];
$description = $c['description'];

$per_page = $download_config['per_page_child'];
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $catalias;

if( $viewcat == 'viewcat_main_bottom' )
{
	$allcats = array( $c['id'] );

	$db->sqlreset()
		->select( 'COUNT(*)' )
		->from( NV_PREFIXLANG . '_' . $module_data )
		->where( 'catid=' . $c['id'] . ' AND status=1' );

	$num_items = $db->query( $db->sql() )->fetchColumn();

	$db->select( 'id, catid, title, alias, introtext , uploadtime, author_name, filesize, fileimage, view_hits, download_hits, comment_hits' )
		->order( 'uploadtime DESC' )
		->limit( $per_page )
		->offset( ( $page - 1 ) * $per_page );
	$result = $db->query( $db->sql() );

	while( $row = $result->fetch() )
	{
		$cattitle = '<a href="' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '">' . $list_cats[$row['catid']]['title'] . '</a>';

		$uploadtime = ( int )$row['uploadtime'];
		if( $uploadtime >= $today )
		{
			$uploadtime = $lang_module['today'] . ', ' . date( 'H:i', $row['uploadtime'] );
		}
		elseif( $uploadtime >= $yesterday )
		{
			$uploadtime = $lang_module['yesterday'] . ', ' . date( 'H:i', $row['uploadtime'] );
		}
		else
		{
			$uploadtime = nv_date( 'd/m/Y H:i', $row['uploadtime'] );
		}

		$array[$row['id']] = array(
			'id' => $row['id'],
			'title' => $row['title'],
			'cattitle' => $cattitle,
			'introtext' => $row['introtext'],
			'uploadtime' => $uploadtime,
			'author_name' => $row['author_name'],
			'filesize' => ! empty( $row['filesize'] ) ? nv_convertfromBytes( $row['filesize'] ) : '',
			'imagesrc' => ( ! empty( $row['fileimage'] ) ) ? NV_BASE_SITEURL . NV_FILES_DIR . $row['fileimage'] : '',
			'view_hits' => $row['view_hits'],
			'download_hits' => $row['download_hits'],
			'comment_hits' => $row['comment_hits'],
			'more_link' =>  NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
			'edit_link' => ( defined( 'NV_IS_MODADMIN' ) ) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $row['id'] : '',
			'del_link' => ( defined( 'NV_IS_MODADMIN' ) ) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name : ''
		);
	}

	$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );

	if( $page > 1 )
	{
		$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
	}

	$subs = array();
	if( ! empty( $subcats ) )
	{
		foreach( $subcats as $sub )
		{
			$allcats = $sub;

			$db->sqlreset()
				->select( 'id, catid, title, alias, introtext , uploadtime, author_name, filesize, fileimage, view_hits, download_hits, comment_hits' )
				->from( NV_PREFIXLANG . '_' . $module_data )
				->order( 'uploadtime DESC' )
				->limit( $list_cats[$sub]['numlink'] );

			if( !empty( $list_cats[$sub]['subcats'] ) )
			{
				$allcats .= ',' . implode( ',', $list_cats[$sub]['subcats'] );
			}

			$array_item = array();
			$result = $db->query( $db->where( 'catid=' . $sub . ' AND status=1' )->sql() );
			$i = 0;
			while( $row = $result->fetch() )
			{
				++$i;
				$uploadtime = nv_date( 'd/m/Y H:i', $row['uploadtime'] );

				$array_item[] = array(
					'id' => $row['id'],
					'title' => $row['title'],
					'introtext' => $row['introtext'],
					'uploadtime' => $uploadtime,
					'author_name' => ! empty( $row['author_name'] ) ? $row['author_name'] : $lang_module['unknown'],
					'filesize' => ! empty( $row['filesize'] ) ? nv_convertfromBytes( $row['filesize'] ) : '',
					'imagesrc' => ( ! empty( $row['fileimage'] ) ) ? NV_BASE_SITEURL . NV_FILES_DIR . $row['fileimage'] : '',
					'view_hits' => $row['view_hits'],
					'download_hits' => ( int )$row['download_hits'],
					'comment_hits' => ( int )$row['comment_hits'],
					'more_link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
					'edit_link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . ( int )$row['id'],
					'del_link' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name
				);
			}

			$numfile = $db->query( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE catid IN ( ' . $allcats . ' )' )->fetchColumn();

			if( $i )
			{
				$subs[] = array(
					'catid' => $sub,
					'title' => $list_cats[$sub]['title'],
					'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$sub]['alias'],
					'description' => $list_cats[$sub]['description'],
					'subcats' => $list_cats[$sub]['subcats'],
					'numfile' => $numfile,
					'items' => $array_item
				);
			}
			unset( $array_item );
		}
	}

	// Chuyen huong neu khong co noi dung gi
	if( empty( $num_items ) and empty( $subs ) )
	{
		Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
		exit();
	}

	$contents = theme_viewcat_main( $viewcat, $subs, $array, $c, $generate_page );
}
elseif( $viewcat == 'viewcat_list_new' )
{
	$db->sqlreset()
		->select( 'COUNT(*)' )
		->from( NV_PREFIXLANG . '_' . $module_data )
		->where( 'catid=' . $c['id'] . ' AND status=1' );

	$num_items = $db->query( $db->sql() )->fetchColumn();
	$c['numfile'] = $num_items;

	$db->select( 'id, catid, title, alias, introtext , uploadtime, author_name, filesize, fileimage, view_hits, download_hits, comment_hits' )
		->order( 'uploadtime DESC' )
		->limit( $per_page )
		->offset( ( $page - 1 ) * $per_page );
	$result = $db->query( $db->sql() );

	while( $row = $result->fetch() )
	{
		$cattitle = '<a href="' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '">' . $list_cats[$row['catid']]['title'] . '</a>';

		$uploadtime = ( int )$row['uploadtime'];
		if( $uploadtime >= $today )
		{
			$uploadtime = $lang_module['today'] . ', ' . date( 'H:i', $row['uploadtime'] );
		}
		elseif( $uploadtime >= $yesterday )
		{
			$uploadtime = $lang_module['yesterday'] . ', ' . date( 'H:i', $row['uploadtime'] );
		}
		else
		{
			$uploadtime = nv_date( 'd/m/Y H:i', $row['uploadtime'] );
		}

		$array[$row['id']] = array(
			'id' => $row['id'],
			'title' => $row['title'],
			'cattitle' => $cattitle,
			'introtext' => $row['introtext'],
			'uploadtime' => $uploadtime,
			'author_name' => $row['author_name'],
			'filesize' => ! empty( $row['filesize'] ) ? nv_convertfromBytes( $row['filesize'] ) : '',
			'imagesrc' => ( ! empty( $row['fileimage'] ) ) ? NV_BASE_SITEURL . NV_FILES_DIR . $row['fileimage'] : '',
			'view_hits' => $row['view_hits'],
			'download_hits' => $row['download_hits'],
			'comment_hits' => $row['comment_hits'],
			'more_link' =>  NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
			'edit_link' => ( defined( 'NV_IS_MODADMIN' ) ) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $row['id'] : '',
			'del_link' => ( defined( 'NV_IS_MODADMIN' ) ) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name : ''
		);
	}

	$generate_page = nv_alias_page( $page_title, $base_url, $num_items, $per_page, $page );

	if( $page > 1 )
	{
		$page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
	}

	$contents = theme_viewcat_list( $array, $generate_page, $c );
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';