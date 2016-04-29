<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 12/9/2010, 22:27
 */

if (! defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

// Get alias
if ($nv_Request->isset_request('gettitle', 'post')) {
    $title = $nv_Request->get_title('gettitle', 'post', '');
    $alias = change_alias($title);
    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . ' where alias = :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $weight = $db->query('SELECT MAX(id) FROM ' . NV_MOD_TABLE)->fetchColumn();
        $weight = intval($weight) + 1;
        $alias = $alias . '-' . $weight;
    }

    include NV_ROOTDIR . '/includes/header.php';
    echo $alias;
    include NV_ROOTDIR . '/includes/footer.php';
}

$id = $nv_Request->get_int('id', 'get', 0);
$groups_list = nv_groups_list();
$array = array();
$error = '';

if ($id) {
    $sql = 'SELECT * FROM ' . NV_MOD_TABLE . ' WHERE id=' . $id;
    $row = $db->query($sql)->fetch();
    
    if (empty($row)) {
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
        exit();
    }
    
    $report = $nv_Request->isset_request('report', 'get');
    // Cap nhat trang thai thong bao
    if ($report) {
        nv_status_notification(NV_LANG_DATA, $module_name, 'report', $id);
    }
    $report = $report ? '&amp;report=1' : '';
    
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;id=' . $id . $report;
    $page_title = $lang_module['download_editfile'];
    
    $array['id'] = ( int )$row['id'];
    $array['catid'] = ( int )$row['catid'];
    $array['title'] = $row['title'];
    $array['alias'] = $row['alias'];
    $array['description'] = nv_editor_br2nl($row['description']);
    $array['introtext'] = nv_br2nl($row['introtext']);
    $array['author_name'] = $row['author_name'];
    $array['author_email'] = $row['author_email'];
    $array['author_url'] = $row['author_url'];
    $array['fileupload'] = $row['fileupload'];
    $array['linkdirect'] = $row['linkdirect'];
    $array['version'] = $row['version'];
    $array['filesize'] = ( int )$row['filesize'];
    $array['fileimage'] = $row['fileimage'];
    $array['copyright'] = $row['copyright'];
    $array['groups_comment'] = $row['groups_comment'];
    $array['groups_view'] = $row['groups_view'];
    $array['groups_onlineview'] = $row['groups_onlineview'];
    $array['groups_download'] = $row['groups_download'];

    $array['fileupload'] = ! empty($array['fileupload']) ? explode('[NV]', $array['fileupload']) : array();
    if (! empty($array['linkdirect'])) {
        $array['linkdirect'] = explode('[NV]', $array['linkdirect']);
        $array['linkdirect'] = array_map('nv_br2nl', $array['linkdirect']);
    } else {
        $array['linkdirect'] = array();
    }
    $array['is_del_report'] = 1;
    
    $array_keywords_old = array();
    $_query_tag = $db->query('SELECT did, keyword FROM ' . NV_MOD_TABLE . '_tags_id WHERE id=' . $id . ' ORDER BY keyword ASC');
    while ($row_tag = $_query_tag->fetch()) {
        $array_keywords_old[$row_tag['did']] = $row_tag['keyword'];
    }
    $array['keywords'] = implode(', ', $array_keywords_old);
    $array['keywords_old'] = $array['keywords'];

} else {
    $page_title = $lang_module['file_addfile'];
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    
    $array['id'] = 0;
    $array['catid'] = $nv_Request->get_int('catid', 'get', 0);
    $array['title'] = $array['description'] = $array['introtext'] = $array['author_name'] = $array['author_email'] = $array['author_url'] = $array['version'] = $array['fileimage'] = '';
    $array['fileupload'] = $array['linkdirect'] = array();
    $array['groups_comment'] = $module_config[$module_name]['setcomm'];
    $array['groups_view'] = $array['groups_onlineview'] = $array['groups_download'] = '6';
    $array['filesize'] = 0;
    $array['is_del_report'] = 1;
    $array['keywords_old'] = '';
    $array_keywords_old = array();
    $report = false;
}

