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

global $global_config, $lang_module, $lang_global, $module_info, $module_name, $module_file, $nv_Request;

$download_config = nv_mod_down_config();

$page = $nv_Request->get_int('page', 'get', 1);
$per_page = $download_config['per_page_child'];
$base_url_rewrite = $request_uri = urldecode($_SERVER['REQUEST_URI']);
$base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=search';
$where = $generate_page = '';
$is_search = false;
$array = array();

$key = nv_substr($nv_Request->get_title('q', 'get', '', 1), 0, NV_MAX_SEARCH_LENGTH);
$cat = $nv_Request->get_int('cat', 'get', 0);

$page_title = $lang_module['search'] . ' ' . $key;

$db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_MOD_TABLE);

$base_url .= '&q=' . $key;
if (! empty($key)) {
    $where .= ' AND (title LIKE :title OR description LIKE :description OR introtext LIKE :introtext)';
}

if (! empty($cat) and isset($list_cats[$cat])) {
    $base_url .= '&cat=' . $cat;
    $array_cat = GetCatidInParent($cat);
    $where .= ' AND catid IN (' . implode(',', $array_cat) . ')';
} else {
    $base_url_rewrite = str_replace('&cat=' . $cat, '', $base_url_rewrite);
}

if (!empty($where)) {
    $is_search = true;
    $base_url_rewrite = nv_url_rewrite($base_url_rewrite, true);

    if ($request_uri != $base_url_rewrite and NV_MAIN_DOMAIN . $request_uri != $base_url_rewrite) {
        header('Location: ' . $base_url_rewrite);
        die();
    }

    $db->where('status=1' . $where);

    $sth = $db->prepare($db->sql());
    if (! empty($key)) {
        $keyword = '%' . addcslashes($key, '_%') . '%';
        $sth->bindParam(':title', $keyword, PDO::PARAM_STR);
        $sth->bindParam(':description', $keyword, PDO::PARAM_STR);
        $sth->bindParam(':introtext', $keyword, PDO::PARAM_STR);
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();

    if (! empty($num_items)) {
        $download_config = nv_mod_down_config();
        $lang_module['search_result_count'] = sprintf($lang_module['search_result_count'], $num_items);

        $today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
        $yesterday = $today - 86400;

        $db->select('id, catid, title, alias, introtext , uploadtime, author_name, filesize, fileimage, view_hits, download_hits, comment_hits')
            ->order('uploadtime DESC')
            ->limit($per_page)
            ->offset(($page - 1) * $per_page);

        $sth = $db->prepare($db->sql());
        if (! empty($key)) {
            $keyword = '%' . addcslashes($key, '_%') . '%';
            $sth->bindParam(':title', $keyword, PDO::PARAM_STR);
            $sth->bindParam(':description', $keyword, PDO::PARAM_STR);
            $sth->bindParam(':introtext', $keyword, PDO::PARAM_STR);
        }
        $sth->execute();

        while ($row = $sth->fetch()) {
            $cattitle = '<a href="' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '">' . $list_cats[$row['catid']]['title'] . '</a>';

            $uploadtime = ( int )$row['uploadtime'];
            if ($uploadtime >= $today) {
                $uploadtime = $lang_module['today'] . ', ' . date('H:i', $row['uploadtime']);
            } elseif ($uploadtime >= $yesterday) {
                $uploadtime = $lang_module['yesterday'] . ', ' . date('H:i', $row['uploadtime']);
            } else {
                $uploadtime = nv_date('d/m/Y H:i', $row['uploadtime']);
            }

            $array[$row['id']] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'cattitle' => $cattitle,
                'introtext' => $row['introtext'],
                'uploadtime' => $uploadtime,
                'author_name' => $row['author_name'],
                'filesize' => ! empty($row['filesize']) ? nv_convertfromBytes($row['filesize']) : '',
                'imagesrc' => (! empty($row['fileimage'])) ? NV_BASE_SITEURL . NV_FILES_DIR . $row['fileimage'] : '',
                'view_hits' => $row['view_hits'],
                'download_hits' => $row['download_hits'],
                'more_link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
                'edit_link' => (defined('NV_IS_MODADMIN')) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $row['id'] : '',
                'del_link' => (defined('NV_IS_MODADMIN')) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name : ''
            );
        }
        $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    }
}

$contents = theme_search($array, $generate_page, $is_search);
if ($page > 1) {
    $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
}

$key_words = $description = 'no';

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
