<!-- BEGIN: main -->
<form action="{FORM_ACTION}" method="get">
    <!-- BEGIN: no_rewrite -->
    <input type="hidden" name="{NV_LANG_VARIABLE}" value="{NV_LANG_DATA}" />
    <input type="hidden" name="{NV_NAME_VARIABLE}" value="{MODULE_NAME}" />
    <input type="hidden" name="{NV_OP_VARIABLE}" value="{OP_NAME}" />
    <!-- END: no_rewrite -->

    <div class="form-group">
        <input type="text" name="q" value="{keyvalue}" class="form-control" placeholder="{LANG.search_key}..." />
    </div>
    <div class="form-group">
        <select class="form-control" name="cat">
            <option value="0">---{LANG.search_option}---</option>
            <!-- BEGIN: loop -->
            <option value="{loop.id}" {loop.select}>{loop.space}{loop.title}</option>
            <!-- END: loop -->
        </select>
    </div>
    <p>
        <input type="submit" value="{LANG.search}" class="btn btn-primary center-block" />
    </p>
</form>
<!-- END: main -->