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

if (empty($list_cats)) {
    $page_title = $module_info['custom_title'];
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme('');
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

$contents = '';
$download_config = nv_mod_down_config();
$per_page = $download_config['per_page_home'];

$today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
$yesterday = $today - 86400;

// Rating
if ($nv_Request->isset_request('rating', 'post')) {
    $in = implode(',', array_keys($list_cats));

    $rating = $nv_Request->get_string('rating', 'post', '');

    if (preg_match('/^([0-9]+)\_([1-5]+)$/', $rating, $m)) {
        $id = ( int )$m[1];
        $point = ( int )$m[2];

        if ($id and ($point > 0 and $point < 6)) {
            $sql = 'SELECT id FROM ' . NV_MOD_TABLE . ' WHERE id=' . $id . ' AND catid IN (' . $in . ') AND status=1';
            list($id) = $db->query($sql)->fetch(3);
            if ($id) {
                $rating_detail = $db->query('SELECT rating_detail FROM ' . NV_MOD_TABLE . '_detail WHERE id=' . $id)->fetchColumn();
                
                $total = $click = 0;
                if (! empty($rating_detail)) {
                    $rating_detail = explode('|', $rating_detail);
                    $total = ( int )$rating_detail[0];
                    $click = ( int )$rating_detail[1];
                }

                $flrt = $nv_Request->get_string('flrt', 'session', '');
                $flrt = ! empty($flrt) ? unserialize($flrt) : array();

                if ($id and ! in_array($id, $flrt)) {
                    $flrt[] = $id;
                    $flrt = serialize($flrt);
                    $nv_Request->set_Session('flrt', $flrt);

                    $total = $total + $point;
                    ++$click;
                    $rating_detail = $total . '|' . $click ;

                    $stmt = $db->prepare('UPDATE ' . NV_MOD_TABLE . '_detail SET rating_detail= :rating_detail WHERE id=' . $id);
                    $stmt->bindParam(':rating_detail', $rating_detail, PDO::PARAM_STR);
                    $stmt->execute();
                }

                if ($total and $click) {
                    $round = round($total / $click);
                    $content = sprintf($lang_module['rating_string'], $lang_module['file_rating' . $round], $total, $click);
                } else {
                    $content = $lang_module['file_rating0'];
                }

                die($content);
            }
        }
    }

    die($lang_module['rating_error1']);
}

$page_title = $mod_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];
$viewcat = $download_config['indexfile'];
$contents = '';

