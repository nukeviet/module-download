<!-- BEGIN: main -->
<div class="row">
    <div class="col-md-12 col-lg-14">
        <div class="panel panel-default">
            <div class="panel-heading">{LANG.fimport_dotitle}</div>
            <div class="panel-body">
                <!-- BEGIN: error -->
                <div class="alert alert-danger">{ERROR}</div>
                <!-- END: error -->
                <!-- BEGIN: success -->
                <div class="alert alert-success">
                    <p>{SUCCESS}:</p>
                    <ul>
                        <li>{LANG.fimport_stat_cat_add}: <strong class="text-danger">{STAT.cat_add}</strong></li>
                        <li>{LANG.fimport_stat_cat_ignore}: <strong class="text-danger">{STAT.cat_ignore}</strong></li>
                        <li>{LANG.fimport_stat_file_add}: <strong class="text-danger">{STAT.file_add}</strong></li>
                        <li>{LANG.fimport_stat_file_ignore}: <strong class="text-danger">{STAT.file_ignore}</strong></li>
                    </ul>
                </div>
                <!-- END: success -->
                <form method="post" action="{FORM_ACTION}" id="fimportform" data-busy="false">
                    <div class="form-group">
                        <label for="" class="control-label"><strong>{LANG.fimport_status}:</strong></label>
                        <select class="form-control" name="status">
                            <!-- BEGIN: status --><option value="{STATUS.key}"{STATUS.selected}>{STATUS.title}</option><!-- END: status -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><strong>{LANG.fimport_mode}:</strong></label>
                        <select class="form-control" name="mode">
                            <!-- BEGIN: mode --><option value="{MODE.key}"{MODE.selected}>{MODE.title}</option><!-- END: mode -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label"><strong>{LANG.fimport_catprocess}:</strong></label>
                        <select class="form-control" name="catprocess">
                            <!-- BEGIN: catprocess --><option value="{CATPROCESS.key}"{CATPROCESS.selected}>{CATPROCESS.title}</option><!-- END: catprocess -->
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit" name="submit" value="{LANG.fimport_submit}"><i class="fa fa-fw fa-spin fa-spinner hidden"></i>{LANG.fimport_submit}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-10">
        <div class="panel panel-default">
            <div class="panel-heading">{LANG.fimport_help_title}</div>
            <div class="panel-body">
                <h2>{LANG.fimport_help_1}</h2>
                <p>{LANG.fimport_help_2}:</p>
                <ul>
                    <li>{LANG.fimport_help_3}</li>
                    <li>{LANG.fimport_help_4}</li>
                    <li>{LANG.fimport_help_5}</li>
                    <li>{LANG.fimport_help_6}</li>
                </ul>
                <p>{LANG.fimport_help_7} uploads/download/import {LANG.fimport_help_8}</p>
                <h2>{LANG.fimport_help_9}</h2>
                <p>{LANG.fimport_help_10}</p>
                <h2>{LANG.fimport_help_11}</h2>
                <p>{LANG.fimport_help_12}</p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $('#fimportform').submit(function(e) {
        if ($(this).data('busy')) {
            e.preventDefault();
        }
        $(this).data('busy', true);
        $('[name="submit"]', $(this)).find('.fa').removeClass('hidden');
    });
});
</script>
<!-- END: main -->