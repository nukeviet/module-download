<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/9/2010, 22:27
 */

if (! defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Get alias
if ($nv_Request->isset_request('gettitle', 'post')) {
    $title = $nv_Request->get_title('gettitle', 'post', '');
    $alias = change_alias($title);
    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . ' where alias = :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $weight = $db->query('SELECT MAX(id) FROM ' . NV_MOD_TABLE)->fetchColumn();
        $weight = intval($weight) + 1;
        $alias = $alias . '-' . $weight;
    }

    include NV_ROOTDIR . '/includes/header.php';
    echo $alias;
    include NV_ROOTDIR . '/includes/footer.php';
}

/**
 * nv_update_upload_dir()
 * 
 * @param mixed $dir
 * @return void
 */
function nv_update_upload_dir($dir)
{
    global $db;
    try {
        $db->query("INSERT INTO " . NV_UPLOAD_GLOBALTABLE . "_dir (dirname, time) VALUES ('" . NV_UPLOADS_DIR . "/" . $dir . "', 0)");
    } catch (PDOException $e) {
        trigger_error($e->getMessage());
    }
}

// Thiết lập thư mục tải lên
$username_alias = change_alias($admin_info['username']);
$array_structure_image = array();
$array_structure_image[''] = $module_upload;
$array_structure_image['Y'] = $module_upload . '/' . date('Y');
$array_structure_image['Ym'] = $module_upload . '/' . date('Y_m');
$array_structure_image['Y_m'] = $module_upload . '/' . date('Y/m');
$array_structure_image['Ym_d'] = $module_upload . '/' . date('Y_m/d');
$array_structure_image['Y_m_d'] = $module_upload . '/' . date('Y/m/d');
$array_structure_image['username'] = $module_upload . '/' . $username_alias;

$array_structure_image['username_Y'] = $module_upload . '/' . $username_alias . '/' . date('Y');
$array_structure_image['username_Ym'] = $module_upload . '/' . $username_alias . '/' . date('Y_m');
$array_structure_image['username_Y_m'] = $module_upload . '/' . $username_alias . '/' . date('Y/m');
$array_structure_image['username_Ym_d'] = $module_upload . '/' . $username_alias . '/' . date('Y_m/d');
$array_structure_image['username_Y_m_d'] = $module_upload . '/' . $username_alias . '/' . date('Y/m/d');

$structure_upload = isset($module_config[$module_name]['structure_upload']) ? $module_config[$module_name]['structure_upload'] : 'Ym';
$currentpath = isset($array_structure_image[$structure_upload]) ? $array_structure_image[$structure_upload] : '';
$currentpath_files = $currentpath_images = '';

if (file_exists(NV_UPLOADS_REAL_DIR . '/' . $currentpath)) {
    $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $currentpath;
} else {
    $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $module_upload;    
    $e = explode('/', $currentpath);
    if (! empty($e)) {
        $cp = '';
        foreach ($e as $p) {
            if (! empty($p) and ! is_dir(NV_UPLOADS_REAL_DIR . '/' . $cp . $p)) {
                $mk = nv_mkdir(NV_UPLOADS_REAL_DIR . '/' . $cp, $p);
                if ($mk[0] > 0) {
                    $upload_real_dir_page = $mk[2];
                    nv_update_upload_dir($cp . $p);
                }
            } elseif (! empty($p)) {
                $upload_real_dir_page = NV_UPLOADS_REAL_DIR . '/' . $cp . $p;
            }
            $cp .= $p . '/';
        }
    }    

    $upload_real_dir_page = str_replace('\\', '/', $upload_real_dir_page);
}

$currentpath = str_replace(NV_ROOTDIR . '/', '', $upload_real_dir_page);
$currentpath_tmp = str_replace(NV_UPLOADS_REAL_DIR . '/', '', $upload_real_dir_page);
$uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_upload;

if (!is_dir($upload_real_dir_page . '/images')) {
    $mk = nv_mkdir($upload_real_dir_page, 'images');
    if ($mk[0] > 0) {
        $currentpath_images = '/images';
        nv_update_upload_dir($currentpath_tmp . '/images');
    }
} else {
    $currentpath_images = '/images';
}
if (!is_dir($upload_real_dir_page . '/files')) {
    $mk = nv_mkdir($upload_real_dir_page, 'files');
    if ($mk[0] > 0) {
        $currentpath_files = '/files';
        nv_update_upload_dir($currentpath_tmp . '/files');
    }
} else {
    $currentpath_files = '/files';
}
unset($currentpath_tmp);

if (! defined('NV_IS_SPADMIN') and strpos($structure_upload, 'username') !== false) {
    $array_currentpath = explode('/', $currentpath);
    if ($array_currentpath[2] == $username_alias) {
        $uploads_dir_user = NV_UPLOADS_DIR . '/' . $module_upload . '/' . $username_alias;
    }
}

$currentpath_images = $currentpath . $currentpath_images;
$currentpath_files = $currentpath . $currentpath_files;

$id = $nv_Request->get_int('id', 'get', 0);
$filequeueid = $nv_Request->get_int('filequeueid', 'get', 0);
$groups_list = nv_groups_list();
$array = array();
$error = '';

