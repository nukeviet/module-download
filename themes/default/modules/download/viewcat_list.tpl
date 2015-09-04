<!-- BEGIN: main -->
<div class="viewcat_list">
	<div class="panel panel-default">
		<div class="row list-title">
			<div class="col-md-10"><strong>{LANG.file_title}</strong></div>
			<div class="col-md-6"><strong>{LANG.uploadtime}</strong></div>
			<div class="col-md-5"><strong>{LANG.filesize}</strong></div>
			<div class="col-md-3 text-center"><strong>{LANG.viewcat_download_hits}</strong></div>
		</div>
		<!-- BEGIN: loop -->
		<div class="row list-content">
			<div class="col-md-10"><a href="{FILE.more_link}" title="{FILE.title}">{FILE.title0}</a></div>
			<div class="col-md-6">{FILE.uploadtime}</div>
			<div class="col-md-5">{FILE.filesize}</div>
			<div class="col-md-3 text-center">{FILE.download_hits}</div>
		</div>
		<!-- END: loop -->
	</div>
	<!-- BEGIN: page -->
	<div class="text-center">{PAGE}</div>
	<!-- END: page -->
</div>
<script>
	$('.pagination').addClass('pagination-sm');
</script>
<!-- END: main -->