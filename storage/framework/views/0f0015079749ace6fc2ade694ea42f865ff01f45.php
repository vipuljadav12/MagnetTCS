<?php $__env->startSection('title'); ?>StudentSearch <?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Student Data Override</div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        <div class="alert-success alrt_suc d-none"> Data updated successfully.</div>
        <div class="alert-danger alrt_err d-none"> Something went wrong, please try again.</div>
        <div class="">
            <div class="form-group">
                <label class="control-label">Student ID : </label>
                <div class=""><input type="text" class="form-control s_id"  value="<?php echo e(old('id')); ?>"></div>
                <?php if($errors->first('id')): ?>
                    <div class="mb-1 text-danger">
                        <?php echo e($errors->first('id')); ?>

                    </div>
                <?php endif; ?>
            </div>
            <button class="btn btn-secondary s_search">Search <div class="spnr spinner-border spinner-border-sm d-none"></div></button>          
        </div>
        <br>
        <div class="s_data"></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript">
        const data_container = $('.s_data');
        // Fetch data
        $('.s_search').on('click', function(){
            var id = $('.s_id');
            if (isSearchTxt()) {
                let search_btn = $(this);
                data_container.html('');
                spinner(search_btn);
                $.ajax({
                    type: "post",
                    async: false,
                    url: "<?php echo e(url($module_url.'/data')); ?>",
                    data: {   
                        "_token": "<?php echo e(csrf_token()); ?>",                 
                        "id": id.val()
                    },
                    success: function(res) {
                        $('.s_data').html(res);
                        formRequirments();
                    }
                });
                spinner(search_btn, false);
            }
        });
        // spinner
        function spinner(search_btn, state=true) {
            let spinner = search_btn.find('.spnr');
            if (state) {
                spinner.removeClass('d-none');
            } else {
                setTimeout(function() {
                    spinner.addClass('d-none');
                }, 100);
            }
        }
        // Search button event
        $('.s_id').on('change keyup', function() {
            data_container.html('');
            isSearchTxt();
        });
        // Hide/Show form data
        function isSearchTxt() {
            let id = $('.s_id');
            if (id.val() == '') {
                data_container.html('');
            } else {
                return true; 
            }
            return false;
        }
        // Form submit
        $(document).on('click', '.s_save', function() {
            let frm = $(document).find('#frm_student_search');
            if (frm.valid()) {
                let search_btn = $(this);
                // spinner(search_btn);
                $.post( "<?php echo e(url($module_url.'/data/update')); ?>", frm.serialize() ).done(function(data) {
                    manageAlert(data);
                }).fail(function() {
                    manageAlert('false');
                });
                // spinner(search_btn, false);
            }
        });
        // Alert 
        function manageAlert(status='') {
            if (status == 'true') {
                alert("Data updated successfully.");
                /*$('.alrt_err').addClass('d-none');
                $('.alrt_suc').removeClass('d-none');*/
            } else {
                alert("Something went wrong, please try again.");
                /*$('.alrt_suc').addClass('d-none');
                $('.alrt_err').removeClass('d-none');*/
            }
        }
        // Form validation
        function formRequirments() {
            $(document).find('#frm_student_search').validate({
                rules: {
                    first_name: {
                        required: true
                    },
                    last_name: {
                        required: true
                    },
                    current_grade: {
                        required: true
                    },
                    birthday: {
                        required: true,
                        date: true
                    },
                    address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    zip: {
                        required: true
                    },
                    race: {
                        required: true
                    }
                },
                messages: {
                    first_name: {
                        required: "First Name is required."
                    },
                    last_name: {
                        required: "Last Name is required."
                    },
                    current_grade: {
                        required: "Curent Grade is required."
                    },
                    birthday: {
                        required: "Birth Day is required."
                    },
                    address: {
                        required: "Address is required."
                    },
                    city: {
                        required: "City is required."
                    },
                    zip: {
                        required: "Zip is required."
                    },
                    race: {
                        required: "Race is required."
                    }
                }
            });
            $("#birthday").datepicker({
                autoclose: true,
                todayHighlight: true,
                endDate: new Date()
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>