if ($id) {
    $sql = 'SELECT * FROM ' . NV_MOD_TABLE . ' WHERE id=' . $id;
    $row = $db->query($sql)->fetch();
    
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
        exit();
    }
    
    $row['description'] = '';
    $row['linkdirect'] = '';
    $row['groups_comment'] = '';
    $row['groups_view'] = '';
    $row['groups_onlineview'] = '';
    $row['groups_download'] = '';
    
    $sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_detail WHERE id=' . $id;
    $detail = $db->query($sql)->fetch();
    
    if (!empty($detail)) {
        $row['description'] = $detail['description'];
        $row['linkdirect'] = $detail['linkdirect'];
        $row['groups_comment'] = $detail['groups_comment'];
        $row['groups_view'] = $detail['groups_view'];
        $row['groups_onlineview'] = $detail['groups_onlineview'];
        $row['groups_download'] = $detail['groups_download'];
    }
    
    $report = $nv_Request->isset_request('report', 'get');
    // Cap nhat trang thai thong bao
    if ($report) {
        nv_status_notification(NV_LANG_DATA, $module_name, 'report', $id);
    }
    $report = $report ? '&amp;report=1' : '';
    
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $id . $report;
    $page_title = $lang_module['download_editfile'];
    
    $array['id'] = ( int )$row['id'];
    $array['catid'] = ( int )$row['catid'];
    $array['title'] = $row['title'];
    $array['alias'] = $row['alias'];
    $array['description'] = nv_editor_br2nl($row['description']);
    $array['introtext'] = nv_br2nl($row['introtext']);
    $array['author_name'] = $row['author_name'];
    $array['author_email'] = $row['author_email'];
    $array['author_url'] = $row['author_url'];
    $array['linkdirect'] = $row['linkdirect'];
    $array['version'] = $row['version'];
    $array['filesize'] = ( int )$row['filesize'];
    $array['fileimage'] = $row['fileimage'];
    $array['copyright'] = $row['copyright'];
    $array['groups_comment'] = $row['groups_comment'];
    $array['groups_view'] = $row['groups_view'];
    $array['groups_onlineview'] = $row['groups_onlineview'];
    $array['groups_download'] = $row['groups_download'];
    
    if (! empty($array['linkdirect'])) {
        $array['linkdirect'] = explode('[NV]', $array['linkdirect']);
        $array['linkdirect'] = array_map('nv_br2nl', $array['linkdirect']);
    } else {
        $array['linkdirect'] = array();
    }
    
    $array['is_del_report'] = 1;
    
    $array_keywords_old = array();
    $_query_tag = $db->query('SELECT did, keyword FROM ' . NV_MOD_TABLE . '_tags_id WHERE id=' . $id . ' ORDER BY keyword ASC');
    while ($row_tag = $_query_tag->fetch()) {
        $array_keywords_old[$row_tag['did']] = $row_tag['keyword'];
    }
    $array['keywords'] = implode(', ', $array_keywords_old);
    $array['keywords_old'] = $array['keywords'];

    $fileupload = $db->query('SELECT * FROM ' . NV_MOD_TABLE . '_files WHERE download_id=' . $id)->fetchAll();
    
    $array['fileupload'] = array();
    $array['fileupload_key'] = array();
    $array['fileupload_old'] = array();
    $array['fileupload_del'] = array();
    $array['scorm_path_old'] = array();

    foreach ($fileupload as $file) {
        $array['fileupload_old'][$file['file_id']] = array(
            'file_id' => $file['file_id'],
            'server_id' => $file['server_id'],
            'file_path' => $file['file_path'],
            'scorm_path' => $file['scorm_path']
        );
        if (!empty($file['file_path'])) {
            $array['fileupload_key'][md5($file['server_id'] . '_' . $file['file_path'])] = $file['file_id'];
            $array['fileupload'][] = $file['file_path'];
        }
        if (!empty($file['scorm_path'])) {
            $array['scorm_path_old'][] = $file['scorm_path'];
        }
    }
    unset($fileupload, $file);
} elseif (!empty($filequeueid)) {
    $page_title = $lang_module['download_filequeue_edit'];
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;filequeueid=' . $filequeueid;
    
    $sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_tmp WHERE id=' . $filequeueid;
    $row = $db->query($sql)->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=filequeue');
        exit();
    }
    
    nv_status_notification(NV_LANG_DATA, $module_name, 'upload_new', $filequeueid);

    $array['catid'] = ( int )$row['catid'];
    $array['title'] = $row['title'];
    $array['alias'] = '';
    $array['description'] = nv_editor_br2nl($row['description']);
    $array['introtext'] = nv_br2nl($row['introtext']);
    $array['author_name'] = $row['author_name'];
    $array['author_email'] = $row['author_email'];
    $array['author_url'] = $row['author_url'];
    $array['linkdirect'] = $row['linkdirect'];
    $array['version'] = $row['version'];
    $array['filesize'] = ( int )$row['filesize'];
    $array['copyright'] = $row['copyright'];
    
    $array['fileimage'] = '';
    $array['fileimage_tmp'] = $row['fileimage'];
    
    if (! empty($array['linkdirect'])) {
        $array['linkdirect'] = explode('[NV]', $array['linkdirect']);
        $array['linkdirect'] = array_map('nv_br2nl', $array['linkdirect']);
    } else {
        $array['linkdirect'] = array();
    }
    
    $array['keywords'] = '';
    $array['keywords_old'] = '';
    
    $array['fileupload'] = array();
    $array['fileupload_key'] = array();
    $array['fileupload_old'] = array();
    $array['fileupload_del'] = array();
    $array['scorm_path_old'] = array();
    
    $array['fileupload_tmp'] = ! empty($row['fileupload']) ? explode('[NV]', $row['fileupload']) : array();
    
    $array['groups_comment'] = $module_config[$module_name]['setcomm'];
    $array['groups_view'] = $array['groups_onlineview'] = $array['groups_download'] = '6';
    $array['is_del_report'] = 1;
} else {
    $page_title = $lang_module['file_addfile'];
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    
    $array['id'] = 0;
    $array['catid'] = $nv_Request->get_int('catid', 'get', 0);
    $array['title'] = $array['description'] = $array['introtext'] = $array['author_name'] = $array['author_email'] = $array['author_url'] = $array['version'] = $array['fileimage'] = '';
    $array['fileupload'] = $array['fileupload_key'] = $array['linkdirect'] = array();
    $array['fileupload_old'] = array();
    $array['fileupload_del'] = array();
    $array['scorm_path_old'] = array();
    $array['fileupload_tmp'] = array();
    $array['groups_comment'] = $module_config[$module_name]['setcomm'];
    $array['groups_view'] = $array['groups_onlineview'] = $array['groups_download'] = '6';
    $array['filesize'] = 0;
    $array['is_del_report'] = 1;
    $array['keywords_old'] = '';
    $array_keywords_old = array();
    $report = false;
}

