<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3-6-2010 0:30
 */

if (!defined('NV_IS_MOD_DOWNLOAD')) {
    die('Stop!!!');
}

if (nv_strtolower($module_info['funcs'][$op]['func_custom_name']) == $module_info['funcs'][$op]['func_name']) {
    $page_title = $lang_module['upload'];
} else {
    $page_title = $module_info['funcs'][$op]['func_custom_name'];
}
$array_mod_title[] = array('title' => $page_title);

$download_config = nv_mod_down_config();
$array_field_key = array_keys($download_config['dis']['ad']);

$list_cats_addfile = array();
foreach ($list_cats as $_catid => $_catvalue) {
    if (!empty($_catvalue['is_addfile_allow'])) {
        $list_cats_addfile[$_catid] = $_catvalue;
    } elseif (isset($list_cats_addfile[$_catvalue['parentid']])) {
        $list_cats_addfile[$_catvalue['parentid']]['numsubcat']--;
    }
}

if (empty($list_cats)) {
    nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
}

if (!$download_config['is_addfile_allow'] or empty($list_cats_addfile)) {
    if (!defined('NV_IS_USER')) {
        $alert_content = $lang_module['error_not_permission_upload_content_guest'];
        $urlback = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=users&amp;' . NV_OP_VARIABLE . '=login&nv_redirect=' . nv_redirect_encrypt($client_info['selfurl']);
        $lang_back = false;
    } else {
        $alert_content = $lang_module['error_not_permission_upload_content_user'];
        $urlback = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true);
        $lang_back = true;
    }
    nv_download_theme_alert($lang_module['error_not_permission_title'], $alert_content, 'info', $urlback, 5, $lang_back);
}

if ($nv_Request->isset_request('loadcat', 'post')) {
    if (!defined('NV_IS_AJAX')) {
        die('Wrong URL');
    }

    $catid = $nv_Request->get_int('catid', 'post', 0);
    $parentid = $nv_Request->get_int('parentid', 'post', 0);
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    if ($checkss != NV_CHECK_SESSION) {
        die('Access denied!!!');
    }
    if ($catid and !isset($list_cats_addfile[$catid])) {
        die('NO');
    }
    if ($parentid and !isset($list_cats_addfile[$parentid])) {
        die('NO');
    }

    $contents = theme_upload_getcat($parentid, $catid, $list_cats_addfile);
    nv_htmlOutput($contents);
}

$is_error = false;
$error = '';
$array = array();

