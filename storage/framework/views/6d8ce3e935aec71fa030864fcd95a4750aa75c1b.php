<?php $__env->startSection('title'); ?>Edit Communication | <?php echo e(config('APP_NAME',env("APP_NAME"))); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit Communication</div>
    </div>
</div>
    <?php echo $__env->make("layouts.admin.common.alerts", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
   
        <ul class="nav nav-tabs" id="myTab1" role="tablist">
            <?php $__currentLoopData = $statusArr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($status == $key): ?>
                    <li class="nav-item">
                        <a class="nav-link active maintab" id="communication-tab" data-toggle="tab" href="#communication" role="tab" aria-controls="communication" aria-selected="true"><?php echo e($value); ?></a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link maintab" href="<?php echo e(url('/admin/Waitlist/EditCommunication/application/'.$application_id.'/'.$key)); ?>"><?php echo e($value); ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </ul>
        <div class="tab-content bordered" id="myTab1Content">
            <div class="tab-pane fade show active" id="communication" role="tabpanel" aria-labelledby="communication-tab">
                <ul class="nav nav-tabs custom-nav-tabs" id="myTab2">
                                <?php if($status != "ContractLetterText"): ?>
                                    <li class="nav-item"><a class="nav-link active" id="active-screen-tab" data-toggle="tab" href="#active-screen">Letters</a></li>
                                <?php endif; ?>
                                <li class="nav-item"><a class="nav-link <?php if($status == "ContractLetterText"): ?> active <?php endif; ?>" id="active-email-tab" data-toggle="tab" href="#active-email">Emails</a></li>
                                <?php if($status != "ContractLetterText"): ?>
                                    <li class="nav-item"><a class="nav-link" id="screen-log-tab" data-toggle="tab" href="#screen-log">Letters Log</a></li>
                                    <li class="nav-item"><a class="nav-link" id="email-log-tab" data-toggle="tab" href="#email-log">Emails Log</a></li>
                                <?php endif; ?>
                            </ul>
                            <div class="tab-content tab-validate  bordered" id="myTab2Content">
                                <?php if($status != "ContractLetterText"): ?>
                                <div class="tab-pane fade show active" id="active-screen">
                                     <form class="" action="<?php echo e(url('admin/Waitlist/EditCommunication/store/letter')); ?>" method="POST">
                                    <?php echo e(csrf_field()); ?>

                                    <input type="hidden" name="application_id" value="<?php echo e($application_id); ?>">
                                    <input type="hidden" name="redirect_status" value="<?php echo e($status); ?>">
                                    <input type="hidden" name="status" value="<?php echo e($dbStatusArr[$status]); ?>">
                                    <div class="form-group d-none">
                                        <label for="">Letter Subject</label>
                                        <div class="">
                                            <input type="text" class="form-control" name="letter_subject" value="<?php echo e($data->letter_subject ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label style="width: 100%">Letter Body : <?php if(isset($data->letter_body) && $data->letter_body != ''): ?><a href="<?php echo e(url('/admin/Waitlist/EditCommunication/preview/letter/'.$status.'/'.$application_id)); ?>" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a><?php endif; ?></label>
                                        
                                        <div class="editor-height-210">
                                            <textarea class="form-control" id="letter_body" name="letter_body">
                                            <?php echo e($data->letter_body ?? ''); ?>

                                            </textarea>
                                        </div>
                                    </div>
                                    <div class="form-group"> <input type="submit" class="btn btn-secondary" value="Save Changes"> <?php if($display_outcome > 0): ?> <?php if($status != "ContractLetterText"): ?><input type="submit" name="generate_letter_now" value="Generate Letters Now" class="btn btn-success"><?php endif; ?> <?php endif; ?></div>
                                    </form>
                                    
                                </div>
                                <?php endif; ?>
                                
                                <div class="tab-pane fade <?php if($status == "ContractLetterText"): ?> show active <?php endif; ?>" id="active-email">
                                    <form class="" action="<?php echo e(url('admin/Waitlist/EditCommunication/store/email')); ?>" method="POST">
                                    <?php echo e(csrf_field()); ?>

                                    <input type="hidden" name="application_id" value="<?php echo e($application_id); ?>">
                                    <input type="hidden" name="redirect_status" value="<?php echo e($status); ?>">
                                    <input type="hidden" name="status" value="<?php echo e($dbStatusArr[$status]); ?>">

                                    <div class="form-group">
                                        <label style="width: 100%">Mail Subject : <?php if(isset($data->mail_subject) && $data->mail_body != ''): ?><a href="<?php echo e(url('/admin/Waitlist/EditCommunication/preview/email/'.$status.'/'.$application_id)); ?>" target="_blank" class="btn btn-success" style="float: right !important;">Preview</a><?php endif; ?></label>
                                        <div class="">
                                            <input type="text" class="form-control" name="mail_subject" id="mail_subjet" value="<?php echo e($data->mail_subject ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Mail Body : </label>
                                        <div class="editor-height-210">
                                            <textarea class="form-control" id="mail_body" name="mail_body">
                                           <?php echo e($data->mail_body ?? ''); ?>

                                            </textarea>
                                        </div>
                                    </div>
                                    <div class="form-group d-none" id="emaillist">
                                        
                                    </div>
                                    <div class="form-group"> <input type="submit" class="btn btn-secondary" value="Save Changes"> <?php if($display_outcome > 0): ?> <?php if($status != "ContractLetterText"): ?><a href="javascript:void(0)" class="btn btn-success" onclick="fetchEmails()" id="generate_email_link">Confirm Emails</a>  <input type="submit" name="send_email_now" value="Send Emails" class="btn btn-success d-none" id="generate_email_submit"><?php endif; ?> <?php endif; ?></div>
                                    </form>
                                    
                                </div>
                                <?php if($status != "ContractLetterText"): ?>
                                    <div class="tab-pane fade" id="screen-log">
                                        <?php echo $__env->make("Waitlist::letter_log", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    </div>
                                    <div class="tab-pane fade" id="email-log">
                                        <?php echo $__env->make("Waitlist::email_log", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
            </div>
        </div>
     </form>
     
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" src="<?php echo e(url('/')); ?>/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo e(url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(url('/')); ?>/resources/assets/admin/js/additional-methods.min.js"></script>
<script type="text/javascript">

    if($("#letter_body").length > 0)
    {
    CKEDITOR.replace('letter_body',{
             toolbar : 'Basic',
            toolbarGroups: [
                    { name: 'document',    groups: [ 'mode', 'document' ] },            // Displays document group with its two subgroups.
                    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },           // Group's name will be used to create voice label.
                    { name: 'basicstyles', groups: [ 'cleanup', 'basicstyles'] },
                
                    '/',                                                                // Line break - next group will be placed in new line.
                    { name: 'links' }
                ],
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
}

         CKEDITOR.replace('mail_body',{
             toolbar : 'Basic',
            toolbarGroups: [
                    { name: 'document',    groups: [ 'mode', 'document' ] },            // Displays document group with its two subgroups.
                    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },           // Group's name will be used to create voice label.
                    { name: 'basicstyles', groups: [ 'cleanup', 'basicstyles'] },
                
                    '/',                                                                // Line break - next group will be placed in new line.
                    { name: 'links' }
                ],
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

        function fetchEmails()
        {
            if($("#mail_body").val() != "" && $("#mail_subjet").val() != "")
            {
                $("#emaillist").removeClass("d-none");
                $("#emaillist").html("<p class='text-center'>Loading Emails</p>");
                var status = "<?php echo e($dbStatusArr[$status]); ?>";

                 $.ajax({
                    url:'<?php echo e(url('/admin/Waitlist/EditCommunication/get/emails/')); ?>',
                    type:"post",
                    data: {'status':status, "_token": "<?php echo e(csrf_token()); ?>", "application_id": "<?php echo e($application_id); ?>"},
                    async: false,
                    success:function(response){
                        var data = JSON.parse(response);
                        var html = '<table id="datatable" class="table table-striped mb-0">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<th class="align-middle w-120 text-center">Submission ID</th>';
                        html += '<th class="align-middle w-120 text-center">Student Name</th>';
                        html += '<th class="align-middle w-120 text-center">Parent Name</th>';
                        html += '<th class="align-middle w-120 text-center">Parent Email</th>';
                        html += '<th class="align-middle w-120 text-center">Grade</th>';
                        html += '</tr>';
                        html += '</thead>';
                        for(i=0; i < data.length; i++)
                        {
                            html += '<tr>';
                            html += '<td class="align-middle w-120 text-center">'+data[i]['id']+'</td>';
                            html += '<td class="align-middle w-120 text-center">'+data[i]['student_name']+'</td>';
                            html += '<td class="align-middle w-120 text-center">'+data[i]['parent_name']+'</td>';
                            html += '<td class="align-middle w-120 text-center">'+data[i]['parent_email']+'</td>';
                            html += '<td class="align-middle w-120 text-center">'+data[i]['grade']+'</td>';
                            html += '</tr>';
                        }
                        html += '</table>';

                        $("#emaillist").html(html);
                        $("#datatable").DataTable();
                        $("#generate_email_submit").removeClass('d-none');
                        $("#generate_email_link").addClass('d-none');

                    }
                })

             }
             else
             {
                alert("Select all fields");
             }
        }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>