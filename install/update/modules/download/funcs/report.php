<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:30
 */

if (! defined('NV_IS_MOD_DOWNLOAD')) {
    die('Stop!!!');
}

if (! defined('NV_IS_AJAX')) {
    die('Wrong URL');
}

$id = $nv_Request->get_int('id', 'post', 0);

$dlrp = $nv_Request->get_string('dlrp', 'session', '');

$dlrp = ! empty($dlrp) ? unserialize($dlrp) : array();

if ($id and ! in_array($id, $dlrp)) {
    $dlrp[] = $id;
    $dlrp = serialize($dlrp);
    $nv_Request->set_Session('dlrp', $dlrp);

    $query = 'SELECT id, title FROM ' . NV_MOD_TABLE . ' WHERE id=' . $id;
    list($id, $title) = $db->query($query)->fetch(3);
    if ($id) {
        $stmt = $db->prepare('INSERT INTO ' . NV_MOD_TABLE . '_report VALUES (' . $id . ', :ip, ' . NV_CURRENTTIME . ')');
        $stmt->bindParam(':ip', $client_info['ip'], PDO::PARAM_STR);
        if ($stmt->execute()) {
            // Them vao thong bao
            $sender_id = !empty($user_info) ? $user_info['userid'] : 0;
            nv_insert_notification($module_name, 'report', array( 'title' => $title ), $id, 0, $sender_id, 1);
        }
    }
}

die('OK');
