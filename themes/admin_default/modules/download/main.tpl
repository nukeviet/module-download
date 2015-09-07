<!-- BEGIN: main -->
<link rel="stylesheet" href="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.css">
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/select2/select2.min.js"></script>

<div class="well">
	<form action="{NV_BASE_ADMINURL}index.php" method="get">
		<input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
		<input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
		<input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
		<div class="row">
			<div class="col-xs-24 col-md-6">
				<div class="form-group">
					<input class="form-control" type="text" value="{SEARCH.q}" name="q" maxlength="255" placeholder="{LANG.search_keywods}" />
				</div>
			</div>
			<div class="col-xs-24 col-md-6">
				<div class="form-group">
					<select class="form-control" name="catid" id="catid">
						<option value="">---{LANG.category_cat_c}---</option>
						<!-- BEGIN: catid -->
						<option value="{LISTCATS.id}"{LISTCATS.selected}>{LISTCATS.name}</option>
						<!-- END: catid -->
					</select>
				</div>
			</div>
			<div class="col-xs-24 col-md-4">
				<div class="form-group">
					<select class="form-control" name="active">
						<option value="-1">---{LANG.category_cat_active}---</option>
						<!-- BEGIN: active -->
						<option value="{ACTIVE.key}"{ACTIVE.selected}>{ACTIVE.value}</option>
						<!-- END: active -->
					</select>
				</div>
			</div>
			<div class="col-xs-24 col-md-3">
				<div class="form-group">
					<select class="form-control" name="per_page">
						<option value="30">---{LANG.search_per_page}---</option>
						<!-- BEGIN: per_page -->
						<option value="{PER_PAGE.key}"{PER_PAGE.selected}>{PER_PAGE.key}</option>
						<!-- END: per_page -->
					</select>
				</div>
			</div>
			<div class="col-xs-12 col-md-3">
				<div class="form-group">
					<input class="btn btn-primary" type="submit" value="{LANG.search}" />
				</div>
			</div>
		</div>
	</form>
</div>

<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<colgroup>
			<col span="2" />
			<col class="w150" />
			<col span="4" class="w100" />
			<col class="w150" />
		</colgroup>
		<thead>
			<tr class="text-center">
				<th>{LANG.file_title}</th>
				<th>{LANG.category_cat_parent}</th>
				<th>{LANG.file_update}</th>
				<th class="text-center">{LANG.file_view_hits}</th>
				<th class="text-center">{LANG.file_download_hits}</th>
				<th class="text-center">{LANG.file_comment_hits}</th>
				<th class="text-center">{LANG.file_active}</th>
				<th class="text-center">{LANG.file_feature}</th>
			</tr>
		</thead>
		<!-- BEGIN: generate_page -->
		<tfoot>
			<tr>
				<td colspan="8" class="text-center">{GENERATE_PAGE}</td>
			</tr>
		</tfoot>
		<!-- END: generate_page -->
		<tbody>
			<!-- BEGIN: row -->
			<tr>
				<td><a target="_blank" href="{ROW.link}">{ROW.title}</a></td>
				<td><a href="{ROW.catlink}">{ROW.cattitle}</a></td>
				<td>{ROW.uploadtime}</td>
				<td class="text-center">{ROW.view_hits}</td>
				<td class="text-center">{ROW.download_hits}</td>
				<td class="text-center">{ROW.comment_hits}</td>
				<td class="text-center"><input name="status" id="change_status{ROW.id}" value="1" type="checkbox"{ROW.status} onclick="nv_chang_file_status({ROW.id})" /></td>
				<td class="text-center"><em class="fa fa-edit fa-lg">&nbsp;</em> <a href="{EDIT_URL}">{GLANG.edit}</a> &nbsp;&nbsp;<em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_file_del({ROW.id});">{GLANG.delete}</a></td>
			</tr>
			<!-- END: row -->
		</tbody>
	</table>
</div>
<script>
	$('#catid').select2();
</script>
<!-- END: main -->