if ($viewcat == 'viewcat_main_bottom') {
    $array_cats = array();
    foreach ($list_cats as $value) {
        if (empty($value['parentid'])) {
            $array_cat = GetCatidInParent($value['id']);

            $db->sqlreset()
                ->select('COUNT(*)')
                ->from(NV_MOD_TABLE)
                ->where('status=1 AND catid IN (' . implode(',', $array_cat) . ')');

            $num_items = $db->query($db->sql())->fetchColumn();

            if ($num_items) {
                $db->select('id, catid, title, alias, introtext , uploadtime, author_name, filesize, fileimage, view_hits, download_hits, comment_hits');
                $db->order('uploadtime DESC');
                $db->limit($value['numlink']);

                $result = $db->query($db->sql());

                $array_item = array();
                while ($row = $result->fetch()) {
                    $uploadtime = ( int )$row['uploadtime'];
                    if ($uploadtime >= $today) {
                        $uploadtime = $lang_module['today'] . ', ' . date('H:i', $row['uploadtime']);
                    } elseif ($uploadtime >= $yesterday) {
                        $uploadtime = $lang_module['yesterday'] . ', ' . date('H:i', $row['uploadtime']);
                    } else {
                        $uploadtime = nv_date('d/m/Y H:i', $row['uploadtime']);
                    }

                    $array_item[$row['id']] = array(
                        'id' => ( int )$row['id'],
                        'title' => $row['title'],
                        'introtext' => $row['introtext'],
                        'uploadtime' => $uploadtime,
                        'author_name' => ! empty($row['author_name']) ? $row['author_name'] : $lang_module['unknown'],
                        'filesize' => ! empty($row['filesize']) ? nv_convertfromBytes($row['filesize']) : '',
                        'imagesrc' => (! empty($row['fileimage'])) ? NV_BASE_SITEURL . NV_FILES_DIR . $row['fileimage'] : '',
                        'view_hits' => ( int )$row['view_hits'],
                        'download_hits' => ( int )$row['download_hits'],
                        'comment_hits' => ( int )$row['comment_hits'],
                        'more_link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
                        'edit_link' => (defined('NV_IS_MODADMIN')) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $row['id'] : '',
                        'del_link' => (defined('NV_IS_MODADMIN')) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name : ''
                    );
                }

                $numfile = $db->query('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . ' WHERE catid IN ( ' . implode(',', $array_cat) . ' )')->fetchColumn();

                $array_cats[$value['id']] = array();
                $array_cats[$value['id']]['catid'] = $value['id'];
                $array_cats[$value['id']]['title'] = $value['title'];
                $array_cats[$value['id']]['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $value['alias'];
                $array_cats[$value['id']]['description'] = $list_cats[$value['id']]['description'];
                $array_cats[$value['id']]['numfile'] = $numfile;
                $array_cats[$value['id']]['viewcat'] = $list_cats[$value['id']]['viewcat'];
                $array_cats[$value['id']]['subcats'] = $list_cats[$value['id']]['subcatid'];
                $array_cats[$value['id']]['items'] = $array_item;
            }
        }
    }

    $contents = theme_viewcat_main($viewcat, $array_cats);
} elseif ($viewcat == 'viewcat_list_new') {
    $array_files = array();
    $base_url = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name;

    // Fetch Limit
    $db->sqlreset()
      ->select('COUNT(*)')
      ->from(NV_MOD_TABLE)
      ->where('status=1');

    $all_page = $db->query($db->sql())->fetchColumn();

    $db->select('id, catid, title, alias, introtext , uploadtime, author_name, filesize, fileimage, view_hits, download_hits, comment_hits')
      ->order('uploadtime DESC')
      ->limit($per_page)
      ->offset(($page - 1) * $per_page);

    $_query = $db->query($db->sql());

    while ($row = $_query->fetch()) {
        $uploadtime = ( int )$row['uploadtime'];
        if ($uploadtime >= $today) {
            $uploadtime = $lang_module['today'] . ', ' . date('H:i', $row['uploadtime']);
        } elseif ($uploadtime >= $yesterday) {
            $uploadtime = $lang_module['yesterday'] . ', ' . date('H:i', $row['uploadtime']);
        } else {
            $uploadtime = nv_date('d/m/Y H:i', $row['uploadtime']);
        }

        $array_files[$row['id']] = array(
            'id' => ( int )$row['id'],
            'title' => $row['title'],
            'introtext' => $row['introtext'],
            'uploadtime' => $uploadtime,
            'author_name' => ! empty($row['author_name']) ? $row['author_name'] : $lang_module['unknown'],
            'filesize' => ! empty($row['filesize']) ? nv_convertfromBytes($row['filesize']) : '',
            'imagesrc' => (! empty($row['fileimage'])) ? NV_BASE_SITEURL . NV_FILES_DIR . $row['fileimage'] : '',
            'view_hits' => ( int )$row['view_hits'],
            'download_hits' => ( int )$row['download_hits'],
            'comment_hits' => ( int )$row['comment_hits'],
            'more_link' => NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $list_cats[$row['catid']]['alias'] . '/' . $row['alias'] . $global_config['rewrite_exturl'],
            'edit_link' => (defined('NV_IS_MODADMIN')) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;edit=1&amp;id=' . $row['id'] : '',
            'del_link' => (defined('NV_IS_MODADMIN')) ? NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name : ''
        );
    }
    $page_title = $page > 1 ? $page_title . ' - ' . $lang_module['page'] . ' ' . $page : $page_title;
    $page = nv_alias_page($page_title, $base_url, $all_page, $per_page, $page);
    $contents = theme_viewcat_list($array_files, $page, array(), 0);
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
