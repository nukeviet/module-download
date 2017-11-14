<!-- BEGIN: main -->

<!-- BEGIN: is_error -->
<div class="alert alert-danger">
    {ERROR}
</div>
<!-- END: is_error -->

<form id="uploadForm" name="uploadForm" action="{FORM_ACTION}" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" data-upload_filetype="{UPLOAD.upload_filetype}">
    <div class="panel panel-default">
        <div class="panel-heading">
            {LANG.upload}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="col-sm-6 col-md-6 control-label">{LANG.file_title} <sup class="text-danger">(*)</sup></label>
                <div class="col-sm-18 col-md-18">
                    <input type="text" class="form-control required" name="upload_title" id="upload_title" value="{UPLOAD.title}" maxlength="250">
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-6 col-md-6 control-label">{LANG.bycat2} <sup class="text-danger">(*)</sup></label>
                <div class="col-sm-18 col-md-18">
                    <div class="list-group upload-catlist" data-toggle="uploadcat" data-catid="{UPLOAD.catid}" data-parentid="{UPLOAD.parentid}" data-tokend="{NV_CHECK_SESSION}"></div>
                </div>
            </div>

            <div class="form-group"{CSS_AUTHOR_NAME}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.author_name}{REQ_AUTHOR_NAME}</label>
                <div class="col-sm-18 col-md-18">
                    <input type="text" class="form-control{REQ_AUTHOR_NAME1}" name="upload_author_name" id="upload_author_name" value="{UPLOAD.author_name}" maxlength="100">
                </div>
            </div>

            <div class="form-group"{CSS_AUTHOR_EMAIL}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.author_email}{REQ_AUTHOR_EMAIL}</label>
                <div class="col-sm-18 col-md-18">
                    <input type="email" class="form-control{REQ_AUTHOR_EMAIL1}" name="upload_author_email" id="upload_author_email_iavim" value="{UPLOAD.author_email}" maxlength="60">
                </div>
            </div>

            <div class="form-group"{CSS_AUTHOR_URL}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.author_url}{REQ_AUTHOR_URL}</label>
                <div class="col-sm-18 col-md-18">
                    <input type="url" class="form-control{REQ_AUTHOR_URL1}" name="upload_author_url" id="upload_author_url_iavim" value="{UPLOAD.author_url}" maxlength="255">
                </div>
            </div>

            <!-- BEGIN: is_upload_allow -->
            <div class="form-group">
                <label class="col-sm-6 col-md-6 control-label">{LANG.upload_files}</label>
                <div class="col-sm-18 col-md-18">
                    <div class="input-group">
                        <input type="text" class="form-control" id="file_name" disabled>
                        <span class="input-group-btn">
                            <button class="btn btn-default" onclick="$('#upload_fileupload').click();" type="button">
                                <em class="fa fa-folder-open-o fa-fix">&nbsp;</em> {LANG.file_selectfile}
                            </button> </span>
                    </div>
                    <em class="help-block">{LANG.upload_valid_ext_info}: {EXT_ALLOWED}</em>
                    <input type="file" name="upload_fileupload" id="upload_fileupload" style="display: none" />
                </div>
            </div>
            <!-- END: is_upload_allow -->

            <div class="form-group"{CSS_LINKDIRECT}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.file_linkdirect}{REQ_LINKDIRECT}</label>
                <div class="col-sm-18 col-md-18">
                    <textarea name="upload_linkdirect" id="upload_linkdirect_iavim" class="form-control{REQ_LINKDIRECT1}" rows="3">{UPLOAD.linkdirect}</textarea>
                    <em class="help-block">{LANG.upload_linkdirect_info}</em>
                </div>
            </div>

            <div class="form-group"{CSS_FILESIZE}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.filesize} (byte){REQ_FILESIZE}</label>
                <div class="col-sm-18 col-md-18">
                    <input type="text" class="form-control{REQ_FILESIZE1}" name="upload_filesize" id="upload_filesize_iavim" value="{UPLOAD.filesize}" maxlength="15">
                </div>
            </div>

            <div class="form-group"{CSS_VERSION}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.file_version}{REQ_VERSION}</label>
                <div class="col-sm-18 col-md-18">
                    <input type="text" class="form-control{REQ_VERSION1}" name="upload_version" id="upload_version" value="{UPLOAD.version}" maxlength="20">
                </div>
            </div>

            <div class="form-group"{CSS_FILEIMAGE}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.fileimage}{REQ_FILEIMAGE}</label>
                <div class="col-lg-18 col-md-18">
                    <div class="input-group">
                        <input type="text" class="form-control{REQ_FILEIMAGE1}" id="photo_name" disabled>
                        <span class="input-group-btn">
                            <button class="btn btn-default" onclick="$('#upload_fileimage').click();" type="button">
                                <em class="fa fa-folder-open-o fa-fix">&nbsp;</em> {LANG.file_selectfile}
                            </button> </span>
                    </div>
                    <em class="help-block">{LANG.upload_valid_ext_info}: jpg, gif, png</em>
                    <input type="file" name="upload_fileimage" id="upload_fileimage" style="display: none" />
                </div>
            </div>

            <div class="form-group"{CSS_COPYRIGHT}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.copyright}{REQ_COPYRIGHT}</label>
                <div class="col-sm-18 col-md-18">
                    <input type="text" class="form-control{REQ_COPYRIGHT1}" name="upload_copyright" id="upload_copyright" value="{UPLOAD.copyright}" maxlength="255">
                </div>
            </div>

            <div class="form-group"{CSS_INTROTEXT}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.file_introtext}{REQ_INTROTEXT}</label>
                <div class="col-sm-18 col-md-18">
                    <textarea name="upload_introtext" id="upload_introtext" class="form-control{REQ_INTROTEXT1}" rows="3">{UPLOAD.introtext}</textarea>
                </div>
            </div>

            <div class="form-group"{CSS_DESCRIPTION}>
                <label class="col-sm-6 col-md-6 control-label">{LANG.file_description}{REQ_DESCRIPTION}</label>
                <div class="col-sm-18 col-md-18">
                    <textarea name="upload_description" id="upload_description" class="form-control{REQ_DESCRIPTION1}" rows="3">{UPLOAD.description}</textarea>
                </div>
            </div>

            <!-- BEGIN: show_username -->
            <div class="form-group">
                <label class="col-sm-6 col-md-6 control-label">{LANG.file_username} <sup class="text-danger">(*)</sup></label>
                <div class="col-sm-18 col-md-18">
                    <input type="text" class="form-control" name="upload_user_name" id="upload_user_name" value="{UPLOAD.user_name}" maxlength="100">
                </div>
            </div>
            <!-- END: show_username -->

            <!-- BEGIN: captcha -->
            <div class="form-group">
                <label class="col-sm-6 col-md-6 control-label">{N_CAPTCHA}</label>
                <div class="col-sm-10 col-md-10">
                    <input type="text" class="form-control" name="upload_seccode" id="upload_seccode_iavim" value="" maxlength="{CAPTCHA_MAXLENGTH}">
                </div>
                <div class="col-sm-8 col-md-8">
                    <img class="middle captchaImg" height="31" name="upload_vimg" src="{NV_BASE_SITEURL}index.php?scaptcha=captcha&t={NV_CURRENTTIME}" alt="{GLANG.captcha}" />
                    <img class="middle fa-pointer refresh" alt="{GLANG.captcharefresh}" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/images/refresh.png" width="16" height="16" onclick="change_captcha('#upload_seccode_iavim');" />
                </div>
            </div>
            <!-- END: captcha -->

            <!-- BEGIN: recaptcha -->
            <div class="form-group">
                <label class="col-sm-6 col-md-6 control-label">{N_CAPTCHA} <span class="txtrequired">(*)</span></label>
                <div class="col-sm-18 col-md-18">
                    <div class="nv-recaptcha-default"><div id="{RECAPTCHA_ELEMENT}"></div></div>
                    <script type="text/javascript">
                    nv_recaptcha_elements.push({
                        id: "{RECAPTCHA_ELEMENT}",
                        btn: $('[type="submit"]', $('#{RECAPTCHA_ELEMENT}').parent().parent().parent().parent())
                    })
                    </script>
                </div>
            </div>
            <!-- END: recaptcha -->

            <div class="form-group">
                <label class="col-sm-6 col-md-6 control-label"></label>
                <div class="col-sm-18 col-md-18">
                    <input type="hidden" name="addfile" value="{UPLOAD.addfile}" />
                    <input class="btn btn-primary" type="submit" name="submit" value="{LANG.upload}" />
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/jquery/jquery.validate.min.js"></script>
<script type="text/javascript" src="{NV_BASE_SITEURL}{NV_ASSETS_DIR}/js/language/jquery.validator-{NV_LANG_INTERFACE}.js"></script>
<script type="text/javascript">
var uploadForm = true;
</script>
<!-- END: main -->

<!-- BEGIN: cat -->
<li class="list-group-item upload-catlist-item list-group-item-info">
    <strong>{PARENT_TEXT}</strong>
</li>
<!-- BEGIN: loop -->
<li class="list-group-item upload-catlist-item">
    <input type="radio" name="upload_catid" id="upload_catid_{CAT.id}" value="{CAT.id}"{CAT.checked}/>&nbsp;<label for="upload_catid_{CAT.id}">{CAT.title}</label>
    <div class="pull-right">
        <!-- BEGIN: loadparentcat --><a href="#" data-toggle="upcatload" data-catid="{CATID}" data-parentid="{PARENTID}" data-tokend="{NV_CHECK_SESSION}" title="{LANG.load_parentcat}"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></a><!-- END: loadparentcat -->
        <!-- BEGIN: hassubcat --><a href="#" data-toggle="upcatload" data-catid="{CATID}" data-parentid="{CAT.id}" data-tokend="{NV_CHECK_SESSION}" title="{LANG.load_subcat}"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a><!-- END: hassubcat -->
    </div>
</li>
<!-- END: loop -->
<!-- END: cat -->
