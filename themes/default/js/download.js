/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC ( contact@vinades.vn )
 * @Copyright ( C ) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 1 - 31 - 2010 5 : 12
 */

// Viewfile
$(document).ready(function() {
    $("#pop").on("click", function() {
       $('#imagepreview').attr('src', $('#imageresource').attr('src'));
       $('#imagemodal').modal('show');
    });

	$('.hover-star').rating({
		focus : function(value, link) {
			var tip = $('#hover-test');
			if (sr != 2) {
				tip[0].data = tip[0].data || tip.html();
				tip.html(file_your_rating + ': ' + link.title || 'value: ' + value);
				sr = 1;
			}
		},
		blur : function(value, link) {
			var tip = $('#hover-test');
			if (sr != 2) {
				$('#hover-test').html(tip[0].data || '');
				sr = 1;
			}
		},
		callback : function(value, link) {
			if (sr == 1) {
				sr = 2;
				$('.hover-star').rating('disable');
				nv_sendrating(id, value);
			}
		}
	});

	$('.hover-star').rating('select', rating_point);
});

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

function nv_link_report(fid) {
	$.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'id=' + fid, function(res) {
		alert(report_thanks_mess);
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