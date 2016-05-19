<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>

<div class="well">
    <form action="{NV_BASE_ADMINURL}index.php" method="get">
        <input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
        <input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
        <input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
        <div class="row">
            <div class="col-xs-24 col-md-6">
                <div class="form-group">
                    <input class="form-control" type="text" value="{SEARCH.q}" name="q" maxlength="255" placeholder="{LANG.search_keywods}" />
                </div>
            </div>
            <div class="col-xs-24 col-md-6">
                <div class="form-group">
                    <select class="form-control" name="catid" id="catid">
                        <option value="">---{LANG.category_cat_c}---</option>
                        <!-- BEGIN: catid -->
                        <option value="{LISTCATS.id}"{LISTCATS.selected}>{LISTCATS.space}{LISTCATS.title}</option>
                        <!-- END: catid -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-md-4">
                <div class="form-group">
                    <select class="form-control" name="active">
                        <option value="-1">---{LANG.category_cat_active}---</option>
                        <!-- BEGIN: active -->
                        <option value="{ACTIVE.key}"{ACTIVE.selected}>{ACTIVE.value}</option>
                        <!-- END: active -->
                    </select>
                </div>
            </div>
            <div class="col-xs-24 col-md-3">
                <div class="form-group">
                    <select class="form-control" name="per_page">
                        <option value="30">---{LANG.search_per_page}---</option>
                        <!-- BEGIN: per_page -->
                        <option value="{PER_PAGE.key}"{PER_PAGE.selected}>{PER_PAGE.key}</option>
                        <!-- END: per_page -->
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="{LANG.search}" />
                </div>
            </div>
        </div>
    </form>
</div>

<form class="form-inline">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <colgroup>
                <col />
                <col span="2" />
                <col class="w150" />
                <col span="4" class="w100" />
                <col class="w150" />
            </colgroup>
            <thead>
                <tr class="text-center">
                    <th class="text-center w50"><input name="check_all[]" type="checkbox" value="yes" onclick="nv_checkAll(this.form, 'idcheck[]', 'check_all[]',this.checked);" /></th>
                    <th>{LANG.file_title}</th>
                    <th>{LANG.category_cat_parent}</th>
                    <th>{LANG.file_update}</th>
                    <th class="text-center">{LANG.file_view_hits}</th>
                    <th class="text-center">{LANG.file_download_hits}</th>
                    <th class="text-center">{LANG.file_comment_hits}</th>
                    <th class="text-center">{LANG.file_active}</th>
                    <th class="text-center">{LANG.file_feature}</th>
                </tr>
            </thead>
            <tbody>
                <!-- BEGIN: row -->
                <tr>
                    <td class="text-center"><input type="checkbox" onclick="nv_UncheckAll(this.form, 'idcheck[]', 'check_all[]', this.checked);" value="{ROW.id}" name="idcheck[]" /></td>
                    <td><a target="_blank" href="{ROW.link}">{ROW.title}</a></td>
                    <td><a href="{ROW.catlink}">{ROW.cattitle}</a></td>
                    <td>{ROW.uploadtime}</td>
                    <td class="text-center">{ROW.view_hits}</td>
                    <td class="text-center">{ROW.download_hits}</td>
                    <td class="text-center">{ROW.comment_hits}</td>
                    <td class="text-center"><input name="status" id="change_status{ROW.id}" value="1" type="checkbox"{ROW.status} onclick="nv_chang_file_status({ROW.id})" /></td>
                    <td class="text-center">
                        <em class="fa fa-edit fa-lg">&nbsp;</em> <a href="{EDIT_URL}">{GLANG.edit}</a> &nbsp;&nbsp;
                        <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_file_del({ROW.id}, {DELETEFILE_MODE});">{GLANG.delete}</a></td>
                </tr>
                <!-- END: row -->
            </tbody>
            <!-- BEGIN: generate_page -->
            <tbody>
                <tr>
                    <td colspan="9" class="text-center">{GENERATE_PAGE}</td>
                </tr>
            </tbody>
            <!-- END: generate_page -->
            <tfoot>
                <tr class="text-left">
                    <td colspan="9">
                        <select class="form-control" name="action" id="action">
                            <!-- BEGIN: action -->
                            <option value="{ACTION.value}">{ACTION.title}</option>
                            <!-- END: action -->
                        </select>
                        <input type="button" class="btn btn-primary" onclick="nv_file_action(this, this.form, '{LANG.msgnocheck}')" value="{LANG.confirm}" />
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</form>
<script>
    $('#catid').select2();
</script>
<div class="hidden" id="delete-filemode" title="{LANG.config_delfile_mode}">
    <div class="input-group">
        <select class="form-control">
            <option value="0">{LANG.config_delfile_mode0}</option>
            <option value="1">{LANG.config_delfile_mode1}</option>
        </select>
        <span class="input-group-btn">
            <input type="button" name="delete-filemode-submit" value="{GLANG.delete}" class="btn btn-danger" data-id="0"/>
        </span>
    </div>
</div>
<!-- END: main -->