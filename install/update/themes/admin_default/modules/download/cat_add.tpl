<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">

<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form action="{FORM_ACTION}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <col class="w300"/>
            <tbody>
                <tr>
                    <td> {LANG.category_cat_name} </td>
                    <td><input class="w300 form-control" type="text" value="{DATA.title}" name="title" id="title" maxlength="100" {ONCHANGE} /></td>
                </tr>
                <tr>
                    <td> {LANG.alias} </td>
                    <td>
                        <input class="w300 form-control pull-left" type="text" value="{DATA.alias}" name="alias" id="alias" maxlength="100" />
                        &nbsp;<i class="fa fa-refresh fa-lg fa-pointer" onclick="get_alias();">&nbsp;</i>
                    </td>
                </tr>
                <tr>
                    <td> {LANG.description} </td>
                    <td><input class="w300 form-control" type="text" value="{DATA.description}" name="description" maxlength="255" /></td>
                </tr>
                <tr>
                    <td> {LANG.category_cat_parent} </td>
                    <td>
                    <select name="parentid" id="parentid" class="form-control w300">
                        <!-- BEGIN: parentid -->
                        <option value="{LISTCATS.id}"{LISTCATS.selected}>{LISTCATS.space}{LISTCATS.title}</option>
                        <!-- END: parentid -->
                    </select></td>
                </tr>
                <tr>
                    <td style="vertical-align:top"> {LANG.groups_view} </td>
                    <td>
                        <div class="groups-allow-area">
                            <!-- BEGIN: groups_view -->
                            <input name="groups_view[]" value="{GROUPS_VIEW.key}" type="checkbox"{GROUPS_VIEW.checked} /> {GROUPS_VIEW.title}
                            <br />
                            <!-- END: groups_view -->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top"> {LANG.groups_onlineview} </td>
                    <td>
                        <div class="groups-allow-area">
                            <!-- BEGIN: groups_onlineview -->
                            <input name="groups_onlineview[]" value="{GROUPS_ONLINEVIEW.key}" type="checkbox"{GROUPS_ONLINEVIEW.checked} /> {GROUPS_ONLINEVIEW.title}
                            <br />
                            <!-- END: groups_onlineview -->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top"> {LANG.groups_download} </td>
                    <td>
                        <div class="groups-allow-area">
                            <!-- BEGIN: groups_download -->
                            <input name="groups_download[]" value="{GROUPS_DOWNLOAD.key}" type="checkbox"{GROUPS_DOWNLOAD.checked} /> {GROUPS_DOWNLOAD.title}
                            <br />
                            <!-- END: groups_download -->
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" name="submit" value="{LANG.cat_save}" class="btn btn-primary" /></td>
                </tr>
            </tbody>
        </table>
    </div>
</form>

<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>
<script>
    $('#parentid').select2();
    function get_alias() {
    var title = strip_tags(document.getElementById('title').value);
    if (title != '') {
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'gettitle=' + encodeURIComponent(title)+'&parentid='+$('#parentid').val() , function(res) {
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
<!-- END: main -->
<!-- BEGIN: get_alias -->
<script type="text/javascript">
$(document).ready(function() {
    $("#title").change(function() {
        get_alias();
    });
    });
</script>
<!-- END: get_alias -->
<!-- END: main -->