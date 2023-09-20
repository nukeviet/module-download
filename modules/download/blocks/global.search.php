<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 3/9/2010 23:25
 */

if (! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (defined('NV_SYSTEM')) {
    global $site_mods, $module_name, $module_info, $nv_Request, $nv_Cache, $global_config, $nv_Lang;

    $module = $block_config['module'];

    if (isset($site_mods[$module])) {
        $_mod_table = (defined('SYS_DOWNLOAD_TABLE')) ? SYS_DOWNLOAD_TABLE : NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'];

        $sql = "SELECT id, title, alias, parentid, lev FROM " . $_mod_table . "_categories WHERE status=1 ORDER BY sort ASC";
        $list = $nv_Cache->db($sql, 'id', $module);

        $key = nv_substr($nv_Request->get_title('q', 'get', '', 1), 0, NV_MAX_SEARCH_LENGTH);
        $cat = $nv_Request->get_int('cat', 'get');

        $path = NV_ROOTDIR . "/themes/" . $module_info['template'] . "/modules/" . $site_mods[$module]['module_file'];
        if (! file_exists(NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $site_mods[$module]['module_file'] . '/block_search.tpl')) {
            $path = NV_ROOTDIR . "/themes/default/modules/" . $site_mods[$module]['module_file'];
        }

        $xtpl = new XTemplate("block_search.tpl", $path);
        if ($module == $module_name) {
            $xtpl->assign('LANG', \NukeViet\Core\Language::$lang_module);
        } else {
            $nv_Lang->loadModule($site_mods[$module]['module_file'], false, true);
            $xtpl->assign('LANG', \NukeViet\Core\Language::$tmplang_module);
            $nv_Lang->changeLang();
        }
        $xtpl->assign('keyvalue', $key);

        if (!$global_config['rewrite_enable']) {
            $xtpl->assign('FORM_ACTION', NV_BASE_SITEURL . 'index.php');
            $xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
            $xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
            $xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
            $xtpl->assign('MODULE_NAME', $module);
            $xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
            $xtpl->assign('OP_NAME', 'search');
            $xtpl->parse('main.no_rewrite');
        } else {
            $xtpl->assign('FORM_ACTION', nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=search', true));
        }

        foreach ($list as $row) {
            if (!$row['parentid'] or isset($list[$row['parentid']])) {
                $row['select'] = ($row['id'] == $cat) ? 'selected=selected' : '';
                $space = '';
                for ($i = 0; $i < $row['lev']; $i++) {
                    $space .= '&nbsp; &nbsp; ';
                }
                $row['space'] = $space;
                $xtpl->assign('loop', $row);
                $xtpl->parse('main.loop');
            }
        }

        $xtpl->parse('main');
        $content = $xtpl->text('main');
    }
}
