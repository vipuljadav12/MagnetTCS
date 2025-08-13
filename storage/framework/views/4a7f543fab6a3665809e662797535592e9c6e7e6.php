<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<style type="text/css">
	table, td, th, tr {border: none !important}
</style>
	<script type="text/javascript" src="<?php echo e(url('/resources/assets/admin/js/jquery/jquery-3.4.1.min.js')); ?>"></script> 
	<div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Send Test Email</div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                            
                        <form id="loginform" class="form-horizontal" role="form">
                                    
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input type="text" class="form-control" name="email" id="email" value="" placeholder="email address">                                        
                                    </div>
                                
                          
                                    

                                
                          


                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">
                                      <a id="btn-login" href="javascript:void(0)" onclick="sendEmail();" class="btn btn-success">Send</a>
                                    </div>
                                </div>


                             
                            </form>     



                        </div>                     
                    </div>  
        </div>


<?php $__env->startSection('content'); ?>
    <?php echo $data['email_text']; ?>

<?php $__env->stopSection(); ?>

<script type="text/javascript">
        
	
	function sendEmail()
	{
        <?php if($type == "regular"): ?>
            var url = "<?php echo e(url('/admin/EditCommunication/Send/Test/Mail')); ?>";
        <?php elseif($type == "waitlist"): ?>
            var url = "<?php echo e(url('/admin/Waitlist/EditCommunication/Send/Test/Mail')); ?>";
        <?php elseif($type == "late_submission"): ?>
            var url = "<?php echo e(url('/admin/LateSubmission/EditCommunication/Send/Test/Mail')); ?>";
        <?php endif; ?>
		$.ajax({
            url: url,
            type:"post",
            data: {"_token": "<?php echo e(csrf_token()); ?>", "email": $("#email").val(), "status": "<?php echo e($status); ?>", "type": "<?php echo e($type); ?>", "application_id": "<?php echo e($application_id); ?>"},
            success:function(response){
                alert("Test email sent successfully.");
            }
        })
	}
</script>
<?php echo $__env->make('emails.maillayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>