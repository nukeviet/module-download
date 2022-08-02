# Hướng dẫn cập nhật module download từ 4.0.29, 4.1.00, 4.1.01, 4.1.02, 4.2.00, 4.2.01, 4.2.02, 4.3.00 lên 4.5.00

Chú ý:
- Gói cập nhật này dành cho module download 4.0.29, 4.1.00, 4.1.01, 4.1.02, 4.2.00, 4.2.01, 4.2.02, 4.3.00 nếu module của bạn không ở phiên bản này cần tìm các hướng dẫn cập nhật lên tối thiểu 4.0.29 trước.
- Module download 4.5.00 hiện tại hoạt động trên NukeViet 4.5.00

## Chuẩn bị cập nhật

Backup toàn bộ CSDL dữ liệu và code của site đề phòng rủi ro.

## Thực hiện cập nhật

Đăng nhập quản trị site, di chuyển vào khu vực Công cụ web => Kiểm tra phiên bản, tại đây nếu hệ thống kiểm tra được module download và có yêu cầu cập nhật hãy tiến hành theo hướng dẫn của hệ thống.

Nếu không cập nhật được theo cách trên hãy thực hiện cập nhật thủ công như sau:

Tải gói cập nhật tại https://github.com/nukeviet/module-download/releases/download/4.5.00/update-to-4.5.00.zip. Giải nén và upload thư mục install lên ngang hàng với thư mục install trên server. Đăng nhập quản trị site, nhận được thông báo cập nhật và tiến hành cập nhật theo hướng dẫn của hệ thống.

## Xử lý sau cập nhật

Cần chú ý một số công việc sau:

- Vào mục cấu hình module để thiết lập quyền thêm file, các trường dữ liệu.
- Nếu giao diện của bạn sử dụng không phải giao diện default cần thực hiện các công việc bên dưới

## Cập nhật giao diện

### Nếu hiện tại module đang là 4.2.01 hoặc nhỏ hơn

#### Cập nhật block_search.tpl

Nếu tồn tại file `themes/ten-theme/modules/download/block_search.tpl`, mở nó tìm

```html
            <option value="{loop.id}" {loop.select}>{loop.title}</option>
            {subcat}
```

Thay lại thành

```html
            <option value="{loop.id}" {loop.select}>{loop.space}{loop.title}</option>
```

Nếu module của bạn hiện tại < 4.2.01 tiếp tục làm các việc bên dưới, nếu đang là 4.2.01 thì tạm dừng tại đây.

### Nếu hiện tại module < 4.2.01

#### Cập nhật theme.php

Nếu tồn tại file `themes/ten-theme/modules/download/theme.php`, mở nó, tìm vị trí hàm view_file. Bên trong hàm thêm vào vị trí thích hợp đoạn

```php
    if ($download_config['shareport'] == 'addthis') {
        $xtpl->assign('ADDTHIS_PUBID', $download_config['addthis_pubid']);
        $xtpl->parse('main.addthis');
    }
```

#### Cập nhật viewfile.tpl

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

#### Cập nhật block_search.tpl

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

#### Cập nhật chức năng upload của thành viên

Đối chiếu upload.tpl, download.js, download.css từ giao diện mặc định để sửa cho phù hợp
