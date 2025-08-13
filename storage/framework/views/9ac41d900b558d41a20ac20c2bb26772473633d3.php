<div class="card-body">
    <div class="row col-md-12 pull-left pb-10">
        <select class="form-control custom-select d-none" onchange="loadSubmissionData(this.value)"> 
            <option value="0" <?php if($late_submission == 0): ?> selected <?php endif; ?>>Submission</option>
            <option value="1" <?php if($late_submission == 1): ?> selected <?php endif; ?>>Late Submission</option>
        </select>
    </div>

    <div class=" mb-10">
        <div id="submission_filters" class="pull-left col-md-6 pl-0" style="float: left !important;"></div> 
        <div class="text-right">    
        <?php if($display_outcome == 0): ?>
         <a href="<?php echo e(url('admin/Reports/import/missing/'.$enrollment_id.'/grade')); ?>" title="Import Missing Grades" class="btn btn-secondary d-none">Import  Missing Grade</a>
         <?php endif; ?>                                        
            <a href="javascript:void(0)" onclick="exportMissing()" title="Export Missing Grade" class="btn btn-secondary">Export Missing Grade</a>
            </div>
    </div>
    
    <?php 
        $config_subjects = Config::get('variables.subjects');
        $subject_count = count($subjects) ?? 0;
        $colspan = 8;
    ?>
    
    <?php if(!empty($firstdata)): ?>

    <div class="table-responsive">
        <table class="table table-striped mb-0 w-100" id="datatable">
            <thead>
                <tr>
                    <th class="align-middle" rowspan="3">Submission ID</th>
                    <th class="align-middle" rowspan="3">State ID</th>
                    <th class="align-middle notexport" rowspan="3">Student Type</th>
                    <th class="align-middle" rowspan="3">Last Name</th>
                    <th class="align-middle" rowspan="3">First Name</th>
                    <th class="align-middle" rowspan="3">Next Grade</th>
                    <th class="align-middle" rowspan="3">Current School</th>
                    <th class="align-middle notexport" rowspan="3">Action</th>
                    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tyear => $tvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="align-middle text-center" colspan="<?php echo e($subject_count*count($tvalue)); ?>"><?php echo e($tyear); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                     <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tyear => $tvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $sub = $config_subjects[$value] ?? $value;
                            ?>
                            <th class="align-middle text-center" colspan="<?php echo e(count($tvalue)); ?>"><?php echo e($sub); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tyear => $tvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $tvalue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="align-middle text-center"><?php echo e($value1); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>

            </thead>
            <tbody>
                <?php $__currentLoopData = $firstdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="row<?php echo e($value['submission_id']); ?>">
                        <td class="text-center"><a href="<?php echo e(url('/admin/Submissions/edit/'.$value['id'])); ?>"><?php echo e($value['id']); ?></a></td>
                        <td class=""><?php echo e($value['student_id']); ?></td>
                        <td class="notexport"><?php echo e(($value['student_id'] != "" ? "Current" : "Non-Current")); ?></td>
                        <td class=""><?php echo e($value['first_name']); ?></td>
                        <td class=""><?php echo e($value['last_name']); ?></td>
                        <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                        <td class=""><?php echo e($value['current_school']); ?></td>
                        <td class="text-center notexport"><div>
                                <a href="javascript:void(0)" id="edit<?php echo e($value['submission_id']); ?>" onclick="editRow(<?php echo e($value['submission_id']); ?>)" title="Edit"><i class="far fa-edit"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" onclick="saveScore(<?php echo e($value['submission_id']); ?>)" id="save<?php echo e($value['submission_id']); ?>" title="Save"><i class="fa fa-save"></i></a>&nbsp;<a href="javascript:void(0)" class="d-none" id="cancel<?php echo e($value['submission_id']); ?>" onclick="hideEditRow(<?php echo e($value['submission_id']); ?>)" title="Cancel"><i class="fa fa-times"></i></a>
                            </div>
                        </td>
                        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tyear => $tvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $tvalue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tvalue1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="align-middle">
                                        <?php
                                            $marks = $value['score'][$tyear][$svalue][$tvalue1] ?? '';
                                        ?>
                                        <div class="text-center">
                                            <span <?php if(!is_numeric($marks)): ?> class="scorelabel" <?php endif; ?>>
                                                    <?php echo $marks; ?>

                                            </span> 
                                            <?php if(!is_numeric($marks)): ?>
                                                <input type="text"  class="form-control numbersOnly d-none scoreinput" value="0" maxlength="3" min="0" max="100" id="<?php echo e($value['submission_id'].','.$svalue.','.$tvalue1.','.$tyear); ?>">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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