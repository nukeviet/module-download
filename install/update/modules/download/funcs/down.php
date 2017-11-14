<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:30
 */

if (! defined('NV_IS_MOD_DOWNLOAD')) {
    die('Stop!!!');
}

$session_files = $nv_Request->get_string('session_files', 'session', '');
if (empty($session_files)) {
    die('Wrong URL');
}

$session_files = unserialize($session_files);

if (empty($session_files['id']) or empty($session_files['tokend'])) {
    die('Wrong URL');
}

$row = $db->query("SELECT * FROM " . NV_MOD_TABLE . " WHERE status=1 AND id=" . intval($session_files['id']))->fetch();
if (empty($row)) {
    die('Wrong URL');
}

$row['groups_view'] = '';
$row['groups_download'] = '';

$sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_detail WHERE id=' . $row['id'];
$detail = $db->query($sql)->fetch();

if (!empty($detail)) {
    $row['groups_view'] = $detail['groups_view'];
    $row['groups_download'] = $detail['groups_download'];
    $row['groups_onlineview'] = $detail['groups_onlineview'];
}
unset($detail);

if (!nv_user_in_groups($row['groups_view']) 
    or !nv_user_in_groups($row['groups_download'])
    or !isset($list_cats[$row['catid']]) 
    or !nv_user_in_groups($list_cats[$row['catid']]['groups_view']) 
    or !nv_user_in_groups($list_cats[$row['catid']]['groups_download'])
    or $session_files['tokend'] != md5($global_config['sitekey'] . session_id() . $row['id'] . $row['alias'])) {
    die('Wrong URL');
}

if ($nv_Request->isset_request('code', 'get')) {
    $code = $nv_Request->get_string('code', 'get', '');

    if (empty($code) or ! preg_match("/^([a-z0-9]{32})$/i", $code) or ! isset($session_files['linkdirect'][$code])) {
        die('Wrong URL');
    }
    $_fid = intval($session_files['linkdirect'][$code]['id']);
    $_setdown = $nv_Request->get_int($module_data . '_' . $op . '_setdown_' . $_fid, 'session');
    if (empty($_setdown)) {
        $nv_Request->set_Session($module_data . '_' . $op . '_setdown_' . $_fid, NV_CURRENTTIME);
        $db->query('UPDATE ' . NV_MOD_TABLE . ' SET download_hits=download_hits+1 WHERE id=' . $_fid);
    }

    $content = "<br /><img border=\"0\" src=\"" . NV_BASE_SITEURL . NV_ASSETS_DIR . "/images/load_bar.gif\"><br /><br />\n";
    $content .= sprintf($lang_module['download_wait2'], $session_files['linkdirect'][$code]['link']);
    $content .= "<meta http-equiv=\"refresh\" content=\"5;url=" . $session_files['linkdirect'][$code]['link'] . "\" />";

    nv_info_die($lang_module['download_detail'], $lang_module['download_wait'], $content);
    die();
}

$filename = $nv_Request->get_string('filename', 'get', '');
if (empty($filename) or ! isset($session_files['fileupload'][$filename])) {
    die('Wrong URL');
}

if (! file_exists($session_files['fileupload'][$filename]['src'])) {
    die('Wrong URL');
}

if (! isset($session_files['fileupload'][$filename]['id'])) {
    die('Wrong URL');
}

$upload_dir = 'files';
$is_zip = false;
$is_resume = false;
$max_speed = 0;

$filepdf = $nv_Request->get_int('filepdf', 'get', 0);

