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

/**
 * nv_show_tags_list()
 *
 * @param string $q
 * @param integer $incomplete
 * @return
 */
function nv_show_tags_list($q = '', $incomplete = false)
{
    global $db, $lang_module, $lang_global, $module_name, $op, $module_file, $global_config, $module_info, $module_config, $nv_Request;
    
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name. '&' . NV_OP_VARIABLE . '='.$op;
    
    $db->sqlreset()
        ->select('count(*)')
        ->from(NV_MOD_TABLE . '_tags')
        ->order('alias ASC');
        
    if (! empty($q)) {
        $q = strip_punctuation($q);
        $db->where('keywords LIKE %' . $q . '%');
    }
        
    if ($incomplete === true) {
        $db->where('description = \'\'');
    }
    
    $num_items = $db->query($db->sql())->fetchColumn();

    $page = $nv_Request->get_int('page', 'post,get', 1);
    $per_page = 20;

    $db->select('*')
    ->order('alias ASC')
    ->limit($per_page)
    ->offset(($page - 1) * $per_page);

    $result2 = $db->query($db->sql());

    $xtpl = new XTemplate('tags_lists.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);

    $number = ($page - 1) * $per_page;
    while ($row = $result2->fetch()) {
        $row['number'] = ++$number;
        $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . $row['alias'];
        $row['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;did=' . $row['did'] . ($incomplete === true ? '&amp;incomplete=1' : '') . '#edit';

        $xtpl->assign('ROW', $row);

        if (empty($row['description']) and $incomplete === false) {
            $xtpl->parse('main.loop.incomplete');
        }

        $xtpl->parse('main.loop');
    }

    if (empty($q) and $number >= 20) {
        $xtpl->parse('main.other');
    }
    
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    
    if (!empty($generate_page)) {
        $xtpl->assign('GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.generate_page');
    }
    
    $xtpl->parse('main');
    $contents = $xtpl->text('main');

    if (empty($contents)) {
        $contents = '&nbsp;';
    }
    return $contents;
}

if ($nv_Request->isset_request('del_did', 'get')) {
    $did = $nv_Request->get_int('del_did', 'get', 0);
    if ($did) {
        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_tags WHERE did=' . $did);
        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_tags_id WHERE did=' . $did);
    }
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_show_tags_list();
    include NV_ROOTDIR . '/includes/footer.php';
} elseif ($nv_Request->isset_request('q', 'get')) {
    $q = $nv_Request->get_title('q', 'get', '');

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_show_tags_list($q);
    include NV_ROOTDIR . '/includes/footer.php';
}

$error = '';
$savecat = 0;
$incomplete = $nv_Request->get_bool('incomplete', 'get,post', false);
list($did, $title, $alias, $description, $image, $keywords) = array( 0, '', '', '', '', '' );

$savecat = $nv_Request->get_int('savecat', 'post', 0);
if (! empty($savecat)) {
    $did = $nv_Request->get_int('did', 'post', 0);
    $keywords = $nv_Request->get_title('keywords', 'post', '');
    $alias = $nv_Request->get_title('alias', 'post', '');
    $description = $nv_Request->get_string('description', 'post', '');
    $description = nv_nl2br(nv_htmlspecialchars(strip_tags($description)), '<br />');

    $alias = str_replace('-', ' ', nv_unhtmlspecialchars($alias));
    $keywords = explode(',', $keywords);
    $keywords[] = $alias;
    $keywords = array_map('strip_punctuation', $keywords);
    $keywords = array_map('trim', $keywords);
    $keywords = array_diff($keywords, array( '' ));
    $keywords = array_unique($keywords);
    $keywords = implode(',', $keywords);

    $alias = str_replace(' ', '-', strip_punctuation($alias));

    $image = $nv_Request->get_string('image', 'post', '');
    if (is_file(NV_DOCUMENT_ROOT . $image)) {
        $lu = strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/');
        $image = substr($image, $lu);
    } else {
        $image = '';
    }
    if (empty($alias)) {
        $error = $lang_module['error_name'];
    } else {
        if ($did == 0) {
            $sth = $db->prepare('INSERT INTO ' . NV_MOD_TABLE . '_tags (numdownload, alias, description, image, keywords) VALUES (0, :alias, :description, :image, :keywords)');
            $msg_lg = 'add_tags';
        } else {
            $sth = $db->prepare('UPDATE ' . NV_MOD_TABLE . '_tags SET alias = :alias, description = :description, image = :image, keywords = :keywords WHERE did =' . $did);
            $msg_lg = 'edit_tags';
        }

        try {
            $sth->bindParam(':alias', $alias, PDO::PARAM_STR);
            $sth->bindParam(':description', $description, PDO::PARAM_STR);
            $sth->bindParam(':image', $image, PDO::PARAM_STR);
            $sth->bindParam(':keywords', $keywords, PDO::PARAM_STR);
            $sth->execute();

            nv_insert_logs(NV_LANG_DATA, $module_name, $msg_lg, $alias, $admin_info['userid']);
            Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . ($incomplete ? '&incomplete=1' : ''));
            die();
        } catch (PDOException $e) {
            $error = $lang_module['errorsave'];
        }
    }
}

$did = $nv_Request->get_int('did', 'get', 0);

if ($did > 0) {
    list($did, $alias, $description, $image, $keywords) = $db->query('SELECT did, alias, description, image, keywords FROM ' . NV_MOD_TABLE . '_tags where did=' . $did)->fetch(3);
    $lang_module['add_tags'] = $lang_module['edit_tags'];
}

$lang_global['title_suggest_max'] = sprintf($lang_global['length_suggest_max'], 65);
$lang_global['description_suggest_max'] = sprintf($lang_global['length_suggest_max'], 160);

$xtpl = new XTemplate('tags.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

$xtpl->assign('TAGS_LIST', nv_show_tags_list('', $incomplete));

$xtpl->assign('did', $did);
$xtpl->assign('alias', $alias);
$xtpl->assign('keywords', $keywords);
$xtpl->assign('description', nv_htmlspecialchars(nv_br2nl($description)));

if (! empty($image) and file_exists(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $image)) {
    $image = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $image;
}
$xtpl->assign('image', $image);
$xtpl->assign('UPLOAD_CURRENT', NV_BASE_SITEURL.NV_UPLOADS_DIR . '/' . $module_upload);

if (! empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

// Nhac nho dang xem cac tags duoi dang khong co mo ta, thay doi gia tri submit form
if ($incomplete) {
    $xtpl->assign('ALL_LINK', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op);

    $xtpl->parse('main.incomplete');
    $xtpl->parse('main.incomplete_link');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['download_tags'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';