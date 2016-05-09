<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Apr 20, 2010 10:47:41 AM
 */

if (! defined('NV_IS_FILE_SITEINFO')) {
    die('Stop!!!');
}

$lang_siteinfo = nv_get_lang_module($mod);

if ($data['type'] == 'report_new') {
    $data['title'] = sprintf($lang_siteinfo['notification_report'], $data['send_from'], $data['content']['title']);
    $data['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $data['module'] . '&amp;edit=1&amp;id=' . $data['obid'] . '&amp;report=1';
} elseif ($data['type'] == 'upload_new') {
    $data['title'] = sprintf($lang_siteinfo['notification_upload'], $data['send_from'], $data['content']['title']);
    $data['link'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $data['module'] . '&amp;' . NV_OP_VARIABLE . '=content&amp;filequeueid=' . $data['obid'];
}
