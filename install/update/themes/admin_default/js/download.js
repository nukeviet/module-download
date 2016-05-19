/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC ( contact@vinades.vn )
 * @Copyright ( C ) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 1 - 31 - 2010 5 : 12
 */

function nv_change_weight_res(res) {
    var r_split = res.split("_");
    if (r_split[0] != 'OK') {
        alert(nv_is_change_act_confirm[2]);
        clearTimeout(nv_timer);
    } else {
        window.location.href = window.location.href;
    }
    return;
}

function nv_chang_weight(catid) {
    var nv_timer = nv_settimeout_disable('weight' + catid, 5000);
    var newpos = $("#weight" + catid).val();
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'changeweight=1&catid=' + catid + '&new=' + newpos, function(res) {
        if (res != 'OK') {
            alert(nv_is_change_act_confirm[2]);
        }
        clearTimeout(nv_timer);
        window.location.href = window.location.href;
    });
    return;
}

//  ---------------------------------------

function nv_chang_status(catid) {
    var nv_timer = nv_settimeout_disable('change_status' + catid, 5000);
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'changestatus=1&catid=' + catid, function(res) {
        if (res != 'OK') {
            alert(nv_is_change_act_confirm[2]);
            window.location.href = window.location.href;
        }
    });
    return;
}

//  ---------------------------------------

function nv_row_del(catid) {
    if (confirm(nv_is_del_confirm[0])) {
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=cat&nocache=' + new Date().getTime(), 'del=1&catid=' + catid, function(res) {
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert(nv_is_del_confirm[2]);
            }
        });
    }
    return false;
}

//  ---------------------------------------
function nv_file_del_submit (fid, delfile) {
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&nocache=' + new Date().getTime(), 'del=1&delfile=' + delfile + '&id=' + fid, function(res) {
        if (res == 'OK') {
            window.location.href = window.location.href;
        } else {
            alert(nv_is_del_confirm[2]);
        }
    });
}

function nv_file_del(fid, mode) {
    if (mode == 2) {
        $('#delete-filemode').find('[type="button"]').attr('data-id', fid);
        modalShowByObj('#delete-filemode')
    } else if (confirm(nv_is_del_confirm[0])) {
        nv_file_del_submit(fid, -1);
    }
    return false;
}

function nv_file_action(oBtn, oForm, msgnocheck) {
    var fa = oForm['idcheck[]'];
    var listid = '';
    if (fa.length) {
        for (var i = 0; i < fa.length; i++) {
            if (fa[i].checked) {
                listid = listid + fa[i].value + ',';
            }
        }
    } else {
        if (fa.checked) {
            listid = listid + fa.value + ',';
        }
    }

    if (listid != '') {
        var action = document.getElementById('action').value,
            delmode = '';
        if (action == 'del0' || action == 'del1') {
            if (!confirm(nv_is_del_confirm[0])) {
                return
            }
            delmode = ('&delfile=' + (action == 'del0' ? '0' : '1'));
            action = 'del';
        }
        $(oBtn).prop('disabled', true)
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&nocache=' + new Date().getTime(), action + '=1&listid=' + listid + delmode, function(res) {
            $(oBtn).prop('disabled', false)
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else if (action == 'del') {
                 alert(nv_is_del_confirm[2]);
            } else {
                 alert(nv_is_change_act_confirm[2]);
            }
        });
    } else {
        alert(msgnocheck);
    }
}

//  ---------------------------------------

function nv_chang_file_status(fid) {
    var nv_timer = nv_settimeout_disable('change_status' + fid, 5000);
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&nocache=' + new Date().getTime(), 'changestatus=1&id=' + fid, function(res) {
        if (res != 'OK') {
            alert(nv_is_change_act_confirm[2]);
            window.location.href = window.location.href;
        }
    });
    return;
}

//  ---------------------------------------

function nv_filequeue_del(fid) {
    if (confirm(nv_is_del_confirm[0])) {
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=filequeue&nocache=' + new Date().getTime(), 'del=1&id=' + fid, function(res) {
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert(nv_is_del_confirm[2]);
            }
        });
    }
    return false;
}

