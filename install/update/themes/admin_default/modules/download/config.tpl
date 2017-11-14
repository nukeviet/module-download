<!-- BEGIN: main -->
<div id="users">
    <form action="{FORM_ACTION}" method="post">
        <div class="panel panel-default">
            <div class="panel-heading"><strong>{LANG.config_display}</strong></div>
            <table class="table table-striped table-bordered table-hover">
                <colgroup>
                    <col style="width: 360px" />
                    <col/>
                </colgroup>
                <tbody>
                    <tr>
                        <td>{LANG.config_indexfile}</td>
                        <td>
                            <select class="form-control" name="indexfile">
                                <!-- BEGIN: indexfile -->
                                <option value="{INDEXFILE.key}" {INDEXFILE.selected}>{INDEXFILE.value}</option>
                                <!-- END: indexfile -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{LANG.config_viewlist_type}</td>
                        <td>
                            <!-- BEGIN: viewlist_type -->
                            <label><input type="radio" name="viewlist_type" value="{VIEWLIST.key}" {VIEWLIST.checked} />{VIEWLIST.value}</label>&nbsp;&nbsp;&nbsp;
                            <!-- END: viewlist_type -->
                        </td>
                    </tr>
                    <tr>
                        <td>{LANG.config_per_page_home}</td>
                        <td><input type="number" name="per_page_home" value="{DATA.per_page_home}" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>{LANG.config_per_page_child}</td>
                        <td><input type="number" name="per_page_child" value="{DATA.per_page_child}" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>{LANG.config_list_title_length}</td>
                        <td><input type="number" name="list_title_length" value="{DATA.list_title_length}" class="form-control" /></td>
                    </tr>
                    <tr>
                        <td>{LANG.config_share_shareport}</td>
                        <td>
                            <select class="form-control" name="shareport">
                                <!-- BEGIN: shareport -->
                                <option value="{SHAREPORT.key}"{SHAREPORT.selected}>{SHAREPORT.title}</option>
                                <!-- END: shareport -->
                            </select>
                        </td>
                    </tr>
                    <tr id="shareport-addthis" data-toggle="shareport"{ADDTHIS_CSS}>
                        <td>{LANG.addthis_pubid}</td>
                        <td><input class="form-control" type="text" name="addthis_pubid" value="{DATA.addthis_pubid}" /></td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-center form-group">
            <input type="submit" value="{LANG.config_confirm}" class="btn btn-primary" />
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><strong>{LANG.config_basic}</strong></div>
            <table class="table table-striped table-bordered table-hover">
                <colgroup>
                    <col style="width: 360px" />
                    <col/>
                </colgroup>
                <tbody>
                    <tr>
                        <td>{LANG.config_is_addfile}</td>
                        <td>
                            <label><input name="is_addfile" value="1" id="is_addfile" type="checkbox"{DATA.is_addfile} />{LANG.config_is_addfile_note}</label>
                        </td>
                    </tr>
                    <tr class="config_is_addfile is_addfile" {IS_ADDFILE}>
                        <td>{LANG.config_whoaddfile}</td>
                        <td>
                            <div class="groups-allow-area">
                                <!-- BEGIN: groups_addfile -->
                                <label class="show"><input name="groups_addfile[]" value="{GROUPS_ADDFILE.key}" type="checkbox"{GROUPS_ADDFILE.checked} /> {GROUPS_ADDFILE.title}</label>
                                <!-- END: groups_addfile -->
                            </div>
                        </td>
                    </tr>
                    <tr class="is_addfile" {IS_ADDFILE}>
                        <td>{LANG.config_whouploadfile}</td>
                        <td>
                            <div class="groups-allow-area">
                                <!-- BEGIN: groups_upload -->
                                <label class="show"><input name="groups_upload[]" value="{GROUPS_UPLOAD.key}" type="checkbox"{GROUPS_UPLOAD.checked} /> {GROUPS_UPLOAD.title}</label>
                                <!-- END: groups_upload -->
                            </div>
                        </td>
                    </tr>
                    <!-- BEGIN: allow_files_type -->
                    <tr class="is_addfile" {IS_ADDFILE}>
                        <td class="top">{LANG.config_allowfiletype}</td>
                        <td>
                            <!-- BEGIN: loop -->
                            <label><input name="upload_filetype[]" type="checkbox" value="{TP}" {CHECKED} /> {TP}</label>&nbsp;&nbsp;&nbsp;
                            <!-- END: loop -->
                        </td>
                    </tr>
                    <!-- END: allow_files_type -->
                    <tr class="is_addfile" {IS_ADDFILE}>
                        <td>{LANG.config_maxfilesize}</td>
                        <td><input name="maxfilesize" value="{DATA.maxfilesize}" type="text" maxlength="10" class="pull-left form-control w200"/><span class="text-middle"> {LANG.config_maxfilemb}. {LANG.config_maxfilesizesys} {NV_UPLOAD_MAX_FILESIZE} </span></td>
                    </tr>
                    <tr>
                        <td>{LANG.is_resume}</td>
                        <td><input name="is_resume" value="1" type="checkbox"{DATA.is_resume} /></td>
                    </tr>
                    <tr>
                        <td>{LANG.max_speed}</td>
                        <td><input name="max_speed" value="{DATA.max_speed}" type="text" class="form-control w100 pull-left" maxlength="4" />&nbsp;<span class="text-middle"> {LANG.kb_sec} </span></td>
                    </tr>
                    <tr>
                        <td>{LANG.tags_alias}</td>
                        <td><input type="checkbox" value="1" name="tags_alias" {DATA.tags_alias}/></td>
                    </tr>
                    <tr>
                        <td>{LANG.config_copy_document}</td>
                        <td><input name="copy_document" value="1" type="checkbox" {DATA.copy_document} /></td>
                    </tr>
                    <tr>
                        <td>{LANG.config_allow_fupload_import}</td>
                        <td><input name="allow_fupload_import" value="1" type="checkbox" {DATA.allow_fupload_import} /></td>
                    </tr>
                    <tr>
                        <td>{LANG.is_zip}</td>
                        <td><input name="is_zip" value="1" type="checkbox" {DATA.is_zip} /></td>
                    </tr>
                    <tr>
                        <td class="top">{LANG.zip_readme}</td>
                        <td><textarea name="readme" cols="20" rows="5" class="form-control">{DATA.readme}</textarea></td>
                    </tr>
                    <tr>
                        <td>{LANG.config_delfile_mode}</td>
                        <td>
                            <select class="form-control" name="delfile_mode">
                                <!-- BEGIN: delfile_mode -->
                                <option value="{DELFILE_MODE.key}" {DELFILE_MODE.selected}>{DELFILE_MODE.title}</option>
                                <!-- END: delfile_mode -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{LANG.config_structure_upload}</td>
                        <td>
                            <select class="form-control" name="structure_upload">
                                <!-- BEGIN: structure_upload -->
                                <option value="{STRUCTURE_UPLOAD.key}" {STRUCTURE_UPLOAD.selected}>{STRUCTURE_UPLOAD.title}</option>
                                <!-- END: structure_upload -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{LANG.config_scorm_handle_mode}</td>
                        <td>
                            <select class="form-control" name="scorm_handle_mode">
                                <!-- BEGIN: scorm_handle_mode -->
                                <option value="{SCORM_HANDLE_MODE.key}" {SCORM_HANDLE_MODE.selected}>{SCORM_HANDLE_MODE.title}</option>
                                <!-- END: scorm_handle_mode -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{LANG.config_fileserver}</td>
                        <td>
                            <select class="form-control pull-left w200" name="fileserver">
                                <!-- BEGIN: fileserver -->
                                <option value="{FILESERVER.key}" {FILESERVER.selected}>{FILESERVER.title}</option>
                                <!-- END: fileserver -->
                            </select>
                            <span id="fileserverLink"{FILESERVER_DISPLAY}>&nbsp; (<a href="{FILESERVER_MANAGER}">{LANG.fileserver_manager}</a>)</span>
                        </td>
                    </tr>
                    <tr>
                        <td>{LANG.config_pdf_handler}</td>
                        <td>
                            <select class="form-control pull-left w500" name="pdf_handler">
                                <!-- BEGIN: pdf_handler -->
                                <option value="{PDF_HANDLER.key}" {PDF_HANDLER.selected}>{PDF_HANDLER.title}</option>
                                <!-- END: pdf_handler -->
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="text-center form-group">
            <input type="submit" value="{LANG.config_confirm}" class="btn btn-primary" />
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><strong>{LANG.config_field}</strong></div>
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="w250">{LANG.config_field_name}</th>
                        <th class="text-center"><a href="#" data-toggle="ckunck" data-target="ck1">{LANG.config_field_required_admin}</a></th>
                        <th class="text-center"><a href="#" data-toggle="ckunck" data-target="ck2">{LANG.config_field_required_user}</a></th>
                        <th class="text-center"><a href="#" data-toggle="ckunck" data-target="ck3">{LANG.config_field_display_admin}</a></th>
                        <th class="text-center"><a href="#" data-toggle="ckunck" data-target="ck4">{LANG.config_field_display_user}</a></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- BEGIN: field -->
                    <tr>
                        <td>{FIELD_TITLE}</td>
                        <td class="text-center"><input type="checkbox" value="1" data-type="ck1" name="arr_req_ad_{FIELD_NAME}"{REQ_AD_CHECKED}/></td>
                        <td class="text-center"><input type="checkbox" value="1" data-type="ck2" name="arr_req_ur_{FIELD_NAME}"{REQ_UR_CHECKED}/></td>
                        <td class="text-center"><input type="checkbox" value="1" data-type="ck3" data-toggle="ckuncko" name="arr_dis_ad_{FIELD_NAME}"{DIS_AD_CHECKED}/></td>
                        <td class="text-center"><input type="checkbox" value="1" data-type="ck4" data-toggle="ckuncko" name="arr_dis_ur_{FIELD_NAME}"{DIS_UR_CHECKED}/></td>
                    </tr>
                    <!-- END: field -->
                </tbody>
            </table>
        </div>
        <div class="text-center">
            <input type="hidden" name="submit" value="submit"/>
            <input type="submit" value="{LANG.config_confirm}" class="btn btn-primary" />
        </div>
    </form>
