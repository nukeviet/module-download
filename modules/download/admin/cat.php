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

// Get cat alias
if ($nv_Request->isset_request('gettitle', 'post')) {
    $title = $nv_Request->get_title('gettitle', 'post', '');
    $alias = change_alias($title);
    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_categories where alias = :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $parentid = $nv_Request->get_int('parentid', 'post', 0);
        if ($parentid > 0) {
            $main_alias = $db->query('SELECT alias FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $parentid)->fetchColumn();
            $alias = $main_alias . '-' . $alias;
        } else {
            $weight = $db->query('SELECT MAX(id) FROM ' . NV_MOD_TABLE . '_categories')->fetchColumn();
            $weight = intval($weight) + 1;
            $alias = $alias . '-' . $weight;
        }
    }

    include NV_ROOTDIR . '/includes/header.php';
    echo $alias;
    include NV_ROOTDIR . '/includes/footer.php';
}

/**
 * nv_del_cat()
 *
 * @param mixed $catid
 * @return
 */
function nv_del_cat($catid)
{
    global $db, $module_name, $module_data, $admin_info, $nv_Cache;

    $sql = 'SELECT parentid, title FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $catid;
    list($p, $title) = $db->query($sql)->fetch(3);

    $sql = 'SELECT id, fileupload, fileimage FROM ' . NV_MOD_TABLE . ' WHERE catid=' . $catid;
    $result = $db->query($sql);

    $ids = array();
    while (list($id, $fileupload, $fileimage) = $result->fetch(3)) {
        $ids[] = $id;
    }

    if (! empty($ids)) {
        $ids = implode(',', $ids);
        $sql = 'DELETE FROM ' . NV_PREFIXLANG . '_comment WHERE module=' . $db->quote($module_name) . ' AND id IN (' . $ids . ')';
        $db->query($sql);

        $sql = 'DELETE FROM ' . NV_MOD_TABLE . '_report WHERE fid IN (' . $ids . ')';
        $db->query($sql);
    }

    $sql = 'DELETE FROM ' . NV_MOD_TABLE . ' WHERE catid=' . $catid;
    $db->query($sql);

    $sql = 'SELECT id FROM ' . NV_MOD_TABLE . '_categories WHERE parentid=' . $catid;
    $result = $db->query($sql);
    while (list($id) = $result->fetch(3)) {
        nv_del_cat($id);
    }

    $sql = 'DELETE FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $catid;
    $db->query($sql);

    $nv_Cache->delMod($module_name);

    nv_insert_logs(NV_LANG_DATA, $module_data, 'Delete Category', $title, $admin_info['userid']);
}