//  ---------------------------------------

function nv_filequeue_alldel() {
    if (confirm(nv_is_del_confirm[0])) {
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=filequeue&nocache=' + new Date().getTime(), 'alldel=1', function(res) {
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert(nv_is_del_confirm[2]);
            }
        });
    }
    return false;
}

//  ---------------------------------------

function nv_change_fileserver_weight(server_id) {
    var nv_timer = nv_settimeout_disable('change_weight_' + server_id, 5000);
    var new_weight = $('#change_weight_' + server_id).val();
    $.post(
        script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fileserver&nocache=' + new Date().getTime(), 
        'changeweight=1&server_id=' + server_id + '&new_weight=' + new_weight, function(res) {
        nv_change_weight_res(res);
    });
    return;
}
function nv_del_fileserver(server_id) {
    if (confirm(nv_is_del_confirm[0])) {
        $.post(
            script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fileserver&nocache=' + new Date().getTime(),
            'delete=1&server_id=' + server_id, function(res) {
            var r_split = res.split("_");
            if (r_split[0] == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert(nv_is_del_confirm[2]);
            }
        });
    }
    return false;
}
function nv_change_fileserver_status(server_id) {
    var nv_timer = nv_settimeout_disable('change_status' + server_id, 4000);
    $.post(
        script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=fileserver&nocache=' + new Date().getTime(),
        'changestatus=1&server_id=' + server_id,
        function(res) {
            if (res != 'OK') {
                alert(nv_is_change_act_confirm[2]);
                window.location.href = window.location.href;
            }
        });
}

//  ---------------------------------------

function nv_checkfile(mylink, is_myUrl, butt) {
    var nv_timer = nv_settimeout_disable(butt, 5000);
    var link = document.getElementById(mylink).value;
    if (trim(link) == '') {
        document.getElementById(mylink).value = '';
        return false;
    }

    link = rawurlencode(link);
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&nocache=' + new Date().getTime(), 'check=1&url=' + link + '&is_myurl=' + is_myUrl, function(res) {
        alert(res);
    });
    return false;
}

//  ---------------------------------------

function nv_gourl(mylink, is_myUrl, butt) {
    var nv_timer = nv_settimeout_disable(butt, 5000);
    var link = document.getElementById(mylink).value;
    if (trim(link) == '') {
        document.getElementById(mylink).value = '';
        return false;
    }

    if (is_myUrl) {
        link = rawurlencode(link);
        link = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&fdownload=' + link;
        window.location.href = link;

    } else {
        if (! link.match(/^(http|ftp)\:\/\/\w+([\.\-]\w+)*\.\w{2,4}(\:\d+)*([\/\.\-\?\&\%\#]\w+)*\/?$/i)) {
            alert(nv_url);
            document.getElementById(mylink).focus();
        } else {
            var w = window.open(link);
            w.focus();
        }
    }
    return false;
}

//  ---------------------------------------

function nv_delurl(id, item, file) {
    if (typeof file != 'undefined') {
        uploader.removeFile(file);
    }
    $("#fileupload_item_" + item).remove();
}

//  ---------------------------------------

function nv_report_del(rid) {
    if (confirm(nv_is_del_confirm[0])) {
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'del=1&id=' + rid, function(res) {
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert(nv_is_del_confirm[2]);
            }
        });
    }
    return false;
}

//  ---------------------------------------

function nv_report_check(fid) {
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'linkcheck=1&id=' + fid, function(res) {
        var r_split = res.split("_");
        if (r_split[0] == "OK") {
            var report_check_ok = document.getElementById('report_check_ok').value;
            if (confirm(report_check_ok)) {
                $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'del=1&id=' + r_split[1], function(res) {
                    if (res == 'OK') {
                        window.location.href = window.location.href;
                    } else {
                        alert(nv_is_del_confirm[2]);
                    }
                });
            }
        } else {
            if (r_split[0] == "NO") {
                var report_check_error = document.getElementById('report_check_error').value;
                if (confirm(report_check_error)) {
                    nv_report_edit(r_split[1]);
                }
            } else {
                var report_check_error2 = document.getElementById('report_check_error2').value;
                if (confirm(report_check_error2)) {
                    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'del=1&id=' + r_split[1], function(res) {
                        if (res == 'OK') {
                            window.location.href = window.location.href;
                        } else {
                            alert(nv_is_del_confirm[2]);
                        }
                    });
                }
            }
        }
    });
    return false;
}