if ($nv_Request->isset_request('submit', 'post')) {
    $array['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $array['title'] = $nv_Request->get_title('title', 'post', '', 1);
    $array['description'] = $nv_Request->get_editor('description', '', NV_ALLOWED_HTML_TAGS);
    $array['introtext'] = $nv_Request->get_textarea('introtext', '', NV_ALLOWED_HTML_TAGS);
    $array['author_name'] = $nv_Request->get_title('author_name', 'post', '', 1);
    $array['author_email'] = $nv_Request->get_title('author_email', 'post', '');
    $array['author_url'] = $nv_Request->get_title('author_url', 'post', '');
    $array['linkdirect'] = $nv_Request->get_typed_array('linkdirect', 'post', 'string');
    $array['version'] = $nv_Request->get_title('version', 'post', '', 1);
    $array['fileimage'] = $nv_Request->get_title('fileimage', 'post', '');
    $array['copyright'] = $nv_Request->get_title('copyright', 'post', '', 1);
    $array['is_del_report'] = $nv_Request->get_int('is_del_report', 'post', 0);

    $_groups_post = $nv_Request->get_array('groups_view', 'post', array());
    $array['groups_view'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_onlineview', 'post', array());
    $array['groups_onlineview'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_download', 'post', array());
    $array['groups_download'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_comment', 'post', array());
    $array['groups_comment'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $array['keywords'] = $nv_Request->get_array('keywords', 'post', '');
    $array['keywords'] = implode(', ', $array['keywords']);

    if (! empty($array['author_url'])) {
        if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $array['author_url'])) {
            $array['author_url'] = 'http://' . $array['author_url'];
        }
    }
    
    // Kiểm tra lại thư mục scorm
    foreach ($array['fileupload_old'] as $key => $fileupload) {
        if (!empty($fileupload['scorm_path'])) {
            if ($fileupload['server_id'] == 0) {
                if (!is_dir(NV_UPLOADS_REAL_DIR . $fileupload['scorm_path'])) {
                    $array['fileupload_old'][$key]['scorm_path'] = '';
                }
            } else {
                // Kiểm tra trên server khác
            }
        }
        
        if (empty($array['fileupload_old'][$key]['scorm_path']) and empty($array['fileupload_old'][$key]['file_path'])) {
            $array['fileupload_del'][$fileupload['file_id']] = $fileupload['file_id'];
        }
    }
    
    $fileupload_submit = $nv_Request->get_typed_array('fileupload', 'post', 'string');
    $fileserver = 0;  
    $array['filesize'] = 0;
    $array['fileupload_new'] = array();
    $array['fileupload_all_key'] = array();

    if (! empty($fileupload_submit)) {
        foreach ($fileupload_submit as $file) {
            if (! empty($file)) {
                $file2 = substr($file, strlen(NV_BASE_SITEURL));
                $file3 = substr($file, strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR));
                $file_key = md5($fileserver . '_' . $file3);

                if (file_exists(NV_ROOTDIR . '/' . $file2) and ($filesize = filesize(NV_ROOTDIR . '/' . $file2)) != 0) {
                    $array['filesize'] += $filesize;
                    if (!isset($array['fileupload_key'][$file_key])) {
                        $array['fileupload_new'][] = array(
                            'file_path' => $file3,
                            'scorm_path' => '',
                            'filesize' => $filesize
                        );
                        $array['fileupload'][] = $file3;
                    }
                    
                    $array['fileupload_all_key'][$file_key] = 2;
                }
            }
        }
        
        foreach ($array['fileupload_key'] as $key => $file_id) {
            if (!isset($array['fileupload_all_key'][$key])) {
                $array['fileupload_del'][$file_id] = $file_id;
            }
        }
    } else {
        $array['fileupload'] = array();
        $array['fileupload_del'] = array_values($array['fileupload_key']);
    }
        
    // Sort image
    if (! empty($array['fileimage'])) {
        if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $array['fileimage'])) {
            $array['fileimage'] = substr($array['fileimage'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR));
        }
    }

    if (! empty($array['linkdirect'])) {
        $linkdirect = $array['linkdirect'];
        $array['linkdirect'] = array();
        foreach ($linkdirect as $links) {
            $linkdirect2 = array();
            if (! empty($links)) {
                $links = nv_nl2br($links, '<br />');
                $links = explode('<br />', $links);
                $links = array_map('trim', $links);
                $links = array_unique($links);

                foreach ($links as $link) {
                    if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $link)) {
                        $link = 'http://' . $link;
                    }
                    if (nv_is_url($link)) {
                        $linkdirect2[] = $link;
                    }
                }
            }

            if (! empty($linkdirect2)) {
                $array['linkdirect'][] = implode("\n", $linkdirect2);
            }
        }
    } else {
        $array['linkdirect'] = array();
    }
    if (! empty($array['linkdirect'])) {
        $array['linkdirect'] = array_unique($array['linkdirect']);
    }

    if (! empty($array['linkdirect']) and empty($array['fileupload'])) {
        $array['filesize'] = $nv_Request->get_int('filesize', 'post', 0);
    }

    // Xử lý liên kết tĩnh
    $array['alias'] = $nv_Request->get_title('alias', 'post', '');
    $array['alias'] = !empty($array['alias']) ? change_alias($array['alias']) : change_alias($array['title']);

    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . ' WHERE alias= :alias' . ($id ? ' AND id!=' . $id : ''));
    $stmt->bindParam(':alias', $array['alias'], PDO::PARAM_STR);
    $stmt->execute();
    $is_exists = $stmt->fetchColumn();

    if (! $is_exists and !$filequeueid) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_tmp WHERE title= :title');
        $stmt->bindParam(':title', $array['title'], PDO::PARAM_STR);
        $stmt->execute();
        $is_exists = $stmt->fetchColumn();
    }

    if (empty($array['title'])) {
        $error = $lang_module['file_error_title'];
    } elseif ($is_exists) {
        $error = $lang_module['file_title_exists'];
    } elseif (! empty($array['author_email']) and ($check_valid_email = nv_check_valid_email($array['author_email'])) != '') {
        $error = $check_valid_email;
    } elseif (! empty($array['author_url']) and ! nv_is_url($array['author_url'])) {
        $error = $lang_module['file_error_author_url'];
    } elseif (empty($array['fileupload']) and empty($array['linkdirect']) and empty($array['scorm_path_old']) and empty($array['fileupload_tmp'])) {
        $error = $lang_module['file_error_fileupload'];
    } else {
        // Xử lý ảnh minh họa nếu duyệt file
        if (empty($array['fileimage']) and !empty($array['fileimage_tmp'])) {
            $fileimage = NV_UPLOADS_DIR . $array['fileimage_tmp'];
            $array['fileimage'] = '';
            if (file_exists(NV_ROOTDIR . '/' . $fileimage)) {
                $newfile = basename($fileimage);

                if (preg_match('/(.*)(\.[a-zA-Z0-9]{32})(\.[a-zA-Z]+)$/', $newfile, $m)) {
                    $newfile = $m[1] . $m[3];
                }

                $newfile2 = $newfile;
                $i = 1;
                while (file_exists(NV_ROOTDIR . '/' . $currentpath_images . '/' . $newfile2)) {
                    $newfile2 = preg_replace('/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $newfile);
                    ++$i;
                }

                if (@nv_copyfile(NV_ROOTDIR . '/' . $fileimage, NV_ROOTDIR . '/' . $currentpath_images . '/' . $newfile2)) {
                    $array['fileimage'] = substr($currentpath_images . '/' . $newfile2, strlen(NV_UPLOADS_DIR));
                }
            }
        }
        
        // Xử lý file upload nếu duyệt file
        if (empty($array['fileupload_new']) and !empty($array['fileupload_tmp'])) {
            foreach ($array['fileupload_tmp'] as $file) {
                $file = NV_UPLOADS_DIR . $file;
                $newfile = basename($file);

                if (preg_match('/(.*)(\.[a-zA-Z0-9]{32})(\.[a-zA-Z]+)$/', $newfile, $m)) {
                    $newfile = $m[1] . $m[3];
                }

                $newfile2 = $newfile;
                $i = 1;
                while (file_exists(NV_ROOTDIR . '/' . $currentpath_files . '/' . $newfile2)) {
                    $newfile2 = preg_replace('/(.*)(\.[a-zA-Z]+)$/', '\1_' . $i . '\2', $newfile);
                    ++$i;
                }

                if (@nv_copyfile(NV_ROOTDIR . '/' . $file, NV_ROOTDIR . '/' . $currentpath_files . '/' . $newfile2)) {
                    $array['fileupload_new'][] = array(
                        'file_path' => substr($currentpath_files . '/' . $newfile2, strlen(NV_UPLOADS_DIR)),
                        'scorm_path' => '',
                        'filesize' => filesize(NV_ROOTDIR . '/' . $currentpath_files . '/' . $newfile2)
                    );
                }
            }
        }
        
        if (!empty($array['fileupload_new'])) {
            foreach ($array['fileupload_new'] as $fileuploadkey => $file_new) {
                $file = $file_new['file_path'];
                
                // Xác định file scorm
                $file_ext = nv_getextension($file);
                $file_name = basename($file);
                $file_path = dirname($file);
                
                if ($file_ext == 'zip') {
                    $zip = new PclZip(NV_UPLOADS_REAL_DIR . $file);
                    $ziplistContent = $zip->listContent();
                    
                    if (!empty($ziplistContent)) {
                        $num_check = 0;
                        foreach ($ziplistContent as $zipCt) {
                            if ($zipCt['filename'] == 'SCORM.htm' or $zipCt['filename'] == 'index.htm' or $zipCt['filename'] == 'viewer.swf') {
                                $num_check ++;
                            }
                            if ($num_check >= 3) {
                                break;
                            }
                        }
                        if ($num_check >= 3) {
                            $scorm_dir = substr($file_name, 0, 0 - (strlen($file_ext) + 1));
                            $scorm_path = $file_path . '/' . $scorm_dir;
                            
                            if (is_dir(NV_UPLOADS_REAL_DIR . $scorm_path) and file_exists(NV_UPLOADS_REAL_DIR . $scorm_path . '/SCORM.htm')) {
                                $array['fileupload_new'][$fileuploadkey]['scorm_path'] = $scorm_path;
                            } else {
                                nv_deletefile(NV_UPLOADS_REAL_DIR . $scorm_path, true);
                                $mkdir = nv_mkdir(NV_UPLOADS_REAL_DIR . $file_path, $scorm_dir);
                                if ($mkdir[0] == 1) {
                                    nv_deletefile(NV_UPLOADS_REAL_DIR . $scorm_path . '/index.html');
                                    
                                    // Kiem tra FTP
                                    $ftp_check_login = 0;
                        
                                    if ($sys_info['ftp_support'] and intval($global_config['ftp_check_login']) == 1) {
                                        $ftp_server = nv_unhtmlspecialchars($global_config['ftp_server']);
                                        $ftp_port = intval($global_config['ftp_port']);
                                        $ftp_user_name = nv_unhtmlspecialchars($global_config['ftp_user_name']);
                                        $ftp_user_pass = nv_unhtmlspecialchars($global_config['ftp_user_pass']);
                                        $ftp_path = nv_unhtmlspecialchars($global_config['ftp_path']);
                                        // set up basic connection
                                        $conn_id = ftp_connect($ftp_server, $ftp_port, 10);
                                        // login with username and password
                                        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                                        if ((! $conn_id) || (! $login_result)) {
                                            $ftp_check_login = 3;
                                        } elseif (ftp_chdir($conn_id, $ftp_path)) {
                                            $ftp_check_login = 1;
                                        } else {
                                            $ftp_check_login = 2;
                                        }
                                    }
                        
                                    // Tao thu muc bang FTP neu co
                                    $scorm_path1 = NV_UPLOADS_DIR . $scorm_path;
                                    if ($ftp_check_login == 1) {
                                        ftp_mkdir($conn_id, $scorm_path1);
                        
                                        if (substr($sys_info['os'], 0, 3) != 'WIN') {
                                            ftp_chmod($conn_id, 0777, $scorm_path1);
                                        }
                        
                                        foreach ($ziplistContent as $array_file) {
                                            if (! empty($array_file['folder']) and ! file_exists(NV_ROOTDIR . '/' . $scorm_path1 . '/' . $array_file['filename'])) {
                                                $cp = '';
                                                $e = explode('/', $array_file['filename']);
                                                foreach ($e as $p) {
                                                    if (! empty($p) and ! is_dir(NV_ROOTDIR . '/' . $scorm_path1 . '/' . $cp . $p)) {
                                                        ftp_mkdir($conn_id, $scorm_path1 . '/' . $cp . $p);
                                                        if (substr($sys_info['os'], 0, 3) != 'WIN') {
                                                            ftp_chmod($conn_id, 0777, $scorm_path1 . '/' . $cp . $p);
                                                        }
                                                    }
                                                    $cp .= $p . '/';
                                                }
                                            }
                                        }
                                    }
                    
                                    if ($ftp_check_login > 0) {
                                        ftp_close($conn_id);
                                    }
                                    
                                    // Đọc cấu hình .htaccess ở thư mục upload
                                    $forbid_ext = array();
                                    if (file_exists(NV_UPLOADS_REAL_DIR . '/.htaccess')) {
                                        $file_handle = fopen(NV_UPLOADS_REAL_DIR . '/.htaccess', 'r');
                                        if ($file_handle !== false) {
                                            $line = trim(fgets($file_handle));
                                            fclose($file_handle);
                                            
                                            if (!empty($line) and preg_match("/\((.*?)\)/i", $line, $m)) {
                                                $forbid_ext = array_map("trim", array_filter(array_unique(explode('|', $m[1]))));
                                            }
                                        }
                                        $forbid_ext = array_unique(array_merge_recursive($global_config['forbid_extensions'], $forbid_ext, array('htaccess')));
                                    }
                                    $forbid_ext = implode('|', $forbid_ext);
                                    
                                    $extract = $zip->extract(PCLZIP_OPT_PATH, NV_ROOTDIR . '/' . $scorm_path1);
                                    
                                    foreach ($extract as $extract_i) {
                                        // Delete forbid file
                                        $array_name_i = explode('/', $extract_i['stored_filename']);
                                        
                                        if (preg_match("/\.(" . $forbid_ext . ")$/i", $array_name_i[sizeof($array_name_i) - 1])) {
                                            nv_deletefile($extract_i['filename']);
                                        }
                                        
                                        if ($extract_i['status'] != 'ok' and $extract_i['status'] != 'already_a_directory') {
                                            $error = $lang_module['file_error_extract_scorm'] . ': ' . $file_name;
                                            break;
                                        }
                                    }
                                    
                                    if (file_exists(NV_UPLOADS_REAL_DIR . '/.htaccess')) {
                                        file_put_contents(NV_ROOTDIR . '/' . $scorm_path1 . '/.htaccess', file_get_contents(NV_UPLOADS_REAL_DIR . '/.htaccess'), LOCK_EX);
                                    }
                                    
                                    if (!empty($error)) {
                                        nv_deletefile(NV_UPLOADS_REAL_DIR . $scorm_path, true);
                                    } elseif (empty($module_config[$module_name]['scorm_handle_mode'])) {
                                        nv_deletefile(NV_UPLOADS_REAL_DIR . $array['fileupload'][$fileuploadkey]);
                                        $array['fileupload_new'][$fileuploadkey]['file_path'] = '';
                                    }
                                    $array['fileupload_new'][$fileuploadkey]['scorm_path'] = $scorm_path;
                                } else {
                                    $error = $mkdir[1];
                                }
                            }
                            
                            // Resets the contents of the opcode cache
                            if (nv_function_exists('opcache_reset')) {
                                opcache_reset();
                            }
                        }
                    }
                }
            }
        }
        
        if (empty($error)) {
            $array['introtext'] = ! empty($array['introtext']) ? nv_nl2br($array['introtext'], '<br />') : '';
            $array['num_fileupload'] = sizeof($array['fileupload']);
            $array['num_linkdirect'] = sizeof($array['linkdirect']);
            
            if ((! empty($array['linkdirect']))) {
                $array['linkdirect'] = array_map('nv_nl2br', $array['linkdirect']);
                $array['linkdirect'] = implode('[NV]', $array['linkdirect']);
            } else {
                $array['linkdirect'] = '';
            }
            
            $action_db = false;
            
            if (empty($id)) {
                $sql = "INSERT INTO " . NV_MOD_TABLE . " (
                    catid, title, alias, introtext, uploadtime, updatetime, user_id, user_name, author_name, author_email, author_url, 
                    version, filesize, fileimage, status, copyright, num_fileupload, num_linkdirect, view_hits, download_hits, comment_hits 
                ) VALUES (
                     " . $array['catid'] . ",
                     :title,
                     :alias ,
                     :introtext ,
                     " . NV_CURRENTTIME . ",
                     " . NV_CURRENTTIME . ",
                     " . $admin_info['admin_id'] . ",
                     :username,
                     :author_name ,
                     :author_email ,
                     :author_url ,
                     :version ,
                     " . $array['filesize'] . ",
                     :fileimage ,
                     1,
                     :copyright ,
                     :num_fileupload ,
                     :num_linkdirect ,
                     0, 0,
                     0
                )";
        
                $data_insert = array();
                $data_insert['title'] = $array['title'];
                $data_insert['alias'] = $array['alias'];
                $data_insert['introtext'] = $array['introtext'];
                $data_insert['username'] = $admin_info['username'];
                $data_insert['author_name'] = $array['author_name'];
                $data_insert['author_email'] = $array['author_email'];
                $data_insert['author_url'] = $array['author_url'];
                $data_insert['version'] = $array['version'];
                $data_insert['fileimage'] = $array['fileimage'];
                $data_insert['copyright'] = $array['copyright'];
                $data_insert['num_fileupload'] = $array['num_fileupload'];
                $data_insert['num_linkdirect'] = $array['num_linkdirect'];
        
                $array['id'] = $db->insert_id($sql, 'id', $data_insert);
        
                if ($array['id'] != 0) {
                    $action_db = true;
                } else {
                    $error = $lang_module['file_error2'];
                }
            } else {
                $stmt = $db->prepare("UPDATE " . NV_MOD_TABLE . " SET
                     catid=" . $array['catid'] . ",
                     title= :title,
                     alias= :alias,
                     introtext= :introtext,
                     updatetime=" . NV_CURRENTTIME . ",
                     author_name= :author_name,
                     author_email= :author_email,
                     author_url= :author_url,
                     version= :version,
                     filesize=" . $array['filesize'] . ",
                     fileimage= :fileimage,
                     copyright= :copyright,
                     num_fileupload= :num_fileupload,
                     num_linkdirect= :num_linkdirect 
                WHERE id=" . $id);
    
                $stmt->bindParam(':title', $array['title'], PDO::PARAM_STR);
                $stmt->bindParam(':alias', $array['alias'], PDO::PARAM_STR);
                $stmt->bindParam(':introtext', $array['introtext'], PDO::PARAM_STR, strlen($array['introtext']));
                $stmt->bindParam(':author_name', $array['author_name'], PDO::PARAM_STR);
                $stmt->bindParam(':author_email', $array['author_email'], PDO::PARAM_STR);
                $stmt->bindParam(':author_url', $array['author_url'], PDO::PARAM_STR);
                $stmt->bindParam(':version', $array['version'], PDO::PARAM_STR);
                $stmt->bindParam(':fileimage', $array['fileimage'], PDO::PARAM_STR);
                $stmt->bindParam(':copyright', $array['copyright'], PDO::PARAM_STR);
                $stmt->bindParam(':num_fileupload', $array['num_fileupload'], PDO::PARAM_INT);
                $stmt->bindParam(':num_linkdirect', $array['num_linkdirect'], PDO::PARAM_INT);
    
                if (! $stmt->execute()) {
                    $error = $lang_module['file_error1'];
                } else {
                    $action_db = true;
    
                    if ($report and $array['is_del_report']) {
                        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_report WHERE fid=' . $id);
                    }
                }
            }
            
            if ($action_db) {
                // Xử lý từ khóa
                if ($array['keywords'] != $array['keywords_old']) {
                    $keywords = explode(',', $array['keywords']);
                    $keywords = array_map('strip_punctuation', $keywords);
                    $keywords = array_map('trim', $keywords);
                    $keywords = array_diff($keywords, array( '' ));
                    $keywords = array_unique($keywords);
                    
                    foreach ($keywords as $keyword) {
                        $alias_i = ($module_config[$module_name]['tags_alias']) ? change_alias($keyword) : str_replace(' ', '-', $keyword);
                        $alias_i = nv_strtolower($alias_i);
                        
                        $sth = $db->prepare('SELECT did, alias, description, keywords FROM ' . NV_MOD_TABLE . '_tags where alias= :alias OR FIND_IN_SET(:keyword, keywords)>0');
                        $sth->bindParam(':alias', $alias_i, PDO::PARAM_STR);
                        $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                        $sth->execute();
    
                        list($did, $alias, $keywords_i) = $sth->fetch(3);
                        
                        if (empty($did)) {
                            $array_insert = array( );
                            $array_insert['alias'] = $alias_i;
                            $array_insert['keyword'] = $keyword;
    
                            $did = $db->insert_id("INSERT INTO " . NV_MOD_TABLE . "_tags (numdownload, alias, description, image, keywords) VALUES (1, :alias, '', '', :keyword)", "did", $array_insert);
                        } else {
                            $db->query('UPDATE ' . NV_MOD_TABLE . '_tags SET numdownload = numdownload+1 WHERE did = ' . $did);
                        }
    
                        $_sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_tags_id WHERE id=' . $array['id'] . ' AND did = ' . $did;
                        $_query = $db->query($_sql);
                        $row = $_query->fetch();
    
                        if (empty($row)) {
                            $sth = $db->prepare('INSERT INTO ' . NV_MOD_TABLE . '_tags_id (id, did, keyword) VALUES (' . $array['id'] . ', ' . intval($did) . ', :keyword)');
                            $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                            $sth->execute();
                        } else {
                            $sth = $db->prepare('UPDATE ' . NV_MOD_TABLE . '_tags_id SET keyword = :keyword WHERE id = ' . $array['id'] . ' AND did=' . intval($did));
                            $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                            $sth->execute();
                        }
                        
                        unset($array_keywords_old[$did]);
                    }
                    
                    foreach ($array_keywords_old as $did => $keyword) {
                        if (! in_array($keyword, $keywords)) {
                            $db->query('DELETE FROM ' . NV_MOD_TABLE . '_tags_id WHERE id = ' . $array['id'] . ' AND did=' . $did);
    
                            $count_tag = $db->query('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_tags_id WHERE did=' . $did);
                            
                            if ($count_tag->fetchColumn()) {
                                $db->query('UPDATE ' . NV_MOD_TABLE . '_tags SET numdownload = numdownload-1 WHERE did = ' . $did);
                            } else {
                                $db->query('DELETE FROM ' . NV_MOD_TABLE . '_tags WHERE did=' . $did);
                            }
                        }
                    }
                }
                
                if ($id) {
                    // Cập nhật bảng detail
                    $stmt = $db->prepare("UPDATE " . NV_MOD_TABLE . "_detail SET 
                        description=:description, 
                        linkdirect=:linkdirect, 
                        groups_comment=:groups_comment, 
                        groups_view=:groups_view, 
                        groups_onlineview=:groups_onlineview, 
                        groups_download=:groups_download  
                    WHERE id=" . $id);
                    
                    $stmt->bindParam(':description', $array['description'], PDO::PARAM_STR, strlen($array['description']));
                    $stmt->bindParam(':linkdirect', $array['linkdirect'], PDO::PARAM_STR, strlen($array['linkdirect']));
                    $stmt->bindParam(':groups_comment', $array['groups_comment'], PDO::PARAM_STR);
                    $stmt->bindParam(':groups_view', $array['groups_view'], PDO::PARAM_STR);
                    $stmt->bindParam(':groups_onlineview', $array['groups_onlineview'], PDO::PARAM_STR);
                    $stmt->bindParam(':groups_download', $array['groups_download'], PDO::PARAM_STR);
                    $stmt->execute();
                    
                    // Xóa file cũ
                    if (!empty($array['fileupload_del'])) {
                        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_files WHERE download_id=' . $id . ' AND file_id IN(' . implode(',', $array['fileupload_del']) . ')');
                    }
                    
                    // Thêm file mới
                    $weight = $db->query('SELECT MAX(weight) FROM ' . NV_MOD_TABLE . '_files WHERE download_id=' . $id)->fetchColumn();
                    $weight ++;
                    foreach ($array['fileupload_new'] as $fileupload) {
                        $sql = 'INSERT INTO ' . NV_MOD_TABLE . '_files (
                            download_id, server_id, file_path, scorm_path, filesize, weight, status
                        ) VALUES (
                            ' . $array['id'] . ', 0, :file_path, :scorm_path, :filesize, ' . ($weight++) . ', 1
                        )';
                        $data_insert = array();
                        $data_insert['file_path'] = $fileupload['file_path'];
                        $data_insert['scorm_path'] = $fileupload['scorm_path'];
                        $data_insert['filesize'] = $fileupload['filesize'];
                        $db->insert_id($sql, 'file_id', $data_insert);
                    }
                    
                    // Sắp xếp lại file
                    $weight = 1;
                    $sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_files WHERE download_id=' . $id . ' ORDER BY weight ASC';
                    $result = $db->query($sql);
                    
                    while ($file = $result->fetch()) {
                        $db->query('UPDATE ' . NV_MOD_TABLE . '_files SET weight=' . ($weight++) . ' WHERE file_id=' . $file['file_id']);
                    }
                    
                    nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['download_editfile'], $array['title'], $admin_info['userid']);
                } else {
                    // Thêm bảng detail
                    $stmt = $db->prepare("INSERT INTO " . NV_MOD_TABLE . "_detail (
                        id, description, linkdirect, groups_comment, groups_view, groups_onlineview, groups_download, rating_detail
                    ) VALUES( 
                        " . $array['id'] . ", :description, :linkdirect, :groups_comment, :groups_view, :groups_onlineview, :groups_download, ''
                    )");
                    
                    $stmt->bindParam(':description', $array['description'], PDO::PARAM_STR, strlen($array['description']));
                    $stmt->bindParam(':linkdirect', $array['linkdirect'], PDO::PARAM_STR, strlen($array['linkdirect']));
                    $stmt->bindParam(':groups_comment', $array['groups_comment'], PDO::PARAM_STR);
                    $stmt->bindParam(':groups_view', $array['groups_view'], PDO::PARAM_STR);
                    $stmt->bindParam(':groups_onlineview', $array['groups_onlineview'], PDO::PARAM_STR);
                    $stmt->bindParam(':groups_download', $array['groups_download'], PDO::PARAM_STR);
                    $stmt->execute();
                    
                    // Thêm bảng file
                    $weight = 1;
                    foreach ($array['fileupload_new'] as $fileupload) {
                        $sql = 'INSERT INTO ' . NV_MOD_TABLE . '_files (
                            download_id, server_id, file_path, scorm_path, filesize, weight, status
                        ) VALUES (
                            ' . $array['id'] . ', 0, :file_path, :scorm_path, :filesize, ' . ($weight++) . ', 1
                        )';
                        $data_insert = array();
                        $data_insert['file_path'] = $fileupload['file_path'];
                        $data_insert['scorm_path'] = $fileupload['scorm_path'];
                        $data_insert['filesize'] = $fileupload['filesize'];
                        $db->insert_id($sql, 'file_id', $data_insert);
                    }
                    
                    nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['file_addfile'], $array['title'], $admin_info['userid']);                    
                }
                
                if (!empty($array['fileimage_tmp'])) {
                    nv_deletefile(NV_ROOTDIR . $array['fileimage_tmp']);
                }
                
                if (!empty($array['fileupload_tmp'])) {
                    foreach ($array['fileupload_tmp'] as $fileupload_tmp) {
                        nv_deletefile(NV_ROOTDIR . $fileupload_tmp);
                    }
                }
                
                if (!empty($filequeueid)) {
                    $db->query('DELETE FROM ' . NV_MOD_TABLE . '_tmp WHERE id=' . $filequeueid);
                }
                
                $nv_Cache->delMod($module_name);
                
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
                exit();
            }
        }
    }
}