</div>
<script type="text/javascript">
$(function() {
    $('#is_addfile').click(function(){
        $('.is_addfile').toggle();
    });
    $('[data-toggle="ckunck"]').click(function(e) {
        e.preventDefault();
        var tg = $(this).data('target');
        var num = 0;
        $('[data-type="' + tg + '"]:not(:disabled)').each(function() {
            if ($(this).is(':checked')) {
                num++;
            }
        });
        if (num >= $('[data-type="' + tg + '"]:not(:disabled)').length) {
            $('[data-type="' + tg + '"]').prop('checked', false);
            if (tg == "ck4") {
                $('[data-type="ck2"]').prop('checked', false);
                $('[data-type="ck2"]').prop('disabled', true);
            }
            if (tg == "ck3") {
                $('[data-type="ck1"]').prop('checked', false);
                $('[data-type="ck1"]').prop('disabled', true);
            }
        } else {
            $('[data-type="' + tg + '"]:not(:disabled)').prop('checked', true);
            if (tg == "ck4") {
                $('[data-type="ck2"]').prop('disabled', false);
            }
            if (tg == "ck3") {
                $('[data-type="ck1"]').prop('disabled', false);
            }
        }
    });
    $('[data-toggle="ckuncko"]').click(function(e) {
        var tp = $(this).data('type');
        var rw = $(this).parent().parent();
        if (!$(this).is(':checked')) {
            if (tp == "ck4") {
                $('[data-type="ck2"]', rw).prop('checked', false);
                $('[data-type="ck2"]', rw).prop('disabled', true);
            }
            if (tp == "ck3") {
                $('[data-type="ck1"]', rw).prop('checked', false);
                $('[data-type="ck1"]', rw).prop('disabled', true);
            }
        } else {
            if (tp == "ck4") {
                $('[data-type="ck2"]', rw).prop('disabled', false);
            }
            if (tp == "ck3") {
                $('[data-type="ck1"]', rw).prop('disabled', false);
            }
        }
    })
});
</script>
<!-- END: main -->