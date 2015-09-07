<!-- BEGIN: main -->
<!-- BEGIN: cat_data -->
<strong class="show m-bottom">{CAT.title}</strong>
<!-- END: cat_data -->
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