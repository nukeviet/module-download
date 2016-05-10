<!-- BEGIN: main -->
<!-- BEGIN: error -->
<div class="alert alert-danger">
    {ERROR}
</div>
<!-- END: error -->

<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<link rel="stylesheet" type="text/css" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.css">

<form action="{FORM_ACTION}" method="post" class="confirm-reload" id="file-upload-form" data-busy="false">
    <div class="row">
        <div class="col-sm-24 col-md-18">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td class="w250"> {LANG.file_title} <sup class="required">(*)</sup></td>
                            <td><input class="w300 form-control" type="text" value="{DATA.title}" name="title" id="title" maxlength="250" {ONCHANGE}/></td>
                        </tr>
                        <tr>
                            <td> {LANG.alias} </td>
                            <td><input class="w300 form-control pull-left" type="text" value="{DATA.alias}" name="alias" id="alias" maxlength="250" />&nbsp;<em class="fa fa-refresh fa-lg fa-pointer" onclick="get_alias();">&nbsp;</em></td>
                        </tr>
                        <tr>
                            <td> {LANG.category_cat_parent} </td>
                            <td>
                            <select name="catid" id="catid" class="form-control w300">
                                <!-- BEGIN: catid -->
                                <option value="{LISTCATS.id}"{LISTCATS.selected}>{LISTCATS.space}{LISTCATS.title}</option>
                                <!-- END: catid -->
                            </select></td>
                        </tr>
                        <tr>
                            <td> {LANG.file_author_name} </td>
                            <td><input class="w300 form-control" type="text" value="{DATA.author_name}" name="author_name" id="author_name" maxlength="100" /></td>
                        </tr>
                        <tr>
                            <td> {LANG.file_author_email} </td>
                            <td><input class="w300 form-control" type="text" value="{DATA.author_email}" name="author_email" id="author_email" maxlength="60" /></td>
                        </tr>
                        <tr>
                            <td> {LANG.file_author_homepage} </td>
                            <td>
                                <input class="w300 form-control pull-left" style="margin-right: 5px" type="text" value="{DATA.author_url}" name="author_url" id="author_url" maxlength="255" />
                                <input class="btn btn-info pull-left" style="margin-right: 5px" type="button" value="{LANG.file_checkUrl}" id="check_author_url" onclick="nv_checkfile('author_url',0, 'check_author_url');" />
                                <input class="btn btn-info pull-left" type="button" value="{LANG.file_gourl}" id="go_author_url" onclick="nv_gourl('author_url',0, 'go_author_url');" /></td>
                        </tr>
                        <tr>
                            <td> {LANG.file_image} </td>
                            <td>
                                <!-- BEGIN: fileimage_tmp -->
                                <div class="clearfix" style="margin-bottom: 5px"> 
                                    <strong>{LANG.file_fileimage_tmp}:</strong>
                                    <div class="clearfix" style="margin-bottom: 5px">
                                        <input readonly="readonly" class="w300 form-control pull-left" type="text" style="margin-right: 5px" value="{DATA.fileimage_tmp}" name="fileimage_tmp" id="fileimage_tmp" maxlength="255" />
                                        <input type="button" class="btn btn-info pull-left" style="margin-right: 5px" value="{LANG.file_checkUrl}" id="check_fileimage_tmp" onclick="nv_checkfile('fileimage_tmp',1, 'check_fileimage_tmp');" />
                                        <input type="button" class="btn btn-info pull-left" value="{LANG.file_gourl}" id= "go_fileimage_tmp" onclick="nv_gourl('fileimage_tmp',1, 'go_fileimage_tmp');" />
                                    </div>
                                    <strong>{LANG.file_fileimage_change}:</strong>
                                </div>
                                <!-- END: fileimage_tmp -->
                                <div class="clearfix">
                                    <input class="w300 form-control pull-left" type="text" style="margin-right: 5px" value="{DATA.fileimage}" name="fileimage" id="fileimage" maxlength="255" />
                                    <input type="button" class="btn btn-info pull-left" style="margin-right: 5px" value="{LANG.file_selectfile}" name="selectimg" />
                                    <input type="button" class="btn btn-info pull-left" style="margin-right: 5px" value="{LANG.file_checkUrl}" id="check_fileimage" onclick="nv_checkfile('fileimage',1, 'check_fileimage');" />
                                    <input type="button" class="btn btn-info pull-left" value="{LANG.file_gourl}" id= "go_fileimage" onclick="nv_gourl('fileimage',1, 'go_fileimage');" />
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="top"> {LANG.intro_title} </td>
                            <td><textarea name="introtext" style="width:500px;height:100px" class="form-control">{DATA.introtext}</textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                {LANG.file_description}
                                <br />
                                {DATA.description}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-sm-24 col-md-6">
            <div class="row">
                <div class="col-sm-12 col-md-24">
                    <label>{LANG.groups_view}</label>
                    <div class="groups-allow-area">
                        <!-- BEGIN: groups_view -->
                        <input name="groups_view[]" value="{GROUPS_VIEW.key}" type="checkbox"{GROUPS_VIEW.checked} /> {GROUPS_VIEW.title}
                        <br />
                        <!-- END: groups_view -->
                    </div>

                    <br />
                    <label>{LANG.groups_onlineview}</label>
                    <div class="groups-allow-area">
                        <!-- BEGIN: groups_onlineview -->
                        <input name="groups_onlineview[]" value="{GROUPS_ONLINEVIEW.key}" type="checkbox"{GROUPS_ONLINEVIEW.checked} /> {GROUPS_ONLINEVIEW.title}
                        <br />
                        <!-- END: groups_onlineview -->
                    </div>

                    <br />
                    <label>{LANG.groups_download}</label>
                    <div class="groups-allow-area">
                        <!-- BEGIN: groups_download -->
                        <input name="groups_download[]" value="{GROUPS_DOWNLOAD.key}" type="checkbox"{GROUPS_DOWNLOAD.checked} /> {GROUPS_DOWNLOAD.title}
                        <br />
                        <!-- END: groups_download -->
                    </div>
                    <br />
                </div>
                <div class="col-sm-12 col-md-24">
                    <label>{LANG.file_whocomment}</label>
                    <div class="groups-allow-area">
                        <!-- BEGIN: groups_comment -->
                        <input name="groups_comment[]" value="{GROUPS_COMMENT.key}" type="checkbox"{GROUPS_COMMENT.checked} /> {GROUPS_COMMENT.title}
                        <br />
                        <!-- END: groups_comment -->
                    </div>
                </div>
                <div class="col-sm-12 col-md-24">
                    <br />
                    <label>{LANG.content_tag}:</label>
                    <div class="clearfix uiTokenizer uiInlineTokenizer">
                        <div id="keywords" class="tokenarea">
                            <!-- BEGIN: keywords -->
                            <span class="uiToken removable" title="{KEYWORDS}" ondblclick="$(this).remove();"> {KEYWORDS} <input type="hidden" autocomplete="off" name="keywords[]" value="{KEYWORDS}" /> <a onclick="$(this).parent().remove();" class="remove uiCloseButton uiCloseButtonSmall" href="javascript:void(0);"></a> </span>
                            <!-- END: keywords -->
                        </div>
                        <div class="uiTypeahead">
                            <div class="wrap">
                                <input type="hidden" class="hiddenInput" autocomplete="off" value="" />
                                <div class="innerWrap">
                                    <input id="keywords-search" type="text" placeholder="{LANG.input_keyword_tags}" class="form-control textInput" style="width: 100%;" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <tbody>
                <tr>
                    <td class="w250" style="vertical-align:top"> {LANG.file_myfile} <sup class="required">(*)</sup></td>
                    <td>
                        <!-- BEGIN: fileupload_tmp -->
                        <div class="clearfix" style="margin-bottom: 5px"> 
                            <strong>{LANG.file_fileupload_tmp}:</strong>
                            <!-- BEGIN: loop -->
                            <div class="clearfix" style="margin-bottom: 5px">
                                <input readonly="readonly" class="w300 form-control pull-left" type="text" value="{FILEUPLOAD_TMP.value}" id="fileuploadtmp{FILEUPLOAD_TMP.key}" maxlength="255" />&nbsp;
                                <input class="btn btn-info" type="button" value="{LANG.file_checkUrl}" id= "check_fileuploadtmp{FILEUPLOAD_TMP.key}" onclick="nv_checkfile('fileuploadtmp{FILEUPLOAD_TMP.key}',1, 'check_fileuploadtmp{FILEUPLOAD_TMP.key}');" />
                                <input class="btn btn-info" type="button" value="{LANG.file_gourl}" id= "go_fileuploadtmp{FILEUPLOAD_TMP.key}" onclick="nv_gourl('fileuploadtmp{FILEUPLOAD_TMP.key}', 1, 'go_fileuploadtmp{FILEUPLOAD_TMP.key}');" />
                            </div>
                            <!-- END: loop -->
                            <strong>{LANG.file_fileupload_change}:</strong>
                        </div>
                        <!-- END: fileupload_tmp -->

                        <div id="fileupload_items">
                            <!-- BEGIN: fileupload -->
                            <div id="fileupload_item_{FILEUPLOAD.key}" style="margin-top: 5px">
                                <input readonly="readonly" class="w300 form-control pull-left" type="text" value="{FILEUPLOAD.value}" name="fileupload[]" id="fileupload{FILEUPLOAD.key}" maxlength="255" />&nbsp;
                                <input class="btn btn-default" type="button" value="{LANG.file_selectfile}" name="selectfile" onclick="nv_open_browse( '{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}=upload&popup=1&area=fileupload{FILEUPLOAD.key}&path={UPLOADS_DIR}&currentpath={FILES_DIR}&type=file', 'NVImg', 850, 420, 'resizable=no,scrollbars=no,toolbar=no,location=no,status=no' );return false;" />
                                <input class="btn btn-info" type="button" value="{LANG.file_checkUrl}" id= "check_fileupload{FILEUPLOAD.key}" onclick="nv_checkfile('fileupload{FILEUPLOAD.key}',1, 'check_fileupload{FILEUPLOAD.key}');" />
                                <input class="btn btn-info" type="button" value="{LANG.file_gourl}" id= "go_fileupload{FILEUPLOAD.key}" onclick="nv_gourl('fileupload{FILEUPLOAD.key}', 1, 'go_fileupload{FILEUPLOAD.key}');" />
                                <input class="btn btn-danger" type="button" onclick="nv_delurl( {DATA.id}, {FILEUPLOAD.key} ); " value="{LANG.file_delurl}">
                            </div>
                            <!-- END: fileupload -->
                        </div>
                        <script type="text/javascript">
                            var file_items = '{DATA.fileupload_num}';
                            var file_selectfile = '{LANG.file_selectfile}';
                            var nv_base_adminurl = '{NV_BASE_ADMINURL}';
                            var file_dir = '{FILES_DIR}';
                            var uploads_dir = '{UPLOADS_DIR}';
                            var file_checkUrl = '{LANG.file_checkUrl}';
                            var file_gourl = '{LANG.file_gourl}';
                            var file_delurl = '{LANG.file_delurl}';
                            var is_direct_upload = false;
                        </script>
                        <p style="margin-top: 10px">
                            <input class="btn btn-default" type="button" value="{LANG.add_file_items}" onclick="nv_file_additem({DATA.id});" />
                            <input class="btn btn-success" type="button" value="{LANG.file_direct_upload}" id="direct-upload"/>
                        </p>
                        <script type="text/javascript">
                        var uploader;
                        $(function(){
                            uploader = new plupload.Uploader({
                                runtimes: 'html5,flash,silverlight,html4',
                                browse_button: 'direct-upload',
                                url: "{DIRECT_UPLOAD_URL}",
                                file_data_name : 'upload',
                                filters: {
                                    max_file_size: '{UPLOAD_MAX_FILESIZE}b',
                                    mime_types : [
                                        <!-- BEGIN: mime -->
                                        { title : "{MIMI_TYPE} files", extensions : "{MIME_EXTS}" },
                                        <!-- END: mime -->
                                    ]
                                },
                                flash_swf_url: '{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/plupload/Moxie.swf',
                                silverlight_xap_url: '{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/plupload/Moxie.xap',
                                multipart : true,
                                init: {
                                      // Event on init uploader
                                    PostInit: function() {
                                    },
                                    // Event on add file (Add to queue or first add)
                                    FilesAdded: function(up, files) {
                                         is_direct_upload = true;
                                        plupload.each(files, function(file) {
                                            nv_file_additem(0, 1, file)
                                        });
                                    },
                                    // Event on trigger a file upload status
                                    UploadProgress: function(up, file) {
                                         $('[data-fileid="' + file.id + '"]').find('.upload-status').html(file.percent + "%");
                                    },
                                    // Event on error
                                    Error: function(up, err) {
                                         modalShow("{LANG.error}", err.code + ": " + err.message);
                                    },
                                    // Event on remove a file
                                    FilesRemoved: function(up, file){
                                        if (uploader.total.queued <= 0) {
                                            is_direct_upload = false;
                                        } else {
                                            is_direct_upload = true;
                                        }
                                    },
                                    // Event on one file finish uploaded (Maybe success or error)
                                    FileUploaded: function(up, file, response){
                                        response = response.response;
                                        var check = response.split('_');
                                        if(check[0] == 'ERROR') {
                                            file.status = plupload.FAILED;
                                            file.hint = check[1];
                                            uploader.total.uploaded --;
                                            uploader.total.failed ++;
                                        }else{
                                            file.name = response;
                                        }
                                        
                                        $.each(uploader.files, function(i, f){
                                            if( f.id == file.id ){
                                                uploader.files[i].status = file.status;
                                                uploader.files[i].hint = file.hint;
                                                uploader.files[i].name = file.name;
                                            }
                                        });
                                        
                                        if (file.status == plupload.DONE) {
                                            $('[name="fileupload[]"]', $('[data-fileid="' + file.id + '"]')).val(nv_base_siteurl + file_dir + '/' + file.name);
                                        } else {
                                            $('[name="fileupload[]"]', $('[data-fileid="' + file.id + '"]')).val(file.hint);
                                        }
                                    },
                                    // Event on all files are uploaded
                                    UploadComplete: function(up, files) {
                                        is_direct_upload = false;
                                        $('#file-upload-form').data('busy', false);
                                        $('#file-upload-form').find('[name="submit"]').click();
                                    }
                                }
                            });
                            uploader.init();                        
                        })
                        </script>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top"> {LANG.file_linkdirect}
                    <br />
                    (<em>{LANG.file_linkdirect_note}</em>) </td>
                    <td>
                    <div id="linkdirect_items">
                        <!-- BEGIN: linkdirect -->
                        <textarea name="linkdirect[]" id="linkdirect{LINKDIRECT.key}" style="width:500px;height:100px" class="form-control pull-left">{LINKDIRECT.value}</textarea>
                        &nbsp; &nbsp;<input type="button" class="btn btn-info pull-left" value="{LANG.file_checkUrl}" id="check_linkdirect{LINKDIRECT.key}" onclick="nv_checkfile('linkdirect{LINKDIRECT.key}',0, 'check_linkdirect{LINKDIRECT.key}');" />
                        <!-- END: linkdirect -->
                    </div>
                    <script type="text/javascript">
                        var linkdirect_items = '{DATA.linkdirect_num}';
                    </script>
                    <div class="clearfix">&nbsp;</div>
                    <p style="margin-top: 10px"><input type="button" class="btn btn-default" value="{LANG.add_linkdirect_items}" onclick="nv_linkdirect_additem();" /> ({LANG.add_linkdirect_items_note})</p>
                    </td>
                </tr>
                <tr>
                    <td> {LANG.file_size} </td>
                    <td><input type="text" class="w100 form-control pull-left" value="{DATA.filesize}" name="filesize" id="filesize" maxlength="11" /><span class="text-middle"> {LANG.config_maxfilemb} </span></td>
                </tr>
                <tr>
                    <td> {LANG.file_version} </td>
                    <td><input class="w300 form-control" type="text" value="{DATA.version}" name="version" id="version" maxlength="20" /></td>
                </tr>
                <tr>
                    <td> {LANG.file_copyright} </td>
                    <td><input class="w300 form-control" type="text" value="{DATA.copyright}" name="copyright" id="copyright" maxlength="20" /></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="text-align:center;padding-top:15px">
        <!-- BEGIN: is_del_report -->
        <input name="is_del_report" value="1" type="checkbox"{DATA.is_del_report} /> {LANG.report_delete} &nbsp;&nbsp;
        <!-- END: is_del_report -->
        <!-- BEGIN: approval -->
        <button type="submit" name="submit" class="btn btn-primary" onclick="$(this).html('<i class=\'fa fa-spin fa-spinner\'></i> {LANG.waiting}')">
            {LANG.download_filequeue_submit}
        </button>
        <input type="button" value="{LANG.download_filequeue_del}" class="btn btn-danger" onclick="nv_filequeue_del('{FILEQUEUEIDs}')"/>
        <!-- END: approval -->
        <!-- BEGIN: submit -->
        <button type="submit" name="submit" class="btn btn-primary" onclick="$(this).html('<i class=\'fa fa-spin fa-spinner\'></i> {LANG.waiting}')">
            {LANG.confirm}
        </button>
        <!-- END: submit -->
    </div>
