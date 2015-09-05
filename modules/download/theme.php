<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/31/2009 0:51
 */

if( ! defined( 'NV_IS_MOD_DOWNLOAD' ) ) die( 'Stop!!!' );

/**
 * theme_viewcat_main()
 *
 * @param mixed $viewcat
 * @param mixed $array_cats
 * @param mixed $list_cats
 * @param mixed $download_config
 * @return
 */
function theme_viewcat_main( $viewcat, $array_cats )
{
	global $global_config, $lang_module, $lang_global, $module_info, $module_name, $module_file, $my_head, $download_config, $list_cats;

	$xtpl = new XTemplate( $viewcat . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/' );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'IMG_FOLDER', NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/download/' );
	$xtpl->assign( 'MODULELINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' );

	foreach( $array_cats as $cat )
	{
		if( empty( $cat['parentid'] ) )
		{
			$xtpl->assign( 'catbox', $cat );

			if( ! empty( $cat['subcats'] ) )
			{
				$i = 0;
				foreach( $list_cats as $subcat )
				{
					if( $subcat['parentid'] == $cat['id'] )
					{
						$subcat['link'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $subcat['alias'];
						$xtpl->assign( 'listsubcat', $subcat );

						if( ++$i >= 4 )
						{
							$xtpl->assign( 'MORE', $cat['link'] );
							$xtpl->parse( 'main.catbox.subcatbox.more' );
							break;
						}

						$xtpl->parse( 'main.catbox.subcatbox.listsubcat' );
					}
				}
				$xtpl->parse( 'main.catbox.subcatbox' );
			}
			$items = $cat['items'];
			#parse the first items
			$thefirstcat = current( $items );

			$xtpl->assign( 'itemcat', $thefirstcat );
			if( ! empty( $thefirstcat['imagesrc'] ) )
			{
				$xtpl->parse( 'main.catbox.itemcat.image' );
			}

			if( defined( 'NV_IS_MODADMIN' ) )
			{
				$xtpl->assign( 'EDIT', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $thefirstcat['id'] );
				$xtpl->assign( 'DEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name );
				$xtpl->parse( 'main.catbox.itemcat.adminlink' );
			}

			$xtpl->parse( 'main.catbox.itemcat' );
			foreach( $items as $item )
			{
				if( $item['id'] != $thefirstcat['id'] )
				{
					$xtpl->assign( 'loop', $item );
					$xtpl->parse( 'main.catbox.related.loop' );
				}
			}
			$xtpl->parse( 'main.catbox.related' );
			$xtpl->parse( 'main.catbox' );
		}
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

/**
 * theme_viewcat_list()
 *
 * @param mixed $array_files
 * @param mixed $page
 * @param mixed $cat_data
 * @return
 */
function theme_viewcat_list( $array_files, $page = '', $cat_data = array() )
{
	global $global_config, $lang_module, $lang_global, $module_info, $module_name, $module_file, $my_head, $download_config;

	$xtpl = new XTemplate( 'viewcat_list.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/' );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'CAT', $cat_data );

	if( !empty( $array_files ) )
	{
		foreach( $array_files as $file )
		{
			$file['title0'] = nv_clean60( $file['title'], 30 );
			$xtpl->assign( 'FILE', $file );
			$xtpl->parse( 'main.loop' );
		}
	}

	if( !empty( $cat_data ) )
	{
		$xtpl->parse( 'main.cat_data' );
	}

	if( !empty( $page ) )
	{
		$xtpl->assign( 'PAGE', $page );
		$xtpl->parse( 'main.page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function theme_viewcat_catmain( $viewcat, $array, $subs, $generate_page )
{
	global $global_config, $lang_module, $lang_global, $module_info, $module_name, $module_file, $download_config, $list_cats;

	$xtpl = new XTemplate( 'viewcat_page.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/' );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'IMG_FOLDER', NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/download/' );
	$xtpl->assign( 'MODULELINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' );

	if( $download_config['is_addfile_allow'] )
	{
		$xtpl->assign( 'UPLOAD', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=upload' );
		$xtpl->parse( 'main.is_addfile_allow' );
	}

	// Hien thi chu de con
	if( ! empty( $subs ) )
	{
		if( $viewcat == 'viewcat_main_bottom' )
		{
			$xtpl->assign( 'SUBCAT', theme_viewcat_main( $viewcat, $subs ) );
			$xtpl->parse( 'main.viewcat_main' );
		}
		elseif( $viewcat == 'viewcat_list_new' )
		{
			foreach( $subs as $subcat )
			{
				if( !empty( $subcat['items'] ) )
				{
					$xtpl->assign( 'SUBCAT', theme_viewcat_list( $subcat['items'], '', $subcat ) );
					$xtpl->parse( 'main.viewcat_list' );
				}
			}
		}
	}

	// Danh sach file trong chu de
	if( ! empty( $array ) )
	{
		foreach( $array as $row )
		{
			$xtpl->assign( 'listpostcat', $row );

			if( ! empty( $row['author_name'] ) )
			{
				$xtpl->parse( 'main.row.author_name' );
			}

			if( ! empty( $row['imagesrc'] ) )
			{
				$xtpl->parse( 'main.listpostcat.image' );
			}

			if( ! empty( $row['edit_link'] ) )
			{
				$xtpl->parse( 'main.listpostcat.is_admin' );
			}

			$xtpl->parse( 'main.listpostcat' );
		}
	}

	if( ! empty( $generate_page ) )
	{
		$xtpl->assign( 'GENERATE_PAGE', $generate_page );
		$xtpl->parse( 'main.generate_page' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

/**
 * view_file()
 *
 * @param mixed $row
 * @param mixed $download_config
 * @return
 */
function view_file( $row, $download_config, $content_comment )
{
	global $global_config, $lang_global, $lang_module, $module_name, $module_file, $module_info, $my_head;

	$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/star-rating/jquery.rating.pack.js\"></script>\n";
	$my_head .= "<script src=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/star-rating/jquery.MetaData.js\" type=\"text/javascript\"></script>\n";
	$my_head .= "<link href=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/star-rating/jquery.rating.css\" type=\"text/css\" rel=\"stylesheet\" />\n";

	$xtpl = new XTemplate( 'viewfile.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/' );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'ROW', $row );

	if( $download_config['is_addfile_allow'] )
	{
		$xtpl->assign( 'UPLOAD', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=upload' );
		$xtpl->parse( 'main.is_addfile_allow' );
	}

	if( ! empty( $row['description'] ) )
	{
		if( isset( $row['fileimage']['src'] ) and ! empty( $row['fileimage']['src'] ) )
		{
			$xtpl->assign( 'FILEIMAGE', $row['fileimage'] );
			$xtpl->parse( 'main.introtext.is_image' );
		}
		$xtpl->parse( 'main.introtext' );
	}

    if( ! empty( $row['download_info'] ) )
    {
        $xtpl->parse( 'main.download_info' );
    }

	if( $row['is_download_allow'] )
	{
		$xtpl->parse( 'main.report' );
		if( ! empty( $row['filepdf'] ) )
		{
			$xtpl->assign( 'FILEPDF', $row['filepdf'] );
			$xtpl->parse( 'main.filepdf' );
		}

		if( ! empty( $row['fileupload'] ) )
		{
			$xtpl->assign( 'SITE_NAME', $global_config['site_name'] );

			$a = 0;
			foreach( $row['fileupload'] as $fileupload )
			{
				$fileupload['key'] = $a;
				$xtpl->assign( 'FILEUPLOAD', $fileupload );
				$xtpl->parse( 'main.download_allow.fileupload.row' );
				++$a;
			}

			$xtpl->parse( 'main.download_allow.fileupload' );
		}

		if( ! empty( $row['linkdirect'] ) )
		{
			foreach( $row['linkdirect'] as $host => $linkdirect )
			{
				$xtpl->assign( 'HOST', $host );

				foreach( $linkdirect as $link )
				{
					$xtpl->assign( 'LINKDIRECT', $link );
					$xtpl->parse( 'main.download_allow.linkdirect.row' );
				}

				$xtpl->parse( 'main.download_allow.linkdirect' );
			}
		}

		$xtpl->parse( 'main.download_allow' );
	}
	else
	{
		$xtpl->parse( 'main.download_not_allow' );
	}

	if( $row['rating_disabled'] )
	{
		$xtpl->parse( 'main.disablerating' );
	}

	if( defined( 'NV_IS_MODADMIN' ) )
	{
		$xtpl->parse( 'main.is_admin' );
	}

	if( !empty( $content_comment ) )
	{
		$xtpl->assign( 'CONTENT_COMMENT', $content_comment );
		$xtpl->parse( 'main.comment' );
	}

	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

/**
 * theme_upload()
 *
 * @param mixed $array
 * @param mixed $list_cats
 * @param mixed $download_config
 * @param mixed $error
 * @return
 */
function theme_upload( $array, $list_cats, $download_config, $error )
{
	global $module_info, $module_name, $module_file, $lang_module, $lang_global, $my_head;

	$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/jquery/jquery.validate.min.js\"></script>\n";
	$my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/language/jquery.validator-" . NV_LANG_INTERFACE . ".js\"></script>\n";
	$my_head .= "<script type=\"text/javascript\">\n";
	$my_head .= "$(document).ready(function(){
    $('#uploadForm').validate({
        rules: {
        upload_title: {
        required: true,
        rangelength: [3, 255]
    },

    upload_author_name: {
        rangelength: [3, 100]
    },

    upload_author_email: {
        email: true
    },

    upload_author_url: {
        url: true
    },

    upload_fileupload: {
        accept: '" . implode( "|", $download_config['upload_filetype'] ) . "'
    },

    upload_filesize: {
        number: true
    },

    upload_fileimage: {
        accept: 'jpg|gif|png'
    },

    upload_introtext: {
        maxlength: 500
    },

    upload_description: {
        maxlength: 5000
    },

    upload_user_name: {
        required: true,
        rangelength: [3, 60]
    },

    upload_seccode: {
        required: true,
        minlength: 6
    }
    }
    });
    });";
	$my_head .= " </script>\n";

	$xtpl = new XTemplate( 'upload.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file . '/' );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'DOWNLOAD', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name );
	$xtpl->assign( 'UPLOAD', $array );
	$xtpl->assign( 'FORM_ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=upload' );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'CAPTCHA_MAXLENGTH', NV_GFX_NUM );
	$xtpl->assign( 'EXT_ALLOWED', implode( ', ', $download_config['upload_filetype'] ) );

	if( ! empty( $error ) )
	{
		$xtpl->assign( 'ERROR', $error );
		$xtpl->parse( 'main.is_error' );
	}

	foreach( $list_cats as $cat )
	{
		$cat['selected'] = $array['catid'] == $cat['id'] ? " selected=\"selected\"" : "";
		$xtpl->assign( 'LISTCATS', $cat );
		$xtpl->parse( 'main.catid' );
	}
	if( $download_config['is_upload_allow'] )
	{
		$xtpl->parse( 'main.is_upload_allow' );
	}
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}

function theme_viewpdf( $filename )
{
	global $module_name, $lang_module;

	$xtpl = new XTemplate( 'viewer.tpl', NV_ROOTDIR . '/themes/default/js/pdf.js/' );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'PDF_JS_DIR', NV_BASE_SITEURL . 'themes/default/js/pdf.js/' );
	$xtpl->assign( 'PDF_URL', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=down&filepdf=2&filename=' . $filename );
	$xtpl->parse( 'main' );
	return $xtpl->text( 'main' );
}