if ($nv_Request->isset_request('submit', 'post')) {
    $array['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $array['title'] = $nv_Request->get_title('title', 'post', '', 1);
    $array['description'] = $nv_Request->get_editor('description', '', NV_ALLOWED_HTML_TAGS);
    $array['introtext'] = $nv_Request->get_textarea('introtext', '', NV_ALLOWED_HTML_TAGS);
    $array['author_name'] = $nv_Request->get_title('author_name', 'post', '', 1);
    $array['author_email'] = $nv_Request->get_title('author_email', 'post', '');
    $array['author_url'] = $nv_Request->get_title('author_url', 'post', '');
    $array['fileupload'] = $nv_Request->get_typed_array('fileupload', 'post', 'string');
    $array['linkdirect'] = $nv_Request->get_typed_array('linkdirect', 'post', 'string');
    $array['version'] = $nv_Request->get_title('version', 'post', '', 1);
    $array['fileimage'] = $nv_Request->get_title('fileimage', 'post', '');
    $array['copyright'] = $nv_Request->get_title('copyright', 'post', '', 1);
    $array['is_del_report'] = $nv_Request->get_int('is_del_report', 'post', 0);

    $_groups_post = $nv_Request->get_array('groups_view', 'post', array());
    $array['groups_view'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_onlineview', 'post', array());
    $array['groups_onlineview'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_download', 'post', array());
    $array['groups_download'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $_groups_post = $nv_Request->get_array('groups_comment', 'post', array());
    $array['groups_comment'] = ! empty($_groups_post) ? implode(',', nv_groups_post(array_intersect($_groups_post, array_keys($groups_list)))) : '';

    $array['keywords'] = $nv_Request->get_array('keywords', 'post', '');
    $array['keywords'] = implode(', ', $array['keywords']);

    if (! empty($array['author_url'])) {
        if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $array['author_url'])) {
            $array['author_url'] = 'http://' . $array['author_url'];
        }
    }

    $array['filesize'] = 0;
    if (! empty($array['fileupload'])) {
        $fileupload = $array['fileupload'];
        $array['fileupload'] = array();
        $array['filesize'] = 0;
        foreach ($fileupload as $file) {
            if (! empty($file)) {
                $file2 = substr($file, strlen(NV_BASE_SITEURL));
                if (file_exists(NV_ROOTDIR . '/' . $file2) and ($filesize = filesize(NV_ROOTDIR . '/' . $file2)) != 0) {
                    $array['fileupload'][] = substr($file, strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR));
                    $array['filesize'] += $filesize;
                }
            }
        }
    } else {
        $array['fileupload'] = array();
    }

    // Sort image
    if (! empty($array['fileimage'])) {
        if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $array['fileimage'])) {
            $array['fileimage'] = substr($array['fileimage'], strlen(NV_BASE_SITEURL . NV_UPLOADS_DIR));
        }
    }

    if (! empty($array['linkdirect'])) {
        $linkdirect = $array['linkdirect'];
        $array['linkdirect'] = array();
        foreach ($linkdirect as $links) {
            $linkdirect2 = array();
            if (! empty($links)) {
                $links = nv_nl2br($links, '<br />');
                $links = explode('<br />', $links);
                $links = array_map('trim', $links);
                $links = array_unique($links);

                foreach ($links as $link) {
                    if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $link)) {
                        $link = 'http://' . $link;
                    }
                    if (nv_is_url($link)) {
                        $linkdirect2[] = $link;
                    }
                }
            }

            if (! empty($linkdirect2)) {
                $array['linkdirect'][] = implode("\n", $linkdirect2);
            }
        }
    } else {
        $array['linkdirect'] = array();
    }
    if (! empty($array['linkdirect'])) {
        $array['linkdirect'] = array_unique($array['linkdirect']);
    }

    if (! empty($array['linkdirect']) and empty($array['fileupload'])) {
        $array['filesize'] = $nv_Request->get_int('filesize', 'post', 0);
    }

    // Xử lý liên kết tĩnh
    $array['alias'] = $nv_Request->get_title('alias', 'post', '');
    $array['alias'] = !empty($array['alias']) ? change_alias($array['alias']) : change_alias($array['title']);

    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . ' WHERE alias= :alias' . ($id ? ' AND id!=' . $id : ''));
    $stmt->bindParam(':alias', $array['alias'], PDO::PARAM_STR);
    $stmt->execute();
    $is_exists = $stmt->fetchColumn();

    if (! $is_exists) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_tmp WHERE title= :title');
        $stmt->bindParam(':title', $array['title'], PDO::PARAM_STR);
        $stmt->execute();
        $is_exists = $stmt->fetchColumn();
    }

    if (empty($array['title'])) {
        $error = $lang_module['file_error_title'];
    } elseif ($is_exists) {
        $error = $lang_module['file_title_exists'];
    } elseif (! empty($array['author_email']) and ($check_valid_email = nv_check_valid_email($array['author_email'])) != '') {
        $error = $check_valid_email;
    } elseif (! empty($array['author_url']) and ! nv_is_url($array['author_url'])) {
        $error = $lang_module['file_error_author_url'];
    } elseif (empty($array['fileupload']) and empty($array['linkdirect'])) {
        $error = $lang_module['file_error_fileupload'];
    } else {
        $array['introtext'] = ! empty($array['introtext']) ? nv_nl2br($array['introtext'], '<br />') : '';
        $array['fileupload'] = (! empty($array['fileupload'])) ? implode('[NV]', $array['fileupload']) : '';
        if ((! empty($array['linkdirect']))) {
            $array['linkdirect'] = array_map('nv_nl2br', $array['linkdirect']);
            $array['linkdirect'] = implode('[NV]', $array['linkdirect']);
        } else {
            $array['linkdirect'] = '';
        }
        
        $action_db = false;
        
        if (empty($id)) {
            $sql = "INSERT INTO " . NV_MOD_TABLE . " (
                catid, title, alias, description, introtext, uploadtime, updatetime, user_id, user_name, author_name, author_email, author_url, fileupload, linkdirect, 
                version, filesize, fileimage, status, copyright, view_hits, download_hits, groups_comment, groups_view, groups_onlineview, groups_download, comment_hits, rating_detail
            ) VALUES (
    			 " . $array['catid'] . ",
    			 :title,
    			 :alias ,
    			 :description ,
    			 :introtext ,
    			 " . NV_CURRENTTIME . ",
    			 " . NV_CURRENTTIME . ",
    			 " . $admin_info['admin_id'] . ",
    			 :username,
    			 :author_name ,
    			 :author_email ,
    			 :author_url ,
    			 :fileupload ,
    			 :linkdirect ,
    			 :version ,
    			 " . $array['filesize'] . ",
    			 :fileimage ,
    			 1,
    			 :copyright ,
    			 0, 0,
    			 :groups_comment ,
    			 :groups_view ,
    			 :groups_onlineview ,
    			 :groups_download ,
    			 0, ''
            )";
    
            $data_insert = array();
            $data_insert['title'] = $array['title'];
            $data_insert['alias'] = $array['alias'];
            $data_insert['description'] = $array['description'];
            $data_insert['introtext'] = $array['introtext'];
            $data_insert['username'] = $admin_info['username'];
            $data_insert['author_name'] = $array['author_name'];
            $data_insert['author_email'] = $array['author_email'];
            $data_insert['author_url'] = $array['author_url'];
            $data_insert['fileupload'] = $array['fileupload'];
            $data_insert['linkdirect'] = $array['linkdirect'];
            $data_insert['version'] = $array['version'];
            $data_insert['fileimage'] = $array['fileimage'];
            $data_insert['copyright'] = $array['copyright'];
            $data_insert['groups_comment'] = $array['groups_comment'];
            $data_insert['groups_view'] = $array['groups_view'];
            $data_insert['groups_onlineview'] = $array['groups_onlineview'];
            $data_insert['groups_download'] = $array['groups_download'];
    
            $array['id'] = $db->insert_id($sql, 'id', $data_insert);
    
            if ($array['id'] != 0) {
                $action_db = true;
            } else {
                $error = $lang_module['file_error2'];
            }
        } else {
            $stmt = $db->prepare("UPDATE " . NV_MOD_TABLE . " SET
				 catid=" . $array['catid'] . ",
				 title= :title,
				 alias= :alias,
				 description= :description,
				 introtext= :introtext,
				 updatetime=" . NV_CURRENTTIME . ",
				 author_name= :author_name,
				 author_email= :author_email,
				 author_url= :author_url,
				 fileupload= :fileupload,
				 linkdirect= :linkdirect,
				 version= :version,
				 filesize=" . $array['filesize'] . ",
				 fileimage= :fileimage,
				 copyright= :copyright,
				 groups_comment= :groups_comment,
				 groups_view= :groups_view,
				 groups_onlineview= :groups_onlineview,
				 groups_download= :groups_download
				 WHERE id=" . $id
            );

            $stmt->bindParam(':title', $array['title'], PDO::PARAM_STR);
            $stmt->bindParam(':alias', $array['alias'], PDO::PARAM_STR);
            $stmt->bindParam(':description', $array['description'], PDO::PARAM_STR, strlen($array['description']));
            $stmt->bindParam(':introtext', $array['introtext'], PDO::PARAM_STR, strlen($array['introtext']));
            $stmt->bindParam(':author_name', $array['author_name'], PDO::PARAM_STR);
            $stmt->bindParam(':author_email', $array['author_email'], PDO::PARAM_STR);
            $stmt->bindParam(':author_url', $array['author_url'], PDO::PARAM_STR);
            $stmt->bindParam(':fileupload', $array['fileupload'], PDO::PARAM_STR, strlen($array['fileupload']));
            $stmt->bindParam(':linkdirect', $array['linkdirect'], PDO::PARAM_STR, strlen($array['linkdirect']));
            $stmt->bindParam(':version', $array['version'], PDO::PARAM_STR);
            $stmt->bindParam(':fileimage', $array['fileimage'], PDO::PARAM_STR);
            $stmt->bindParam(':copyright', $array['copyright'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_comment', $array['groups_comment'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_view', $array['groups_view'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_onlineview', $array['groups_onlineview'], PDO::PARAM_STR);
            $stmt->bindParam(':groups_download', $array['groups_download'], PDO::PARAM_STR);

            if (! $stmt->execute()) {
                $error = $lang_module['file_error1'];
            } else {
                $action_db = true;

                if ($report and $array['is_del_report']) {
                    $db->query('DELETE FROM ' . NV_MOD_TABLE . '_report WHERE fid=' . $id);
                }
            }
        }
        
        if ($action_db) {
            if ($array['keywords'] != $array['keywords_old']) {
                $keywords = explode(',', $array['keywords']);
                $keywords = array_map('strip_punctuation', $keywords);
                $keywords = array_map('trim', $keywords);
                $keywords = array_diff($keywords, array( '' ));
                $keywords = array_unique($keywords);
                
                foreach ($keywords as $keyword) {
                    $alias_i = ($module_config[$module_name]['tags_alias']) ? change_alias($keyword) : str_replace(' ', '-', $keyword);
                    $alias_i = nv_strtolower($alias_i);
                    
                    $sth = $db->prepare('SELECT did, alias, description, keywords FROM ' . NV_MOD_TABLE . '_tags where alias= :alias OR FIND_IN_SET(:keyword, keywords)>0');
                    $sth->bindParam(':alias', $alias_i, PDO::PARAM_STR);
                    $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                    $sth->execute();

                    list($did, $alias, $keywords_i) = $sth->fetch(3);
                    
                    if (empty($did)) {
                        $array_insert = array( );
                        $array_insert['alias'] = $alias_i;
                        $array_insert['keyword'] = $keyword;

                        $did = $db->insert_id("INSERT INTO " . NV_MOD_TABLE . "_tags (numdownload, alias, description, image, keywords) VALUES (1, :alias, '', '', :keyword)", "did", $array_insert);
                    } else {
                        $db->query('UPDATE ' . NV_MOD_TABLE . '_tags SET numdownload = numdownload+1 WHERE did = ' . $did);
                    }

                    $_sql = 'SELECT * FROM ' . NV_MOD_TABLE . '_tags_id WHERE id=' . $array['id'] . ' AND did = ' . $did;
                    $_query = $db->query($_sql);
                    $row = $_query->fetch();

                    if (empty($row)) {
                        $sth = $db->prepare('INSERT INTO ' . NV_MOD_TABLE . '_tags_id (id, did, keyword) VALUES (' . $array['id'] . ', ' . intval($did) . ', :keyword)');
                        $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                        $sth->execute();
                    } else {
                        $sth = $db->prepare('UPDATE ' . NV_MOD_TABLE . '_tags_id SET keyword = :keyword WHERE id = ' . $array['id'] . ' AND did=' . intval($did));
                        $sth->bindParam(':keyword', $keyword, PDO::PARAM_STR);
                        $sth->execute();
                    }
                    
                    unset($array_keywords_old[$did]);
                }
                
                foreach ($array_keywords_old as $did => $keyword) {
                    if (! in_array($keyword, $keywords)) {
                        $db->query('DELETE FROM ' . NV_MOD_TABLE . '_tags_id WHERE id = ' . $array['id'] . ' AND did=' . $did);

                        $count_tag = $db->query('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_tags_id WHERE did=' . $did);
                        
                        if ($count_tag->fetchColumn()) {
                            $db->query('UPDATE ' . NV_MOD_TABLE . '_tags SET numdownload = numdownload-1 WHERE did = ' . $did);
                        } else {
                            $db->query('DELETE FROM ' . NV_MOD_TABLE . '_tags WHERE did=' . $did);
                        }
                    }
                }
            }
            
            $nv_Cache->delMod($module_name);
            
            if ($id) {
                nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['download_editfile'], $array['title'], $admin_info['userid']);
            } else {
                nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['file_addfile'], $array['title'], $admin_info['userid']);
            }
            
            Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
            exit();
        }
        
        $array['fileupload'] = (! empty($array['fileupload'])) ? explode('[NV]', $array['fileupload']) : array();
    }
}

