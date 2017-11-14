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
$nv_update_config['packageID'] = 'NVUDDOWNLOAD4300';

// Cap nhat cho module nao, de trong neu la cap nhat NukeViet, ten thu muc module neu la cap nhat module
$nv_update_config['formodule'] = 'download';

// Thong tin phien ban, tac gia, ho tro
$nv_update_config['release_date'] = 1510628543;
$nv_update_config['author'] = 'VINADES.,JSC (contact@vinades.vn)';
$nv_update_config['support_website'] = 'https://github.com/nukeviet/module-download/tree/to-4.3.00';
$nv_update_config['to_version'] = '4.3.00';
$nv_update_config['allow_old_version'] = array('4.0.29', '4.1.00', '4.1.01', '4.2.01', '4.2.02', '4.2.03');

// 0:Nang cap bang tay, 1:Nang cap tu dong, 2:Nang cap nua tu dong
$nv_update_config['update_auto_type'] = 1;

$nv_update_config['lang'] = array();
$nv_update_config['lang']['vi'] = array();

// Tiếng Việt
$nv_update_config['lang']['vi']['nv_up_addthis'] = 'Thêm chức năng chia sẻ';
$nv_update_config['lang']['vi']['nv_up_onlineview'] = 'Thêm chức năng xử lý xem trực tuyến';
$nv_update_config['lang']['vi']['nv_up_cuttitlelen'] = 'Thêm cấu hình cắt tiêu đề';
$nv_update_config['lang']['vi']['nv_up_s1'] = 'Cấu hình ai được đăng tài liệu theo chủ đề';
$nv_update_config['lang']['vi']['nv_up_s2'] = 'Cấu hình hiển thị, bắt buộc nhập các trường dữ liệu';

$nv_update_config['lang']['vi']['nv_up_4300_config'] = 'Thêm các cấu hình bản 4.3.00';

$nv_update_config['lang']['vi']['nv_up_finish'] = 'Đánh dấu phiên bản mới';

$nv_update_config['tasklist'] = array();
$nv_update_config['tasklist'][] = array(
    'r' => '4.1.00',
    'rq' => 1,
    'l' => 'nv_up_addthis',
    'f' => 'nv_up_addthis'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.1.00',
    'rq' => 1,
    'l' => 'nv_up_onlineview',
    'f' => 'nv_up_onlineview'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.1.01',
    'rq' => 1,
    'l' => 'nv_up_cuttitlelen',
    'f' => 'nv_up_cuttitlelen'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.1.02',
    'rq' => 1,
    'l' => 'nv_up_s1',
    'f' => 'nv_up_s1'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.1.02',
    'rq' => 1,
    'l' => 'nv_up_s2',
    'f' => 'nv_up_s2'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.3.00',
    'rq' => 1,
    'l' => 'nv_up_4300_config',
    'f' => 'nv_up_4300_config'
);
$nv_update_config['tasklist'][] = array(
    'r' => '4.3.00',
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
 * nv_up_addthis()
 *
 * @return
 *
 */
function nv_up_addthis()
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
            try {
                $db->query("INSERT INTO " . $table_prefix . "_config (config_name, config_value) VALUES ('shareport', 'none'), ('addthis_pubid', '');");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }
    return $return;
}

/**
 * nv_up_onlineview()
 *
 * @return
 *
 */
function nv_up_onlineview()
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
            try {
                $db->query("INSERT INTO " . $table_prefix . "_config (config_name, config_value) VALUES ('pdf_handler', 'filetmp');");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }
    return $return;
}

/**
 * nv_up_cuttitlelen()
 *
 * @return
 *
 */
function nv_up_cuttitlelen()
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
            try {
                $db->query("INSERT INTO " . $table_prefix . "_config (config_name, config_value) VALUES ('list_title_length', '30');");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }
    return $return;
}

/**
 * nv_up_s1()
 *
 * @return
 *
 */
