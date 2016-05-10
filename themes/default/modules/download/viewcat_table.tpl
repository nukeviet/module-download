<!-- BEGIN: main -->

<div class="clearfix">
    <!-- BEGIN: cat_data -->
    <strong class="show m-bottom pull-left">{CAT.title} <span class="text-danger">({CAT.numfile} {LANG.file})</span></strong>
    <!-- END: cat_data -->
    <!-- BEGIN: is_addfile_allow -->
    <small><strong class="pull-right m-bottom"><a title="{LANG.upload_to}" href="{CAT.uploadurl}"><em class="fa fa-upload fa-lg">&nbsp;</em>{LANG.upload_to}</a></strong></small>
    <!-- END: is_addfile_allow -->
</div>

<div class="divTable m-bottom">
    <div class="divHeading">
        <div class="Cell">
            <strong>{LANG.file_title}</strong>
        </div>
        <div class="Cell">
            <strong>{LANG.uploadtime}</strong>
        </div>
        <div class="Cell">
            <strong>{LANG.filesize}</strong>
        </div>
        <div class="Cell text-center">
            <strong>{LANG.viewcat_download_hits}</strong>
        </div>
    </div>
    <!-- BEGIN: loop -->
    <div class="Row">
        <div class="Cell">
            <a href="{FILE.more_link}" title="{FILE.title}">{FILE.title0}</a>
        </div>
        <div class="Cell">
            {FILE.uploadtime}
        </div>
        <div class="Cell">
            {FILE.filesize}
        </div>
        <div class="Cell text-center">
            {FILE.download_hits}
        </div>
    </div>
    <!-- END: loop -->
</div>
<!-- BEGIN: page -->
<div class="text-center">
    {PAGE}
</div>
<!-- END: page -->
<script>$('.pagination').addClass('pagination-sm');</script>
<!-- END: main -->