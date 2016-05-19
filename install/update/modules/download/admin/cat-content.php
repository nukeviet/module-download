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

$set_active_op = 'cat';
$catid = $nv_Request->get_int('catid', 'get', 0);

if ($catid) {
    $sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $catid;
    $row = $db->query($sql)->fetch();
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat');
        exit();
    }

    $page_title = $lang_module['editcat_cat'];
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;catid=' . $row['id'];
    
    $array['parentid'] = (int)$row['parentid'];
    $array['title'] = $row['title'];
    $array['alias'] = $row['alias'];
    $array['description'] = $row['description'];

    $array['groups_view'] = $row['groups_view'];
    $array['groups_onlineview'] = $row['groups_onlineview'];
    $array['groups_download'] = $row['groups_download'];
} else {
    $pid = $nv_Request->get_int('pid', 'get', 0);
    $page_title = $lang_module['addcat_titlebox'];
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;pid=' . $pid;
    
    $array['parentid'] = $pid;
    $array['title'] = '';
    $array['alias'] = '';
    $array['description'] = '';
    $array['groups_view'] = $array['groups_onlineview'] = $array['groups_download'] = '6';
}

$error = '';
$groups_list = nv_groups_list();

if ($nv_Request->isset_request('submit', 'post')) {
    $array['parentid'] = $nv_Request->get_int('parentid', 'post', 0);
    $array['title'] = $nv_Request->get_title('title', 'post', '', 1);
    $array['description'] = $nv_Request->get_title('description', 'post', '', 1);
    $array['alias'] = $nv_Request->get_title('alias', 'post', '');
    $array['alias'] = ($array['alias'] == '') ? change_alias($array['title']) : change_alias($array['alias']);
    
    if (empty($array['title'])) {
        $error = $lang_module['error_cat2'];
    } else {
        if (! empty($array['parentid'])) {
            $sql = 'SELECT COUNT(*) AS count FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $array['parentid'];
            $count = $db->query($sql)->fetchColumn();
            if (! $count) {
                $error = $lang_module['error_cat3'];
            }
        }

        if (empty($error)) {
            $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_categories WHERE alias= :alias' . (!empty($catid) ? ' AND id!=' . $catid : ''));
            $stmt->bindParam(':alias', $array['alias'], PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            if ($count) {
                $error = $lang_module['error_cat1'];
            }
        }
    }

    $_groups_post = $nv_Request->get_array('groups_view', 'post', array());
    $array['groups_view'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_onlineview', 'post', array());
    $array['groups_onlineview'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_download', 'post', array());
    $array['groups_download'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    if (empty($error)) {
        if (empty($catid) or $array['parentid'] != $row['parentid']) {
            $sql = 'SELECT MAX(weight) AS new_weight FROM ' . NV_MOD_TABLE . '_categories WHERE parentid=' . $array['parentid'];
            $result = $db->query($sql);
            $new_weight = $result->fetchColumn();
            $new_weight = ( int )$new_weight;
            ++$new_weight;
        } else {
            $new_weight = $row['weight'];
        }
        
        if (!empty($catid)) {
            $stmt = $db->prepare('UPDATE ' . NV_MOD_TABLE . '_categories SET
                parentid=' . $array['parentid'] . ',
                title= :title,
                alias= :alias,
                description= :description,
                groups_view= :groups_view,
                groups_onlineview= :groups_onlineview,
                groups_download= :groups_download,
                weight=' . $new_weight . '
            WHERE id=' . $catid);

            $stmt->bindParam(':title', $array['title'], PDO::PARAM_STR);
            $stmt->bindParam(':alias', $array['alias'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $array['description'], PDO::PARAM_STR, strlen($array['description']));
            $stmt->bindParam(':groups_view', $array['groups_view'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_onlineview', $array['groups_onlineview'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_download', $array['groups_download'], PDO::PARAM_STR);

            if ($stmt->execute()) {
                nv_fix_cat_order();
                $nv_Cache->delMod($module_name);
                nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['editcat_cat'], $array['title'], $admin_info['userid']);

                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat&pid=' . $array['parentid']);
                exit();
            } else {
                $error = $lang_module['error_cat5'];
            }
        } else {
            $sql = 'INSERT INTO ' . NV_MOD_TABLE . '_categories (
                parentid, title, alias, description, groups_view, groups_onlineview, groups_download, weight, status
            ) VALUES (
                 ' . $array['parentid'] . ',
                 :title,
                 :alias,
                 :description,
                 :groups_view,
                 :groups_onlineview,
                 :groups_download,
                 ' . $new_weight . ',
                 1
            )';
            
            $data_insert = array();
            $data_insert['title'] = $array['title'];
            $data_insert['alias'] = $array['alias'];
            $data_insert['description'] = $array['description'];
            $data_insert['groups_view'] = $array['groups_view'];
            $data_insert['groups_onlineview'] = $array['groups_onlineview'];
            $data_insert['groups_download'] = $array['groups_download'];
            
            if ($db->insert_id($sql, 'id', $data_insert)) {
                nv_fix_cat_order();
                nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['addcat_titlebox'], $array['title'], $admin_info['userid']);
                $nv_Cache->delMod($module_name);
    
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat&pid=' . $array['parentid']);
                exit();
            } else {
                $error = $lang_module['error_cat4'];
            }
        }
    }
}

$listcats = array(
    array(
        'id' => 0,
        'title' => $lang_module['category_cat_maincat'],
        'lev' => 0,
        'selected' => ''
    )
);
$list_cats = $listcats + $list_cats;

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

$xtpl = new XTemplate('cat_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('FORM_ACTION', $form_action);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATA', $array);
$xtpl->assign('ONCHANGE', $catid ? '' : 'onchange="get_alias();"');

if (! empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

$list_catsubs = GetCatidInParent($catid);
foreach ($list_cats as $_catid => $value) {
    if (!in_array($_catid, $list_catsubs)) {
        $value['space'] = '';
        if ($value['lev'] > 0) {
            for ($i = 1; $i <= $value['lev']; $i++) {
                $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        }
        $value['selected'] = $_catid == $array['parentid'] ? ' selected="selected"' : '';
    
        $xtpl->assign('LISTCATS', $value);
        $xtpl->parse('main.parentid');
    }
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

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
