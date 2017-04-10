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
    if (preg_match('/^arr\_(dis|req)\_(ad|ur)\_([a-zA-Z0-9\_\-]+)$/', $row_config['config_name'], $m)) {
        $module_config[$module_name][$m[1]][$m[2]][$m[3]] = $row_config['config_value'];
    } else {
        $module_config[$module_name][$row_config['config_name']] = $row_config['config_value'];
    }
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


/**
 * nv_get_viewImage()
 *
 * @param mixed $fileName
 * @return
 */
function nv_get_viewImage($fileName)
{
    global $db;
    
    $sql = 'SELECT * FROM ' . NV_UPLOAD_GLOBALTABLE . '_dir ORDER BY dirname ASC';
    $result = $db->query($sql);
    
    $array_thumb_config = array();
    while ($row = $result->fetch()) {
        if ($row['thumb_type']) {
            $array_thumb_config[$row['dirname']] = $row;
        }
    }
    
    if (preg_match('/^' . nv_preg_quote(NV_UPLOADS_DIR) . '\/(([a-z0-9\-\_\/]+\/)*([a-z0-9\-\_\.]+)(\.(gif|jpg|jpeg|png|bmp|ico)))$/i', $fileName, $m)) {
        $viewFile = NV_FILES_DIR . '/' . $m[1];

        if (file_exists(NV_ROOTDIR . '/' . $viewFile)) {
            $size = @getimagesize(NV_ROOTDIR . '/' . $viewFile);
            return array( $viewFile, $size[0], $size[1] );
        } else {
            $m[2] = rtrim($m[2], '/');

            if (isset($array_thumb_config[NV_UPLOADS_DIR . '/' . $m[2]])) {
                $thumb_config = $array_thumb_config[NV_UPLOADS_DIR . '/' . $m[2]];
            } else {
                $thumb_config = $array_thumb_config[''];
                $_arr_path = explode('/', NV_UPLOADS_DIR . '/' . $m[2]);
                while (sizeof($_arr_path) > 1) {
                    array_pop($_arr_path);
                    $_path = implode('/', $_arr_path);
                    if (isset($array_thumb_config[$_path])) {
                        $thumb_config = $array_thumb_config[$_path];
                        break;
                    }
                }
            }

            $viewDir = NV_FILES_DIR;
            if (! empty($m[2])) {
                if (! is_dir(NV_ROOTDIR . '/' . $m[2])) {
                    $e = explode('/', $m[2]);
                    $cp = NV_FILES_DIR;
                    foreach ($e as $p) {
                        if (is_dir(NV_ROOTDIR . '/' . $cp . '/' . $p)) {
                            $viewDir .= '/' . $p;
                        } else {
                            $mk = nv_mkdir(NV_ROOTDIR . '/' . $cp, $p);
                            if ($mk[0] > 0) {
                                $viewDir .= '/' . $p;
                            }
                        }
                        $cp .= '/' . $p;
                    }
                }
            }
            $image = new NukeViet\Files\Image(NV_ROOTDIR . '/' . $fileName, NV_MAX_WIDTH, NV_MAX_HEIGHT);
            if ($thumb_config['thumb_type'] == 4) {
                $thumb_width = $thumb_config['thumb_width'];
                $thumb_height = $thumb_config['thumb_height'];
                $maxwh = max($thumb_width, $thumb_height);
                if ($image->fileinfo['width'] > $image->fileinfo['height']) {
                    $thumb_config['thumb_width'] = 0;
                    $thumb_config['thumb_height'] = $maxwh;
                } else {
                    $thumb_config['thumb_width'] = $maxwh;
                    $thumb_config['thumb_height'] = 0;
                }
            }
            if ($image->fileinfo['width'] > $thumb_config['thumb_width'] or $image->fileinfo['height'] > $thumb_config['thumb_height']) {
                $image->resizeXY($thumb_config['thumb_width'], $thumb_config['thumb_height']);
                if ($thumb_config['thumb_type'] == 4) {
                    $image->cropFromCenter($thumb_width, $thumb_height);
                }
                $image->save(NV_ROOTDIR . '/' . $viewDir, $m[3] . $m[4], $thumb_config['thumb_quality']);
                $create_Image_info = $image->create_Image_info;
                $error = $image->error;
                $image->close();
                if (empty($error)) {
                    return array( $viewDir . '/' . basename($create_Image_info['src']), $create_Image_info['width'], $create_Image_info['height'] );
                }
            } elseif (copy(NV_ROOTDIR . '/' . $fileName, NV_ROOTDIR . '/' . $viewDir . '/' . $m[3] . $m[4])) {
                $return = array( $viewDir . '/' . $m[3] . $m[4], $image->fileinfo['width'], $image->fileinfo['height'] );
                $image->close();
                return $return;
            } else {
                return false;
            }
        }
    } else {
        $size = @getimagesize(NV_ROOTDIR . '/' . $fileName);
        return array( $fileName, $size[0], $size[1] );
    }
    return false;
}
