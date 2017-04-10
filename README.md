# Hướng dẫn cập nhật module download từ 4.1.01 lên 4.1.02

Chú ý: 
- Gói cập nhật này chỉ dành cho module download 4.1.01, nếu module của bạn không ở phiên bản này cần tìm các hướng dẫn cập nhật lên 4.1.01 trước.
- Module download 4.1.02 hiện tại hoạt động trên NukeViet 4.1 Beta 2 (4.1.01)

## Chuẩn bị cập nhật

Backup toàn bộ CSDL dữ liệu và code của site đề phòng rủi ro.

## Thực hiện cập nhật

Đăng nhập quản trị site, di chuyển vào khu vực Công cụ web => Kiểm tra phiên bản, tại đây nếu hệ thống kiểm tra được module download và có yêu cầu cập nhật hãy tiến hành theo hướng dẫn của hệ thống.

Nếu không cập nhật được theo cách trên hãy thực hiện cập nhật thủ công như sau:

Tải gói cập nhật tại https://github.com/nukeviet/module-download/releases/download/4.1.02/update-to-4.1.02.zip. Giải nén và upload thư mục install lên ngang hàng với thư mục install trên server. Đăng nhập quản trị site, nhận được thông báo cập nhật và tiến hành cập nhật theo hướng dẫn của hệ thống.

## Xử lý sau cập nhật

Cần chú ý một số công việc sau:

- Vào mục cấu hình module để thiết lập quyền thêm file, các trường dữ liệu.
- Nếu giao diện của bạn sử dụng không phải giao diện default cần thực hiện các công việc bên dưới

## Cập nhật giao diện

### Cập nhật block_search.tpl

Tìm:

```html
<form action="{BASE_URL_SITE}index.php" method="get">
```

Thay lại thành:

```html
<form action="{FORM_ACTION}" method="get">
```

Tìm:

```html
    <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" />
    <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
    <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP_NAME}" />
```

Thêm lên trên:

```html
    <!-- BEGIN: no_rewrite -->
```

Thêm xuống dưới:

```html
    <!-- END: no_rewrite -->
```

### Cập nhật chức năng upload của thành viên

Đối chiếu upload.tpl, download.js, download.css từ giao diện mặc định để sửa cho phù hợp