function nv_up_s1()
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
            try {
                $db->query("ALTER TABLE " . $table_prefix . "_categories ADD groups_addfile VARCHAR(255) NOT NULL DEFAULT '4' AFTER groups_download;");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }

    return $return;
}

/**
 * nv_up_s2()
 *
 * @return
 *
 */
function nv_up_s2()
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
            try {
                $db->query("INSERT INTO " . $table_prefix . "_config (config_name, config_value) VALUES
                ('arr_dis_ad_author_name', '1'),
                ('arr_dis_ad_author_email', '1'),
                ('arr_dis_ad_author_url', '1'),
                ('arr_dis_ad_fileimage', '1'),
                ('arr_dis_ad_introtext', '1'),
                ('arr_dis_ad_description', '1'),
                ('arr_dis_ad_linkdirect', '1'),
                ('arr_dis_ad_filesize', '1'),
                ('arr_dis_ad_version', '1'),
                ('arr_dis_ad_copyright', '1'),
                ('arr_req_ad_author_name', '0'),
                ('arr_req_ad_author_email', '0'),
                ('arr_req_ad_author_url', '0'),
                ('arr_req_ad_fileimage', '0'),
                ('arr_req_ad_introtext', '0'),
                ('arr_req_ad_description', '0'),
                ('arr_req_ad_linkdirect', '0'),
                ('arr_req_ad_filesize', '0'),
                ('arr_req_ad_version', '0'),
                ('arr_req_ad_copyright', '0'),
                ('arr_dis_ur_author_name', '1'),
                ('arr_dis_ur_author_email', '1'),
                ('arr_dis_ur_author_url', '1'),
                ('arr_dis_ur_fileimage', '1'),
                ('arr_dis_ur_introtext', '1'),
                ('arr_dis_ur_description', '1'),
                ('arr_dis_ur_linkdirect', '1'),
                ('arr_dis_ur_filesize', '1'),
                ('arr_dis_ur_version', '1'),
                ('arr_dis_ur_copyright', '1'),
                ('arr_req_ur_author_name', '0'),
                ('arr_req_ur_author_email', '0'),
                ('arr_req_ur_author_url', '0'),
                ('arr_req_ur_fileimage', '0'),
                ('arr_req_ur_introtext', '0'),
                ('arr_req_ur_description', '0'),
                ('arr_req_ur_linkdirect', '0'),
                ('arr_req_ur_filesize', '0'),
                ('arr_req_ur_version', '0'),
                ('arr_req_ur_copyright', '0');");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
        }
    }

    return $return;
}

/**
 * nv_up_4300_config()
 *
 * @return
 *
 */
function nv_up_4300_config()
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
            try {
                $db->query("INSERT INTO `" . $table_prefix . "_config` (`config_name`, `config_value`) VALUES ('copy_document', '0');");
            } catch (PDOException $e) {
                trigger_error($e->getMessage());
            }
            try {
                $db->query("INSERT INTO `" . $table_prefix . "_config` (`config_name`, `config_value`) VALUES ('allow_fupload_import', '0');");
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

    @nv_deletefile(NV_ROOTDIR . '/themes/default/js/pdf.js', true);

    try {
        $num = $db->query("SELECT COUNT(*) FROM " . $db_config['prefix'] . "_setup_extensions WHERE basename='" . $nv_update_config['formodule'] . "' AND type='module'")->fetchColumn();
        $version = $nv_update_config['to_version'] . " " . $nv_update_config['release_date'];

        if (!$num) {
            $db->query("INSERT INTO " . $db_config['prefix'] . "_setup_extensions (
                id, type, title, is_sys, is_virtual, basename, table_prefix, version, addtime, author, note
            ) VALUES (
                25, 'module', 'download', 0, 1, 'download', 'download', '" . $nv_update_config['to_version'] . " " . $nv_update_config['release_date'] . "', " . NV_CURRENTTIME . ", 'VINADES.,JSC (contact@vinades.vn)',
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

    $nv_Cache->delAll(true);
    return $return;
}
