<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-9-2010 14:43
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['fimport'];

$array = array(
    'catprocess' => $nv_Request->get_int('catprocess', 'post', 0),
    'status' => $nv_Request->get_int('status', 'post', 0),
    'mode' => $nv_Request->get_int('mode', 'post', 0)
);

$error = '';
$success = '';
$array_stat = array(
    'cat_add' => 0,
    'cat_ignore' => 0,
    'file_add' => 0,
    'file_ignore' => 0
);

$list_cats_alias = array();
foreach ($list_cats as $cat) {
    $list_cats_alias[md5(strtolower($cat['alias']))] = $cat['id'];
}

$currentpath = getUploadcurrentPath();
$currentpath_images = $currentpath[0];
$currentpath_files = $currentpath[1];
$uploads_dir_user = $currentpath[2];

if ($nv_Request->isset_request('submit', 'post')) {
    if ($sys_info['allowed_set_time_limit']) {
        set_time_limit(0);
    }

    $array_foldercat = nv_scandir(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/import', '/(.*)/');
    $array_folders = array();
    foreach ($array_foldercat as $name) {
        if (is_dir(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/import/' . $name)) {
            $array_folders[] = $name;
        }
    }

    if (empty($array_folders)) {
        $error = $lang_module['fimport_error_nofolder'];
    } else {
        asort($array_folders);
        foreach ($array_folders as $folder) {
            // Xác định tên chuyên mục, liên kết tĩnh
            if ($array['catprocess']) {
                $cat_title = $folder;
            } else {
                $cat_title = trim(preg_replace('/^([0-9\.]*)(.*)$/', '\\2', $folder));
            }
            $cat_alias = strtolower(change_alias($cat_title));
            $cat_alias_key = md5($cat_alias);

            // Tạo mới chủ đề nếu chưa có hoặc thiết lập luôn luôn tạo
            if (!isset($list_cats_alias[$cat_alias_key]) or $array['mode']) {
                // Xác định liên kết tĩnh không bị trùng
                $stt = 0;
                $cat_alias_check = $cat_alias;
                while ($db->query("SELECT COUNT(*) FROM " . NV_MOD_TABLE . "_categories WHERE alias=" . $db->quote($cat_alias_check))->fetchColumn() > 0) {
                    $stt++;
                    $cat_alias_check = $cat_alias . '-' . $stt;
                }
                $cat_alias = $cat_alias_check;

                // Xác định thứ tự mới
                $sql = 'SELECT MAX(weight) AS new_weight FROM ' . NV_MOD_TABLE . '_categories WHERE parentid=0';
                $result = $db->query($sql);
                $new_weight = $result->fetchColumn();
                $new_weight = (int)$new_weight;
                ++$new_weight;

                $sql = 'INSERT INTO ' . NV_MOD_TABLE . '_categories (
                    parentid, title, alias, description, groups_view, groups_onlineview, groups_download, groups_addfile, weight, status
                ) VALUES (
                     0,
                     :title,
                     :alias,
                     :description,
                     :groups_view,
                     :groups_onlineview,
                     :groups_download,
                     :groups_addfile,
                     ' . $new_weight . ',
                     1
                )';

                $data_insert = array();
                $data_insert['title'] = $cat_title;
                $data_insert['alias'] = $cat_alias;
                $data_insert['description'] = '';
                $data_insert['groups_view'] = '6';
                $data_insert['groups_onlineview'] = '6';
                $data_insert['groups_download'] = '6';
                $data_insert['groups_addfile'] = '1';

                $new_catid = $db->insert_id($sql, 'id', $data_insert);

                if ($new_catid) {
                    $catid = $new_catid;
                    nv_fix_cat_order();
                    $array_stat['cat_add']++;
                }
            } else {
                $array_stat['cat_ignore']++;
                $catid = $list_cats_alias[$cat_alias_key];
            }

            // Sau khi đã xác định được chủ đề
            if ($catid) {
                $folder = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/import/' . $folder;
                $array_foldercontent = nv_scandir($folder, '/(.*)/');

                foreach ($array_foldercontent as $filename) {
                    if (is_dir($folder . '/' . $filename)) {
                        $files = list_all_file($folder . '/' . $filename, $filename);
                    } else {
                        $files = array($filename);
                    }
                    if (!empty($files)) {
                        $now_title = preg_replace('/\.(.*)$/', '', $filename);
                        $row_alias = change_alias($now_title);
                        $row_status = $array['status'] ? 0 : 1;

                        // Kiểm tra trùng
                        if (!$array['mode']) {
                            $is_exists = $db->query("SELECT COUNT(*) FROM " . NV_MOD_TABLE . " WHERE alias=" . $db->quote($row_alias))->fetchColumn();
                        } else {
                            $is_exists = false;
                        }

                        if (!$is_exists) {
                            // Di chuyển file trước thành công rồi mới ghi vào CSDL
                            $allFileComplete = array();
                            foreach ($files as $file) {
                                $filePath = $folder . '/' . $file;
                                $filename = string_to_filename(basename($filePath));
                                $filename2 = $filename;
                                $i = 1;
                                while (file_exists(NV_ROOTDIR . '/' . $currentpath_files . '/' . $filename2)) {
                                    $filename2 = preg_replace('/(.*)(\.[a-zA-Z0-9]+)$/', '\1_' . $i . '\2', $filename);
                                    ++$i;
                                }
                                $filename = $filename2;
                                if (nv_copyfile($filePath, NV_ROOTDIR . '/' . $currentpath_files . '/' . $filename)) {
                                    $allFileComplete[] = array(
                                        'path' => substr($currentpath_files . '/' . $filename, strlen(NV_UPLOADS_DIR)),
                                        'size' => filesize(NV_ROOTDIR . '/' . $currentpath_files . '/' . $filename)
                                    );
                                }
                            }

                            if (sizeof($allFileComplete) == sizeof($files)) {
                                // Xác định liên kết tĩnh không trùng
                                $stt = 0;
                                $row_alias_check = $row_alias;
                                while ($db->query("SELECT COUNT(*) FROM " . NV_MOD_TABLE . " WHERE alias=" . $db->quote($row_alias_check))->fetchColumn() > 0) {
                                    $stt++;
                                    $row_alias_check = $row_alias . '-' . $stt;
                                }
                                $row_alias = $row_alias_check;

                                $sql = "INSERT INTO " . NV_MOD_TABLE . " (
                                    catid, title, alias, introtext, uploadtime, updatetime, user_id, user_name, author_name, author_email, author_url,
                                    version, filesize, fileimage, status, copyright, num_fileupload, num_linkdirect, view_hits, download_hits, comment_hits
                                ) VALUES (
                                     " . $catid . ",
                                     :title,
                                     :alias ,
                                     :introtext ,
                                     " . NV_CURRENTTIME . ",
                                     " . NV_CURRENTTIME . ",
                                     " . $admin_info['userid'] . ",
                                     :username,
                                     :author_name ,
                                     :author_email ,
                                     :author_url ,
                                     :version ,
                                     0,
                                     :fileimage ,
                                     " . $row_status . ",
                                     :copyright ,
                                     :num_fileupload ,
                                     :num_linkdirect ,
                                     0, 0,
                                     0
                                )";

                                $data_insert = array();
                                $data_insert['title'] = $now_title;
                                $data_insert['alias'] = $row_alias;
                                $data_insert['introtext'] = '';
                                $data_insert['username'] = $admin_info['username'];
                                $data_insert['author_name'] = '';
                                $data_insert['author_email'] = '';
                                $data_insert['author_url'] = '';
                                $data_insert['version'] = '';
                                $data_insert['fileimage'] = '';
                                $data_insert['copyright'] = '';
                                $data_insert['num_fileupload'] = '';
                                $data_insert['num_linkdirect'] = '';

                                $row_id = $db->insert_id($sql, 'id', $data_insert);
                                if ($row_id > 0) {
                                    $array_stat['file_add']++;

                                    // Thêm bảng detail
                                    $stmt = $db->prepare("INSERT INTO " . NV_MOD_TABLE . "_detail (
                                        id, description, linkdirect, groups_comment, groups_view, groups_onlineview, groups_download, rating_detail
                                    ) VALUES(
                                        " . $row_id . ", '', '', '4', '6', '6', '4', ''
                                    )");
                                    $stmt->execute();

                                    // Thêm bảng file
                                    $weight = 1;
                                    $totalSize = 0;
                                    foreach ($allFileComplete as $fileupload) {
                                        $sql = 'INSERT INTO ' . NV_MOD_TABLE . '_files (
                                            download_id, server_id, file_path, scorm_path, filesize, weight, status
                                        ) VALUES (
                                            ' . $row_id . ', 0, :file_path, :scorm_path, :filesize, ' . ($weight++) . ', 1
                                        )';
                                        $data_insert = array();
                                        $data_insert['file_path'] = $fileupload['path'];
                                        $data_insert['scorm_path'] = '';
                                        $data_insert['filesize'] = $fileupload['size'];
                                        $db->insert_id($sql, 'file_id', $data_insert);
                                        $totalSize += $fileupload['size'];
                                    }

                                    // Cập nhật lại kích thước file
                                    $db->query("UPDATE " . NV_MOD_TABLE . " SET filesize=" . $totalSize . " WHERE id=" . $row_id);
                                }
                            }
                        } else {
                            $array_stat['file_ignore']++;
                        }
                    }
                }
            }
        }

        $nv_Cache->delMod($module_name);
    }

    if (empty($error)) {
        $success = $lang_module['fimport_success'];
    }
}

