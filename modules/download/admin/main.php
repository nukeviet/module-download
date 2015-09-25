<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-9-2010 14:43
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

// Edit file
if( $nv_Request->isset_request( 'edit', 'get' ) )
{
	$id = $nv_Request->get_int( 'id', 'get', 0 );
	$report = $nv_Request->isset_request( 'report', 'get' );

	// Cap nhat trang thai thong bao
	if( $report )
	{
		nv_status_notification( NV_LANG_DATA, $module_name, 'report', $id );
	}

	$query = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id;
	$row = $db->query( $query )->fetch();
	if( empty( $row ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name );
		exit();
	}

	define( 'IS_EDIT', true );
	$page_title = $lang_module['download_editfile'];

	$groups_list = nv_groups_list();

	$array = array();
	$is_error = false;
	$error = '';
	
	// Xử lý liên kết tĩnh
	$alias = $nv_Request->get_title( 'alias', 'post', $row['alias'] );
	if( empty( $alias ) )
	{
		$alias = change_alias( $row['title'] );
	}
	else
	{
		$alias = change_alias( $alias );
	}

	if( empty( $alias ) or !preg_match( "/^([a-zA-Z0-9\_\-]+)$/", $alias ) )
	{
		if( empty( $array['alias'] ) )
		{
			$array['alias'] = 'post';
		}
	}
	else
	{
		$array['alias'] = $alias;
	}
	
	$array_keywords_old=array();
	$_query_tag = $db->query( 'SELECT did, keyword FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id=' . $id . ' ORDER BY keyword ASC' );
	while( $row_tag = $_query_tag->fetch( ) )
	{
		$array_keywords_old[$row_tag['did']] = $row_tag['keyword'];
	}
	$array['keywords'] = implode( ', ', $array_keywords_old );
	$array['keywords_old'] = $array['keywords'];
	
	if( $nv_Request->isset_request( 'submit', 'post' ) )
	{
		$array['catid'] = $nv_Request->get_int( 'catid', 'post', 0 );
		$array['title'] = $nv_Request->get_title( 'title', 'post', '', 1 );
		$array['description'] = $nv_Request->get_editor( 'description', '', NV_ALLOWED_HTML_TAGS );
		$array['introtext'] = $nv_Request->get_textarea( 'introtext', '', NV_ALLOWED_HTML_TAGS );
		$array['author_name'] = $nv_Request->get_title( 'author_name', 'post', '', 1 );
		$array['author_email'] = $nv_Request->get_title( 'author_email', 'post', '' );
		$array['author_url'] = $nv_Request->get_title( 'author_url', 'post', '' );
		$array['fileupload'] = $nv_Request->get_typed_array( 'fileupload', 'post', 'string' );
		$array['linkdirect'] = $nv_Request->get_typed_array( 'linkdirect', 'post', 'string' );
		$array['version'] = $nv_Request->get_title( 'version', 'post', '', 1 );
		$array['fileimage'] = $nv_Request->get_title( 'fileimage', 'post', '' );
		$array['copyright'] = $nv_Request->get_title( 'copyright', 'post', '', 1 );
		$array['is_del_report'] = $nv_Request->get_int( 'is_del_report', 'post', 0 );
		
		$array['keywords'] = $nv_Request->get_array( 'keywords', 'post', '' );
		$array['keywords'] = implode( ', ', $array['keywords'] );

		$_groups_post = $nv_Request->get_array( 'groups_view', 'post', array() );
		$array['groups_view'] = ! empty( $_groups_post ) ? implode( ',', nv_groups_post( array_intersect( $_groups_post, array_keys( $groups_list ) ) ) ) : '';

		$_groups_post = $nv_Request->get_array( 'groups_download', 'post', array() );
		$array['groups_download'] = ! empty( $_groups_post ) ? implode( ',', nv_groups_post( array_intersect( $_groups_post, array_keys( $groups_list ) ) ) ) : '';

		$_groups_post = $nv_Request->get_array( 'groups_comment', 'post', array() );
		$array['groups_comment'] = ! empty( $_groups_post ) ? implode( ',', nv_groups_post( array_intersect( $_groups_post, array_keys( $groups_list ) ) ) ) : '';

		if( ! empty( $array['author_url'] ) )
		{
			if( ! preg_match( '#^(http|https|ftp|gopher)\:\/\/#', $array['author_url'] ) )
			{
				$array['author_url'] = 'http://' . $array['author_url'];
			}
		}

		$array['filesize'] = 0;
		if( ! empty( $array['fileupload'] ) )
		{
			$fileupload = array_unique( $array['fileupload'] );
			$array['fileupload'] = array();
			foreach( $fileupload as $file )
			{
				if( ! empty( $file ) )
				{
					$file2 = substr( $file, strlen( NV_BASE_SITEURL ) );
					if( file_exists( NV_ROOTDIR . '/' . $file2 ) and ( $filesize = filesize( NV_ROOTDIR . '/' . $file2 ) ) != 0 )
					{
						$array['fileupload'][] = substr( $file, strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR ) );
						$array['filesize'] += $filesize;
					}
				}
			}
		}
		else
		{
			$array['fileupload'] = array();
		}

		// Sort image
		if( ! empty( $array['fileimage'] ) )
		{
			if( ! preg_match( '#^(http|https|ftp|gopher)\:\/\/#', $array['fileimage'] ) )
			{
				$array['fileimage'] = substr( $array['fileimage'], strlen( NV_BASE_SITEURL . NV_UPLOADS_DIR ) );
			}
		}

		if( ! empty( $array['linkdirect'] ) )
		{
			$linkdirect = $array['linkdirect'];
			$array['linkdirect'] = array();
			foreach( $linkdirect as $links )
			{
				$linkdirect2 = array();
				if( ! empty( $links ) )
				{
					$links = nv_nl2br( $links, '<br />' );
					$links = explode( '<br />', $links );
					$links = array_map( 'trim', $links );
					$links = array_unique( $links );

					foreach( $links as $link )
					{
						if( ! preg_match( '#^(http|https|ftp|gopher)\:\/\/#', $link ) )
						{
							$link = 'http://' . $link;
						}
						if( nv_is_url( $link ) )
						{
							$linkdirect2[] = $link;
						}
					}
				}

				if( ! empty( $linkdirect2 ) )
				{
					$array['linkdirect'][] = implode( "\n", $linkdirect2 );
				}
			}
		}
		else
		{
			$array['linkdirect'] = array();
		}
		if( ! empty( $array['linkdirect'] ) )
		{
			$array['linkdirect'] = array_unique( $array['linkdirect'] );
		}

		if( ! empty( $array['linkdirect'] ) and empty( $array['fileupload'] ) )
		{
			$array['filesize'] = $nv_Request->get_float( 'filesize', 'post', 0 );
            $array['filesize'] = intval( $array['filesize'] * 1048576 );
		}

		$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id!=' . $id . ' AND alias= :alias ');
		$stmt->bindParam( ':alias', $array['alias'], PDO::PARAM_STR );
		$stmt->execute();
		$is_exists = $stmt->fetchColumn();

		if( ! $is_exists )
		{
			$stmt = $db->prepare( 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tmp WHERE title= :title');
			$stmt->bindParam( ':title', $array['title'], PDO::PARAM_STR );
			$stmt->execute();
			$is_exists = $stmt->fetchColumn();
		}

		if( empty( $array['title'] ) )
		{
			$is_error = true;
			$error = $lang_module['file_error_title'];
		}
		elseif( $is_exists )
		{
			$is_error = true;
			$error = $lang_module['file_title_exists'];
		}
		elseif( ! empty( $array['author_email'] ) and ( $check_valid_email = nv_check_valid_email( $array['author_email'] ) ) != '' )
		{
			$is_error = true;
			$error = $check_valid_email;
		}
		elseif( ! empty( $array['author_url'] ) and ! nv_is_url( $array['author_url'] ) )
		{
			$is_error = true;
			$error = $lang_module['file_error_author_url'];
		}
		elseif( empty( $array['fileupload'] ) and empty( $array['linkdirect'] ) )
		{
			$is_error = true;
			$error = $lang_module['file_error_fileupload'];
		}
		else
		{
			$array['introtext'] = ! empty( $array['introtext'] ) ? nv_nl2br( $array['introtext'], '<br />' ) : '';
			$array['fileupload'] = ( ! empty( $array['fileupload'] ) ) ? implode( '[NV]', $array['fileupload'] ) : '';
			if( ( ! empty( $array['linkdirect'] ) ) )
			{
				$array['linkdirect'] = array_map( 'nv_nl2br', $array['linkdirect'] );
				$array['linkdirect'] = implode( '[NV]', $array['linkdirect'] );
			}
			else
			{
				$array['linkdirect'] = '';
			}

			$stmt = $db->prepare( "UPDATE " . NV_PREFIXLANG . "_" . $module_data . " SET
				 catid=" . $array['catid'] . ",
				 title= :title,
				 alias= :alias,
				 description= :description,
				 introtext= :introtext,
				 updatetime=" . NV_CURRENTTIME . ",
				 author_name= :author_name,
				 author_email= :author_email,
				 author_url= :author_url,
				 fileupload= :fileupload,
				 linkdirect= :linkdirect,
				 version= :version,
				 filesize=" . $array['filesize'] . ",
				 fileimage= :fileimage,
				 copyright= :copyright,
				 groups_comment= :groups_comment,
				 groups_view= :groups_view,
				 groups_download= :groups_download
				 WHERE id=" . $id );

			$stmt->bindParam( ':title', $array['title'], PDO::PARAM_STR );
			$stmt->bindParam( ':alias', $array['alias'], PDO::PARAM_STR );
			$stmt->bindParam( ':description', $array['description'], PDO::PARAM_STR, strlen( $array['description'] ) );
			$stmt->bindParam( ':introtext', $array['introtext'], PDO::PARAM_STR, strlen( $array['introtext'] ) );
			$stmt->bindParam( ':author_name', $array['author_name'], PDO::PARAM_STR );
			$stmt->bindParam( ':author_email', $array['author_email'], PDO::PARAM_STR );
			$stmt->bindParam( ':author_url', $array['author_url'], PDO::PARAM_STR );
			$stmt->bindParam( ':fileupload', $array['fileupload'], PDO::PARAM_STR, strlen( $array['fileupload'] ) );
			$stmt->bindParam( ':linkdirect', $array['linkdirect'], PDO::PARAM_STR, strlen( $array['linkdirect'] ) );
			$stmt->bindParam( ':version', $array['version'], PDO::PARAM_STR );
			$stmt->bindParam( ':fileimage', $array['fileimage'], PDO::PARAM_STR );
			$stmt->bindParam( ':copyright', $array['copyright'], PDO::PARAM_STR );
			$stmt->bindParam( ':groups_comment', $array['groups_comment'], PDO::PARAM_STR );
			$stmt->bindParam( ':groups_view', $array['groups_view'], PDO::PARAM_STR );
			$stmt->bindParam( ':groups_download', $array['groups_download'], PDO::PARAM_STR );

			if( ! $stmt->execute() )
			{
				$is_error = true;
				$error = $lang_module['file_error1'];
			}
			else
			{
				if( $array['keywords'] != $array['keywords_old'] )
				{
					// keywords
					$keywords = explode( ',', $array['keywords'] );
					$keywords = array_map( 'strip_punctuation', $keywords );
					$keywords = array_map( 'trim', $keywords );
					$keywords = array_diff( $keywords, array( '' ) );
					$keywords = array_unique( $keywords );
					foreach( $keywords as $keyword )
					{
						$alias_i = change_alias( $keyword );
						$alias_i = nv_strtolower( $alias_i );
						$sth = $db->prepare( 'SELECT did, alias, description, keywords FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags where alias= :alias OR FIND_IN_SET(:keyword, keywords)>0' );
						$sth->bindParam( ':alias', $alias_i, PDO::PARAM_STR );
						$sth->bindParam( ':keyword', $keyword, PDO::PARAM_STR );
						$sth->execute( );
	
						list( $did, $alias, $keywords_i ) = $sth->fetch( 3 );
						if( empty( $did ) )
						{
							$array_insert = array( );
							$array_insert['alias'] = $alias_i;
							$array_insert['keyword'] = $keyword;
	
							$did = $db->insert_id( "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_tags (numdownload, alias, description, image, keywords) VALUES (1, :alias, '', '', :keyword)", "did", $array_insert );
						}
						else
						{
							$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags SET numdownload = numdownload+1 WHERE did = ' . $did );
						}
						
						$_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id=' . $id . ' AND did = ' . $did;
						$_query = $db->query( $_sql );
						$row = $_query->fetch();
	
						if( empty($row) )
						{
						  	$sth = $db->prepare( 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id (id, did, keyword) VALUES (' . $id . ', ' . intval( $did ) . ', :keyword)' );
							$sth->bindParam( ':keyword', $keyword, PDO::PARAM_STR );
							$sth->execute( );
						}
						else 
						{
							$sth = $db->prepare( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id SET keyword = :keyword WHERE id = ' . $id . ' AND did=' . intval( $did ) );
							$sth->bindParam( ':keyword', $keyword, PDO::PARAM_STR );
							$sth->execute();
						}
						unset( $array_keywords_old[$did] );
					}
					foreach( $array_keywords_old as $did => $keyword )
					{
						if( ! in_array( $keyword, $keywords ) )
						{
							$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_tags SET numdownload = numdownload-1 WHERE did = ' . $did );
							$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_tags_id WHERE id = ' . $id . ' AND did=' . $did );
						}
					}
				}
				
				if( $report and $array['is_del_report'] )
				{
					$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_report WHERE fid=' . $id );
				}

                nv_del_moduleCache( $module_name );
				nv_insert_logs( NV_LANG_DATA, $module_name, $lang_module['download_editfile'], $array['title'], $admin_info['userid'] );
				Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name );
				exit();
			}
		}

		$array['fileupload'] = ( ! empty( $array['fileupload'] ) ) ? explode( '[NV]', $array['fileupload'] ) : array();
	}
	else
	{
		$array['catid'] = ( int )$row['catid'];
		$array['title'] = $row['title'];
		$array['description'] = nv_editor_br2nl( $row['description'] );
		$array['introtext'] = nv_br2nl( $row['introtext'] );
		$array['author_name'] = $row['author_name'];
		$array['author_email'] = $row['author_email'];
		$array['author_url'] = $row['author_url'];
		$array['fileupload'] = $row['fileupload'];
		$array['linkdirect'] = $row['linkdirect'];
		$array['version'] = $row['version'];
		$array['filesize'] = ( int )$row['filesize'];
		$array['fileimage'] = $row['fileimage'];
		$array['copyright'] = $row['copyright'];
		$array['groups_comment'] = $row['groups_comment'];
		$array['groups_view'] = $row['groups_view'];
		$array['groups_download'] = $row['groups_download'];

		$array['fileupload'] = ! empty( $array['fileupload'] ) ? explode( '[NV]', $array['fileupload'] ) : array();
		if( ! empty( $array['linkdirect'] ) )
		{
			$array['linkdirect'] = explode( '[NV]', $array['linkdirect'] );
			$array['linkdirect'] = array_map( 'nv_br2nl', $array['linkdirect'] );
		}
		else
		{
			$array['linkdirect'] = array();
		}
		$array['is_del_report'] = 1;
	}
	$array['groups_comment'] = ! empty( $array['groups_comment'] ) ? explode( ',', $array['groups_comment'] ) : array( 6 );
	$array['groups_view'] = ! empty( $array['groups_view'] ) ? explode( ',', $array['groups_view'] ) : array( 6 );
	$array['groups_download'] = ! empty( $array['groups_download'] ) ? explode( ',', $array['groups_download'] ) : array( 6 );

	$array['introtext'] = nv_htmlspecialchars( $array['introtext'] );

	$array['fileupload_num'] = sizeof( $array['fileupload'] );
	$array['linkdirect_num'] = sizeof( $array['linkdirect'] );

	// Build fileimage
	if( ! empty( $array['fileimage'] ) )
	{
		if( ! preg_match( '#^(http|https|ftp|gopher)\:\/\/#', $array['fileimage'] ) )
		{
			$array['fileimage'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . $array['fileimage'];
		}
	}

	//Rebuild fileupload
	if( ! empty( $array['fileupload'] ) )
	{
		$fileupload = $array['fileupload'];
		$array['fileupload'] = array();
		foreach( $fileupload as $tmp )
		{
			if( ! preg_match( '#^(http|https|ftp|gopher)\:\/\/#', $tmp ) )
			{
				$tmp = NV_BASE_SITEURL . NV_UPLOADS_DIR . $tmp;
			}
			$array['fileupload'][] = $tmp;
		}
	}

	if( ! sizeof( $array['fileupload'] ) ) array_push( $array['fileupload'], '' );
	if( ! sizeof( $array['linkdirect'] ) ) array_push( $array['linkdirect'], '' );

	$listcats = nv_listcats( $array['catid'] );
	if( empty( $listcats ) )
	{
		Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat&add=1' );
		exit();
	}

	$array['is_del_report'] = $array['is_del_report'] ? ' checked="checked"' : '';

	$groups_comment = $array['groups_comment'];
	$array['groups_comment'] = array();
	foreach( $groups_list as $key => $title )
	{
		$array['groups_comment'][] = array(
			'key' => $key,
			'title' => $title,
			'checked' => in_array( $key, $groups_comment ) ? ' checked="checked"' : ''
		);
	}

	$groups_view = $array['groups_view'];
	$array['groups_view'] = array();
	foreach( $groups_list as $key => $title )
	{
		$array['groups_view'][] = array(
			'key' => $key,
			'title' => $title,
			'checked' => in_array( $key, $groups_view ) ? ' checked="checked"' : ''
		);
	}

	$groups_download = $array['groups_download'];
	$array['groups_download'] = array();
	foreach( $groups_list as $key => $title )
	{
		$array['groups_download'][] = array(
			'key' => $key,
			'title' => $title,
			'checked' => in_array( $key, $groups_download ) ? ' checked="checked"' : ''
		);
	}

	if( defined( 'NV_EDITOR' ) )
	{
		require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
	}
	$array['description'] = htmlspecialchars( nv_editor_br2nl( $array['description'] ) );
	if( defined( 'NV_EDITOR' ) and nv_function_exists( 'nv_aleditor' ) )
	{
		$array['description'] = nv_aleditor( 'description', '100%', '300px', $array['description'] );
	}
	else
	{
		$array['description'] = "<textarea style=\"width:100%; height:300px\" name=\"description\" id=\"description\">" . $array['description'] . "</textarea>";
	}
    $array['id'] = $id;

	if( empty( $array['filesize'] ) )
	{
	    $array['filesize'] = '';
    }
    else
    {
        $array['filesize'] = number_format( $array['filesize']/1048576, 2);
    }
	
	$xtpl = new XTemplate( 'content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );

	$report = $report ? '&amp;report=1' : '';
	$xtpl->assign( 'FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $id . $report );

	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'DATA', $array );
	$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'IMG_DIR', NV_UPLOADS_DIR . '/' . $module_upload . '/images' );
	$xtpl->assign( 'FILES_DIR', NV_UPLOADS_DIR . '/' . $module_upload . '/files' );

	if( ! empty( $error ) )
	{
		$xtpl->assign( 'ERROR', $error );
		$xtpl->parse( 'main.error' );
	}

	foreach( $listcats as $cat )
	{
		$xtpl->assign( 'LISTCATS', $cat );
		$xtpl->parse( 'main.catid' );
	}

	$a = 0;
	foreach( $array['fileupload'] as $file )
	{
		$xtpl->assign( 'FILEUPLOAD', array( 'value' => $file, 'key' => $a ) );
		$xtpl->parse( 'main.fileupload' );
		++$a;
	}

	$a = 0;
	foreach( $array['linkdirect'] as $link )
	{
		$xtpl->assign( 'LINKDIRECT', array( 'value' => $link, 'key' => $a ) );
		$xtpl->parse( 'main.linkdirect' );
		++$a;
	}

	foreach( $array['groups_comment'] as $group )
	{
		$xtpl->assign( 'GROUPS_COMMENT', $group );
		$xtpl->parse( 'main.groups_comment' );
	}

	foreach( $array['groups_view'] as $group )
	{
		$xtpl->assign( 'GROUPS_VIEW', $group );
		$xtpl->parse( 'main.groups_view' );
	}
	
	if( !empty( $array['keywords'] ) )
	{
		$keywords_array = explode( ',', $array['keywords'] );
		foreach( $keywords_array as $keywords )
		{
			$xtpl->assign( 'KEYWORDS', $keywords );
			$xtpl->parse( 'main.keywords' );
		}
	}
	
	foreach( $array['groups_download'] as $group )
	{
		$xtpl->assign( 'GROUPS_DOWNLOAD', $group );
		$xtpl->parse( 'main.groups_download' );
	}

	$xtpl->parse( 'main.is_del_report' );

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );

	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';
	exit();
}

// Avtive - Deactive
if( $nv_Request->isset_request( 'changestatus', 'post' ) )
{
	if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );

	$id = $nv_Request->get_int( 'id', 'post', 0 );

	$query = 'SELECT status FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id;
	$row = $db->query( $query )->fetch();
	if( empty( $row ) ) die( 'NO' );

	$status = $row['status'] ? 0 : 1;

	$db->query( 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . ' SET status=' . $status . ' WHERE id=' . $id );

    nv_del_moduleCache( $module_name );
	die( 'OK' );
}

// Delete file
if( $nv_Request->isset_request( 'del', 'post' ) )
{
	if( ! defined( 'NV_IS_AJAX' ) ) die( 'Wrong URL' );

	$id = $nv_Request->get_int( 'id', 'post', 0 );

	$query = 'SELECT fileupload, fileimage, title FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id;
	$row = $db->query( $query )->fetch();
	if( empty( $row ) ) die( 'NO' );

	$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_comment WHERE module=' . $db->quote( $module_name ) . ' AND id=' . $id );
	$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_report WHERE fid=' . $id );
	$db->query( 'DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE id=' . $id );

	// Xoa thong bao loi
	nv_delete_notification( NV_LANG_DATA, $module_name, 'report', $id );

    nv_del_moduleCache( $module_name );

	nv_insert_logs( NV_LANG_DATA, $module_data, $lang_module['download_filequeue_del'], $row['title'], $admin_info['userid'] );
	die( 'OK' );
}

// List file
$page_title = $lang_module['download_filemanager'];

$where = '';
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
$array_search = array(
	'q' => $nv_Request->get_title( 'q', 'get', '' ),
	'catid' => $nv_Request->get_int( 'catid', 'get', 0 ),
	'active' => $nv_Request->get_int( 'active', 'get', '-1' ),
	'per_page' => $nv_Request->get_int( 'per_page', 'get', '30' )
);

$listcats = nv_listcats( 0 );
if( empty( $listcats ) )
{
	Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat&add=1' );
	exit();
}

if( !empty( $array_search['q'] ) )
{
	$base_url .= '&q=' . $array_search['q'];
	$where .= ' AND title LIKE "%' . $array_search['q'] . '%" OR description LIKE "%' . $array_search['q'] . '%" OR introtext LIKE "%' . $array_search['q'] . '%" OR author_name LIKE "%' . $array_search['q'] . '%" OR author_email LIKE "%' . $array_search['q'] . '%"';
}
if( !empty( $array_search['catid'] ) )
{
	$base_url .= '&catid=' . $array_search['catid'];
	$where .= ' AND catid=' . $array_search['catid'];
}
if( $array_search['active'] >= 0 )
{
	$base_url .= '&active=' . $array_search['active'];
	$where .= ' AND status=' . $array_search['active'];
}

$db->sqlreset()
	->select( 'COUNT(*)' )
	->from( NV_PREFIXLANG . '_' . $module_data )
	->where( '1=1' . $where );

$num_items = $db->query( $db->sql() )->fetchColumn();

$page = $nv_Request->get_int( 'page', 'get', 1 );
$per_page = $array_search['per_page'];

$db->select( '*' )
	->order( 'uploadtime DESC' )
	->limit( $per_page )
	->offset( ( $page - 1 ) * $per_page );

$result2 = $db->query( $db->sql() );

$array = array();

while( $row = $result2->fetch() )
{
	$array[$row['id']] = array(
		'id' => $row['id'],
		'title' => $row['title'],
		'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $listcats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
		'cattitle' => $listcats[$row['catid']]['title'],
		'catlink' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;catid=' . $row['catid'],
		'uploadtime' => nv_date( 'd/m/Y H:i', $row['uploadtime'] ),
		'status' => $row['status'] ? ' checked="checked"' : '',
		'view_hits' => $row['view_hits'],
		'download_hits' => $row['download_hits'],
		'comment_hits' => $row['comment_hits']
	);
}

$generate_page = nv_generate_page( $base_url, $num_items, $per_page, $page );

$xtpl = new XTemplate( 'main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'GLANG', $lang_global );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'OP', $op );
$xtpl->assign( 'SEARCH', $array_search );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'NV_ASSETS_DIR', NV_ASSETS_DIR );

if( ! empty( $array ) )
{
	foreach( $array as $row )
	{
		$xtpl->assign( 'ROW', $row );
		$xtpl->assign( 'EDIT_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $row['id'] );
		$xtpl->parse( 'main.row' );
	}
}

foreach( $listcats as $cat )
{
	$cat['selected'] = $cat['id'] == $array_search['catid'] ? 'selected="selected"' : '';
	$xtpl->assign( 'LISTCATS', $cat );
	$xtpl->parse( 'main.catid' );
}

$array_active = array(
	'1' => $lang_global['yes'],
	'0' => $lang_global['no']
);
foreach( $array_active as $key => $value )
{
	$sl = $array_search['active'] == $key ? 'selected="selected"' : '';
	$xtpl->assign( 'ACTIVE', array( 'key' => $key, 'value' => $value, 'selected' => $sl ) );
	$xtpl->parse( 'main.active' );
}

for( $i = 5; $i <= 300; $i+=5 )
{
	$sl = $array_search['per_page'] == $i ? 'selected="selected"' : '';
	$xtpl->assign( 'PER_PAGE', array( 'key' => $i, 'selected' => $sl ) );
	$xtpl->parse( 'main.per_page' );
}

if( ! empty( $generate_page ) )
{
	$xtpl->assign( 'GENERATE_PAGE', $generate_page );
	$xtpl->parse( 'main.generate_page' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';