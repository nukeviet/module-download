<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (defined('NV_SYSTEM')) {
    global $site_mods, $module_name, $module_info, $lang_module, $nv_Request, $nv_Cache;

    $module = $block_config['module'];

    if (isset($site_mods[$module])) {
        if ($module == $module_name) {
            $lang_block_module = $lang_module;
        } else {
            $temp_lang_module = $lang_module;
            $lang_module = array();
            include NV_ROOTDIR . '/modules/' . $site_mods[$module]['module_file'] . '/language/' . NV_LANG_INTERFACE . '.php' ;
            $lang_block_module = $lang_module;
            $lang_module = $temp_lang_module;
        }
        $_mod_table = (defined('SYS_DOWNLOAD_TABLE')) ? SYS_DOWNLOAD_TABLE : NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'];
        
        $sql = "SELECT id, title, alias, parentid FROM " . $_mod_table . "_categories WHERE parentid=0 ORDER BY weight";
        $list = $nv_Cache->db($sql, '', $module);

        $key = nv_substr($nv_Request->get_title('q', 'get', '', 1), 0, NV_MAX_SEARCH_LENGTH);
        $cat = $nv_Request->get_int('cat', 'get');

        $path = NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $site_mods[$module]['module_file'];
        if (! file_exists(NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $site_mods[$module]['module_file'] . '/block_search.tpl')) {
            $path = NV_ROOTDIR . "/themes/default/modules/" . $site_mods[$module]['module_file'];
        }

        $xtpl = new XTemplate("block_search.tpl", $path);
        $xtpl->assign('LANG', $lang_block_module);
        $xtpl->assign('keyvalue', $key);
        $xtpl->assign('BASE_URL_SITE', NV_BASE_SITEURL);
        $xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
        $xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
        $xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
        $xtpl->assign('MODULE_NAME', $module);
        $xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
        $xtpl->assign('OP_NAME', 'search');

        foreach ($list as $row) {
            $row['select'] = ($row['id'] == $cat) ? 'selected=selected' : '';
            $xtpl->assign('loop', $row);
            $xtpl->parse('main.loop');
        }
        $xtpl->parse('main');
        $content = $xtpl->text('main');
    }
}