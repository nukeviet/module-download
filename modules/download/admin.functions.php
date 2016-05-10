<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/31/2009 2:29
 */

if (! defined('NV_ADMIN') or ! defined('NV_MAINFILE') or ! defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);
require_once NV_ROOTDIR . '/modules/' . $module_file . '/global.functions.php';

$allow_func = array('main', 'content', 'filequeue', 'report', 'config', 'cat', 'cat-content', 'view', 'tags', 'tagsajax', 'change_cat', 'fileserver');

// Load config module
$_sql_config = 'SELECT * FROM ' . NV_MOD_TABLE . '_config ';
$_query_config = $db->query($_sql_config);
while ($row_config = $_query_config->fetch()) {
    $module_config[$module_name][$row_config['config_name']]=$row_config['config_value'];
}

/**
 * get_allow_exts()
 *
 * @return
 */
function get_allow_exts()
{
    global $global_config;

    $all_file_ext = nv_parse_ini_file(NV_ROOTDIR . '/includes/ini/mime.ini', true);
    $file_allowed_ext = ( array )$global_config['file_allowed_ext'];

    $exts = array();
    if (! empty($file_allowed_ext)) {
        foreach ($file_allowed_ext as $type) {
            if (! empty($type) and isset($all_file_ext[$type])) {
                foreach ($all_file_ext[$type] as $e => $m) {
                    if (! in_array($e, $global_config['forbid_extensions']) and ! in_array($m, $global_config['forbid_mimes'])) {
                        $exts[$e] = is_array($m) ? implode(', ', $m) : $m;
                    }
                }
            }
        }
    }

    return $exts;
}

// Check file
if ($nv_Request->isset_request('check', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $url = $nv_Request->get_string('url', 'post', '');
    $is_myurl = $nv_Request->get_int('is_myurl', 'post', 0);

    if (empty($url)) {
        die($lang_module['file_checkUrl_error']);
    }

    $url = rawurldecode($url);

    if ($is_myurl) {
        $url = substr($url, strlen(NV_BASE_SITEURL));
        $url = NV_ROOTDIR . '/' . $url;
        if (! file_exists($url)) {
            die($lang_module['file_checkUrl_error']);
        }
    } else {
        $url = trim($url);
        $url = nv_nl2br($url, '<br />');
        $url = explode('<br />', $url);
        $url = array_map('trim', $url);
        foreach ($url as $l) {
            if (! empty($l)) {
                if (! nv_is_url($l)) {
                    die($lang_module['file_checkUrl_error']);
                }
                if (! nv_check_url($l)) {
                    die($lang_module['file_checkUrl_error']);
                }
            }
        }
    }

    die($lang_module['file_checkUrl_ok']);
}

// Download file
if ($nv_Request->isset_request('fdownload', 'get')) {
    $file = $nv_Request->get_string('fdownload', 'get', '');
    if (! empty($file)) {
        $file = substr($file, strlen(NV_BASE_SITEURL));
        $file = NV_ROOTDIR . '/' . $file;

        $download = new NukeViet\Files\Download($file, NV_UPLOADS_REAL_DIR);

        $download->download_file();
    }
    exit();
}

/**
 * nv_fix_cat_order()
 *
 * @param integer $parentid
 * @param integer $order
 * @param integer $lev
 * @return
 */
function nv_fix_cat_order($parentid = 0, $order = 0, $lev = 0)
{
    global $db;

    $sql = 'SELECT id, parentid FROM ' . NV_MOD_TABLE . '_categories WHERE parentid=' . $parentid . ' ORDER BY weight ASC';
    $result = $db->query($sql);
    $array_cat_order = array();
    while ($row = $result->fetch()) {
        $array_cat_order[] = $row['id'];
    }
    $result->closeCursor();
    $weight = 0;
    if ($parentid > 0) {
        ++$lev;
    } else {
        $lev = 0;
    }
    foreach ($array_cat_order as $catid_i) {
        ++$order;
        ++$weight;
        $sql = 'UPDATE ' . NV_MOD_TABLE . '_categories SET weight=' . $weight . ', sort=' . $order . ', lev=' . $lev . ' WHERE id=' . intval($catid_i);
        $db->query($sql);
        $order = nv_fix_cat_order($catid_i, $order, $lev);
    }
    $numsubcat = $weight;
    if ($parentid > 0) {
        $sql = 'UPDATE ' . NV_MOD_TABLE . '_categories SET numsubcat=' . $numsubcat;
        if ($numsubcat == 0) {
            $sql .= ",subcatid='', viewcat='viewcat_list_new'";
        } else {
            $sql .= ",subcatid='" . implode(',', $array_cat_order) . "'";
        }
        $sql .= ' WHERE id=' . intval($parentid);
        $db->query($sql);
    }
    return $order;
}
