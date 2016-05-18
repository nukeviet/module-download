<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 2-10-2010 20:59
 */

if (!defined('NV_IS_FILE_MODULES')) {
    die('Stop!!!');
}

if (!defined('SYS_DOWNLOAD_TABLE')) {
    $sql_drop_module = array();
    
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data;
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_files";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_detail";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_report";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tmp";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags_id";
    $sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_server";
    
    $result = $db->query("SHOW TABLE STATUS LIKE '" . $db_config['prefix'] . "\_" . $lang . "\_comment'");
    $rows = $result->fetchAll();
    if (sizeof($rows)) {
        $sql_drop_module[] = "DELETE FROM " . $db_config['prefix'] . "_" . $lang . "_comment WHERE module='" . $module_name . "'";
    }
    
    $sql_create_module = $sql_drop_module;
    
    $sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . " (
         id int(11) unsigned NOT NULL AUTO_INCREMENT,
         catid smallint(5) unsigned NOT NULL,
         title varchar(250) NOT NULL,
         alias varchar(250) NOT NULL,
         introtext text NOT NULL,
         uploadtime int(11) unsigned NOT NULL,
         updatetime int(11) unsigned NOT NULL DEFAULT '0',
         user_id mediumint(8) unsigned NOT NULL,
         user_name varchar(100) NOT NULL,
         author_name varchar(100) NOT NULL,
         author_email varchar(60) NOT NULL,
         author_url varchar(255) NOT NULL,
         version varchar(20) NOT NULL,
         filesize int(11) NOT NULL DEFAULT '0',
         fileimage varchar(255) NOT NULL,
         status tinyint(1) unsigned NOT NULL DEFAULT '0',
         copyright varchar(255) NOT NULL,
         num_fileupload smallint(4) unsigned NOT NULL DEFAULT '0',
         num_linkdirect smallint(4) unsigned NOT NULL DEFAULT '0',
         view_hits int(11) NOT NULL DEFAULT '0',
         download_hits int(11) NOT NULL DEFAULT '0',
         comment_hits int(11) NOT NULL DEFAULT '0',
         PRIMARY KEY (id),
         UNIQUE KEY alias (alias),
         KEY catid (catid),
         KEY user_id (user_id)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_files (
         file_id int(11) unsigned NOT NULL AUTO_INCREMENT,
         download_id int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'ID file download',
         server_id smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'ID fileserver hoặc 0 nếu là local',
         file_path varchar(255) NOT NULL DEFAULT '',
         scorm_path varchar(255) NOT NULL DEFAULT '',
         filesize int(11) NOT NULL DEFAULT '0',
         weight smallint(4) unsigned NOT NULL DEFAULT '0',
         status tinyint(1) unsigned NOT NULL DEFAULT '0',
         PRIMARY KEY (file_id),
         KEY download_id (download_id),
         KEY server_id (server_id)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_detail (
         id int(11) unsigned NOT NULL,
         description mediumtext NOT NULL,
         linkdirect text NOT NULL,
         groups_comment varchar(255) NOT NULL,
         groups_view varchar(255) NOT NULL,
         groups_onlineview varchar(255) NOT NULL,
         groups_download varchar(255) NOT NULL,
         rating_detail varchar(255) NOT NULL,
         PRIMARY KEY (id)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tmp (
         id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
         catid int(10) unsigned NOT NULL DEFAULT '0',
         title varchar(250) NOT NULL,
         description mediumtext NOT NULL,
         introtext text NOT NULL,
         uploadtime int(11) unsigned NOT NULL DEFAULT '0',
         user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
         user_name varchar(100) NOT NULL,
         author_name varchar(100) NOT NULL,
         author_email varchar(60) NOT NULL,
         author_url varchar(255) NOT NULL,
         fileupload text NOT NULL,
         linkdirect text NOT NULL,
         version varchar(20) NOT NULL,
         filesize varchar(255) NOT NULL,
         fileimage varchar(255) NOT NULL,
         copyright varchar(255) NOT NULL,
         PRIMARY KEY (id),
         UNIQUE KEY title (title),
         KEY catid (catid)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE IF NOT EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories (
         id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
         parentid smallint(5) unsigned NOT NULL,
         title varchar(250) NOT NULL,
         alias varchar(250) NOT NULL,
         description text,
         groups_view varchar(255) DEFAULT '',
         groups_onlineview varchar(255) DEFAULT '',
         groups_download varchar(255) DEFAULT '',
         numsubcat smallint(5) NOT NULL DEFAULT '0',
         subcatid varchar(255) NOT NULL DEFAULT '',
         viewcat varchar(100) DEFAULT 'viewcat_list_new',
         numlink smallint(4) DEFAULT '3',
         sort smallint(5) NOT NULL DEFAULT '0',
         lev smallint(5) NOT NULL DEFAULT '0',
         weight smallint(4) unsigned NOT NULL DEFAULT '0',
         status tinyint(1) unsigned NOT NULL DEFAULT '0',
         PRIMARY KEY (id),
         UNIQUE KEY alias (alias)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_report (
         fid int(11) unsigned NOT NULL DEFAULT '0',
         post_ip varchar(45) NOT NULL,
         post_time int(11) unsigned NOT NULL DEFAULT '0',
         UNIQUE KEY fid (fid),
         KEY post_time (post_time)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (
         config_name varchar(30) NOT NULL,
         config_value varchar(255) NOT NULL,
         UNIQUE KEY config_name (config_name)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags (
         did int(11) unsigned NOT NULL AUTO_INCREMENT,
         numdownload mediumint(8) NOT NULL DEFAULT '0',
         alias varchar(255) NOT NULL,
         image varchar(255),
         description text,
         keywords varchar(255),
         PRIMARY KEY (did)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_tags_id (
          id int(11) unsigned NOT NULL DEFAULT '0',
          did int(11) unsigned NOT NULL DEFAULT '0',
          keyword varchar(65) NOT NULL,
          UNIQUE KEY id_tid (id,did),
          KEY did (did)
        )ENGINE=MyISAM";
    
    $sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_server (
          server_id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
          server_name varchar(250) NOT NULL DEFAULT '',
          upload_url varchar(255) NOT NULL DEFAULT '',
          access_key varchar(255) NOT NULL DEFAULT '',
          secret_key varchar(255) NOT NULL DEFAULT '',
          status tinyint(1) unsigned NOT NULL DEFAULT '0',
          UNIQUE KEY server_name (server_name),
          PRIMARY KEY (server_id)
        )ENGINE=MyISAM";
    
    $maxfilesize = min($global_config['nv_max_size'], nv_converttoBytes(ini_get('upload_max_filesize')), nv_converttoBytes(ini_get('post_max_size')));
    
    $sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config VALUES
        ('indexfile', 'viewcat_main_bottom'),
        ('viewlist_type', 'list'),
        ('per_page_home', '20'),
        ('per_page_child', '20'),
        ('is_addfile', '1'),
        ('groups_upload', '4'),
        ('maxfilesize', '" . $maxfilesize . "'),
        ('upload_filetype', 'adobe,archives,audio,documents,flash,images,real,video'),
        ('groups_addfile', '4'),
        ('tags_alias', '0'),
        ('is_zip', '0'),
        ('is_resume', '1'),
        ('max_speed', '0'),
        ('delfile_mode', '0'),
        ('structure_upload', 'Ym'),
        ('scorm_handle_mode', '0'),
        ('fileserver', '0')
    ";
    
    // Comments
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'auto_postcomm', '1')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'allowed_comm', '-1')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'view_comm', '6')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'setcomm', '4')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'activecomm', '1')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'emailcomm', '0')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'adminscomm', '')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'sortcomm', '0')";
    $sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'captcha', '1')";
}