</form>
<script type="text/javascript">
    $("input[name=selectimg]").click(function() {
        nv_open_browse("{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&{NV_NAME_VARIABLE}=upload&popup=1&area=fileimage&path={UPLOADS_DIR}&currentpath={IMG_DIR}&type=image", "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
        return false;
    });
    function get_alias() {
        var title = strip_tags(document.getElementById('title').value);
        if (title != '') {
            $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=content&nocache=' + new Date().getTime(), 'gettitle=' + encodeURIComponent(title), function(res) {
                if (res != "") {
                    document.getElementById('alias').value = res;
                } else {
                    document.getElementById('alias').value = '';
                }
            });
        }
        return false;
    }
</script>
<!-- BEGIN: get_alias -->
<script type="text/javascript">
    $(document).ready(function() {
        $("#title").change(function() {
            get_alias();
        });
    });
</script>
<!-- END: get_alias -->

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/admin_default/js/download.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#catid').select2();

        $("#keywords-search").bind("keydown", function(event) {
            if (event.keyCode === $.ui.keyCode.TAB && $(this).data("ui-autocomplete").menu.active) {
                event.preventDefault();
            }

            if(event.keyCode==13){
                var keywords_add= $("#keywords-search").val();
                keywords_add = trim( keywords_add );
                if( keywords_add != '' ){
                    nv_add_element( 'keywords', keywords_add, keywords_add );
                    $(this).val('');
                }
                return false;
            }

        }).autocomplete({
            source : function(request, response) {
                $.getJSON(script_name + "?" + nv_name_variable + "=" + nv_module_name + "&" + nv_fc_variable + "=tagsajax", {
                    term : extractLast(request.term)
                }, response);
            },
            search : function() {
                var term = extractLast(this.value);
                if (term.length < 2) {
                    return false;
                }
            },
            focus : function() {
            },
            select : function(event, ui) {
                if(event.keyCode!=13){
                    nv_add_element( 'keywords', ui.item.value, ui.item.value );
                    $(this).val('');
                   }
                return false;
            }
        });
        $("#keywords-search").blur(function() {
            var keywords_add= $("#keywords-search").val();
            keywords_add = trim( keywords_add );
            if( keywords_add != '' ){
                nv_add_element( 'keywords', keywords_add, keywords_add );
                $(this).val('');
            }
            return false;
        });
        $("#keywords-search").bind("keyup", function(event) {
            var keywords_add= $("#keywords-search").val();
            if(keywords_add.search(',') > 0 )
            {
                keywords_add = keywords_add.split(",");
                for (i = 0; i < keywords_add.length; i++) {
                    var str_keyword = trim( keywords_add[i] );
                    if( str_keyword != '' ){
                        nv_add_element( 'keywords', str_keyword, str_keyword );
                    }
                }
                $(this).val('');
            }
            return false;
        });
    });
</script>
<!-- END: main -->