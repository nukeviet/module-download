<!-- BEGIN: main -->
<!-- BEGIN: loop -->
<div class="panel panel-default">
    <div class="clearfix panel-body">
        <!-- BEGIN: image -->
          <div class="col-xs-12 col-md-4">
            <a href="{ITEM.more_link}"><img src="{ITEM.fileimage}" alt="{ITEM.title}" class="img-thumbnail" /></a>
        </div>
        <!-- END: image -->
        <h4><a title="{ITEM.title}" href="{ITEM.more_link}">{ITEM.title}</a></h4>
        <p>
            {ITEM.introtext}
        </p>
    </div>
    <div class="panel-footer">
        <ul class="list-inline">
            <li><em class="fa fa-upload">&nbsp;</em> {LANG.uploadtime}: {ITEM.uploadtime}</li>
            <li><em class="fa fa-eye">&nbsp;</em> {LANG.view_hits} {ITEM.view_hits}</li>
            <li><em class="fa fa-download">&nbsp;</em> {LANG.download_hits} {ITEM.download_hits}</li>
            <li><em class="fa fa-comments">&nbsp;</em> {LANG.comment_hits}: {ITEM.comment_hits}</li>
        </ul>
    </div>
</div>
<!-- END: loop -->
<!-- BEGIN: generate_page -->
<div class="page-nav m-bottom text-center">
    {GENERATE_PAGE}
</div>
<!-- END: generate_page -->
<!-- END: main -->