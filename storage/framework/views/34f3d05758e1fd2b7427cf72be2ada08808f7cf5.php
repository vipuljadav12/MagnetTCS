<div class="card-body">

    <div class=" mb-10">
        <div id="submission_filters" class="pull-left col-md-6 pl-0" style="float: left !important;"></div> 
        
    </div>
    
    <?php if(!empty($data)): ?>

    <div class="table-responsive">
        <table class="table table-striped mb-0 w-100" id="tbl_test_score">
            <thead>
                <tr>
                    <th class="align-middle">Submission ID</th>
                    <th class="align-middle">State ID</th>
                    <th class="align-middle notexport">Student Type</th>
                    <th class="align-middle">Last Name</th>
                    <th class="align-middle">First Name</th>
                    <th class="align-middle">Next Grade</th>
                    <th class="align-middle">Current School</th>
                    <th class="align-middle notexport">Action</th>
                    <?php $__currentLoopData = $ts_name_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="align-middle"><?php echo e($ts_name); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    $choices = ['first', 'second'];
                ?>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="row<?php echo e($value['submission_id']); ?>">
                        <td class="text-center"><a href="<?php echo e(url('/admin/Submissions/edit/'.$value['id'])); ?>"><?php echo e($value['id']); ?></a></td>
                        <td class=""><?php echo e($value['student_id']); ?></td>
                        <td class="notexport"><?php echo e(($value['student_id'] != "" ? "Current" : "Non-Current")); ?></td>
                        <td class=""><?php echo e($value['first_name']); ?></td>
                        <td class=""><?php echo e($value['last_name']); ?></td>
                        <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                        <td class=""><?php echo e($value['current_school']); ?></td>
                        <td class="text-center notexport">
                            <div>
                                <a href="javascript:void(0)" id="edit<?php echo e($value['submission_id']); ?>" onclick="editRow(<?php echo e($value['submission_id']); ?>)" title="Edit"><i class="far fa-edit"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" onclick="saveScore(<?php echo e($value['submission_id']); ?>, <?php echo e($value['test_scores']['program_id']); ?>)" id="save<?php echo e($value['submission_id']); ?>" title="Save"><i class="fa fa-save"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" id="cancel<?php echo e($value['submission_id']); ?>" onclick="hideEditRow(<?php echo e($value['submission_id']); ?>)" title="Cancel"><i class="fa fa-times"></i></a>
                            </div>
                        </td>

                        <?php $__currentLoopData = $ts_name_fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts_name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $ts_rank = $value['test_scores']['data'][$ts_name] ?? 'NA';
                                // if ($value['id'] == 3176) {
                                //     dd($ts_rank);
                                // }
                            ?>
                            <td class="text-center">
                                <span <?php if($ts_rank === 404): ?> class="scorelabel" <?php endif; ?>>
                                    <?php if($ts_rank === 404): ?>
                                        <?php echo '<i class="fas fa-exclamation-circle text-danger"></i>'; ?>

                                    <?php else: ?>
                                        <?php echo e($ts_rank); ?>

                                    <?php endif; ?>
                                </span> 
                                <?php if($ts_rank === ''): ?>
                                    <input type="text"  class="form-control numbersOnly d-none scoreinput" value="0" maxlength="3" min="0" max="100" id="<?php echo e($value['test_scores']['program_id'].','.$ts_name); ?>">
                                <?php endif; ?>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="table-responsive text-center"><p>No records found.</div>
    <?php endif; ?>
</div>