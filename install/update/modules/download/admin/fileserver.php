<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2010 - 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 08 Apr 2012 00:00:00 GMT GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

$page_title = $lang_module['fileserver'];
$set_active_op = 'config';

// Delete fileserver
if ($nv_Request->isset_request('delete', 'post')) {
    $server_id = $nv_Request->get_int('server_id', 'post', 0);

    $sql = 'SELECT server_id FROM ' . NV_MOD_TABLE . '_server WHERE server_id=' . $server_id;
    $server_id = $db->query($sql)->fetchColumn();

    if (empty($server_id))
        die('NO_' . $server_id);

    $sql = 'DELETE FROM ' . NV_MOD_TABLE . '_server WHERE server_id = ' . $server_id;

    if ($db->exec($sql)) {
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete FileServer', 'ID: ' . $server_id, $admin_info['userid']);
        $db->query('UPDATE ' . NV_MOD_TABLE . '_files SET server_id = 0 WHERE server_id =' . $server_id);
        $nv_Cache->delMod($module_name);
    } else {
        die('NO_' . $server_id);
    }

    include NV_ROOTDIR . '/includes/header.php';
    echo 'OK_' . $server_id;
    include NV_ROOTDIR . '/includes/footer.php';
}

// Change status
if ($nv_Request->isset_request('changestatus', 'post')) {
    $server_id = $nv_Request->get_int('server_id', 'post', 0);

    if (empty($server_id))
        die("NO");

    $sql = "SELECT server_name, status FROM " . NV_MOD_TABLE . "_server WHERE server_id=" . $server_id;
    $result = $db->query($sql);
    list($server_name, $status) = $result->fetch(3);

    if (empty($server_name))
        die('NO');
    $status = $status == 1 ? 0 : 1;

    $sql = "UPDATE " . NV_MOD_TABLE . "_server SET status = " . $status . " WHERE server_id = " . $server_id;
    $db->query($sql);

    $nv_Cache->delMod($module_name);

    die("OK");
}

$data = array();
$error = '';

$server_id = $nv_Request->get_int('server_id', 'post,get', 0);

if (!empty($server_id)) {
    $sql = 'SELECT server_id, server_name, upload_url, access_key, secret_key FROM ' . NV_MOD_TABLE . '_server WHERE server_id = ' . $server_id;
    $result = $db->query($sql);
    $data = $result->fetch();

    if (empty($data)) {
        nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content']);
    }

    $caption = $lang_module['fileserver_edit'];
} else {
    $data = array(
        'server_id' => 0,
        'server_name' => '',
        'upload_url' => '',
        'access_key' => '',
        'secret_key' => ''
    );

    $caption = $lang_module['fileserver_add'];
}

if ($nv_Request->isset_request('submit', 'post')) {
    $data['server_name'] = $nv_Request->get_title('server_name', 'post', '', true);
    $data['upload_url'] = $nv_Request->get_title('upload_url', 'post', '', false);
    $data['access_key'] = $nv_Request->get_title('access_key', 'post', '', false);
    $data['secret_key'] = $nv_Request->get_title('secret_key', 'post', '', false);

    if (empty($data['server_name'])) {
        $error = $lang_module['fileserver_error_server_name'];
    } elseif (empty($data['upload_url'])) {
        $error = $lang_module['fileserver_error_upload_url'];
    } else {
        $sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_server WHERE server_name = :server_name' . ($server_id ? ' AND server_id != ' . $server_id : '');
        $sth = $db->prepare($sql);
        $sth->bindParam(':server_name', $data['server_name'], PDO::PARAM_STR);
        $sth->execute();
        $num = $sth->fetchColumn();

        if (!empty($num)) {
            $error = $lang_module['fileserver_error_exists'];
        } else {
            if (!$server_id) {
                $sql = 'INSERT INTO ' . NV_MOD_TABLE . '_server (server_name, upload_url, access_key, secret_key, status) VALUES (
					:server_name, :upload_url, :access_key, :secret_key, 1
				)';
            } else {
                $sql = 'UPDATE ' . NV_MOD_TABLE . '_server SET 
                    server_name = :server_name, upload_url = :upload_url, access_key = :access_key, secret_key = :secret_key 
                WHERE server_id = ' . $server_id;
            }

            try {
                $sth = $db->prepare($sql);
                $sth->bindParam(':server_name', $data['server_name'], PDO::PARAM_STR);
                $sth->bindParam(':upload_url', $data['upload_url'], PDO::PARAM_STR);
                $sth->bindParam(':access_key', $data['access_key'], PDO::PARAM_STR);
                $sth->bindParam(':secret_key', $data['secret_key'], PDO::PARAM_STR);

                if ($sth->execute()) {
                    if ($server_id) {
                        nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit FileServer', 'ID: ' . $server_id, $admin_info['userid']);
                    } else {
                        nv_insert_logs(NV_LANG_DATA, $module_name, 'Add FileServer', ' ', $admin_info['userid']);
                    }

                    $nv_Cache->delMod($module_name);
                    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
                    die();
                } else {
                    $error = $lang_module['errorsave'];
                }
            }
            catch (PDOException $e) {
                $error = $lang_module['errorsave'];
            }
        }
    }
}

$xtpl = new XTemplate('fileserver.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('CAPTION', $caption);
$xtpl->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op);
$xtpl->assign('DATA', $data);

$sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_server ORDER BY server_id DESC';
$array = $db->query($sql)->fetchAll();
$num = sizeof($array);

foreach ($array as $row) {
    $row['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=fileserver&amp;server_id=' . $row['server_id'] . "#addedit";
    $row['status'] = $row['status'] ? " checked=\"checked\"" : "";

    $xtpl->assign('ROW', $row);
    $xtpl->parse('main.loop');
}

if (empty($data['alias'])) {
    $xtpl->parse('main.getalias');
}

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