$array['description'] = htmlspecialchars(nv_editor_br2nl($array['description']));
$array['introtext'] = nv_htmlspecialchars($array['introtext']);

$array['fileupload_num'] = sizeof($array['fileupload']);
$array['linkdirect_num'] = sizeof($array['linkdirect']);

// Build fileimage
if (! empty($array['fileimage'])) {
    if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $array['fileimage'])) {
        $array['fileimage'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . $array['fileimage'];
    }
}

// Build fileimage tmp
if (! empty($array['fileimage_tmp'])) {
    if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $array['fileimage_tmp'])) {
        $array['fileimage_tmp'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . $array['fileimage_tmp'];
    }
}

// Rebuild fileupload
if (! empty($array['fileupload'])) {
    $fileupload = $array['fileupload'];
    $array['fileupload'] = array();
    foreach ($fileupload as $tmp) {
        if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $tmp)) {
            $tmp = NV_BASE_SITEURL . NV_UPLOADS_DIR . $tmp;
        }
        $array['fileupload'][] = $tmp;
    }
}

// Rebuild fileupload tmp
if (! empty($array['fileupload_tmp'])) {
    $fileupload = $array['fileupload_tmp'];
    $array['fileupload_tmp'] = array();
    foreach ($fileupload as $tmp) {
        if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $tmp)) {
            $tmp = NV_BASE_SITEURL . NV_UPLOADS_DIR . $tmp;
        }
        $array['fileupload_tmp'][] = $tmp;
    }
}