if ($filepdf == 1) {
    if (!nv_user_in_groups($row['groups_onlineview']) or !nv_user_in_groups($list_cats[$row['catid']]['groups_onlineview'])) {
        die('Wrong URL');
    }
    
    $download_config = nv_mod_down_config();
    $file_url = '';
    $file_src = $session_files['fileupload'][$filename]['src'];
    
    if ($download_config['pdf_handler'] == 'filetmp') {
        $file_src_new = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . NV_TEMPNAM_PREFIX . md5($file_src) . '.' . nv_getextension($file_src);
        if (file_exists($file_src_new) or nv_copyfile($file_src, $file_src_new)) {
            $file_url = NV_MY_DOMAIN . NV_BASE_SITEURL . substr($file_src_new, strlen(NV_ROOTDIR . '/'));
        }
    } elseif ($download_config['pdf_handler'] == 'base64') {
        $file_url = 'data:application/pdf;base64,' . base64_encode(file_get_contents($file_src));
    } else {
        $file_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=down&filepdf=2&filename=' . $filename;
    }

    if (empty($file_url)) {
        nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'], 404);
    }    
    
    $html = theme_viewpdf($file_url);
    nv_htmlOutput($html);
} elseif (empty($filepdf)) {
    $_fid = intval($session_files['fileupload'][$filename]['id']);
    $_setdown = $nv_Request->get_int($module_data . '_' . $op . '_setdown_' . $_fid, 'session');
    if (empty($_setdown)) {
        $nv_Request->set_Session($module_data . '_' . $op . '_setdown_' . $_fid, NV_CURRENTTIME);
        $db->query('UPDATE ' . NV_MOD_TABLE . ' SET download_hits=download_hits+1 WHERE id=' . $_fid);
    }
}

$sql = "SELECT config_name, config_value FROM " . NV_MOD_TABLE . "_config WHERE config_name='upload_dir' OR config_name='is_zip' OR config_name='is_resume' OR config_name='max_speed'";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    if ($row['config_name'] == 'is_zip') {
        $is_zip = ($filepdf == 2) ? false : ( bool )$row['config_value'];
    } elseif ($row['config_name'] == 'is_resume') {
        $is_resume = ( bool )$row['config_value'];
    } elseif ($row['config_name'] == 'max_speed') {
        $max_speed = ( int )$row['config_value'];
    }
}
$file_src = $session_files['fileupload'][$filename]['src'];
$file_basename = $filename;
$directory = NV_UPLOADS_REAL_DIR;

if ($is_zip) {
    $subfile = nv_pathinfo_filename($filename);
    $tem_file = NV_ROOTDIR . '/' . NV_TEMP_DIR . '/' . NV_TEMPNAM_PREFIX . $subfile;

    $file_exists = file_exists($tem_file);

    if ($file_exists and filemtime($tem_file) > NV_CURRENTTIME - 600) {
        $file_src = $tem_file;
        $file_basename = $subfile . '.zip';
        $directory = NV_ROOTDIR . '/' . NV_TEMP_DIR;
    } else {
        if ($file_exists) {
            @nv_deletefile($tem_file);
        }

        $zip = new PclZip($tem_file);

        $paths = explode('/', $file_src);
        array_pop($paths);
        $paths = implode('/', $paths);
        $zip->add($file_src, PCLZIP_OPT_REMOVE_PATH, $paths);

        if (isset($global_config['site_logo']) and ! empty($global_config['site_logo']) and file_exists(NV_ROOTDIR . '/' . $global_config['site_logo'])) {
            $paths = explode('/', $global_config['site_logo']);
            array_pop($paths);
            $paths = implode('/', $paths);
            $zip->add(NV_ROOTDIR . '/' . $global_config['site_logo'], PCLZIP_OPT_REMOVE_PATH, NV_ROOTDIR . '/' . $paths);
        }

        if (file_exists(NV_ROOTDIR . '/' . NV_DATADIR . '/README.txt')) {
            $zip->add(NV_ROOTDIR . '/' . NV_DATADIR . '/README.txt', PCLZIP_OPT_REMOVE_PATH, NV_ROOTDIR . '/' . NV_DATADIR);
        }

        if (file_exists($tem_file)) {
            $file_src = $tem_file;
            $file_basename = $subfile . '.zip';
            $directory = NV_ROOTDIR . '/' . NV_TEMP_DIR;
        }
    }
}

$download = new NukeViet\Files\Download($file_src, $directory, $file_basename, $is_resume, $max_speed);
if ($is_zip) {
    $mtime = ($mtime = filemtime($session_files['fileupload'][$filename]['src'])) > 0 ? $mtime : NV_CURRENTTIME;
    $download->set_property('mtime', $mtime);
}
$download->download_file();
exit();