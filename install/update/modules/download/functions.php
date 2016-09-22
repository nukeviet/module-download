<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/31/2009 0:51
 */

if (! defined('NV_SYSTEM')) {
    die('Stop!!!');
}

define('NV_IS_MOD_DOWNLOAD', true);
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';

/**
 * nv_mod_down_config()
 *
 * @return
 */
function nv_mod_down_config()
{
    global $module_name, $module_name, $nv_Cache;

    $sql = 'SELECT config_name,config_value FROM ' . NV_MOD_TABLE . '_config';
    $list = $nv_Cache->db($sql, '', $module_name);

    $download_config = array();
    foreach ($list as $values) {
        $download_config[$values['config_name']] = $values['config_value'];
    }

    $download_config['upload_filetype'] = ! empty($download_config['upload_filetype']) ? explode(',', $download_config['upload_filetype']) : array();
    if (! empty($download_config['upload_filetype'])) {
        $download_config['upload_filetype'] = array_map('trim', $download_config['upload_filetype']);
    }

    if ($download_config['is_addfile']) {
        $download_config['is_addfile_allow'] = nv_user_in_groups($download_config['groups_addfile']);
    } else {
        $download_config['is_addfile_allow'] = false;
    }

    if ($download_config['is_addfile_allow']) {
        $download_config['is_upload_allow'] = nv_user_in_groups($download_config['groups_upload']);
    } else {
        $download_config['is_upload_allow'] = false;
    }

    return $download_config;
}

$list_cats_tmp = $list_cats;
$list_cats = array();
if (!empty($list_cats_tmp)) {
    foreach ($list_cats_tmp as $catid => $catvalue) {
        if (! $catvalue['parentid'] or isset($list_cats_tmp[$catvalue['parentid']])) {
            if (nv_user_in_groups($catvalue['groups_view'])) {
                $catvalue['is_download_allow'] =  nv_user_in_groups($catvalue['groups_download']);
                $catvalue['is_onlineview_allow'] =  nv_user_in_groups($catvalue['groups_onlineview']);
                $list_cats[$catid] = $catvalue;
            }
        }
    }
    unset($list_cats_tmp);
}

if ($op == 'main') {
    $page = 1; // Trang mặc định
    $catalias = '';
    $filealias = '';
    $catid = 0;
    $nv_vertical_menu = array();

    if (! empty($list_cats)) {
        if (! empty($array_op)) {
            $catalias = isset($array_op[0]) ? $array_op[0] : '';
            $filealias = isset($array_op[1]) ? $array_op[1] : '';
        }

        // Xac dinh ID cua chu de
        foreach ($list_cats as $c) {
            if ($c['alias'] == $catalias) {
                $catid = intval($c['id']);
                break;
            }
        }
        //Het Xac dinh ID cua chu de

        //Xac dinh menu, RSS
        if ($module_info['rss']) {
            $rss[] = array(
                'title' => $module_info['custom_title'],
                'src' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['rss']
            );
        }

        foreach ($list_cats as $c) {
            if ($c['parentid'] == 0) {
                $sub_menu = array();
                $act = ($c['id'] == $catid) ? 1 : 0;
                if (!empty($c['subcatid']) and $act or ($catid > 0 and $c['id'] == $list_cats[$catid]['parentid'])) {
                    $c['subcatid'] = explode(',', $c['subcatid']);
                    foreach ($c['subcatid'] as $catid_i) {
                        $s_c = $list_cats[$catid_i];
                        $s_act = ($s_c['alias'] == $catalias) ? 1 : 0;
                        $s_link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $s_c['alias'];
                        $sub_menu[] = array( $s_c['title'], $s_link, $s_act );
                    }
                }

                $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $c['alias'];
                $nv_vertical_menu[] = array( $c['title'], $link, $act, 'submenu' => $sub_menu );
            }
            if ($module_info['rss']) {
                $rss[] = array(
                    'title' => $module_info['custom_title'] . ' - ' . $c['title'],
                    'src' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $module_info['alias']['rss'] . '/' . $c['alias']
                );
            }
        }
        //het Xac dinh menu, RSS
        //Xem chi tiet
        if ($catid > 0) {
            $op = 'viewcat';
            $page = 1;
            if (preg_match('/^page\-([0-9]+)$/', $filealias, $m)) {
                $page = intval($m[1]);
            } elseif (! empty($filealias)) {
                $op = 'viewfile';
            }
            $parentid = $catid;
            while ($parentid > 0) {
                $c = $list_cats[$parentid];
                $array_mod_title[] = array(
                    'catid' => $parentid,
                    'title' => $c['title'],
                    'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $c['alias']
                );
                $parentid = $c['parentid'];
            }
            sort($array_mod_title, SORT_NUMERIC);
        } elseif (preg_match('/^page\-([0-9]+)$/', (isset($array_op[0]) ? $array_op[0] : ''), $m)) {
            $page = ( int )$m[1];
        }
    }
}