//  ---------------------------------------

function nv_report_edit(fid) {
    window.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&edit=1&id=' + fid + '&report=1';
    return false;
}

//  ---------------------------------------

function nv_report_alldel() {
    if (confirm(nv_is_del_confirm[0])) {
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'alldel=1', function(res) {
            if (res == 'OK') {
                window.location.href = window.location.href;
            } else {
                alert(nv_is_del_confirm[2]);
            }
        });
    }
    return false;
}

//  ---------------------------------------

function nv_file_additem(id, queue, file) {
    file_items ++;
    var isQueue = (typeof queue != 'undefined' && queue)
    
    var newitem = "<div" + (isQueue ? " data-fileid=\"" + file.id + "\"" : "") + " id=\"fileupload_item_" + file_items + "\" style=\"margin-top: 5px\">&nbsp;<input readonly=\"readonly\" class=\"w300 form-control pull-left\" value=\"" + (isQueue ? file.name : "") + "\" name=\"fileupload[]\" id=\"fileupload" + file_items + "\" maxlength=\"255\" />";
    newitem += "&nbsp;<input" + (isQueue ? " disabled=\"disabled\"" : "") + " class=\"btn btn-default\" type=\"button\" value=\"" + file_selectfile + "\" name=\"selectfile\" onclick=\"nv_open_browse( '" + nv_base_adminurl + "index.php?" + nv_name_variable + "=upload&popup=1&area=fileupload" + file_items + "&path=" + uploads_dir + "&currentpath=" + file_dir + "&type=file', 'NVImg', 850, 420, 'resizable=no,scrollbars=no,toolbar=no,location=no,status=no' ); return false; \" />";
    newitem += "&nbsp;<input" + (isQueue ? " disabled=\"disabled\"" : "") + " class=\"btn btn-info\" type=\"button\" value=\"" + file_checkUrl + "\" id= \"check_fileupload" + file_items + "\" onclick=\"nv_checkfile( 'fileupload" + file_items + "', 1, 'check_fileupload" + file_items + "' ); \" />";
    newitem += "&nbsp;<input" + (isQueue ? " disabled=\"disabled\"" : "") + " class=\"btn btn-info\" type=\"button\" value=\"" + file_gourl + "\" id= \"go_fileupload" + file_items + "\" onclick=\"nv_gourl( 'fileupload" + file_items + "', 1, 'go_fileupload" + file_items + "' ); \" />";
    newitem += "&nbsp;<input class=\"btn btn-danger\" type=\"button\" value=\"" + file_delurl + "\" onclick=\"nv_delurl( " + id + ", " + file_items + (isQueue ? ", '" + file.id + "'" : "") + " ); \" />&nbsp;<span class=\"upload-status\"></span></div>";
    $("#fileupload_items").append(newitem);
}

//  ---------------------------------------

function nv_linkdirect_additem() {
    var newitem = "<div class=\"clearfix\">&nbsp;</div><textarea name=\"linkdirect[]\" id=\"linkdirect" + linkdirect_items + "\" style=\"width : 500px; height : 100px; margin-top: 10px\" class=\"form-control pull-left\"></textarea>";
    newitem += "&nbsp;&nbsp;<input type=\"button\" style=\"margin-top: 10px\" class=\"btn btn-info pull-left\" value=\"" + file_checkUrl + "\" id=\"check_linkdirect" + linkdirect_items + "\" onclick=\"nv_checkfile( 'linkdirect" + linkdirect_items + "', 0, 'check_linkdirect" + linkdirect_items + "' ); \" /><br />";
    $("#linkdirect_items").append(newitem);
    linkdirect_items++;
}