$array['description'] = htmlspecialchars(nv_editor_br2nl($array['description']));
$array['introtext'] = nv_htmlspecialchars($array['introtext']);

$array['fileupload_num'] = sizeof($array['fileupload']);
$array['linkdirect_num'] = sizeof($array['linkdirect']);

// Build fileimage
if (! empty($array['fileimage'])) {
    if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $array['fileimage'])) {
        $array['fileimage'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . $array['fileimage'];
    }
}

//Rebuild fileupload
if (! empty($array['fileupload'])) {
    $fileupload = $array['fileupload'];
    $array['fileupload'] = array();
    foreach ($fileupload as $tmp) {
        if (! preg_match('#^(http|https|ftp|gopher)\:\/\/#', $tmp)) {
            $tmp = NV_BASE_SITEURL . NV_UPLOADS_DIR . $tmp;
        }
        $array['fileupload'][] = $tmp;
    }
}

if (! sizeof($array['linkdirect'])) {
    array_push($array['linkdirect'], '');
}
if (! sizeof($array['fileupload'])) {
    array_push($array['fileupload'], '');
}

if (empty($list_cats)) {
    $redirect = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat-content';
    $contents = '<p class="note_cat">' . $lang_module['note_cat'] . '</p>';
    $contents .= "<meta http-equiv=\"refresh\" content=\"3;URL=" . $redirect . "\" />";
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_admin_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
    die();
}