if ($nv_Request->isset_request('addfile', 'post')) {
    $addfile = $nv_Request->get_string('addfile', 'post', '');

    if (empty($addfile) or $addfile != md5($client_info['session_id'])) {
        nv_redirect_location(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true);
    }

    $array['catid'] = $nv_Request->get_int('upload_catid', 'post', 0);
    $array['title'] = nv_substr($nv_Request->get_title('upload_title', 'post', '', 1), 0, 250);
    $array['description'] = $nv_Request->get_editor('upload_description', '', NV_ALLOWED_HTML_TAGS);
    $array['introtext'] = $nv_Request->get_textarea('upload_introtext', '', NV_ALLOWED_HTML_TAGS);
    $array['author_name'] = nv_substr($nv_Request->get_title('upload_author_name', 'post', '', 1), 0, 100);
    $array['author_email'] = nv_substr($nv_Request->get_title('upload_author_email', 'post', ''), 0, 60);
    $array['author_url'] = nv_substr($nv_Request->get_title('upload_author_url', 'post', '', 0), 0, 255);
    $array['linkdirect'] = $nv_Request->get_textarea('upload_linkdirect', '');
    $array['version'] = nv_substr($nv_Request->get_title('upload_version', 'post', '', 1), 0, 20);
    $array['filesize'] = $nv_Request->get_int('upload_filesize', 'post', 0);
    $array['copyright'] = nv_substr($nv_Request->get_title('upload_copyright', 'post', '', 1), 0, 255);
    $array['user_name'] = nv_substr($nv_Request->get_title('upload_user_name', 'post', '', 1), 0, 100);
    $array['user_id'] = 0;

    if ($global_config['captcha_type'] == 2) {
        $seccode = $nv_Request->get_title('g-recaptcha-response', 'post', '');
    } else {
        $seccode = $nv_Request->get_title('upload_seccode', 'post', '');
    }

    if (defined('NV_IS_USER')) {
        $array['user_name'] = $user_info['username'];
        $array['user_id'] = $user_info['userid'];
    }

    if (!empty($array['author_url'])) {
        if (!preg_match("#^(http|https|ftp|gopher)\:\/\/#", $array['author_url'])) {
            $array['author_url'] = 'http://' . $array['author_url'];
        }
    }

    if (!empty($array['linkdirect'])) {
        $linkdirect = $array['linkdirect'];
        $linkdirect = nv_nl2br($linkdirect, '<br />');
        $linkdirect = explode('<br />', $linkdirect);
        $linkdirect = array_map('trim', $linkdirect);
        $linkdirect = array_unique($linkdirect);

        $array['linkdirect'] = array();
        foreach ($linkdirect as $link) {
            if (!preg_match("#^(http|https|ftp|gopher)\:\/\/#", $link)) {
                $link = 'http://' . $link;
            }

            if (nv_is_url($link)) {
                $array['linkdirect'][] = $link;
            }
        }

        $array['linkdirect'] = !empty($array['linkdirect']) ? implode("\n", $array['linkdirect']) : '';
    }

    $alias = nv_substr(change_alias($array['title']), 0, 250);

    $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . ' WHERE alias= :alias');
    $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
    $stmt->execute();
    $is_exists = $stmt->fetchColumn();

    if (!$is_exists) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM ' . NV_MOD_TABLE . '_tmp WHERE title= :title');
        $stmt->bindParam(':title', $array['title'], PDO::PARAM_STR);
        $stmt->execute();
        $is_exists = $stmt->fetchColumn();
    }

    if (!nv_capcha_txt($seccode)) {
        $is_error = true;
        $error = $lang_module['upload_error1'];
    } elseif (empty($array['user_name'])) {
        $is_error = true;
        $error = $lang_module['upload_error2'];
    } elseif (empty($array['title'])) {
        $is_error = true;
        $error = $lang_module['file_error_title'];
    } elseif ($is_exists) {
        $is_error = true;
        $error = $lang_module['file_title_exists'];
    } elseif (!$array['catid'] or !isset($list_cats_addfile[$array['catid']])) {
        $is_error = true;
        $error = $lang_module['file_catid_exists'];
    } elseif (empty($array['author_name']) and !empty($download_config['req']['ur']['author_name'])) {
        $error = $lang_module['file_error_author_name'];
    } elseif (empty($array['author_email']) and !empty($download_config['req']['ur']['author_email'])) {
        $error = $lang_module['file_error_author_email'];
    } elseif (!empty($array['author_email']) and ($check_valid_email = nv_check_valid_email($array['author_email'])) != '') {
        $is_error = true;
        $error = $check_valid_email;
    } elseif (empty($array['author_url']) and !empty($download_config['req']['ur']['author_url'])) {
        $error = $lang_module['file_error_author_url_empty'];
    } elseif (!empty($array['author_url']) and !nv_is_url($array['author_url'])) {
        $is_error = true;
        $error = $lang_module['file_error_author_url'];
    } elseif (empty($array['linkdirect']) and !empty($download_config['req']['ur']['linkdirect'])) {
        $error = $lang_module['file_error_linkdirect'];
    } elseif (empty($array['filesize']) and !empty($download_config['req']['ur']['filesize'])) {
        $error = $lang_module['file_error_filesize'];
    } elseif (empty($array['version']) and !empty($download_config['req']['ur']['version'])) {
        $error = $lang_module['file_error_version'];
    } elseif ((!isset($_FILES['upload_fileimage']) or !is_uploaded_file($_FILES['upload_fileimage']['tmp_name'])) and !empty($download_config['req']['ur']['fileimage'])) {
        $error = $lang_module['file_error_fileimage'];
    } elseif (empty($array['copyright']) and !empty($download_config['req']['ur']['copyright'])) {
        $error = $lang_module['file_error_copyright'];
    } elseif (empty($array['introtext']) and !empty($download_config['req']['ur']['introtext'])) {
        $error = $lang_module['file_error_introtext'];
    } elseif (empty($array['description']) and !empty($download_config['req']['ur']['description'])) {
        $error = $lang_module['file_error_description'];
    } else {
        $fileupload = '';
        if ($download_config['is_upload_allow']) {
            if (isset($_FILES['upload_fileupload']) and is_uploaded_file($_FILES['upload_fileupload']['tmp_name'])) {
                $file_allowed_ext = !empty($download_config['upload_filetype']) ? $download_config['upload_filetype'] : $global_config['file_allowed_ext'];
                if (!file_exists(NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . 'temp')) {
                    nv_mkdir(NV_UPLOADS_REAL_DIR . '/' . $module_upload, 'temp');
                }
                $upload = new NukeViet\Files\Upload($file_allowed_ext, $global_config['forbid_extensions'], $global_config['forbid_mimes'], $download_config['maxfilesize'], NV_MAX_WIDTH, NV_MAX_HEIGHT);
                $upload->setLanguage($lang_global);
                $upload_info = $upload->save_file($_FILES['upload_fileupload'], NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/temp', false);

                @unlink($_FILES['upload_fileupload']['tmp_name']);

                if (empty($upload_info['error'])) {
                    mt_srand(( double )microtime() * 1000000);
                    $maxran = 1000000;
                    $random_num = mt_rand(0, $maxran);
                    $random_num = md5($random_num);
                    $nv_pathinfo_filename = nv_pathinfo_filename($upload_info['name']);
                    $new_name = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/temp/' . $nv_pathinfo_filename . '.' . $random_num . '.' . $upload_info['ext'];

                    $rename = nv_renamefile($upload_info['name'], $new_name);

                    if ($rename[0] == 1) {
                        $fileupload = $new_name;
                    } else {
                        $fileupload = $upload_info['name'];
                    }

                    @chmod($fileupload, 0644);
                    $fileupload = str_replace(NV_ROOTDIR . '/' . NV_UPLOADS_DIR, '', $fileupload);
                    $array['filesize'] = $upload_info['size'];
                } else {
                    $is_error = true;
                    $error = $upload_info['error'];
                }

                unset($upload, $upload_info);
            }
        }

        if (!$is_error) {
            if (empty($fileupload) and empty($array['linkdirect'])) {
                $is_error = true;
                if (!empty($download_config['dis']['ur']['linkdirect'])) {
                    $error = $lang_module['file_error_fileupload'];
                } else {
                    $error = $lang_module['file_error_fileupload1'];
                }
            } else {
                $fileimage = '';
                if (isset($_FILES['upload_fileimage']) and is_uploaded_file($_FILES['upload_fileimage']['tmp_name'])) {
                    $upload = new NukeViet\Files\Upload( array('images'), $global_config['forbid_extensions'], $global_config['forbid_mimes'], NV_UPLOAD_MAX_FILESIZE, NV_MAX_WIDTH, NV_MAX_HEIGHT);
                    $upload->setLanguage($lang_global);
                    $upload_info = $upload->save_file($_FILES['upload_fileimage'], NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/temp', false);

                    @unlink($_FILES['upload_fileimage']['tmp_name']);

                    if (empty($upload_info['error'])) {
                        mt_srand(( double )microtime() * 1000000);
                        $maxran = 1000000;
                        $random_num = mt_rand(0, $maxran);
                        $random_num = md5($random_num);
                        $nv_pathinfo_filename = nv_pathinfo_filename($upload_info['name']);
                        $new_name = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/temp/' . $nv_pathinfo_filename . '.' . $random_num . '.' . $upload_info['ext'];

                        $rename = nv_renamefile($upload_info['name'], $new_name);

                        if ($rename[0] == 1) {
                            $fileimage = $new_name;
                        } else {
                            $fileimage = $upload_info['name'];
                        }

                        @chmod($fileimage, 0644);
                        $fileimage = str_replace(NV_ROOTDIR . '/' . NV_UPLOADS_DIR, '', $fileimage);
                    }
                }

                $array['description'] = nv_nl2br($array['description'], '<br />');
                $array['introtext'] = nv_nl2br($array['introtext'], '<br />');
                $array['linkdirect'] = nv_nl2br($array['linkdirect'], '<br />');

                $sql = 'INSERT INTO ' . NV_MOD_TABLE . '_tmp (catid, title, description, introtext, uploadtime, user_id, user_name, author_name, author_email, author_url, fileupload, linkdirect, version, filesize, fileimage, copyright) VALUES (
                     ' . $array['catid'] . ',
                     :title,
                     :description,
                     :introtext,
                     ' . NV_CURRENTTIME . ',
                     ' . $array['user_id'] . ',
                     :user_name,
                     :author_name,
                     :author_email,
                     :author_url,
                     :fileupload,
                     :linkdirect,
                     :version,
                     ' . $array['filesize'] . ',
                     :fileimage,
                     :copyright)';

                $data_insert = array();
                $data_insert['title'] = $array['title'];
                $data_insert['description'] = $array['description'];
                $data_insert['introtext'] = $array['introtext'];
                $data_insert['user_name'] = $array['user_name'];
                $data_insert['author_name'] = $array['author_name'];
                $data_insert['author_email'] = $array['author_email'];
                $data_insert['author_url'] = $array['author_url'];
                $data_insert['fileupload'] = $fileupload;
                $data_insert['linkdirect'] = $array['linkdirect'];
                $data_insert['version'] = $array['version'];
                $data_insert['fileimage'] = $fileimage;
                $data_insert['copyright'] = $array['copyright'];

                $file_id = $db->insert_id($sql, 'id', $data_insert);

                if (!$file_id) {
                    $is_error = true;
                    $error = $lang_module['upload_error3'];
                } else {
                    $user_post = defined("NV_IS_USER") ? " | " . $user_info['username'] : "";
                    nv_insert_logs(NV_LANG_DATA, $module_name, $lang_module['upload_files_log'], $array['title'] . " | " . $client_info['ip'] . $user_post, 0);

                    $user_post = defined("NV_IS_USER") ? $user_info['userid'] : 0;
                    nv_insert_notification($module_name, 'upload_new', array('title' => $array['title']), $file_id, 0, $user_post, 1);

                    $url_back = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name, true);
                    nv_download_theme_alert($lang_module['file_upload_success_title'], $lang_module['file_upload_success_content'], 'info', $url_back);

                    include NV_ROOTDIR . '/includes/header.php';
                    echo nv_site_theme($contents);
                    include NV_ROOTDIR . '/includes/footer.php';
                    exit();
                }
            }
        }
    }
} else {
    $array['catid'] = sizeof($array_op) == 2 ? (int)$array_op[1] : 0;
    $array['filesize'] = $array['user_id'] = 0;
    $array['title'] = $array['description'] = $array['introtext'] = $array['author_name'] = $array['author_email'] = $array['author_url'] = $array['linkdirect'] = $array['version'] = $array['copyright'] = $array['user_name'] = '';
    if (defined('NV_IS_USER')) {
        $array['user_name'] = $user_info['username'];
        $array['user_id'] = $user_info['userid'];
    }
}

if (!$array['filesize']) {
    $array['filesize'] = '';
}

if (!empty($array['description'])) {
    $array['description'] = nv_htmlspecialchars($array['description']);
}
if (!empty($array['introtext'])) {
    $array['introtext'] = nv_htmlspecialchars($array['introtext']);
}

$array['addfile'] = md5($client_info['session_id']);
$array['upload_filetype'] = implode("|", $download_config['upload_filetype']);

$contents = theme_upload($array, $list_cats_addfile, $download_config, $error, $array_field_key);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
