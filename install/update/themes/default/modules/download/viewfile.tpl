<!-- BEGIN: main -->
<script type="text/javascript" src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/{MODULE_FILE}_jquery.shorten.js"></script>

<div class="block_download">
    <!-- BEGIN: scorm -->
    <a class="btn btn-success pull-right btn-xs" href="{SCORM_LINK}" target="_blank">{LANG.onlineview}</a>
    <!-- END: scorm -->
    
    <!-- BEGIN: scorms -->
    <div class="pull-right">
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle btn-xs" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            {LANG.onlineview}
            <span class="caret"></span>
          </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <!-- BEGIN: loop --><li><a href="{SCORM_LINK}" target="_blank">{LANG.onlineview_path} {SCORM_NUM}</a></li><!-- END: loop -->
            </ul>
        </div>
    </div>
    <!-- END: scorms -->
    
    <h2 class="m-bottom">{ROW.title}</h2>
    <!-- BEGIN: introtext -->
    <div class="panel panel-default">
        <div class="panel-body">
            <!-- BEGIN: is_image -->
            <div class="image">
                <a href="#" id="pop" title="{ROW.title}"> <img id="imageresource" alt="{ROW.title}" src="{FILEIMAGE.src}" width="{FILEIMAGE.width}" height="{FILEIMAGE.height}" class="img-thumbnail" > </a>
                <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                    &times;
                                </button>
                                <h4 class="modal-title" id="myModalLabel">{ROW.title}</h4>
                            </div>
                            <div class="modal-body">
                                <img src="" id="imagepreview" style="width: 400px; height: 264px;" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END: is_image -->
            <div class="introtext m-bottom">
                {ROW.description}
            </div>
        </div>
        <script>
        $(document).ready(function() {
            $('.introtext').shorten({
                showChars: 300,
                moreText: '<p class="text-center"><button class="btn btn-primary btn-xs">{LANG.expand}</button></p>',
                lessText: '<p class="text-center"><button class="btn btn-primary btn-xs">{LANG.collapse}</button></p>',
            });
        });
        </script>
    </div>
    <!-- END: introtext -->
    <!-- BEGIN: filepdf -->
    <iframe frameborder="0" height="600" scrolling="yes" src="{FILEPDF}" width="100%"></iframe>
    <!-- END: filepdf -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <em class="fa fa-tasks">&nbsp;</em> {LANG.listing_details}
        </div>
        <dl class="dl-horizontal">
            <dt>
                {LANG.file_title}:
            </dt>
            <dd>
                {ROW.title}
            </dd>

            <dt>
                {LANG.file_version}:
            </dt>
            <dd>
                {ROW.version}
            </dd>

            <dt>
                {LANG.author_name}:
            </dt>
            <dd>
                {ROW.author_name}
            </dd>

            <dt>
                {LANG.author_url}:
            </dt>
            <dd>
                {ROW.author_url}
            </dd>

            <dt>
                {LANG.bycat2}:
            </dt>
            <dd>
                {ROW.catname}
            </dd>

            <dt>
                {LANG.uploadtime}:
            </dt>
            <dd>
                {ROW.uploadtime}
            </dd>

            <dt>
                {LANG.updatetime}:
            </dt>
            <dd>
                {ROW.updatetime}
            </dd>

            <dt>
                {LANG.user_name}:
            </dt>
            <dd>
                {ROW.user_name}
            </dd>

            <dt>
                {LANG.copyright}:
            </dt>
            <dd>
                {ROW.copyright}
            </dd>

            <dt>
                {LANG.filesize}:
            </dt>
            <dd>
                {ROW.filesize}
            </dd>

            <dt>
                {LANG.view_hits}:
            </dt>
            <dd>
                {ROW.view_hits}
            </dd>

            <dt>
                {LANG.download_hits}:
            </dt>
            <dd id="download_hits">
                {ROW.download_hits}
            </dd>

            <!-- BEGIN: comment_hits -->
            <dt>
                {LANG.comment_hits}:
            </dt>
            <dd>
                {ROW.comment_hits}
            </dd>
            <!-- END: comment_hits -->
        </dl>
    </div>

    <!-- BEGIN: report -->
    <div class="info_download">
        <div class="report pull-right">
            <a href="javascript:void(0);" data-thanks="{LANG.report_thanks}" onclick="nv_link_report( $(this), {ROW.id} );">{LANG.report}</a>
        </div>
        <em class="fa fa-download">&nbsp;</em> {LANG.download_detail}
    </div>
    <!-- END: report -->
    <!-- BEGIN: download_allow -->
    <!-- BEGIN: fileupload -->
    <div class="panel panel-default download">
        <div class="hidden">
            <iframe name="idown">
                &nbsp;
            </iframe>
        </div>

        <div class="panel-heading">
            {LANG.download_fileupload} {SITE_NAME}:
        </div>

        <div class="panel-body">
            <!-- BEGIN: row -->
            <em class="fa fa-link">&nbsp;</em>&nbsp;<a id="myfile{FILEUPLOAD.key}" href="{FILEUPLOAD.link}" onclick="nv_download_file('idown','{FILEUPLOAD.title}');return false;">{FILEUPLOAD.title}</a>
            <!-- END: row -->
        </div>
    </div>
    <!-- END: fileupload -->

    <!-- BEGIN: linkdirect -->
    <div class="panel panel-default download">
        <div class="panel-heading">
            {LANG.download_linkdirect} {HOST}:
        </div>

        <div class="panel-body">
            <!-- BEGIN: row -->
            <span class="fa fa-link">&nbsp;</span>&nbsp;<a href="{LINKDIRECT.link}" onclick="nv_linkdirect('{LINKDIRECT.code}');return false;">{LINKDIRECT.name}</a>
            <!-- END: row -->
        </div>
    </div>
    <!-- END: linkdirect -->
    <!-- END: download_allow -->

    <!-- BEGIN: download_info -->
    <div class="download">
        <div class="alert alert-danger">
            {ROW.download_info}
        </div>
    </div>
    <!-- END: download_info -->

    <div class="panel panel-default">
    <div class="panel-heading">
        <span class="fa fa-info">&nbsp;</span>&nbsp;&nbsp;{LANG.file_rating}
    </div>
    <div class="panel-body">
            <div id="stringrating">
                {LANG.rating_question}
            </div>
            <div style="padding: 5px;">
                <input class="hover-star" type="radio" value="1" title="{LANG.file_rating1}" style="vertical-align: middle" />
                <input class="hover-star" type="radio" value="2" title="{LANG.file_rating2}" style="vertical-align: middle" />
                <input class="hover-star" type="radio" value="3" title="{LANG.file_rating3}" style="vertical-align: middle" />
                <input class="hover-star" type="radio" value="4" title="{LANG.file_rating4}" style="vertical-align: middle" />
                <input class="hover-star" type="radio" value="5" title="{LANG.file_rating5}" style="vertical-align: middle" />
                <span id="hover-test" style="margin-left:20px">{LANG.file_rating_note}</span>
            </div>
        </div>
    </div>
    <!-- BEGIN: keywords -->
    <div class="news_column panel panel-default">
        <div class="panel-body">
            <div class="h5">
                <em class="fa fa-tags">&nbsp;</em><strong>{LANG.keywords}: </strong><!-- BEGIN: loop --><a title="{KEYWORD}" href="{LINK_KEYWORDS}"><em>{KEYWORD}</em></a>{SLASH}<!-- END: loop -->
            </div>
        </div>
    </div>
    <!-- END: keywords -->
    <script type="text/javascript">
        var sr = 0;
        var file_your_rating = '{LANG.file_your_rating}';
        var rating_point = '{LANG.rating_point}';
        var id = '{ROW.id}';

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
    </script>

    <!-- BEGIN: disablerating -->
    <script type="text/javascript">
        $(".hover-star").rating('disable');
        $('#hover-test').html('{ROW.rating_string}');
        $('#stringrating').html('{LANG.file_rating_note2}');
        sr = 2;
    </script>
    <!-- END: disablerating -->

    <!-- BEGIN: is_admin -->
    <div class="well well-sm">
        <div class="text-right pull-right">
            <a href="{ROW.edit_link}">{GLANG.edit}</a> &divide; <a href="{ROW.del_link}" onclick="nv_del_row(this,{ROW.id});return false;">{GLANG.delete}</a>
        </div>
        {LANG.file_admin}:
    </div>
    <!-- END: is_admin -->

    <!-- BEGIN: comment -->
    <div class="panel panel-default">
        <div class="panel-body">
            {CONTENT_COMMENT}
        </div>
    </div>
    <!-- END: comment -->
</div>
<!-- END: main -->