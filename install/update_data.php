<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2012 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if (!defined('NV_IS_UPDATE'))
    die('Stop!!!');

$nv_update_config = array();

// Kieu nang cap 1: Update; 2: Upgrade
$nv_update_config['type'] = 1;

// ID goi cap nhat
$nv_update_config['packageID'] = 'NVUDDOWNLOAD4029';

// Cap nhat cho module nao, de trong neu la cap nhat NukeViet, ten thu muc module neu la cap nhat module
$nv_update_config['formodule'] = 'download';

// Thong tin phien ban, tac gia, ho tro
$nv_update_config['release_date'] = 1463590800;
$nv_update_config['author'] = 'VINADES.,JSC (contact@vinades.vn)';
$nv_update_config['support_website'] = 'https://github.com/nukeviet/module-download/tree/4.0.29';
$nv_update_config['to_version'] = '4.0.29';
$nv_update_config['allow_old_version'] = array(
    '4.0.25',
    '4.0.27'
);

// 0:Nang cap bang tay, 1:Nang cap tu dong, 2:Nang cap nua tu dong
$nv_update_config['update_auto_type'] = 1;

$nv_update_config['lang'] = array();
$nv_update_config['lang']['vi'] = array();

// Tiếng Việt
$nv_update_config['lang']['vi']['nv_up_addtable'] = 'Thêm các bảng dữ liệu mới';
$nv_update_config['lang']['vi']['nv_up_copydata'] = 'Chuyển dữ liệu sang bảng mới';
$nv_update_config['lang']['vi']['nv_up_structre'] = 'Thay đổi cấu trúc dữ liệu các bảng cũ';
$nv_update_config['lang']['vi']['nv_up_finish'] = 'Đánh dấu phiên bản mới';

$nv_update_config['tasklist'] = array();
$nv_update_config['tasklist'][] = array(
    'r' => '4.0.29',
    'rq' => 1,
    'l' => 'nv_up_addtable',
    'f' => 'nv_up_addtable'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.0.29',
    'rq' => 2,
    'l' => 'nv_up_copydata',
    'f' => 'nv_up_copydata'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.0.29',
    'rq' => 1,
    'l' => 'nv_up_structre',
    'f' => 'nv_up_structre'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.0.29',
    'rq' => 1,
    'l' => 'nv_up_finish',
    'f' => 'nv_up_finish'
);

// Danh sach cac function
/*
Chuan hoa tra ve:
array(
'status' =>
'complete' => 
'next' =>
'link' =>
'lang' =>
'message' =>
);
status: Trang thai tien trinh dang chay
- 0: That bai
- 1: Thanh cong
complete: Trang thai hoan thanh tat ca tien trinh
- 0: Chua hoan thanh tien trinh nay
- 1: Da hoan thanh tien trinh nay
next:
- 0: Tiep tuc ham nay voi "link"
- 1: Chuyen sang ham tiep theo
link:
- NO
- Url to next loading
lang:
- ALL: Tat ca ngon ngu
- NO: Khong co ngon ngu loi
- LangKey: Ngon ngu bi loi vi,en,fr ...
message:
- Any message
Duoc ho tro boi bien $nv_update_baseurl de load lai nhieu lan mot function
Kieu cap nhat module duoc ho tro boi bien $old_module_version
*/

$array_modlang_update = array();
$array_modtable_update = array();

// Lay danh sach ngon ngu
$result = $db->query("SELECT lang FROM " . $db_config['prefix'] . "_setup_language WHERE setup=1");
while (list($_tmp) = $result->fetch(PDO::FETCH_NUM)) {
    $array_modlang_update[$_tmp] = array("lang" => $_tmp, "mod" => array());

    // Get all module
    $result1 = $db->query("SELECT title, module_data FROM " . $db_config['prefix'] . "_" . $_tmp . "_modules WHERE module_file=" . $db->quote($nv_update_config['formodule']));
    while (list($_modt, $_modd) = $result1->fetch(PDO::FETCH_NUM)) {
        $array_modlang_update[$_tmp]['mod'][] = array("module_title" => $_modt, "module_data" => $_modd);
        $array_modtable_update[] = $db_config['prefix'] . "_" . $_tmp . "_" . $_modd;
    }
}

/**
 * nv_up_addtable()
 *
 * @return
 *
 */
