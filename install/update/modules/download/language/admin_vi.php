<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @Language Tiếng Việt
 * @License CC BY-SA (http://creativecommons.org/licenses/by-sa/4.0/)
 * @Createdate Mar 04, 2010, 03:22:00 PM
 */

if (! defined('NV_ADMIN') or ! defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$lang_translator['author'] = 'VINADES.,JSC <contact@vinades.vn>';
$lang_translator['createdate'] = '04/03/2010, 15:22';
$lang_translator['copyright'] = '@Copyright (C) 2012 VINADES.,JSC. All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

$lang_module['msgnocheck'] = 'Vui lòng chọn ít nhất một dòng để thực hiện';
$lang_module['error'] = 'Lỗi';
$lang_module['waiting'] = 'Đang xử lý';
$lang_module['errorsave'] = 'Lỗi lưu dữ liệu không xác định';
$lang_module['function'] = 'Chức năng';
$lang_module['status'] = 'Hoạt động';

$lang_module['search'] = 'Tìm kiếm';
$lang_module['search_keywods'] = 'Từ khóa tìm kiếm';
$lang_module['search_per_page'] = 'Số file hiển thị';

$lang_module['download_config'] = 'Cấu hình module';
$lang_module['config_confirm'] = 'Chấp nhận';
$lang_module['config_indexfile'] = 'Phương án hiển thị trên trang chủ';
$lang_module['config_indexfile_main_bottom'] = 'Chuyên mục, tập tin khác nằm bên dưới';
$lang_module['config_indexfile_list_new'] = 'Danh sách, tập tin mới lên trên';
$lang_module['config_indexfile_none'] = 'Không hiển thị';
$lang_module['config_viewlist_type'] = 'Trình bày danh sách';
$lang_module['config_viewlist_list'] = 'Danh sách';
$lang_module['config_viewlist_table'] = 'Bảng';
$lang_module['config_per_page_home'] = 'Số lượng tập tin trên trang chủ';
$lang_module['config_per_page_child'] = 'Số lượng tập tin trên trang con';
$lang_module['config_is_addfile'] = 'Cho phép thêm file';
$lang_module['config_is_addfile_note'] = 'Cho phép người dùng có thể đóng góp tập tin cho hệ thống';
$lang_module['config_is_uploadfile'] = 'Cho phép upload file lên server';
$lang_module['config_allowfiletype'] = 'Loại file được cho phép tải lên';
$lang_module['config_maxfilesize'] = 'Dung lượng tối đa của file';
$lang_module['config_maxfilemb'] = 'MB';
$lang_module['config_maxfilesizesys'] = 'Giới hạn tải lên hệ thống của bạn là';
$lang_module['config_whouploadfile'] = 'Ai được upload file';
$lang_module['config_whoaddfile'] = 'Ai được thêm file';
$lang_module['config_delfile_mode'] = 'Thao tác với tệp tin khi xóa tài liệu';
$lang_module['config_delfile_mode0'] = 'Giữ lại tệp tin';
$lang_module['config_delfile_mode1'] = 'Xóa luôn tệp tin';
$lang_module['config_delfile_mode2'] = 'Hỏi người xóa hình thức thực hiện';
$lang_module['config_scorm_handle_mode'] = 'Sau khi giải nén SCORM thì';
$lang_module['config_scorm_handle_mode0'] = 'Xóa luôn file gốc';
$lang_module['config_scorm_handle_mode1'] = 'Giữ lại file gốc';
$lang_module['config_structure_upload'] = 'Thư mục tải file lên';
$lang_module['config_fileserver'] = 'Server upload file';
$lang_module['config_fileserver0'] = 'Local server';
$lang_module['config_fileserver1'] = 'File server';
$lang_module['config_share_shareport'] = 'Công cụ chia sẻ mạng xã hội';
$lang_module['config_share_shareport_none'] = 'Không sử dụng';
$lang_module['config_share_shareport_addthis'] = 'AddThis';
$lang_module['addthis_pubid'] = 'AddThis PubID';
$lang_module['config_pdf_handler'] = 'Kiểu xử lý việc xem PDF trực tuyến';
$lang_module['config_pdf_handler_filetmp'] = 'Copy ra file tạm (Có thể đầy bộ nhớ nếu tiến trình xóa file tạm không chạy)';
$lang_module['config_pdf_handler_phpattachment'] = 'Php Attachment (Khuyến nghị sử dụng, nếu thất bại hãy thử các cách khác)';
$lang_module['config_list_title_length'] = 'Số ký tự tên file tại trang danh sách (Nhập 0 nếu không giới hạn)';
$lang_module['config_basic'] = 'Các thiết lập hệ thống';
$lang_module['config_field'] = 'Thiết lập các trường đăng';
$lang_module['config_field_name'] = 'Tên trường';
$lang_module['config_field_required_admin'] = 'Bắt buộc nhập với admin';
$lang_module['config_field_required_user'] = 'Bắt buộc nhập với thành viên';
$lang_module['config_field_display_admin'] = 'Hiển thị trang đăng admin';
$lang_module['config_field_display_user'] = 'Hiển thị ở trang đăng thành viên';
$lang_module['config_copy_document'] = 'Cho phép copy tài liệu';
$lang_module['config_allow_fupload_import'] = 'Kích hoạt chức năng nhập file từ thư mục upload';
$lang_module['config_display'] = 'Cấu hình hiển thị';

$lang_module['note_cat'] = 'Bạn cần tạo chuyên mục trước';
$lang_module['error_cat1'] = 'Lỗi: Chủ đề này đã có !';
$lang_module['error_cat2'] = 'Lỗi: Chủ đề chưa được khai báo !';
$lang_module['error_cat3'] = 'Lỗi: Chủ đề mẹ mà bạn chọn không tồn tại !';
$lang_module['error_cat4'] = 'Vì một lý do nào đó, chủ đề mới đã không được tạo !';
$lang_module['error_cat5'] = 'Vì một lý do nào đó, các thay đổi mà bạn vừa khai báo đã không được lưu !';
$lang_module['addcat_titlebox'] = 'Thêm chủ đề';
$lang_module['category_cat_name'] = 'Tên chủ đề';
$lang_module['category_cat_parent'] = 'Thuộc chủ đề';
$lang_module['category_cat_maincat'] = 'Chủ đề chính';
$lang_module['category_viewcat'] = 'Cách thể hiện';
$lang_module['category_numlink'] = 'Số liên kết';
$lang_module['category_groups_addfile_note'] = 'Chú ý: Quyền thêm file ở đây tuân thủ theo thiết lập ở phần cấu hình module trước';
$lang_module['groups_view'] = 'Quyền xem mô tả';
$lang_module['groups_onlineview'] = 'Quyền xem trực tuyến (Nếu hỗ trợ)';
$lang_module['groups_download'] = 'Quyền tải file';
$lang_module['description'] = 'Mô tả';
$lang_module['cat_save'] = 'Lưu lại';
$lang_module['download_catmanager'] = 'Quản lý chủ đề';
$lang_module['category_cat_sub'] = 'chủ đề con';
$lang_module['category_cat_c'] = 'Chọn chủ đề';
$lang_module['category_cat_active'] = 'Hoạt động';
$lang_module['category_cat_feature'] = 'Chức năng';
$lang_module['table_caption1'] = 'Danh sách các chủ đề là chủ đề chính';
$lang_module['table_caption2'] = 'Danh sách các chủ đề con của chủ đề &ldquo;<strong>%s</strong>&rdquo;';
$lang_module['category_cat_sort'] = 'Vị trí';

$lang_module['file_error_fileupload'] = 'Hãy chọn file để upload hoặc điền vào link trực tiếp!';
$lang_module['file_error_fileupload1'] = 'Lỗi: Bạn chưa chọn file upload';
$lang_module['file_error_author_name'] = 'Lỗi: Tên tác giả không được để trống';
$lang_module['file_error_author_email'] = 'Lỗi: Email tác giả không được để trống';
$lang_module['file_error_author_url_empty'] = 'Lỗi: Trang cá nhân của tác giả không được để trống';
$lang_module['file_error_author_url'] = 'Lỗi: URL trang cá nhân của tác giả không hợp lệ!';
$lang_module['file_error_fileimage'] = 'Lỗi: Ảnh minh họa không được để trống';
$lang_module['file_error_introtext'] = 'Lỗi: Tóm tắt không được để trống';
$lang_module['file_error_description'] = 'Lỗi: Mô tả file không được để trống';
$lang_module['file_error_linkdirect'] = 'Lỗi: Chưa nhập nguồn bên ngoài';
$lang_module['file_error_filesize'] = 'Lỗi: Dung lượng file không thể bằng 0';
$lang_module['file_error_version'] = 'Lỗi: Thông tin phiên bản không được để trống';
$lang_module['file_error_copyright'] = 'Lỗi: Thông tin bản quyền không được để trống';
$lang_module['file_error1'] = 'Vì một lý do nào đó, các thay đổi mà bạn vừa khai báo đã không được lưu !';
$lang_module['file_error2'] = 'Vì một lý do nào đó, file mới đã không được lưu vào CSDL !';
$lang_module['file_catid_exists'] = 'Lỗi: Chủ đề chưa được chọn';
$lang_module['file_title'] = 'Tên file';
$lang_module['file_author_name'] = 'Tên tác giả';
$lang_module['file_author_email'] = 'Email tác giả';
$lang_module['file_author_url'] = 'Trang cá nhân của tác giả';
$lang_module['file_selectfile'] = 'Chọn file';
$lang_module['file_myfile'] = 'File tải lên';
$lang_module['file_linkdirect'] = 'Nguồn bên ngoài';
$lang_module['file_linkdirect_note'] = 'Nếu nguồn gồm nhiều file nhỏ, link đến các file này được phân cách bằng dấu xuống dòng - phím ENTER';
$lang_module['file_version'] = 'Thông tin phiên bản';
$lang_module['file_image'] = 'Hình minh họa';
$lang_module['file_copyright'] = 'Thông tin bản quyền';
$lang_module['file_allowcomment'] = 'Cho phép thảo luận';
$lang_module['file_whocomment'] = 'Ai được quyền thảo luận';
$lang_module['intro_title'] = 'Tóm tắt';
$lang_module['file_description'] = 'Mô tả file';
$lang_module['confirm'] = 'Thực hiện';
$lang_module['file_error_title'] = 'Tiêu đề không được để trống !';
$lang_module['file_size'] = 'Dung lượng';
$lang_module['file_list_by_cat'] = 'Danh sách các file thuộc chủ đề &ldquo;<strong>%s</strong>&rdquo;';
$lang_module['file_update'] = 'Thời gian đăng';
$lang_module['file_view_hits'] = 'Xem';
$lang_module['file_download_hits'] = 'Tải';
$lang_module['file_comment_hits'] = 'Bình';
$lang_module['file_feature'] = 'Chức năng';
$lang_module['file_active'] = 'Hoạt động';
$lang_module['file_addfile'] = 'Thêm file mới';
$lang_module['file_title_exists'] = 'Lỗi: Tên này đã được sử dụng. Hãy chọn một tên khác';
$lang_module['file_checkUrl'] = 'Kiểm tra';
$lang_module['file_checkUrl_error'] = 'Lỗi: URL không tồn tại!';
$lang_module['file_checkUrl_ok'] = 'URL được chấp nhận!';
$lang_module['file_gourl'] = 'Truy cập';
$lang_module['file_delurl'] = 'Xóa file';
$lang_module['file_delmode0'] = 'Xóa file và giữ lại đính kèm';
$lang_module['file_delmode1'] = 'Xóa file và xóa đính kèm';
$lang_module['file_direct_upload'] = 'Hoặc upload trực tiếp';
$lang_module['file_error_extract_scorm'] = 'Lỗi: Không thể giải nén file SCORM';
$lang_module['file_fileimage'] = 'Hình minh họa';
$lang_module['file_fileimage_tmp'] = 'Ảnh thành viên đưa lên';
$lang_module['file_fileimage_change'] = 'Thay mới <small>(Nếu chọn ảnh cũ sẽ bị xóa)</small>';
$lang_module['file_fileupload_tmp'] = 'File thành viên tải lên';
$lang_module['file_fileupload_change'] = 'Thay mới <small>(Nếu chọn các file cũ sẽ bị xóa)</small>';
$lang_module['file_introtext'] = 'Tóm tắt';
$lang_module['file_filesize'] = 'Dung lượng';

$lang_module['filequeue_empty'] = 'Rất tiếc là chưa có file nào được gửi đến!';
$lang_module['download_filequeue'] = 'File chờ kiểm duyệt';
$lang_module['download_filequeue_del'] = 'Xóa file';
$lang_module['download_filequeue_edit'] = 'Kiểm duyệt file';
$lang_module['download_filequeue_submit'] = 'Duyệt file';
$lang_module['download_alldel'] = 'Xóa tất cả';

$lang_module['report_empty'] = 'Chưa có báo cáo lỗi!';
$lang_module['report_post_time'] = 'Thời gian báo cáo';
$lang_module['report_check_ok'] = 'Hệ thống đã kiểm tra file và không phát hiện ra lỗi. Xóa báo cáo lỗi này?';
$lang_module['report_check_error'] = 'Hệ thống phát hiện link hỏng đối với file này. Hãy click OK để sửa hoặc CANCEL để thôi';
$lang_module['report_check_error2'] = 'Lỗi: File không tồn tại. Hãy click OK để xóa báo cáo này hoặc CANCEL để thôi';
$lang_module['report_delete'] = 'Xóa báo cáo lỗi';

$lang_module['download_report'] = 'Báo cáo lỗi';
$lang_module['download_filemanager'] = 'Quản lý file';
$lang_module['download_editfile'] = 'Sửa file';

$lang_module['editcat_cat'] = 'Sửa chủ đề';
$lang_module['add_file_items'] = 'Thêm file tải lên';
$lang_module['add_file_items_note'] = 'Nếu file có nhiều phần nhỏ';
$lang_module['add_linkdirect_items'] = 'Thêm nguồn bên ngoài';
$lang_module['add_linkdirect_items_note'] = 'Không được trùng với các nguồn đã liệt kê';
$lang_module['add_fileupload'] = 'Thay file tải lên mới';
$lang_module['add_new_img'] = 'Thay hình minh họa mới';
$lang_module['is_zip'] = 'ZIP file khi download';
$lang_module['zip_readme'] = 'Nội dung file README.txt kèm theo file ZIP';
$lang_module['is_resume'] = 'Hỗ trợ chế độ resume khi download';
$lang_module['max_speed'] = 'Hạn chế tốc độ tải file';
$lang_module['kb_sec'] = 'KB/sec (0 = không hạn chế)';
$lang_module['alias'] = 'Liên kết tĩnh';
$lang_module['siteinfo_publtime'] = 'Số file hiệu lực';
$lang_module['siteinfo_expired'] = 'Số file hết hạn';
$lang_module['siteinfo_users_send'] = 'Số file chờ kiểm duyệt';
$lang_module['siteinfo_eror'] = 'Số báo cáo lỗi được gửi tới';
$lang_module['siteinfo_comment'] = 'Số bình luận được gửi tới';
$lang_module['siteinfo_comment_pending'] = 'Số bình luận chờ kiểm duyệt';

$lang_module['notification_report'] = '<strong>%s</strong> đã thông báo tập tin <strong>%s</strong> bị lỗi';
$lang_module['notification_upload'] = '<strong>%s</strong> đã gửi tập tin <strong>%s</strong> lên hệ thống';

$lang_module['search_key'] = 'Từ khóa tìm kiếm';
$lang_module['search_note'] = 'Từ khóa tìm kiếm không ít hơn 2 ký tự, không lớn hơn 64 ký tự, không dùng các mã html';
$lang_module['keywords'] = 'Từ khóa';
$lang_module['numlinks'] = 'Số liên kết';
$lang_module['alias_search'] = 'Để hiển thị các tags khác, bạn dùng chức năng tìm kiếm để hiển thị nhiều kết quả hơn';
$lang_module['content_homeimg'] = 'Hình minh họa';
$lang_module['save'] = 'Lưu thay đổi';
$lang_module['download_tags'] = 'Quản lý Tags';
$lang_module['add_tags'] = 'Thêm Tags';
$lang_module['edit_tags'] = 'Sửa Tags';
$lang_module['tags_no_description'] = 'Chưa có mô tả';
$lang_module['tags_alias'] = 'Lọc bỏ dấu tiếng việt, các ký tự khác a-z, 0-9, - trong Liên kết tĩnh của tags';
$lang_module['alias_search'] = 'Để hiển thị các tags khác, bạn dùng chức năng tìm kiếm để hiển thị nhiều kết quả hơn';
$lang_module['tags_all_link'] = 'Chế độ xem các tags chưa có mô tả đang được kích hoạt, nhấp vào đây để xem tất cả các tags';
$lang_module['content_tag'] = 'Các tag cho file';
$lang_module['input_keyword_tags'] = 'Nhập từ khóa...';

$lang_module['action_active'] = 'Cho hoạt động';
$lang_module['action_deactive'] = 'Đình chỉ hoạt động';

$lang_module['fileserver_manager'] = 'Quản lý FileServer';
$lang_module['fileserver'] = 'FileServer';
$lang_module['fileserver_server_name'] = 'Tên FileServer';
$lang_module['fileserver_upload_url'] = 'Upload url';
$lang_module['fileserver_access_key'] = 'Access key';
$lang_module['fileserver_secret_key'] = 'Secret key';
$lang_module['fileserver_add'] = 'Thêm FileServer';
$lang_module['fileserver_edit'] = 'Sửa FileServer';
$lang_module['fileserver_error_server_name'] = 'Lỗi: Tên FileServer không được để trống';
$lang_module['fileserver_error_upload_url'] = 'Lỗi: Tham số upload_url không được để trống';
$lang_module['fileserver_error_exists'] = 'Lỗi: Tên FileServer bị trùng';

$lang_module['copy_title'] = 'Sao chép tài liệu';
$lang_module['copy_feature'] = 'Copy';
$lang_module['fimport'] = 'Import từ thư mục upload';
$lang_module['fimport_dotitle'] = 'Thực hiện import từ thư mục upload';
$lang_module['fimport_help_title'] = 'Hướng dẫn nhập file từ thư mục upload';
$lang_module['fimport_help_1'] = 'Bước 1: Chuẩn bị thư mục import';
$lang_module['fimport_help_2'] = 'Hệ thống hỗ trợ import với cấu trúc như sau';
$lang_module['fimport_help_3'] = 'Mỗi thư mục chính đầu tiên sẽ được tự động tạo một chủ đề riêng, tên thư mục là tên chủ đề. Để sắp xếp thứ tự hãy sử dụng số phía trước ví dụ <em>1. Bài giảng điện tử</em>';
$lang_module['fimport_help_4'] = 'Bên trong thư mục chính có thể là thư mục con chứa các tập tin hoặc trực tiếp chứa các tập tin';
$lang_module['fimport_help_5'] = 'Nếu trong thư mục chính trực tiếp chứa các tập tin thì mỗi tập tin sẽ được sử dụng làm một file/tài liệu mới';
$lang_module['fimport_help_6'] = 'Nếu trong thư mục chính chứa các thư mục con thì tên mỗi thư mục con sẽ được sử dụng làm một file/tài liệu mới và tất cả các tập tin bên trong đó sẽ là một phần của tài liệu';
$lang_module['fimport_help_7'] = 'Sau khi chuẩn bị dữ liệu theo cấu trúc trên, sử dụng phần mềm FTP để toàn bộ vào thư mục';
$lang_module['fimport_help_8'] = 'và đợi cho đến khi quá trình upload hoàn tất';
$lang_module['fimport_help_9'] = 'Bước 2: Thực hiện import';
$lang_module['fimport_help_10'] = 'Sau khi quá trình upload đã hoàn tất, truy cập lại khu vực này. Điền các thông tin cần thiết sau đó nhấp nút <em>Thực hiện</em> để tiến hành import';
$lang_module['fimport_help_11'] = 'Bước 3: Hiệu chỉnh dữ liệu';
$lang_module['fimport_help_12'] = 'Do dữ liệu import chưa đầy đủ nên bạn cần truy cập phần quản lý chủ đề, phần danh sách các file/tài liệu để tiến hành bổ sung thêm các thông tin.';
$lang_module['fimport_status'] = 'Trạng thái sau khi import';
$lang_module['fimport_status0'] = 'Hoạt động';
$lang_module['fimport_status1'] = 'Dừng hoạt động';
$lang_module['fimport_mode'] = 'Hình thức import';
$lang_module['fimport_mode0'] = 'Bỏ qua nếu bị trùng';
$lang_module['fimport_mode1'] = 'Luôn tạo mới';
$lang_module['fimport_catprocess'] = 'Hình thức xử lý tên chủ đề';
$lang_module['fimport_catprocess0'] = 'Cắt bỏ số thứ tự ở trước tên thư mục';
$lang_module['fimport_catprocess1'] = 'Giữ nguyên tên thư mục';
$lang_module['fimport_submit'] = 'Thực hiện';
$lang_module['fimport_error_nofolder'] = 'Không có gì để import';
$lang_module['fimport_success'] = 'Nhập file/tài liệu thành công, dưới đây là thông tin';
$lang_module['fimport_stat_cat_add'] = 'Số chủ đề được tạo mới';
$lang_module['fimport_stat_cat_ignore'] = 'Số chủ đề đã bỏ qua do đã có';
$lang_module['fimport_stat_file_add'] = 'Số file/tài liệu được tạo mới';
$lang_module['fimport_stat_file_ignore'] = 'Số file/tài liệu đã bỏ qua do đã có';