$xtpl = new XTemplate('fimport.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op);

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
} elseif (!empty($success)) {
    $array_stat['cat_add'] = number_format($array_stat['cat_add'], 0, ',', '.');
    $array_stat['cat_ignore'] = number_format($array_stat['cat_ignore'], 0, ',', '.');
    $array_stat['file_add'] = number_format($array_stat['file_add'], 0, ',', '.');
    $array_stat['file_ignore'] = number_format($array_stat['file_ignore'], 0, ',', '.');
    $xtpl->assign('STAT', $array_stat);
    $xtpl->assign('SUCCESS', $success);
    $xtpl->parse('main.success');
}

for ($i = 0; $i <= 1; $i++) {
    $status = array(
        'key' => $i,
        'title' => $lang_module['fimport_status' . $i],
        'selected' => $i == $array['status'] ? ' selected="selected"' : ''
    );
    $xtpl->assign('STATUS', $status);
    $xtpl->parse('main.status');
}
for ($i = 0; $i <= 1; $i++) {
    $mode = array(
        'key' => $i,
        'title' => $lang_module['fimport_mode' . $i],
        'selected' => $i == $array['mode'] ? ' selected="selected"' : ''
    );
    $xtpl->assign('MODE', $mode);
    $xtpl->parse('main.mode');
}
for ($i = 0; $i <= 1; $i++) {
    $catprocess = array(
        'key' => $i,
        'title' => $lang_module['fimport_catprocess' . $i],
        'selected' => $i == $array['catprocess'] ? ' selected="selected"' : ''
    );
    $xtpl->assign('CATPROCESS', $catprocess);
    $xtpl->parse('main.catprocess');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