$array['is_del_report'] = $array['is_del_report'] ? ' checked="checked"' : '';

$groups_comment = explode(',', $array['groups_comment']);
$array['groups_comment'] = array();
foreach ($groups_list as $key => $title) {
    $array['groups_comment'][] = array(
        'key' => $key,
        'title' => $title,
        'checked' => in_array($key, $groups_comment) ? ' checked="checked"' : ''
    );
}

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

if (defined('NV_EDITOR')) {
    require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
}

if (defined('NV_EDITOR') and nv_function_exists('nv_aleditor')) {
    $array['description'] = nv_aleditor('description', '100%', '300px', $array['description']);
} else {
    $array['description'] = '<textarea style="width:100%; height:300px" name="description" id="description">' . $array['description'] . '</textarea>';
}
$array['id'] = 0;

if (! $array['filesize']) {
    $array['filesize'] = '';
}

$xtpl = new XTemplate('content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('FORM_ACTION', $form_action);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('DATA', $array);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
$xtpl->assign('IMG_DIR', NV_UPLOADS_DIR . '/' . $module_upload . '/images');
$xtpl->assign('FILES_DIR', NV_UPLOADS_DIR . '/' . $module_upload . '/files');
$xtpl->assign('ONCHANGE', 'onchange="get_alias();"');

if (! empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

if (!empty($list_cats)) {
    foreach ($list_cats as $catid => $value) {
        $value['space'] = '';
        if ($value['lev'] > 0) {
            for ($i = 1; $i <= $value['lev']; $i++) {
                $value['space'] .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
        }
        $value['selected'] = $catid == $array['catid'] ? ' selected="selected"' : '';

        $xtpl->assign('LISTCATS', $value);
        $xtpl->parse('main.catid');
    }
}

$a = 0;
foreach ($array['fileupload'] as $file) {
    $xtpl->assign('FILEUPLOAD', array( 'value' => $file, 'key' => $a ));
    $xtpl->parse('main.fileupload');
    ++$a;
}

$a = 0;
foreach ($array['linkdirect'] as $link) {
    $xtpl->assign('LINKDIRECT', array( 'value' => $link, 'key' => $a ));
    $xtpl->parse('main.linkdirect');
    ++$a;
}

foreach ($array['groups_comment'] as $group) {
    $xtpl->assign('GROUPS_COMMENT', $group);
    $xtpl->parse('main.groups_comment');
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
