<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 07/30/2013 10:27
 */

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

$NV_MOD_TABLE = (defined('SYS_DOWNLOAD_TABLE')) ? SYS_DOWNLOAD_TABLE : NV_PREFIXLANG . '_' . $module_data;

// Cấu hình module
$_sql_config = 'SELECT * FROM ' . $NV_MOD_TABLE . '_config';
$_query_config = $db->query($_sql_config);
while ($row_config = $_query_config->fetch()) {
    if (preg_match('/^arr\_(dis|req)\_(ad|ur)\_([a-zA-Z0-9\_\-]+)$/', $row_config['config_name'], $m)) {
        if (!isset($module_config[$module_name][$m[1]])) {
            $module_config[$module_name][$m[1]] = array();
        }
        if (!isset($module_config[$module_name][$m[1]][$m[2]])) {
            $module_config[$module_name][$m[1]][$m[2]] = array();
        }
        $module_config[$module_name][$m[1]][$m[2]][$m[3]] = $row_config['config_value'];
    } else {
        $module_config[$module_name][$row_config['config_name']] = $row_config['config_value'];
    }
}

$submenu['content'] = $lang_module['file_addfile'];

if (!empty($module_config[$module_name]['allow_fupload_import'])) {
    $submenu['fimport'] = $lang_module['fimport'];
}

$submenu['filequeue'] = $lang_module['download_filequeue'];
$submenu['report'] = $lang_module['download_report'];
$submenu['tags'] = $lang_module['download_tags'];
$submenu['cat'] = $lang_module['download_catmanager'];
$submenu['config'] = $lang_module['download_config'];
