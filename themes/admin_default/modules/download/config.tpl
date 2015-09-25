<!-- BEGIN: main -->
<div id="users">
	<form action="{FORM_ACTION}" method="post">
		<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
				<colgroup>
					<col style="width: 260px" />
					<col/>
				</colgroup>
				<tfoot>
					<tr>
						<td class="text-center" colspan="2"><input type="submit" name="submit" value="{LANG.config_confirm}" class="btn btn-primary" /></td>
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<td>{LANG.config_indexfile}</td>
						<td>
							<select class="form-control" name="indexfile">
								<!-- BEGIN: indexfile -->
								<option value="{INDEXFILE.key}" {INDEXFILE.selected}>{INDEXFILE.value}</option>
								<!-- END: indexfile -->
							</select>
						</td>
					</tr>
					<tr>
						<td>{LANG.config_viewlist_type}</td>
						<td>
							<!-- BEGIN: viewlist_type -->
							<label><input type="radio" name="viewlist_type" value="{VIEWLIST.key}" {VIEWLIST.checked} />{VIEWLIST.value}</label>&nbsp;&nbsp;&nbsp;
							<!-- END: viewlist_type -->
						</td>
					</tr>
					<tr>
						<td>{LANG.config_per_page_home}</td>
						<td><input type="number" name="per_page_home" value="{DATA.per_page_home}" class="form-control" /></td>
					</tr>
					<tr>
						<td>{LANG.config_per_page_child}</td>
						<td><input type="number" name="per_page_child" value="{DATA.per_page_child}" class="form-control" /></td>
					</tr>
					<tr>
						<td>{LANG.config_is_addfile}</td>
						<td>
							<label><input name="is_addfile" value="1" id="is_addfile" type="checkbox"{DATA.is_addfile} />{LANG.config_is_addfile_note}</label>
						</td>
					</tr>
					<tr class="config_is_addfile is_addfile" {IS_ADDFILE}>
						<td>{LANG.config_whoaddfile}</td>
						<td>
							<!-- BEGIN: groups_addfile -->
							<label class="show"><input name="groups_addfile[]" value="{GROUPS_ADDFILE.key}" type="checkbox"{GROUPS_ADDFILE.checked} /> {GROUPS_ADDFILE.title}</label>
							<!-- END: groups_addfile -->
						</td>
					</tr>
					<tr class="is_addfile" {IS_ADDFILE}>
						<td>{LANG.config_whouploadfile}</td>
						<td>
							<!-- BEGIN: groups_upload -->
							<label class="show"><input name="groups_upload[]" value="{GROUPS_UPLOAD.key}" type="checkbox"{GROUPS_UPLOAD.checked} /> {GROUPS_UPLOAD.title}</label>
							<!-- END: groups_upload -->
						</td>
					</tr>
					<!-- BEGIN: allow_files_type -->
					<tr class="is_addfile" {IS_ADDFILE}>
						<td class="top">{LANG.config_allowfiletype}</td>
						<td>
						<!-- BEGIN: loop -->
						<label><input name="upload_filetype[]" type="checkbox" value="{TP}" {CHECKED} /> {TP}</label>&nbsp;&nbsp;&nbsp;
						<!-- END: loop -->
						</td>
					</tr>
					<!-- END: allow_files_type -->
					<tr class="is_addfile" {IS_ADDFILE}>
						<td>{LANG.config_maxfilesize}</td>
						<td><input name="maxfilesize" value="{DATA.maxfilesize}" type="text" maxlength="10" class="pull-left form-control w200"/><span class="text-middle"> {LANG.config_maxfilemb}. {LANG.config_maxfilesizesys} {NV_UPLOAD_MAX_FILESIZE} </span></td>
					</tr>
					<tr>
						<td>{LANG.is_resume}</td>
						<td><input name="is_resume" value="1" type="checkbox"{DATA.is_resume} /></td>
					</tr>
					<tr>
						<td>{LANG.max_speed}</td>
						<td><input name="max_speed" value="{DATA.max_speed}" type="text" class="form-control w100 pull-left" maxlength="4" />&nbsp;<span class="text-middle"> {LANG.kb_sec} </span></td>
					</tr>
					<tr>
						<td>{LANG.is_zip}</td>
						<td><input name="is_zip" value="1" type="checkbox"{DATA.is_zip} /></td>
					</tr>
					<tr>
						<td class="top">{LANG.zip_readme}</td>
						<td><textarea name="readme" cols="20" rows="5" class="form-control">{DATA.readme}</textarea></td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div>
<script>
	$('#is_addfile').click(function(){
		$('.is_addfile').toggle();
	});
</script>
<!-- END: main -->