if (! sizeof($array['linkdirect'])) {
    array_push($array['linkdirect'], '');
}
if (! sizeof($array['fileupload'])) {
    array_push($array['fileupload'], '');
}

if (empty($list_cats)) {
    $redirect = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat-content';
    $contents = '<p class="note_cat">' . $lang_module['note_cat'] . '</p>';
    $contents .= "<meta http-equiv=\"refresh\" content=\"3;URL=" . $redirect . "\" />";
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
    die();
}

$array['is_del_report'] = $array['is_del_report'] ? ' checked="checked"' : '';

$groups_comment = explode(',', $array['groups_comment']);
$array['groups_comment'] = array();
foreach ($groups_list as $key => $title) {
    $array['groups_comment'][] = array(
        'key' => $key,
        'title' => $title,
        'checked' => in_array($key, $groups_comment) ? ' checked="checked"' : ''
    );
}

$groups_view = explode(',', $array['groups_view']);
$array['groups_view'] = array();
foreach ($groups_list as $key => $title) {
    $array['groups_view'][] = array(
        'key' => $key,
        'title' => $title,
        'checked' => in_array($key, $groups_view) ? ' checked="checked"' : ''
    );
}

$groups_onlineview = explode(',', $array['groups_onlineview']);
$array['groups_onlineview'] = array();
foreach ($groups_list as $key => $title) {
    $array['groups_onlineview'][] = array(
        'key' => $key,
        'title' => $title,
        'checked' => in_array($key, $groups_onlineview) ? ' checked="checked"' : ''
    );
}

