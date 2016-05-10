<!-- BEGIN: main -->
<form action="{BASE_URL_SITE}index.php" method="get">
    <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" />
    <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
    <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP_NAME}" />

    <div class="form-group">
        <input type="text" name="q" value="{keyvalue}" class="form-control" placeholder="{LANG.search_key}..." />
    </div>
    <div class="form-group">
        <select class="form-control" name="cat">
            <option value="0">---{LANG.search_option}---</option>
            <!-- BEGIN: loop -->
            <option value="{loop.id}" {loop.select}>{loop.title}</option>
            {subcat}
            <!-- END: loop -->
        </select>
    </div>
    <p>
        <input type="submit" value="{LANG.search}" class="btn btn-primary center-block" />
    </p>
</form>
<!-- END: main -->