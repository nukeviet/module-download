<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-9-2010 14:43
 */

if (! defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Delete file
if ($nv_Request->isset_request('del', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $id = $nv_Request->get_int('id', 'post', 0);

    $query = $db->query('SELECT id, fileupload, fileimage FROM ' . NV_MOD_TABLE . '_tmp WHERE id=' . $id);
    list($id, $fileupload, $fileimage) = $query->fetch(3);
    if (empty($id)) {
        die('NO');
    }

    if (! empty($fileupload)) {
        $fileupload = explode('[NV]', $fileupload);
        foreach ($fileupload as $file) {
            $file = NV_UPLOADS_DIR . $file;
            if (file_exists(NV_ROOTDIR . '/' . $file)) {
                @nv_deletefile(NV_ROOTDIR . '/' . $file);
            }
        }
    }

    if (! empty($fileimage)) {
        $fileimage = NV_UPLOADS_DIR . $fileimage;
        if (file_exists(NV_ROOTDIR . '/' . $fileimage)) {
            @nv_deletefile(NV_ROOTDIR . '/' . $fileimage);
        }
    }

    $sql = 'DELETE FROM ' . NV_MOD_TABLE . '_tmp WHERE id=' . $id;
    if ($db->query($sql)) {
        nv_status_notification(NV_LANG_DATA, $module_name, 'upload_new', $id);
    }

    die('OK');
}

// All del
if ($nv_Request->isset_request('alldel', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $query = 'SELECT fileupload, fileimage FROM ' . NV_MOD_TABLE . '_tmp';
    $result = $db->query($query);
    while (list($fileupload, $fileimage) = $result->fetch(3)) {
        if (! empty($fileupload)) {
            $fileupload = explode('[NV]', $fileupload);
            foreach ($fileupload as $file) {
                $file = NV_UPLOADS_DIR . $file;
                if (file_exists(NV_ROOTDIR . '/' . $file)) {
                    @nv_deletefile(NV_ROOTDIR . '/' . $file);
                }
            }
        }

        if (! empty($fileimage)) {
            $fileimage = NV_UPLOADS_DIR . $fileimage;
            if (file_exists(NV_ROOTDIR . '/' . $fileimage)) {
                @nv_deletefile(NV_ROOTDIR . '/' . $fileimage);
            }
        }
    }

    $result = $db->query('SELECT id FROM ' . NV_MOD_TABLE . '_tmp');
    while (list($_id) = $result->fetch(3)) {
        nv_status_notification(NV_LANG_DATA, $module_name, 'upload_new', $_id);
    }

    $db->query('TRUNCATE TABLE ' . NV_MOD_TABLE . '_tmp');

    die('OK');
}

// List files
$page_title = $lang_module['download_filequeue'];

$sql = 'FROM ' . NV_MOD_TABLE . '_tmp';

$sql1 = 'SELECT COUNT(*) ' . $sql;
$result1 = $db->query($sql1);
$all_file = $result1->fetchColumn();

if (! $all_file) {
    $contents = "<div style=\"padding-top:15px;text-align:center\">\n";
    $contents .= "<strong>" . $lang_module['filequeue_empty'] . "</strong>";
    $contents .= "</div>\n";
    $contents .= "<meta http-equiv=\"refresh\" content=\"2;url=" . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "\" />";

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

if (empty($list_cats)) {
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat-content');
    exit();
}

$sql2 = 'SELECT * ' . $sql . ' ORDER BY uploadtime DESC';
$result2 = $db->query($sql2);

$array = array();

while ($row = $result2->fetch()) {
    $array[$row['id']] = array(
        'id' => ( int )$row['id'],
        'title' => $row['title'],
        'cattitle' => $list_cats[$row['catid']]['title'],
        'catlink' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;catid=' . $row['catid'],
        'uploadtime' => nv_date('d/m/Y H:i', $row['uploadtime'])
    );
}

$xtpl = new XTemplate('filequeue.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('TABLE_CAPTION', $page_title);

if (! empty($array)) {
    $a = 0;
    foreach ($array as $row) {
        $xtpl->assign('ROW', $row);
        $xtpl->assign('EDIT_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;filequeueid=' . $row['id']);
        $xtpl->parse('main.row');
        ++$a;
    }
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