function nv_up_addtable()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $array_modlang_update;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    foreach ($array_modlang_update as $lang => $array_mod) {
        foreach ($array_mod['mod'] as $module_info) {
            $table_prefix = $db_config['prefix'] . "_" . $lang . "_" . $module_info['module_data'];
            
            // Thêm bảng fileupload
            try {
                $db->query("CREATE TABLE IF NOT EXISTS " . $table_prefix . "_files (
                    file_id int(11) unsigned NOT NULL AUTO_INCREMENT,
                    download_id int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID file download',
                    server_id smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'ID fileserver hoặc 0 nếu là local',
                    file_path varchar(255) NOT NULL DEFAULT '',
                    scorm_path varchar(255) NOT NULL DEFAULT '',
                    filesize int(11) NOT NULL DEFAULT '0',
                    weight smallint(4) unsigned NOT NULL DEFAULT '0',
                    status tinyint(1) unsigned NOT NULL DEFAULT '0',
                    PRIMARY KEY (file_id),
                    KEY download_id (download_id),
                    KEY server_id (server_id)
                )ENGINE=MyISAM");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Thêm bảng uploadserver
            try {
                $db->query("CREATE TABLE " . $table_prefix . "_server (
                    server_id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
                    server_name varchar(250) NOT NULL DEFAULT '',
                    upload_url varchar(255) NOT NULL DEFAULT '',
                    access_key varchar(255) NOT NULL DEFAULT '',
                    secret_key varchar(255) NOT NULL DEFAULT '',
                    status tinyint(1) unsigned NOT NULL DEFAULT '0',
                    UNIQUE KEY server_name (server_name),
                    PRIMARY KEY (server_id)
                )ENGINE=MyISAM");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Thêm bảng detail
            try {
                $db->query("CREATE TABLE IF NOT EXISTS " . $table_prefix . "_detail (
                    id int(11) unsigned NOT NULL,
                    description mediumtext NOT NULL,
                    linkdirect text NOT NULL,
                    groups_comment varchar(255) NOT NULL,
                    groups_view varchar(255) NOT NULL,
                    groups_onlineview varchar(255) NOT NULL,
                    groups_download varchar(255) NOT NULL,
                    rating_detail varchar(255) NOT NULL,
                    PRIMARY KEY (id)
                )ENGINE=MyISAM");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Thêm hết các trường vào bảng rows
            try {
                $db->query("ALTER TABLE " . $table_prefix . " 
                    ADD groups_onlineview VARCHAR(255) NOT NULL DEFAULT '' AFTER groups_view, 
                    ADD num_fileupload SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER copyright, 
                    ADD num_linkdirect SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER num_fileupload
                ");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }

    return $return;
}

/**
 * nv_up_copydata()
 *
 * @return
 *
 */
function nv_up_copydata()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $array_modlang_update, $nv_Request, $array_modtable_update;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    $request = array();
    $request['umodkey'] = $nv_Request->get_int('umodkey', 'get,post', 0); // Cập nhật cho module nào
    $request['uoffset'] = $nv_Request->get_int('uoffset', 'get,post', 0); // Vị trí chạy tiếp theo
    
    // Kết thúc quá trình nếu hết bảng update
    if (!isset($array_modtable_update[$request['umodkey']])) {
        return $return;
    }
    
    $return['complete'] = 0;
    $return['next'] = 0;
    
    $table_prefix = $array_modtable_update[$request['umodkey']];
    $limit_rows = 1;
    
    $db->sqlreset()->select("id, description, fileupload, linkdirect, groups_comment, groups_view, groups_onlineview, groups_download, rating_detail")->from($table_prefix)->order("id ASC")->limit($limit_rows)->offset($request['uoffset']);
    $sql = $db->sql();
    $result = $db->query($sql);
    $num_rows = $result->rowCount();
    
    while ($row = $result->fetch()) {
        // Tính lại thống kê
        $num_fileupload = 0;
        $num_linkdirect = 0;

        // Copy fileupload
        $fileupload = explode('[NV]', $row['fileupload']);
        $weight = 1;
        
        foreach ($fileupload as $file) {
            if (! empty($file)) {
                $file2 = NV_UPLOADS_DIR . $file;
                
                if (file_exists(NV_ROOTDIR . '/' . $file2) and ($filesize = filesize(NV_ROOTDIR . '/' . $file2)) != 0) {
                    $num_fileupload ++;
                    
                    $sql = 'INSERT INTO ' . $table_prefix . '_files (
                        download_id, server_id, file_path, scorm_path, filesize, weight, status
                    ) VALUES (
                        ' . $row['id'] . ', 0, :file_path, :scorm_path, :filesize, ' . ($weight++) . ', 1
                    )';
                    $data_insert = array();
                    $data_insert['file_path'] = $file;
                    $data_insert['scorm_path'] = '';
                    $data_insert['filesize'] = $filesize;
                    $file_id = $db->insert_id($sql, 'file_id', $data_insert);
                    
                    if (!$file_id) {
                        $return['status'] = 0;
                        return $return;
                    }
                }
            }
        }
        
        // Kiểm tra linkredirect
        $linkdirect = explode('[NV]', $row['linkdirect']);
        foreach ($linkdirect as $links) {
            $links = array_filter(explode('<br />', $links));
            $num_linkdirect += sizeof($links);
        }
        
        // Copy detail
        if (empty($row['groups_onlineview'])) {
            $row['groups_onlineview'] = $row['groups_download'];
        }
        
        try {
            $stmt = $db->prepare("INSERT INTO " . $table_prefix . "_detail (
                id, description, linkdirect, groups_comment, groups_view, groups_onlineview, groups_download, rating_detail
            ) VALUES( 
                " . $row['id'] . ", :description, :linkdirect, :groups_comment, :groups_view, :groups_onlineview, :groups_download, :rating_detail
            )");
            
            $stmt->bindParam(':description', $row['description'], PDO::PARAM_STR, strlen($row['description']));
            $stmt->bindParam(':linkdirect', $row['linkdirect'], PDO::PARAM_STR, strlen($row['linkdirect']));
            $stmt->bindParam(':groups_comment', $row['groups_comment'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_view', $row['groups_view'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_onlineview', $row['groups_onlineview'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_download', $row['groups_download'], PDO::PARAM_STR);
            $stmt->bindParam(':rating_detail', $row['rating_detail'], PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            trigger_error($e->getMessage());
            $return['status'] = 0;
            return $return;
        }
        
        // Cập nhật lại thông tin
        try {
            $db->query("UPDATE " . $table_prefix . " SET num_fileupload=" . $num_fileupload . ", num_linkdirect=" . $num_linkdirect . " WHERE id=" . $row['id']);
        } catch (PDOException $e) {
            // Nothing
        }
    }
    
    if ($num_rows < $limit_rows) {
        $request['uoffset'] = 0;
        $request['umodkey'] ++;
    } else {
        // Tiếp tục
        $request['uoffset'] += $limit_rows;
    }
    
    $return['link'] = $nv_update_baseurl . '&umodkey=' . $request['umodkey'] . '&uoffset=' . $request['uoffset'];
    return $return;
}


/**
 * nv_up_structre()
 *
 * @return
 *
 */
function nv_up_structre()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $array_modlang_update;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    foreach ($array_modlang_update as $lang => $array_mod) {
        foreach ($array_mod['mod'] as $module_info) {
            $table_prefix = $db_config['prefix'] . "_" . $lang . "_" . $module_info['module_data'];
            
            // Chỉnh sửa bảng categories
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_categories ADD groups_onlineview VARCHAR(255) NOT NULL DEFAULT '' AFTER groups_view");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Cập nhật lại quyền xem trực tuyến
            try {
                $db->query("UPDATE " . $table_prefix . "_categories SET groups_onlineview=groups_download");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Thêm một số cấu hình module
            try {
                $db->query("INSERT INTO " . $table_prefix . "_config VALUES
                ('delfile_mode', '0'),
                ('structure_upload', 'Ym'),
                ('scorm_handle_mode', '0'),
                ('fileserver', 0)");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Xóa bỏ các trưởng bảng rows
            try {
                $db->query("ALTER TABLE " . $table_prefix . " 
                  DROP fileupload,
                  DROP description,
                  DROP linkdirect,
                  DROP groups_comment,
                  DROP groups_view,
                  DROP groups_onlineview,
                  DROP groups_download,
                  DROP rating_detail
                ");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Mở rộng giới hạn các trường
            try {
                $db->query("ALTER TABLE " . $table_prefix . " CHANGE id id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_report CHANGE fid fid INT(11) UNSIGNED NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_tags CHANGE did did INT(11) UNSIGNED NOT NULL AUTO_INCREMENT");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_tags_id CHANGE did did INT(11) UNSIGNED NOT NULL DEFAULT '0'");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            
            // Đánh index
            try {
                $db->query("ALTER TABLE " . $table_prefix . " ADD INDEX title (title), ADD INDEX status (status), ADD INDEX uploadtime (uploadtime), ADD INDEX updatetime (updatetime)");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }

    return $return;
}

/**
 * nv_up_finish()
 *
 * @return
 *
 */
function nv_up_finish()
{
    global $nv_update_baseurl, $db, $db_config, $nv_Cache, $nv_update_config;

    $return = array(
        'status' => 1,
        'complete' => 1,
        'next' => 1,
        'link' => 'NO',
        'lang' => 'NO',
        'message' => ''
    );

    try {
        $num = $db->query("SELECT COUNT(*) FROM " . $db_config['prefix'] . "_setup_extensions WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'")->fetchColumn();
        $version = "4.0.29 1463590800";
        
        if (!$num) {
            $db->query("INSERT INTO " . $db_config['prefix'] . "_setup_extensions (
                id, type, title, is_sys, is_virtual, basename, table_prefix, version, addtime, author, note
            ) VALUES (
                25, 'module', 'download', 0, 1, 'download', 'download', '4.0.29 1463590800', " . NV_CURRENTTIME . ", 'VINADES.,JSC (contact@vinades.vn)', 
                'Module download for NukeViet'
            )");
        } else {
            $db->query("UPDATE " . $db_config['prefix'] . "_setup_extensions SET 
                id=25, 
                version='" . $version . "', 
                author='VINADES.,JSC (contact@vinades.vn)' 
            WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'");
        }
    } catch (PDOException $e) {
        trigger_error($e->getMessage());
    }

    $nv_Cache->delAll();
    return $return;
}
