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

// Avtive - Deactive
if ($nv_Request->isset_request('changestatus', 'post') or $nv_Request->isset_request('active', 'post') or $nv_Request->isset_request('deactive', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }
    
    $control_status = -1;
    if ($nv_Request->isset_request('active', 'post')) {
        $control_status = 1;
    } elseif ($nv_Request->isset_request('deactive', 'post')) {
        $control_status = 0;
    }
    
    $id = $nv_Request->get_int('id', 'post', 0);
    $listid = $nv_Request->get_string('listid', 'post', '');
    
    if ($listid != '') {
        $array_id = array_filter(array_map('intval', explode(',', $listid)));
    } else {
        $array_id = array($id);
    }
    
    foreach ($array_id as $id) {
        $sql = 'SELECT status FROM ' . NV_MOD_TABLE . ' WHERE id=' . $id;
        $row = $db->query($sql)->fetch();
        if (empty($row)) {
            die('NO');
        }
    
        $status = $control_status != -1 ? $control_status : ($row['status'] ? 0 : 1);
    
        $db->query('UPDATE ' . NV_MOD_TABLE . ' SET status=' . $status . ' WHERE id=' . $id);
    }

    $nv_Cache->delMod($module_name);
    die('OK');
}

// Delete file
if ($nv_Request->isset_request('del', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $id = $nv_Request->get_int('id', 'post', 0);
    $delfile = $nv_Request->get_int('delfile', 'post', -1);
    $listid = $nv_Request->get_string('listid', 'post', '');
    
    if ($delfile < 0) {
        if ($module_config[$module_name]['delfile_mode'] == 2) {
            die('NO');
        }
        
        $delfile = $module_config[$module_name]['delfile_mode'];
    }
    
    if ($listid != '') {
        $array_id = array_filter(array_map('intval', explode(',', $listid)));
    } else {
        $array_id = array($id);
    }
    
    foreach ($array_id as $id) {
        $sql = 'SELECT fileimage, title FROM ' . NV_MOD_TABLE . ' WHERE id=' . $id;
        $row = $db->query($sql)->fetch();
        if (empty($row)) {
            die('NO');
        }
        
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_comment WHERE module=' . $db->quote($module_name) . ' AND id=' . $id);
        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_report WHERE fid=' . $id);
        
        if ($db->query('DELETE FROM ' . NV_MOD_TABLE . ' WHERE id=' . $id) and $delfile) {
            $fileupload = $db->query('SELECT * FROM ' . NV_MOD_TABLE . '_files WHERE download_id=' . $id)->fetchAll();
            
            foreach ($fileupload as $file) {
                if ($file['server_id'] == 0) {
                    // Delete local file
                    nv_deletefile(NV_UPLOADS_REAL_DIR . $file['file_path']);
                    $db->query("DELETE FROM " . NV_UPLOAD_GLOBALTABLE . "_file WHERE did=(SELECT did FROM " . NV_UPLOAD_GLOBALTABLE . "_dir WHERE dirname=" . $db->quote(NV_UPLOADS_DIR . '/' . ltrim(dirname($file['file_path']), '/')) . ") AND title=" . $db->quote(basename($file['file_path'])));
                    
                    if (!empty($file['scorm_path']) and is_dir(NV_UPLOADS_REAL_DIR . $file['scorm_path'])) {
                        nv_deletefile(NV_UPLOADS_REAL_DIR . $file['scorm_path'], true);
                    }
                } else {
                    // Delete file on fileserver
                }
            }
        }
        
        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_files WHERE download_id=' . $id);    
        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_detail WHERE id=' . $id);    
        
        // Xoa thong bao loi
        nv_delete_notification(NV_LANG_DATA, $module_name, 'report', $id);
    }
    
    // Resets the contents of the opcode cache
    if ($delfile and nv_function_exists('opcache_reset')) {
        opcache_reset();
    }
        
    $nv_Cache->delMod($module_name);

    nv_insert_logs(NV_LANG_DATA, $module_data, $lang_module['download_filequeue_del'], $row['title'], $admin_info['userid']);
    die('OK');
}

// List file
$page_title = $lang_module['download_filemanager'];

$where = array();
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;
$array_search = array(
    'q' => $nv_Request->get_title('q', 'get', ''),
    'catid' => $nv_Request->get_int('catid', 'get', 0),
    'active' => $nv_Request->get_int('active', 'get', '-1'),
    'per_page' => $nv_Request->get_int('per_page', 'get', '30')
);

if (empty($list_cats)) {
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat-content');
    exit();
}

if (!empty($array_search['q'])) {
    $base_url .= '&q=' . $array_search['q'];
    $where[] = '(title LIKE "%' . $array_search['q'] . '%" OR description LIKE "%' . $array_search['q'] . '%" OR introtext LIKE "%' . $array_search['q'] . '%" OR author_name LIKE "%' . $array_search['q'] . '%" OR author_email LIKE "%' . $array_search['q'] . '%")';
}
if (!empty($array_search['catid'])) {
    $base_url .= '&catid=' . $array_search['catid'];
    $where[] = 'catid IN (' . implode(',', GetCatidInParent($array_search['catid'])) . ')';
}
if ($array_search['active'] >= 0) {
    $base_url .= '&active=' . $array_search['active'];
    $where[] = 'status=' . $array_search['active'];
}

$db->sqlreset()->select('COUNT(*)')->from(NV_MOD_TABLE);
    
if (!empty($where)) {
    $db->where(implode(' AND ', $where));
}

$num_items = $db->query($db->sql())->fetchColumn();

$page = $nv_Request->get_int('page', 'get', 1);
$per_page = $array_search['per_page'];

$db->select('*')
    ->order('uploadtime DESC')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);

