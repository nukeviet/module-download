<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/31/2009 0:51
 */

if (!defined('NV_IS_MOD_DOWNLOAD')) {
    die('Stop!!!');
}

/**
 * theme_viewcat_main()
 *
 * @param mixed $viewcat
 * @param mixed $array_cats
 * @param mixed $list_cats
 * @param mixed $download_config
 * @return
 */
function theme_viewcat_main($viewcat, $array_cats, $array_files = array(), $cat_data = array(), $generate_page = '')
{
    global $global_config, $site_mods, $lang_module, $lang_global, $module_info, $module_name, $my_head, $download_config, $list_cats;

    $xtpl = new XTemplate($viewcat . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('IMG_FOLDER', NV_BASE_SITEURL . 'themes/' . $module_info['template'] . '/images/' . $module_info['module_theme'] . '/');
    $xtpl->assign('MODULELINK', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=');

    foreach ($array_cats as $cat) {
        if (empty($cat['parentid'])) {
            if (defined('NV_IS_ADMIN') or $download_config['is_addfile_allow']) {
                if (defined('NV_IS_ADMIN')) {
                    $cat['uploadurl'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;catid=' . $cat['catid'];
                } else {
                    $cat['uploadurl'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module_name]['alias']['upload'] . '/' . $cat['catid'];
                }
            }
            $xtpl->assign('catbox', $cat);

            if (!empty($cat['subcats'])) {
                $i = 0;
                foreach ($list_cats as $subcat) {
                    if ($subcat['parentid'] == $cat['catid']) {
                        $subcat['link'] = NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=" . $module_name . "&amp;" . NV_OP_VARIABLE . "=" . $subcat['alias'];
                        $xtpl->assign('listsubcat', $subcat);

                        if (++$i >= 3) {
                            $xtpl->assign('MORE', $cat['link']);
                            $xtpl->parse('main.catbox.subcatbox.more');
                            break;
                        }

                        $xtpl->parse('main.catbox.subcatbox.listsubcat');
                    }
                }

                $xtpl->parse('main.catbox.subcatbox');
            }

            $items = $cat['items'];
            #parse the first items
            $thefirstcat = current($items);

            $xtpl->assign('itemcat', $thefirstcat);
            if (!empty($thefirstcat['imagesrc'])) {
                $xtpl->parse('main.catbox.itemcat.image');
            }

            if (defined('NV_IS_MODADMIN')) {
                $xtpl->assign('EDIT', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;id=' . $thefirstcat['id']);
                $xtpl->assign('DEL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
                $xtpl->parse('main.catbox.itemcat.adminlink');
            }

            if ($download_config['is_addfile_allow']) {
                $xtpl->parse('main.catbox.is_addfile_allow');
            }

            $xtpl->parse('main.catbox.itemcat');
            foreach ($items as $item) {
                if ($item['id'] != $thefirstcat['id']) {
                    $xtpl->assign('loop', $item);
                    $xtpl->parse('main.catbox.related.loop');
                }
            }
            $xtpl->parse('main.catbox.related');
            $xtpl->parse('main.catbox');
        }
    }

    // Danh sach file trong chu de
    if (!empty($array_files)) {
        if (!empty($cat_data)) {
            $xtpl->assign('CAT_TITLE', sprintf($lang_module['viewcat_listfile'], $cat_data['title']));
        }
        $xtpl->assign('FILE_LIST', theme_viewcat_list($array_files, $generate_page, $cat_data, false));
        $xtpl->parse('main.filelist');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * theme_viewcat_list()
 *
 * @param mixed $array_files
 * @param mixed $page
 * @param mixed $cat_data
 * @return
 */
function theme_viewcat_list($array_files, $page = '', $cat_data = array(), $subcat = true, $upload = true)
{
    global $global_config, $site_mods, $lang_module, $lang_global, $module_info, $module_name, $my_head, $download_config;

    $viewcat = $download_config['viewlist_type'] == 'list' ? 'viewcat_list' : 'viewcat_table';

    if ((defined('NV_IS_ADMIN') or $download_config['is_addfile_allow']) and $upload) {
        if (defined('NV_IS_ADMIN')) {
            $cat_data['uploadurl'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content' . (!empty($cat_data) ? '&amp;catid=' . $cat_data['id'] : '');
        } elseif (!empty($cat_data)) {
            $cat_data['uploadurl'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module_name]['alias']['upload'] . '/' . $cat_data['id'];
        } else {
            $cat_data['uploadurl'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $site_mods[$module_name]['alias']['upload'];
        }
    }

    $xtpl = new XTemplate($viewcat . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('CAT', $cat_data);

    if (!empty($array_files)) {
        foreach ($array_files as $file) {
            if (isset($download_config['list_title_length'])) {
                $file['title0'] = nv_clean60($file['title'], $download_config['list_title_length']);
            } else {
                $file['title0'] = nv_clean60($file['title'], 30);
            }
            $xtpl->assign('FILE', $file);
            $xtpl->parse('main.loop');
        }
    }

    if (!empty($cat_data) and $subcat) {
        $xtpl->parse('main.cat_data');
    }

    if ((defined('NV_IS_ADMIN') or $download_config['is_addfile_allow']) and $upload) {
        $xtpl->parse('main.is_addfile_allow');
    }

    if (!empty($page)) {
        $xtpl->assign('PAGE', $page);
        $xtpl->parse('main.page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * view_file()
 *
 * @param mixed $row
 * @param mixed $download_config
 * @return
 */
function view_file($row, $download_config, $content_comment, $array_keyword)
{
    global $global_config, $lang_global, $lang_module, $module_name, $module_file, $module_info, $my_head;

    $my_head .= "<script type=\"text/javascript\" src=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/star-rating/jquery.rating.pack.js\"></script>\n";
    $my_head .= "<script src=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/star-rating/jquery.MetaData.js\" type=\"text/javascript\"></script>\n";
    $my_head .= "<link href=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/js/star-rating/jquery.rating.css\" type=\"text/css\" rel=\"stylesheet\" />\n";

    $xtpl = new XTemplate('viewfile.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
    $xtpl->assign('MODULE_FILE', $module_file);
    $xtpl->assign('TEMPLATE', $module_info['template']);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('ROW', $row);

    if ($download_config['is_addfile_allow']) {
        $xtpl->assign('UPLOAD', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=upload');
        $xtpl->parse('main.is_addfile_allow');
    }

    if ($download_config['shareport'] == 'addthis') {
        $xtpl->assign('ADDTHIS_PUBID', $download_config['addthis_pubid']);
        $xtpl->parse('main.addthis');
    }

    if (!empty($row['description'])) {
        if (isset($row['fileimage']['src']) and !empty($row['fileimage']['src'])) {
            $xtpl->assign('FILEIMAGE', $row['fileimage']);
            $xtpl->parse('main.introtext.is_image');
        }
        $xtpl->parse('main.introtext');
    }

    if (!empty($row['download_info'])) {
        $xtpl->parse('main.download_info');
    }

    if ($row['scorm_num'] == 1) {
        $xtpl->assign('SCORM_LINK', $row['scorm'][0]);
        $xtpl->parse('main.scorm');
    } elseif ($row['scorm_num'] > 1) {
        $i = 1;
        foreach ($row['scorm'] as $scorm) {
            $xtpl->assign('SCORM_LINK', $scorm);
            $xtpl->assign('SCORM_NUM', $i++);
            $xtpl->parse('main.scorms.loop');
        }

        $xtpl->parse('main.scorms');
    }

    if ($row['is_download_allow'] and (!empty($row['fileupload']) or !empty($row['linkdirect']))) {
        $xtpl->parse('main.report');

        if (!empty($row['filepdf']) and $row['is_onlineview_allow']) {
            $xtpl->assign('FILEPDF', $row['filepdf']);
            $xtpl->parse('main.filepdf');
        }

        if (!empty($row['fileupload'])) {
            $xtpl->assign('SITE_NAME', $global_config['site_name']);

            $a = 0;
            foreach ($row['fileupload'] as $fileupload) {
                $fileupload['key'] = $a;
                $xtpl->assign('FILEUPLOAD', $fileupload);
                $xtpl->parse('main.download_allow.fileupload.row');
                ++$a;
            }

            $xtpl->parse('main.download_allow.fileupload');
        }

        if (!empty($row['linkdirect'])) {
            foreach ($row['linkdirect'] as $host => $linkdirect) {
                $xtpl->assign('HOST', $host);

                foreach ($linkdirect as $link) {
                    $xtpl->assign('LINKDIRECT', $link);
                    $xtpl->parse('main.download_allow.linkdirect.row');
                }

                $xtpl->parse('main.download_allow.linkdirect');
            }
        }

        $xtpl->parse('main.download_allow');
    } else {
        $xtpl->parse('main.download_not_allow');
    }

    if ($row['rating_disabled']) {
        $xtpl->parse('main.disablerating');
    }

    if (!empty($array_keyword)) {
        $t = sizeof($array_keyword) - 1;
        foreach ($array_keyword as $i => $value) {
            $xtpl->assign('KEYWORD', $value['keyword']);
            $xtpl->assign('LINK_KEYWORDS', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . urlencode($value['alias']));
            $xtpl->assign('SLASH', ($t == $i) ? '' : ', ');
            $xtpl->parse('main.keywords.loop');
        }
        $xtpl->parse('main.keywords');
    }

    if (defined('NV_IS_MODADMIN')) {
        $xtpl->parse('main.is_admin');
    }

    if (!empty($content_comment)) {
        $xtpl->assign('CONTENT_COMMENT', $content_comment);
        $xtpl->parse('main.comment');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * theme_upload()
 *
 * @param mixed $array
 * @param mixed $list_cats
 * @param mixed $download_config
 * @param mixed $error
 * @param mixed $array_field_key
 * @return
 */
function theme_upload($array, $list_cats, $download_config, $error, $array_field_key)
{
    global $module_info, $module_name, $lang_module, $lang_global, $global_config;

    $array['parentid'] = 0;
    if ($array['catid'] and isset($list_cats[$array['catid']])) {
        $array['parentid'] = $list_cats[$array['catid']]['parentid'];
    }

    $xtpl = new XTemplate('upload.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
    $xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
    $xtpl->assign('NV_LANG_INTERFACE', NV_LANG_INTERFACE);
    $xtpl->assign('DOWNLOAD', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name);
    $xtpl->assign('UPLOAD', $array);
    $xtpl->assign('FORM_ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=upload');
    $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
    $xtpl->assign('EXT_ALLOWED', implode(', ', $download_config['upload_filetype']));
    $xtpl->assign('NV_CHECK_SESSION', NV_CHECK_SESSION);

    if ($global_config['captcha_type'] == 2) {
        $xtpl->assign('RECAPTCHA_ELEMENT', 'recaptcha' . nv_genpass(8));
        $xtpl->assign('N_CAPTCHA', $lang_global['securitycode1']);
        $xtpl->parse('main.recaptcha');
    } else {
        $xtpl->assign('CAPTCHA_MAXLENGTH', NV_GFX_NUM);
        $xtpl->assign('N_CAPTCHA', $lang_global['securitycode']);
        $xtpl->assign('NV_CURRENTTIME', NV_CURRENTTIME);
        $xtpl->parse('main.captcha');
    }

    if (!empty($error)) {
        $xtpl->assign('ERROR', $error);
        $xtpl->parse('main.is_error');
    }

    foreach ($array_field_key as $field) {
        $xtpl->assign(strtoupper('CSS_' . $field), empty($download_config['dis']['ur'][$field]) ? ' style="display:none;"' : '');
        $xtpl->assign(strtoupper('REQ_' . $field), !empty($download_config['req']['ur'][$field]) ? ' <sup class="text-danger">(*)</sup>' : '');
        $xtpl->assign(strtoupper('REQ_' . $field . '1'), !empty($download_config['req']['ur'][$field]) ? ' required' : '');
    }

    if (!defined('NV_IS_USER')) {
        $xtpl->parse('main.show_username');
    }

    if ($download_config['is_upload_allow']) {
        $xtpl->parse('main.is_upload_allow');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * theme_upload_getcat()
 *
 * @param mixed $parentid
 * @param mixed $catid
 * @param mixed $list_cats_addfile
 * @return
 */
function theme_upload_getcat($parentid, $catid, $list_cats_addfile)
{
    global $module_info, $module_name, $lang_module, $lang_global;

    $xtpl = new XTemplate('upload.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('NV_CHECK_SESSION', NV_CHECK_SESSION);
    $xtpl->assign('CATID', $catid);

    $xtpl->assign('PARENT_TEXT', $parentid ? $list_cats_addfile[$parentid]['title'] : $lang_module['categories_main']);

    foreach ($list_cats_addfile as $cat) {
        if ($cat['parentid'] == $parentid) {
            $cat['checked'] = $cat['id'] == $catid ? ' checked="checked"' : '';
            $xtpl->assign('CAT', $cat);

            if (!empty($cat['parentid'])) {
                $xtpl->assign('PARENTID', $list_cats_addfile[$cat['parentid']]['parentid']);
                $xtpl->parse('cat.loop.loadparentcat');
            }
            if ($cat['numsubcat'] > 0) {
                $xtpl->parse('cat.loop.hassubcat');
            }

            $xtpl->parse('cat.loop');
        }
    }

    $xtpl->parse('cat');
    return $xtpl->text('cat');
}

/**
 * theme_search()
 *
 * @param mixed $array
 * @param mixed $generate_page
 * @param mixed $is_search
 * @return
 */
function theme_search($array, $generate_page, $is_search)
{
    global $module_info, $module_name, $lang_module, $lang_global;

    $xtpl = new XTemplate('search.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);

    if ($is_search) {
        if (!empty($array)) {
            $xtpl->assign('SEARCH', theme_viewcat_list($array, $generate_page, array(), false, false));
            $xtpl->parse('main.is_search.data');
        } else {
            $xtpl->parse('main.is_search.empty');
        }
        $xtpl->parse('main.is_search');
    } else {
        $xtpl->parse('main.not_search');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * theme_viewpdf()
 *
 * @param mixed $file_url
 * @return
 */
function theme_viewpdf($file_url)
{
    global $module_name, $lang_module, $lang_global;
    $xtpl = new XTemplate('viewer.tpl', NV_ROOTDIR . '/' . NV_ASSETS_DIR . '/js/pdf.js');
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);
    $xtpl->assign('PDF_JS_DIR', NV_BASE_SITEURL . NV_ASSETS_DIR . '/js/pdf.js/');
    $xtpl->assign('PDF_URL', $file_url);
    $xtpl->parse('main');
    return $xtpl->text('main');
}

/**
 * nv_download_theme_alert()
 *
 * @param mixed $message_title
 * @param mixed $message_content
 * @param mixed $type
 * @param mixed $url_back
 * @param mixed $time_back
 * @return
 */
function nv_download_theme_alert($message_title, $message_content, $type = 'info', $url_back = '', $time_back = 5, $lang_back = true)
{
    global $module_info, $lang_module, $page_title;

    $xtpl = new XTemplate('alert.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('CONTENT', $message_content);

    if ($type == 'success') {
        $xtpl->parse('main.success');
    } elseif ($type == 'warning') {
        $xtpl->parse('main.warning');
    } elseif ($type == 'danger') {
        $xtpl->parse('main.danger');
    } else {
        $xtpl->parse('main.info');
    }

    if (!empty($message_title)) {
        $page_title = $message_title;
        $xtpl->assign('TITLE', $message_title);
        $xtpl->parse('main.title');
    } else {
        $page_title = $module_info['site_title'];
    }

    if (!empty($url_back)) {
        $xtpl->assign('TIME', $time_back);
        $xtpl->assign('URL', $url_back);
        $xtpl->parse('main.url_back');
        if ($lang_back) {
            $xtpl->parse('main.url_back_button');
        }
    }

    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    include (NV_ROOTDIR . "/includes/header.php");
    echo nv_site_theme($contents);
    include (NV_ROOTDIR . "/includes/footer.php");
    exit();
}

/**
 * view_items_tag()
 *
 * @param mixed $array_item
 * @return
 */
function view_items_tag($array_item, $generate_page)
{
    global $lang_module, $module_info, $module_name, $topicalias, $module_config;

    $xtpl = new XTemplate('topic.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);

    foreach ($array_item as $array_item_i) {
        $array_item_i['uploadtime'] = date('H:i d/m/Y', $array_item_i['uploadtime']);
        $xtpl->assign('ITEM', $array_item_i);

        if (!empty($array_item_i['fileimage'])) {
            $xtpl->parse('main.loop.image');
        }

        $xtpl->parse('main.loop');
    }

    if (!empty($generate_page)) {
        $xtpl->assign('GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.generate_page');
    }

    $xtpl->parse('main');
    return $xtpl->text('main');
}
