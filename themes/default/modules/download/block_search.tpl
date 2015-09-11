<!-- BEGIN: main -->
<form action="{FORMACTION}" method="post">
	<div class="form-group">
		<input type="text" name="q" value="{keyvalue}" class="form-control" placeholder="{LANG.search_key}..." />
	</div>
	<div class="form-group">
		<select class="form-control" name="cat">
			<option value="">---{LANG.search_option}---</option>
			<!-- BEGIN: loop -->
			<option value="{loop.id}" {loop.select}>{loop.title}</option>
			{subcat}
			<!-- END: loop -->
		</select>
	</div>
	<p>
		<input type="submit" value="{LANG.search}" class="btn btn-primary center-block" name="submit"/>
	</p>
</form>
<!-- END: main -->