$groups_download = explode(',', $array['groups_download']);
$array['groups_download'] = array();
foreach ($groups_list as $key => $title) {
    $array['groups_download'][] = array(
        'key' => $key,
        'title' => $title,
        'checked' => in_array($key, $groups_download) ? ' checked="checked"' : ''
    );
}

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $array['description'] = nv_aleditor('description', '100%', '300px', $array['description']);
} else {
    $array['description'] = '<textarea style="width:100%; height:300px" name="description" id="description">' . $array['description'] . '</textarea>';
}
$array['id'] = 0;

if (! $array['filesize']) {
    $array['filesize'] = '';
}

$xtpl = new XTemplate('content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('FORM_ACTION', $form_action);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATA', $array);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
$xtpl->assign('IMG_DIR', $currentpath_images);
$xtpl->assign('FILES_DIR', $currentpath_files);
$xtpl->assign('UPLOADS_DIR', $uploads_dir_user);
$xtpl->assign('ONCHANGE', 'onchange="get_alias();"');
$xtpl->assign('UPLOAD_MAX_FILESIZE', NV_UPLOAD_MAX_FILESIZE);
$xtpl->assign('DIRECT_UPLOAD_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=upload&' . NV_OP_VARIABLE . '=upload&path=' . urlencode($currentpath_files) . '&random=' . nv_genpass(10));