// Delete cat
if ($nv_Request->isset_request('del', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $catid = $nv_Request->get_int('catid', 'post', 0);
    $sql = 'SELECT id, parentid FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $catid;
    $result = $db->query($sql);
    list($catid, $parentid) = $result->fetch(3);

    if (empty($catid)) {
        die('NO');
    }

    nv_del_cat($catid);
    nv_fix_cat_order();
    $nv_Cache->delMod($module_name);

    die('OK');
}

// Change weight cat
if ($nv_Request->isset_request('changeweight', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $catid = $nv_Request->get_int('catid', 'post', 0);
    $new = $nv_Request->get_int('new', 'post', 0);

    $query = 'SELECT parentid FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $catid;
    $row = $db->query($query)->fetch();
    if (empty($row)) {
        die('NO');
    }

    $query = 'SELECT id FROM ' . NV_MOD_TABLE . '_categories WHERE id!=' . $catid . ' AND parentid=' . $row['parentid'] . ' ORDER BY weight ASC';
    $result = $db->query($query);
    $weight = 0;
    while ($row = $result->fetch()) {
        ++$weight;
        if ($weight == $new) {
            ++$weight;
        }
        $db->query('UPDATE ' . NV_MOD_TABLE . '_categories SET weight=' . $weight . ' WHERE id=' . $row['id']);
    }
    $db->query('UPDATE ' . NV_MOD_TABLE . '_categories SET weight=' . $new . ' WHERE id=' . $catid);

    $nv_Cache->delMod($module_name);
    die('OK');
}

// Active - Deactive
if ($nv_Request->isset_request('changestatus', 'post')) {
    if (! defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $catid = $nv_Request->get_int('catid', 'post', 0);

    $query = 'SELECT status FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $catid;
    $row = $db->query($query)->fetch();
    if (empty($row)) {
        die('NO');
    }

    $status = $row['status'] ? 0 : 1;

    $sql = 'UPDATE ' . NV_MOD_TABLE . '_categories SET status=' . $status . ' WHERE id=' . $catid;
    $db->query($sql);

    $nv_Cache->delMod($module_name);

    die('OK');
}

// List cat
$page_title = $lang_module['download_catmanager'];

$pid = $nv_Request->get_int('pid', 'get', 0);

$sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_categories WHERE parentid=' . $pid . ' ORDER BY weight ASC';
$_array_cat = $db->query($sql)->fetchAll();
$num = sizeof($_array_cat);

if (! $num) {
    if (empty($pid)) {
        $_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat-content';
    } else {
        $_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat';
    }
    Header('Location: ' . $_url);
    exit();
}

if ($pid) {
    $sql2 = 'SELECT title,parentid FROM ' . NV_MOD_TABLE . '_categories WHERE id=' . $pid;
    $result2 = $db->query($sql2);
    list($parentid, $parentid2) = $result2->fetch(3);
    $caption = sprintf($lang_module['table_caption2'], $parentid);
    $parentid = '<a href="' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=cat&amp;pid=' . $parentid2 . '">' . $parentid . '</a>';
} else {
    $caption = $lang_module['table_caption1'];
    $parentid = $lang_module['category_cat_maincat'];
}

$list = array();
$a = 0;
foreach ($_array_cat as $row) {
    $numsub = $db->query('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_categories WHERE parentid=' . $row['id'])->fetchColumn();
    if ($numsub) {
        $numsub_str = ' (<a href="' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=cat&amp;pid=' . $row['id'] . '">' . $numsub . ' ' . $lang_module['category_cat_sub'] . '</a>)';
    } else {
        $numsub_str = '';
    }

    $weight = array();
    for ($i = 1; $i <= $num; ++$i) {
        $weight[$i]['title'] = $i;
        $weight[$i]['pos'] = $i;
        $weight[$i]['selected'] = ($i == $row['weight']) ? ' selected="selected"' : '';
    }

    $list[$row['id']] = array(
        'id' => ( int )$row['id'],
        'title' => $row['title'],
        'titlelink' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;catid=' . $row['id'],
        'numsub' => $numsub,
        'numsub_str' => $numsub_str,
        'parentid' => $parentid,
        'viewcat' => $row['viewcat'],
        'numlink' => $row['numlink'],
        'weight' => $weight,
        'status' => $row['status'] ? ' checked="checked"' : ''
    );

    ++$a;
}

$xtpl = new XTemplate('cat_list.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('ADD_NEW_CAT', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=cat-content&amp;pid=' . $row['parentid']);
$xtpl->assign('TABLE_CAPTION', $caption);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('LANG', $lang_module);

foreach ($list as $row) {
    $xtpl->assign('ROW', $row);

    foreach ($row['weight'] as $weight) {
        $xtpl->assign('WEIGHT', $weight);
        $xtpl->parse('main.row.weight');
    }

    $array_viewcat = array(
        'viewcat_list_new' => $lang_module['config_indexfile_list_new']
    );
    if ($row['numsub'] > 0) {
        $array_viewcat['viewcat_main_bottom'] = $lang_module['config_indexfile_main_bottom'];
    }

    foreach ($array_viewcat as $key => $value) {
        $sl = $key == $row['viewcat'] ? 'selected="selected"' : '';
        $xtpl->assign('VIEWCAT', array( 'key' => $key, 'value' => $value, 'selected' => $sl ));
        $xtpl->parse('main.row.viewcat');
    }

    for ($i = 1; $i <= 20; $i++) {
        $sl = $row['numlink'] == $i ? 'selected="selected"' : '';
        $xtpl->assign('NUMLINK', array( 'key' => $i, 'selected' => $sl ));
        $xtpl->parse('main.row.numlink');
    }

    $xtpl->assign('EDIT_URL', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=cat-content&amp;catid=' . $row['id']);
    $xtpl->parse('main.row');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
