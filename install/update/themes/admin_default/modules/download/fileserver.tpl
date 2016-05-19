<!-- BEGIN: main -->
<div class="table-responsive">
	<table class="table table-striped table-bordered table-hover">
		<thead>
			<tr>
				<th>{LANG.fileserver_server_name}</th>
				<th>{LANG.fileserver_upload_url}</th>
				<th>{LANG.fileserver_access_key}</th>
                <th class="w150 text-center">{LANG.status}</th>
				<th class="text-center w200">{LANG.function}</th>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN: loop -->
			<tr>
				<td>{ROW.server_name}</td>
				<td>{ROW.upload_url}</td>
				<td>{ROW.access_key}</td>
				<td class="text-center">
					<input name="status" id="change_status{ROW.server_id}" value="1" type="checkbox"{ROW.status} onclick="nv_change_fileserver_status('{ROW.server_id}');" />
				</td>
				<td class="text-center">
					<em class="fa fa-edit fa-lg">&nbsp;</em> <a href="{ROW.url_edit}">{GLANG.edit}</a> &nbsp;
					<em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="javascript:void(0);" onclick="nv_del_fileserver('{ROW.server_id}');">{GLANG.delete}</a>
				</td>
			</tr>
			<!-- END: loop -->
		</tbody>
	</table>
</div>
<a id="addedit" name="addedit"></a>
<!-- BEGIN: error -->
<div class="alert alert-danger">{ERROR}</div>
<!-- END: error -->
<form method="post" action="{FORM_ACTION}">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<caption>{CAPTION}</caption>
			<col class="w200"/>
			<tbody>
				<tr>
					<td class="text-right text-strong">{LANG.fileserver_server_name}<span class="fa-required text-danger">(<em class="fa fa-asterisk"></em>)</span></td>
					<td>
						<input type="text" name="server_name" value="{DATA.server_name}" class="form-control w500">
					</td>
				</tr>
				<tr>
					<td class="text-right text-strong">{LANG.fileserver_upload_url}<span class="fa-required text-danger">(<em class="fa fa-asterisk"></em>)</span></td>
					<td>
						<input type="text" name="upload_url" value="{DATA.upload_url}" class="form-control w500">
					</td>
				</tr>
				<tr>
					<td class="text-right text-strong">{LANG.fileserver_access_key}</td>
					<td>
						<input type="text" name="access_key" value="{DATA.access_key}" class="form-control w500">
					</td>
				</tr>
				<tr>
					<td class="text-right text-strong">{LANG.fileserver_secret_key}</td>
					<td>
						<input type="text" name="secret_key" value="{DATA.secret_key}" class="form-control w500">
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<input type="hidden" name="server_id" value="{DATA.server_id}">
						<input type="submit" name="submit" value="{GLANG.submit}" class="btn btn-primary">
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</form>
<!-- END: main -->