# Hướng dẫn cập nhật module download từ 4.0.29 lên 4.1.00

Chú ý: 
- Gói cập nhật này chỉ dành cho module download 4.0.29, nếu module của bạn không ở phiên bản này cần tìm các hướng dẫn cập nhật lên 4.0.29 trước.
- Module download 4.1.00 hoạt động tốt trên NukeViet 4 RC3, NukeViet 4 Official và NukeViet 4.1 Beta

## Chuẩn bị cập nhật

Backup toàn bộ CSDL dữ liệu và code của site đề phòng rủi ro.

## Thực hiện cập nhật

Đăng nhập quản trị site, di chuyển vào khu vực Công cụ web => Kiểm tra phiên bản, tại đây nếu hệ thống kiểm tra được module download và có yêu cầu cập nhật hãy tiến hành theo hướng dẫn của hệ thống.

Nếu không cập nhật được theo cách trên hãy thực hiện cập nhật thủ công như sau:

Tải gói cập nhật tại https://github.com/nukeviet/module-download/releases/download/4.1.00/update-to-4.1.00.zip. Giải nén và upload thư mục install lên ngang hàng với thư mục install trên server. Đăng nhập quản trị site, nhận được thông báo cập nhật và tiến hành cập nhật theo hướng dẫn của hệ thống.

## Xử lý sau cập nhật

Cần chú ý một số công việc sau:

- Vào mục cấu hình module để thiết lập chức năng share qua cổng AddThis, chức năng xử lý phần xem trực tuyến.
- Nếu giao diện của bạn sử dụng không phải giao diện default cần thực hiện các công việc bên dưới

## Cập nhật giao diện

Nếu tồn tại file `themes/ten-theme/modules/download/theme.php`, mở nó, tìm vị trí hàm `view_file`. Bên trong hàm thêm vào vị trí thích hợp đoạn

```php
    if ($download_config['shareport'] == 'addthis') {
        $xtpl->assign('ADDTHIS_PUBID', $download_config['addthis_pubid']);
        $xtpl->parse('main.addthis');
    }
```

Nếu tồn tại file `themes/ten-theme/modules/download/viewfile.tpl`, mở nó tìm 

```html
<h2 class="m-bottom">{ROW.title}</h2>
```

Hoặc tương đương thêm vào sau

```html
    <!-- BEGIN: addthis -->
    <div class="m-bottom clearfix">
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={ADDTHIS_PUBID}"></script>
        <div class="addthis_sharing_toolbox"></div>
    </div>
    <!-- END: addthis -->
```