function nv_viewcat_change( catid, mod ) {
    var nv_timer = nv_settimeout_disable('id_' + mod + '_' + catid, 5000);
    var new_vid = $('#id_' + mod + '_' + catid).val();
    $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=change_cat&nocache=' + new Date().getTime(), 'catid=' + catid + '&mod=' + mod + '&new_vid=' + new_vid, function(res) {
        var r_split = res.split('_');
        if (r_split[0] != 'OK') {
            alert(nv_is_change_act_confirm[2]);
        }
        clearTimeout(nv_timer);
        window.location.href = window.location.href;
    });
    return;
}

function split(val) {
    return val.split(/,\s*/);
}

function extractLast(term) {
    return split(term).pop();
}

function modalShowByObj(a) {
    var b = $(a).attr("title"),
        c = $(a).html();
    modalShow(b, c)
}

$(document).ready(function() {
    $("input[name='catids[]']").click(function() {
        var catid = $("input:radio[name=catid]:checked").val();
        var radios_catid = $("input:radio[name=catid]");
        var catids = [];
        $("input[name='catids[]']").each(function() {
            if ($(this).prop('checked')) {
                $("#catright_" + $(this).val()).show();
                catids.push($(this).val());
            } else {
                $("#catright_" + $(this).val()).hide();
                if ($(this).val() == catid) {
                    radios_catid.filter("[value=" + catid + "]").prop("checked", false);
                }
            }
        });

        if (catids.length > 1) {
            for ( i = 0; i < catids.length; i++) {
                $("#catright_" + catids[i]).show();
            };
            catid = parseInt($("input:radio[name=catid]:checked").val() + "");
            if (!catid) {
                radios_catid.filter("[value=" + catids[0] + "]").prop("checked", true);
            }
        }
    });
    // Delete file
    $(document).delegate('[name="delete-filemode-submit"]', 'click', function(e){
        e.preventDefault();
        var $this = $(this),
            wrap = $(this).parent().parent();
        $('#sitemodal').modal('toggle');
        nv_file_del_submit($this.data('id'), $('select', wrap).val());
    });
    // Upload handler
    $('#file-upload-form').submit(function(e){
        var $this = $(this);
        if($this.data('busy')) {
            e.preventDefault()
            console.log('Busy')
            return
        }
        if (is_direct_upload) {
            e.preventDefault()
            $this.data('busy', true)
            $("html, body").animate({
                scrollTop: $('#fileupload_items').offset().top
            }, 200, function(){
                uploader.start();
            });
        }
    })
    // Show/Hide fileserver link
    $('[name="fileserver"]').change(function(){
        if ($(this).val() == '0') {
            $('#fileserverLink').hide();
        } else {
            $('#fileserverLink').show();
        }
    })
});

function nv_search_tag(did) {
    $("#module_show_list").html('<p class="text-center"><img src="' + nv_base_siteurl + 'assets/images/load_bar.gif" alt="Waiting..."/></p>').load(script_name + "?" + nv_lang_variable + "=" + nv_lang_data + "&" + nv_name_variable + "=" + nv_module_name + "&" + nv_fc_variable + "=tags&q=" + rawurlencode($("#q").val()) + "&num=" + nv_randomPassword(10));
    return false;
}

function nv_del_tags(did) {
    if (confirm(nv_is_del_confirm[0])) {
        $("#module_show_list").html('<p class="text-center"><img src="' + nv_base_siteurl + 'assets/images/load_bar.gif" alt="Waiting..."/></p>').load(script_name + "?" + nv_lang_variable + "=" + nv_lang_data + "&" + nv_name_variable + "=" + nv_module_name + "&" + nv_fc_variable + "=tags&del_did=" + did + "&num=" + nv_randomPassword(10));
    }
    return false;
}

function nv_add_element( idElment, key, value ){
   var html = "<span title=\"" + value + "\" class=\"uiToken removable\" ondblclick=\"$(this).remove();\">" + value + "<input type=\"hidden\" value=\"" + key + "\" name=\"" + idElment + "[]\" autocomplete=\"off\"><a onclick=\"$(this).parent().remove();\" href=\"javascript:void(0);\" class=\"remove uiCloseButton uiCloseButtonSmall\"></a></span>";
    $("#" + idElment).append( html );
    return false;
}
