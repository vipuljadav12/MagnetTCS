<?php $__env->startSection('title'); ?>Edit Communication | <?php echo e(config('APP_NAME',env("APP_NAME"))); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit Waitlist Final Confirmation Email</div>
    </div>
</div>
    <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
   
        <div class="tab-content bordered" id="myTab1Content">
            <div class="tab-pane fade show active" id="communication" role="tabpanel" aria-labelledby="communication-tab">
                <ul class="nav nav-tabs custom-nav-tabs" id="myTab2">
                                <li class="nav-item"><a class="nav-link active" id="accept-tab" data-toggle="tab" href="#accept-email">Offered and Accepted</a></li>
                                <li class="nav-item"><a class="nav-link" id="declined-tab" data-toggle="tab" href="#declined-email">Offered and Declined</a></li>
                                <li class="nav-item"><a class="nav-link" id="waitlisted-tab" data-toggle="tab" href="#waitlisted-email">Offered and Waitlist for Other</a></li>
                            </ul>
                            <div class="tab-content tab-validate  bordered" id="myTab2Content">
                                <div class="tab-pane fade show active" id="accept-email">
                                     <form class="" action="<?php echo e(url('admin/DistrictConfiguration/saveWaitlistEditEmail')); ?>" method="POST" onsubmit="return ValidateForm('waitlist_offer_accepted')">
                                        <?php echo e(csrf_field()); ?>

                                        <div class="form-group col-12">
	                                        <label style="width: 100%">Mail Subject : <a href="<?php echo e(url('/admin/DistrictConfiguration/preview/thanks/email/waitlist_offer_accepted')); ?>" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a></label>
	                                        <div class="">
	                                            <input type="text" class="form-control" name="waitlist_offer_accepted_mail_subject" id="waitlist_offer_accepted_mail_subject" value="<?php echo e($offer_accepted_mail_subject->value ?? ''); ?>">
	                                        </div>
	                                    </div>
                                        <div class="form-group col-12">
                                            <label class="control-label">Mail Body : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor01" name="waitlist_offer_accepted_mail_body">
                                                        <?php echo $offer_accepted_mail_body->value ?? ''; ?>

                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>
                                
                                <div class="tab-pane fade" id="declined-email">
                                     <form class="" action="<?php echo e(url('admin/DistrictConfiguration/saveWaitlistEditEmail')); ?>" method="POST" onsubmit="return ValidateForm('waitlist_offer_declined')">
                                        <?php echo e(csrf_field()); ?>

                                        <div class="form-group col-12">
	                                        <label style="width: 100%">Mail Subject : <a href="<?php echo e(url('/admin/DistrictConfiguration/preview/thanks/email/waitlist_offer_declined')); ?>" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a></label>
	                                        <div class="">
	                                            <input type="text" class="form-control" name="waitlist_offer_declined_mail_subject" id="waitlist_offer_declined_mail_subject" value="<?php echo e($offer_declined_mail_subject->value ?? ''); ?>">
	                                        </div>
	                                    </div>
                                        <div class="form-group col-12">
                                            <label class="control-label">Mail Body : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor02" name="waitlist_offer_declined_mail_body">
                                                        <?php echo $offer_declined_mail_body->value ?? ''; ?>

                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="waitlisted-email">
                                      <form class="" action="<?php echo e(url('admin/DistrictConfiguration/saveWaitlistEditEmail')); ?>" method="POST" onsubmit="return ValidateForm('waitlist_offer_waitlisted')">
                                        <?php echo e(csrf_field()); ?>

                                        <div class="form-group col-12">
	                                        <label style="width: 100%">Mail Subject : <a href="<?php echo e(url('/admin/DistrictConfiguration/preview/thanks/email/waitlist_offer_waitlisted')); ?>" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a></label>
	                                        <div class="">
	                                            <input type="text" class="form-control" name="waitlist_offer_waitlisted_mail_subject" id="waitlist_offer_waitlisted_mail_subject" value="<?php echo e($offer_waitlisted_mail_subject->value ?? ''); ?>">
	                                        </div>
	                                    </div>
                                        <div class="form-group col-12">
                                            <label class="control-label">Mail Body : </label>
                                            <div class="row">
                                                <div class="col-md-12">
                                                     <textarea class="form-control" id="editor03" name="waitlist_offer_waitlisted_mail_body">
                                                        <?php echo $offer_waitlisted_mail_body->value ?? ''; ?>

                                                    </textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 text-right"> <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button></div>
                                    </form>
                                </div>

                                

            </div>
        </div>
     </div>
     
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" src="<?php echo e(url('/')); ?>/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo e(url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(url('/')); ?>/resources/assets/admin/js/additional-methods.min.js"></script>
<script type="text/javascript">
	function ValidateForm(str)
	{
		if($("#"+str+"_mail_subject").val() == "")
		{
			alert("Please enter email subject");
			return false;
		}
		if($("#"+str+"_mail_body").val() == "")
		{
			alert("Please enter email body");
			return false;
		}

	}
    CKEDITOR.replace('editor01', {
            filebrowserImageBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path=<?php echo e(url("/")); ?>',
            filebrowserBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'<?php echo e(url('/admin/shortCode/list')); ?>',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });


        CKEDITOR.replace('editor02', {
            filebrowserImageBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path=<?php echo e(url("/")); ?>',
            filebrowserBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'<?php echo e(url('/admin/shortCode/list')); ?>',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });

        CKEDITOR.replace('editor03', {
            filebrowserImageBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path=<?php echo e(url("/")); ?>',
            filebrowserBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'<?php echo e(url('/admin/shortCode/list')); ?>',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });
/*
        CKEDITOR.replace('editor04', {
            filebrowserImageBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path=<?php echo e(url("/")); ?>',
            filebrowserBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'<?php echo e(url('/admin/shortCode/list')); ?>',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });*/

        CKEDITOR.replace('editor05', {
            filebrowserImageBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageBrowser.php?path=<?php echo e(url("/")); ?>',
            filebrowserBrowseUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?type=Files',
            filebrowserUploadUrl: '<?php echo e(url("/")); ?>/resources/assets/admin/plugins/laravel-ckeditor/imageupload.php?command=QuickUpload&type=Files',
            filebrowserWindowWidth: (screen.width/1.5),
            filebrowserWindowHeight: (screen.height/1.5),
            on: {
                pluginsLoaded: function() {
                    var editor = this,
                        config = editor.config;
                    
                    editor.ui.addRichCombo( 'my-combo', {
                        label: 'Insert Short Code',
                        title: 'Insert Short Code',
                        toolbar: 'basicstyles',
                
                        panel: {               
                            css: [ CKEDITOR.skin.getPath( 'editor' ) ].concat( config.contentsCss ),
                            multiSelect: false,
                            attributes: { 'aria-label': 'Insert Short Code' }
                        },
            
                        init: function() {   
                            var chk = []; 
                            $.ajax({
                                url:'<?php echo e(url('/admin/shortCode/list')); ?>',
                                type:"get",
                                async: false,
                                success:function(response){
                                    chk = response;
                                }
                            }) 
                            for(var i=0;i<chk.length;i++){
                                this.add( chk[i], chk[i] );
                            }
                        },
            
                        onClick: function( value ) {
                            editor.focus();
                            editor.fire( 'saveSnapshot' );
                           
                            editor.insertHtml( value );
                        
                            editor.fire( 'saveSnapshot' );
                        }
                    } );        
                }        
            }
        });


</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>