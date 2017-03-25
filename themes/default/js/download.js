/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC ( contact@vinades.vn )
 * @Copyright ( C ) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 1 - 31 - 2010 5 : 12
 */

$(document).ready(function() {
    if (typeof uploadForm !== 'undefined') {
        $('#uploadForm').validate({
            rules: {
                upload_title: {
                    required: true,
                    rangelength: [3, 255]
                },
                upload_author_name: {
                    rangelength: [3, 100]
                },

                upload_author_email: {
                    email: true
                },

                upload_author_url: {
                    url: true
                },

                upload_fileupload: {
                    accept: $(this).data('upload_filetype')
                },

                upload_filesize: {
                    number: true
                },

                upload_fileimage: {
                    accept: 'jpg|gif|png'
                },

                upload_introtext: {
                    maxlength: 500
                },

                upload_description: {
                    maxlength: 5000
                },

                upload_user_name: {
                    required: true,
                    rangelength: [3, 60]
                },

                upload_seccode: {
                    required: true,
                    minlength: $('#upload_seccode_iavim').attr('maxlength')
                }
            }
        });
        $('[data-toggle="uploadcat"]').each(function() {
            var catid = $(this).data('catid');
            var parentid = $(this).data('parentid');
            var checkss = $(this).data('tokend');
            var $this = $(this);
            $.post(
                nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=upload&nocache=' + new Date().getTime(), 
                'loadcat=1&catid=' + catid + '&parentid=' + parentid + '&checkss=' + checkss, 
            function(res) {
                $this.html(res);
            });
        });
        $('#uploadForm').delegate('[data-toggle="upcatload"]', 'click', function(e) {
            e.preventDefault();
            var catid = $(this).data('catid');
            var parentid = $(this).data('parentid');
            var checkss = $(this).data('tokend');
            $.post(
                nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=upload&nocache=' + new Date().getTime(), 
                'loadcat=1&catid=' + catid + '&parentid=' + parentid + '&checkss=' + checkss, 
            function(res) {
                $('[data-toggle="uploadcat"]').html(res);
            });
        });
    }
});

// Upload
$('#upload_fileupload').change(function() {
    $('#file_name').val($(this).val().match(/[-_\w]+[.][\w]+$/i)[0]);
});

$('#upload_fileimage').change(function() {
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

function nv_download_file(fr, flnm) {
    var download_hits = document.getElementById('download_hits').innerHTML;
    download_hits = intval(download_hits);
    download_hits = download_hits + 1;
    document.getElementById('download_hits').innerHTML = download_hits;

    window.location.href = nv_base_siteurl + "index.php?" + nv_lang_variable + "=" + nv_lang_data + "&" + nv_name_variable + "=" + nv_module_name + "&" + nv_fc_variable + "=down&filename=" + flnm;
    return false;
}

function nv_linkdirect(code) {
    var download_hits = document.getElementById('download_hits').innerHTML;
    download_hits = intval(download_hits);
    download_hits = download_hits + 1;
    document.getElementById('download_hits').innerHTML = download_hits;

    win = window.open(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=down&code=' + code, 'mydownload');
    win.focus();
    return false;
}

function nv_link_report($_this, fid) {
    $.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=report&nocache=' + new Date().getTime(), 'id=' + fid, function(res) {
        alert($_this.data('thanks'));
    });
    return false;
}

function nv_sendrating(fid, point) {
    if (fid > 0 && point > 0 && point < 6) {
        $.post(nv_base_siteurl + 'index.php?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&nocache=' + new Date().getTime(), 'rating=' + fid + '_' + point, function(res) {
            $("#stringrating").html(res);
        });
    }
    return false;
}

function fix_download_image(){
	var news = $('#download-bodyhtml'), newsW, w, h;
	if( news.length ){
		var newsW = news.innerWidth();
		$.each($('img', news), function(){
			if( typeof $(this).data('width') == "undefined" ){
				w = $(this).innerWidth();
				h = $(this).innerHeight();
				$(this).data('width', w);
				$(this).data('height', h);
			}else{
				w = $(this).data('width');
				h = $(this).data('height');
			}
			
			if( w > newsW ){
				$(this).prop('width', newsW);
				$(this).prop('height', h * newsW / w);
			}
		});
	}
}

$(window).on('load', function() {
	fix_download_image();
});

$(window).on("resize", function() {
	fix_download_image();
});