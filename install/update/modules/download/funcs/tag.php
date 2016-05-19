<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:14
 */

if (! defined('NV_IS_MOD_DOWNLOAD')) {
    die('Stop!!!');
}

$alias = $nv_Request->get_title('alias', 'get');
$array_op = explode('/', $alias);
$alias = $array_op[0];
$page=1;
if (isset($array_op[1])) {
    if (sizeof($array_op) == 2 and preg_match('/^page\-([0-9]+)$/', $array_op[1], $m)) {
        $page = intval($m[1]);
    } else {
        $alias = '';
    }
}
$page_title = trim(str_replace('-', ' ', $alias));
$per_page=10;

if (! empty($page_title) and $page_title == strip_punctuation($page_title)) {
    $array_item=array();

    $stmt = $db->prepare('SELECT did, image, description, keywords FROM ' . NV_MOD_TABLE . '_tags WHERE alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    list($tid, $image_tag, $description, $key_words) = $stmt->fetch(3);

    if ($tid > 0) {
        $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=tag/' . $alias;
        if ($page > 1) {
            $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
        }

        $array_mod_title[] = array(
            'catid' => 0,
            'title' => $page_title,
            'link' => $base_url
        );

        $db->sqlreset()
            ->select('COUNT(*)')
            ->from(NV_MOD_TABLE)
            ->where('status=1 AND id IN (SELECT id FROM ' . NV_MOD_TABLE . '_tags_id WHERE did=' . $tid . ')');

        $num_items = $db->query($db->sql())->fetchColumn();

        $db->select('*')
            ->order('uploadtime DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);

        $result = $db->query($db->sql());

        while ($rows = $result->fetch()) {
            $rows['fileimage']=(! empty($rows['fileimage'])) ? NV_BASE_SITEURL . NV_FILES_DIR . $rows['fileimage'] : '';
            $rows['more_link']=NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$rows['catid']]['alias'] . '/' . $rows['alias'] . $global_config['rewrite_exturl'];
            $array_item[$rows['id']]=$rows;
        }

        $generate_page = nv_alias_page($page_title, $base_url, $num_items, $per_page, $page);
    }

    $contents = view_items_tag($array_item, $generate_page);

    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
}

$redirect = '<meta http-equiv="Refresh" content="3;URL=' . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true) . '" />';
nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content'] . $redirect);
