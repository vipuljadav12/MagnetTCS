<?php if(isset($data['student'])): ?>
    <?php
        $grades = [ 'PreK', 'K', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12' ];
    ?>
    <form method="post" id="frm_student_search" action="<?php echo e(url($module_url)); ?>/update">
        <?php echo e(csrf_field()); ?>

        <input type="hidden" name="id" value="<?php echo e($data['student']->stateID); ?>">
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">First Name : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="first_name" value="<?php echo e($data['student']->first_name); ?>"></div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Last Name : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="last_name" value="<?php echo e($data['student']->last_name); ?>"></div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Current Grade : </label>
                <div class="">
                    <select class="form-control" name="current_grade">
                        <option value="">Select Grade</option>
                        <?php $__currentLoopData = $grades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option <?php if($data['student']->current_grade == $grade): ?> selected <?php endif; ?>><?php echo e($grade); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    
                </div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Birth Day : </label>
                <div class=""><input type="text" class="form-control" id="birthday" maxlength="20" name="birthday" value="<?php echo e(getDateFormat($data['student']->birthday)); ?>"></div>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Address : </label>
                <div class="">
                    <textarea class="form-control" maxlength="255" name="address"><?php echo e($data['student']->address); ?></textarea>
                    
                </div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">City : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="city" value="<?php echo e($data['student']->city); ?>"></div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label class="control-label">Zip : </label>
                <div class=""><input type="text" class="form-control" maxlength="20" name="zip" value="<?php echo e($data['student']->zip); ?>"></div>
            </div>
            <div class="form-group col-6">
                <label class="control-label">Race : </label>
                <div class=""><input type="text" class="form-control" maxlength="100" name="race" value="<?php echo e($data['student']->race); ?>"></div>
            </div>
        </div>
    </form>
    <div class="" align="right">
        <button class="btn btn-success s_save">Save <div class="spnr spinner-border spinner-border-sm d-none"></button>
    </div>

    <div class="row">
        <?php $__currentLoopData = $termIds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $termid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-12 col-lg-12 mb-20">
                <div class="card shadow h-100 mb-0">
                    <div class="card-header"><?php echo e((1990+$termid) . "-".(1991+$termid)); ?></div>
                        <div class="card-body">
                            <?php if(isset($homeroomData[$termid])): ?>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-12 text-info" style="font-size:14px; font-weight: bold;">Home Room Teacher</label>
                                    <div class="col-12 col-md-12">
                                        <strong>Email: </strong><?php echo e($homeroomData[$termid]->email_addr); ?>              
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <strong>Name: </strong><?php echo e($homeroomData[$termid]->first_name); ?> <?php echo e($homeroomData[$termid]->last_name); ?>              
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($engTeacherData[$termid])): ?>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-12 text-info" style="font-size:14px; font-weight: bold;">English Teacher</label>
                                    <div class="col-12 col-md-12">
                                        <strong>Email: </strong><?php echo e($engTeacherData[$termid]->email_addr); ?>              
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <strong>Name: </strong><?php echo e($engTeacherData[$termid]->first_name); ?> <?php echo e($engTeacherData[$termid]->last_name); ?>              
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if(isset($mathTeacherData[$termid])): ?>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-md-12 text-info" style="font-size:14px; font-weight: bold;">Math Teacher Teacher</label>
                                    <div class="col-12 col-md-12">
                                        <strong>Email: </strong><?php echo e($mathTeacherData[$termid]->email_addr); ?>              
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <strong>Name: </strong><?php echo e($mathTeacherData[$termid]->first_name); ?> <?php echo e($mathTeacherData[$termid]->last_name); ?>              
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

<?php else: ?>
    <div class="" align="center">Data not found..</div>
<?php endif; ?>