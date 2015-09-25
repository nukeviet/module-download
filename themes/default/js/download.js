/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC ( contact@vinades.vn )
 * @Copyright ( C ) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 1 - 31 - 2010 5 : 12
 */

// Upload
$('#upload_fileupload').change(function(){
     $('#file_name').val($(this).val().match(/[-_\w]+[.][\w]+$/i)[0]);
});

$('#upload_fileimage').change(function(){
     $('#photo_name').val($(this).val().match(/[-_\w]+[.][\w]+$/i)[0]);
});

function nv_del_row(hr, fid) {
	if (confirm(nv_is_del_confirm[0])) {
		$.post(hr.href + '&nocache=' + new Date().getTime(), 'del=1&id=' + fid, function(res) {
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

function nv_download_file(fr, flnm) {
	var download_hits = document.getElementById('download_hits').innerHTML;
	download_hits = intval(download_hits);
	download_hits = download_hits + 1;
	document.getElementById('download_hits').innerHTML = download_hits;

	window.location.href = nv_base_siteurl + "index.php?" + nv_lang_variable + "=" + nv_lang_data + "&" + nv_name_variable + "=" + nv_module_name + "&" + nv_fc_variable + "=down&filename=" + flnm;
	return false;
}

//  ---------------------------------------

function nv_linkdirect(code) {
	var download_hits = document.getElementById('download_hits').innerHTML;
	download_hits = intval(download_hits);
	download_hits = download_hits + 1;
	document.getElementById('download_hits').innerHTML = download_hits;

	win = window.open(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=down&code=' + code, 'mydownload');
	win.focus();
	return false;
}

//  ---------------------------------------

function nv_link_report($_this, fid) {
	$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'id=' + fid, function(res) {
		alert($_this.data('thanks'));
	});
	return false;
}

//  ---------------------------------------

function nv_sendrating(fid, point) {
	if (fid > 0 && point > 0 && point < 6) {
		$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&nocache=' + new Date().getTime(), 'rating=' + fid + '_' + point, function(res) {
			$("#stringrating").html(res);
		});
	}
	return false;
}