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

if (! nv_function_exists('nv_bdown_news')) {
    /**
     * nv_block_config_bdown_news()
     *
     * @param mixed $module
     * @param mixed $data_block
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_bdown_news($module, $data_block, $lang_block)
    {
        global $db, $site_mods;
        $html = '';
        $html .= '<div class="form-group">';
        $html .= '    <label class="control-label col-sm-6">' . $lang_block['title_length'] . ':</label>';
        $html .= '    <div class="col-sm-5"><input class="form-control" type="text" name="config_title_length" value="' . $data_block['title_length'] . '"/></div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '    <label class="control-label col-sm-6">' . $lang_block['numrow'] . ':</label>';
        $html .= '    <div class="col-sm-5"><input class="form-control" type="text" name="config_numrow" value="' . $data_block['numrow'] . '"/></div>';
        $html .= '</div>';
        $html .= '<div class="form-group">';
        $html .= '    <label class="control-label col-sm-6">' . $lang_block['class_name'] . ':</label>';
        $html .= '    <div class="col-sm-18"><input class="form-control" type="text" name="config_class_name" value="' . $data_block['class_name'] . '"/></div>';

        $html .= '</div>';
        return $html;
    }

    /**
     * nv_block_config_bdown_news_submit()
     *
     * @param mixed $module
     * @param mixed $lang_block
     * @return
     */
    function nv_block_config_bdown_news_submit($module, $lang_block)
    {
        global $nv_Request;
        $return = array();
        $return['error'] = array();
        $return['config'] = array();
        $return['config']['title_length'] = $nv_Request->get_int('config_title_length', 'post', 24);
        $return['config']['numrow'] = $nv_Request->get_int('config_numrow', 'post', 5);
        $return['config']['class_name'] = $nv_Request->get_title('config_class_name', 'post', 'list_item');
        return $return;
    }

    /**
     * nv_bdown_news()
     *
     * @param mixed $block_config
     * @return
     */
    function nv_bdown_news($block_config)
    {
        global $db, $module_info, $site_mods, $global_config, $nv_Cache;

        $module = $block_config['module'];
        $file = $site_mods[$module]['module_file'];
        $_mod_table = (defined('SYS_DOWNLOAD_TABLE')) ? SYS_DOWNLOAD_TABLE : NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'];

        // Lay thong tin phan quyen
        $sql = 'SELECT id, alias, groups_view FROM ' . $_mod_table . '_categories WHERE status=1';
        $_tmp = $nv_Cache->db($sql, 'id', $module);
        $list_cat = array();
        if ($_tmp) {
            foreach ($_tmp as $row) {
                if (nv_user_in_groups($row['groups_view'])) {
                    $list_cat[$row['id']] = $row['alias'];
                }
            }
        }
        unset($_tmp, $sql);

        if ($list_cat) {
            $db->sqlreset()
                ->select('id, catid, title, alias, updatetime')
                ->from($_mod_table)
                ->where('status AND catid IN (' . implode(',', array_keys($list_cat)) . ')')
                ->order('updatetime DESC')
                ->limit($block_config['numrow']);

            $list = $nv_Cache->db($db->sql(), 'id', $module);

            if (! empty($list)) {
                if (file_exists(NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $file . '/block_new_files.tpl')) {
                    $block_theme = $global_config['module_theme'];
                } else {
                    $block_theme = 'default';
                }
                $xtpl = new XTemplate('block_new_files.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $file);
                $xtpl->assign('CONFIG', $block_config);

                foreach ($list as $row) {
                    $row['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module . '&amp;' . NV_OP_VARIABLE . '=' . $list_cat[$row['catid']] . '/' . $row['alias'] . $global_config['rewrite_exturl'];

                    $row['updatetime'] = nv_date('d/m/Y', $row['updatetime']);
                    $row['stitle'] = nv_clean60($row['title'], $block_config['title_length']);

                    $xtpl->assign('ROW', $row);

                    $xtpl->parse('main.loop');
                }

                $xtpl->parse('main');
                return $xtpl->text('main');
            }
        }
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_bdown_news($block_config);
}