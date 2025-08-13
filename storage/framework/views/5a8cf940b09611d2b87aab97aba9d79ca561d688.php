<?php 
    $config_subjects = Config::get('variables.subjects');
    $subject_count = count($subjects) ?? 0;
    $colspan = 10;
?>

<?php if(!empty($firstdata)): ?>

    <table class="table table-striped mb-0 w-100" id="datatable">
        <thead>
            <tr>
                <th class="align-middle" colspan="<?php echo e($colspan); ?>" rowspan="2"></th>
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
                        <th class="align-middle" colspan="<?php echo e(count($tvalue)); ?>"><?php echo e($sub); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
            <tr>
                <th class="align-middle">Submission ID</th>
                <th class="align-middle">Submission Status</th>
                <th class="align-middle">State ID</th>
                <th class="align-middle">Last Name</th>
                <th class="align-middle">First Name</th>
                <th class="align-middle">Current Grade</th>
                <th class="align-middle">Next Grade</th>
                <th class="align-middle">Current School</th>
                <th class="align-middle">First Choice</th>
                <th class="align-middle">Second Choice</th>
                <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tyear => $tvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $tvalue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="align-middle"><?php echo e($value1); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $firstdata; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr id="row<?php echo e($value['submission_id']); ?>">
                    <td class="text-center"><?php echo e($value['id']); ?></td>
                    <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                    <td class=""><?php echo e($value['student_id']); ?></td>
                    <td class=""><?php echo e($value['last_name']); ?></td>
                    <td class=""><?php echo e($value['first_name']); ?></td>
                    <td class="text-center"><?php echo e($value['current_grade']); ?></td>
                    <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                    <td class=""><?php echo e($value['current_school']); ?></td>
                    <td class=""><?php echo e($value['first_program']); ?></td>
                    <td class=""><?php echo e($value['second_program']); ?></td>
                    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tyear => $tvalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svalue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $tvalue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tvalue1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="align-middle">
                                    <?php
                                        $marks = $value['score'][$tyear][$svalue][$tvalue1] ?? '';
                                    ?>
                                    <div class="text-center">
                                        <?php if(is_numeric($marks) || $marks == 'NA'): ?>
                                            <span>
                                                <?php echo $marks; ?>

                                            </span> 
                                        <?php else: ?>
                                            <span>
                                                <?php echo e('0'); ?>

                                            </span> 
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
<?php endif; ?>