$mimes = nv_parse_ini_file(NV_ROOTDIR . '/includes/ini/mime.ini', true);

foreach ($mimes as $mime_type => $file_ext) {
    if (! in_array($mime_type, $global_config['forbid_mimes']) and in_array($mime_type, $admin_info['allow_files_type'])) {
        $file_ext = array_diff(array_keys($file_ext), $global_config['forbid_extensions']);

        if (! empty($file_ext)) {
            $xtpl->assign('MIMI_TYPE', ucfirst($mime_type));
            $xtpl->assign('MIME_EXTS', implode(',', $file_ext));

            $xtpl->parse('main.mime');
        }
    }
}

if (! empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

if (!empty($list_cats)) {
    foreach ($list_cats as $catid => $value) {
        $value['space'] = '';
        if ($value['lev'] > 0) {
            for ($i = 1; $i <= $value['lev']; $i++) {
                $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        }
        $value['selected'] = $catid == $array['catid'] ? ' selected="selected"' : '';

        $xtpl->assign('LISTCATS', $value);
        $xtpl->parse('main.catid');
    }
}

$a = 0;
foreach ($array['fileupload'] as $file) {
    $xtpl->assign('FILEUPLOAD', array( 'value' => $file, 'key' => $a ));
    $xtpl->parse('main.fileupload');
    ++$a;
}

if (!empty($array['fileupload_tmp'])) {
    $a = 0;
    foreach ($array['fileupload_tmp'] as $file) {
        $xtpl->assign('FILEUPLOAD_TMP', array( 'value' => $file, 'key' => $a ));
        $xtpl->parse('main.fileupload_tmp.loop');
        ++$a;
    }
    
    $xtpl->parse('main.fileupload_tmp');
}

$a = 0;
foreach ($array['linkdirect'] as $link) {
    $xtpl->assign('LINKDIRECT', array( 'value' => $link, 'key' => $a ));
    $xtpl->parse('main.linkdirect');
    ++$a;
}

foreach ($array['groups_comment'] as $group) {
    $xtpl->assign('GROUPS_COMMENT', $group);
    $xtpl->parse('main.groups_comment');
}

foreach ($array['groups_view'] as $group) {
    $xtpl->assign('GROUPS_VIEW', $group);
    $xtpl->parse('main.groups_view');
}

foreach ($array['groups_onlineview'] as $group) {
    $xtpl->assign('GROUPS_ONLINEVIEW', $group);
    $xtpl->parse('main.groups_onlineview');
}

foreach ($array['groups_download'] as $group) {
    $xtpl->assign('GROUPS_DOWNLOAD', $group);
    $xtpl->parse('main.groups_download');
}

if (!empty($array['fileimage_tmp'])) {
    $xtpl->parse('main.fileimage_tmp');
}

if (!empty($filequeueid)) {
    $xtpl->assign('FILEQUEUEID', $filequeueid);
    $xtpl->parse('main.approval');
} else {
    $xtpl->parse('main.submit');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