$result2 = $db->query($db->sql());

$array = array();

while ($row = $result2->fetch()) {
    $array[$row['id']] = array(
        'id' => $row['id'],
        'title' => $row['title'],
        'link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
        'cattitle' => $list_cats[$row['catid']]['title'],
        'catlink' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;catid=' . $row['catid'],
        'uploadtime' => nv_date('d/m/Y H:i', $row['uploadtime']),
        'status' => $row['status'] ? ' checked="checked"' : '',
        'view_hits' => $row['view_hits'],
        'download_hits' => $row['download_hits'],
        'comment_hits' => $row['comment_hits']
    );
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);

$array_list_action = array(
    'del0' => $lang_module['file_delmode0'],
    'del1' => $lang_module['file_delmode1'],
    'active' => $lang_module['action_active'],
    'deactive' => $lang_module['action_deactive'],
);

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('SEARCH', $array_search);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
$xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
$xtpl->assign('ADD_NEW_FILE', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content');
$xtpl->assign('DELETEFILE_MODE', $module_config[$module_name]['delfile_mode']);

if (! empty($array)) {
    foreach ($array as $row) {
        $xtpl->assign('ROW', $row);
        $xtpl->assign('EDIT_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;id=' . $row['id']);
        $xtpl->parse('main.row');
    }
}

foreach ($list_cats as $catid => $value) {
    $value['space'] = '';
    if ($value['lev'] > 0) {
        for ($i = 1; $i <= $value['lev']; $i++) {
            $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
    }
    $value['selected'] = $catid == $array_search['catid'] ? ' selected="selected"' : '';

    $xtpl->assign('LISTCATS', $value);
    $xtpl->parse('main.catid');
}

$array_active = array(
    '1' => $lang_global['yes'],
    '0' => $lang_global['no']
);
foreach ($array_active as $key => $value) {
    $sl = $array_search['active'] == $key ? 'selected="selected"' : '';
    $xtpl->assign('ACTIVE', array( 'key' => $key, 'value' => $value, 'selected' => $sl ));
    $xtpl->parse('main.active');
}

for ($i = 5; $i <= 300; $i+=5) {
    $sl = $array_search['per_page'] == $i ? 'selected="selected"' : '';
    $xtpl->assign('PER_PAGE', array( 'key' => $i, 'selected' => $sl ));
    $xtpl->parse('main.per_page');
}

if (! empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

while (list($action_i, $title_i) = each($array_list_action)) {
    $action_assign = array(
        'value' => $action_i,
        'title' => $title_i
    );
    $xtpl->assign('ACTION', $action_assign);
    $xtpl->parse('main.action');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
