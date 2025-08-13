<div class="">
<div class="">
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0 w-100" id="datatable">
                <thead>
                    <tr>
                        <th class="align-middle text-center">Sub ID</th>
                        <th class="align-middle text-center">Submission Status</th>
                        <th class="align-middle hiderace text-center">Race</th>
                        <th class="align-middle text-center">Student Status</th>
                        <th class="align-middle text-center">First Name</th>
                        <th class="align-middle text-center">Last Name</th>
                        <th class="align-middle text-center">Next Grade</th>
                        <th class="align-middle text-center">Current School</th>
                        <th class="align-middle hidezone text-center">Zoned School</th>
                        <th class="align-middle text-center">First Choice</th>
                        <th class="align-middle text-center">Second Choice</th>
                        <th class="align-middle text-center">Sibling ID</th>
                        <th class="align-middle text-center">Lottery Number</th>
                        <th class="align-middle text-center committee_score-col">Profile Score</th>
                        <th class="align-middle text-center committee_score-col">Final Status</th>
                        <th class="align-middle text-center committee_score-col">% Status</th>
                    </tr>
                    
                </thead>
                <tbody>
                    <?php if(!empty($magnet_offer_data)): ?>
                        <?php $__currentLoopData = $magnet_offer_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class=""><?php echo e($value['id']); ?></td>
                                <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                                <td class="hiderace"><?php echo e($value['race']); ?></td>
                                <td class="">
                                    <?php if($value['student_id'] != ''): ?>
                                        Current
                                    <?php else: ?>
                                        New
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['first_name']); ?></td>
                                <td class=""><?php echo e($value['last_name']); ?></td>
                                
                                <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                                <td class=""><?php echo e($value['current_school']); ?></td>
                                <td class="hidezone"><?php echo e($value['zoned_school']); ?></td>
                                <?php if($value['choice'] == "first"): ?>
                                    <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                    <td class="text-center"></td>
                                <?php else: ?>
                                    <td class="text-center"></td>
                                    <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                <?php endif; ?>
                                <td class="">
                                    <?php $sibling_id = $value[$value['choice'].'_sibling'] ?>
                                    <?php if($sibling_id  != ''): ?>
                                        <div class="alert1 alert-success p-10 text-center"><?php echo e($sibling_id); ?></div>
                                    <?php else: ?>
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    <?php endif; ?>
                                </td>

                                <td class=""><?php echo e($value['lottery_number']); ?></td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            <?php echo $value['student_profile']; ?>

                                        </div>
                                </td>
                                <td class="text-center"><?php echo e($value['offer_status']); ?></td>
                                <td class="text-center"><?php echo $value['percent_status'] ?? ''; ?>	</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if(isset($first_magnet_processing['waitlisted_arr'])): ?>

                        <?php $__currentLoopData = $first_magnet_processing['waitlisted_arr']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class=""><?php echo e($value['id']); ?></td>
                                <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                                <td class="hiderace"><?php echo e($value['race']); ?></td>
                                <td class="">
                                    <?php if($value['student_id'] != ''): ?>
                                        Current
                                    <?php else: ?>
                                        New
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['first_name']); ?></td>
                                <td class=""><?php echo e($value['last_name']); ?></td>
                                
                                <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                                <td class=""><?php echo e($value['current_school']); ?></td>
                                <td class="hidezone"><?php echo e($value['zoned_school']); ?></td>
                                <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                <td class="text-center"></td>
                                <td class="">
                                        <?php $sibling_id = $value['first_sibling'] ?>
                                    <?php if($sibling_id  != ''): ?>
                                        <div class="alert1 alert-success p-10 text-center"><?php echo e($sibling_id); ?></div>
                                    <?php else: ?>
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['lottery_number']); ?></td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            <?php echo $value['student_profile']; ?>

                                        </div>
                                </td>
                                <td class="text-center"><?php echo e($value['offer_status']); ?></td>
                                <td class="text-center"><?php echo $value['percent_status'] ?? ''; ?>	</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if(isset($second_magnet_processing['waitlisted_arr'])): ?>
                        <?php $__currentLoopData = $second_magnet_processing['waitlisted_arr']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class=""><?php echo e($value['id']); ?></td>
                                <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                                <td class="hiderace"><?php echo e($value['race']); ?></td>
                                <td class="">
                                    <?php if($value['student_id'] != ''): ?>
                                        Current
                                    <?php else: ?>
                                        New
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['first_name']); ?></td>
                                <td class=""><?php echo e($value['last_name']); ?></td>
                                
                                <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                                <td class=""><?php echo e($value['current_school']); ?></td>
                                <td class="hidezone"><?php echo e($value['zoned_school']); ?></td>
                                <td class="text-center"></td>
                                <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                

                                <td class="">
                                        <?php $sibling_id = $value['second_sibling'] ?>
                                    <?php if($sibling_id  != ''): ?>
                                        <div class="alert1 alert-success p-10 text-center"><?php echo e($sibling_id); ?></div>
                                    <?php else: ?>
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['lottery_number']); ?></td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            <?php echo $value['student_profile']; ?>

                                        </div>
                                </td>
                                <td class="text-center"><?php echo e($value['offer_status']); ?></td>
                                <td class="text-center"><?php echo $value['percent_status'] ?? ''; ?>	</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                    <?php endif; ?>


                    <?php $__currentLoopData = $first_magnet_processing['no_availability_arr']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class=""><?php echo e($value['id']); ?></td>
                            <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                            <td class="hiderace"><?php echo e($value['race']); ?></td>
                            <td class="">
                                <?php if($value['student_id'] != ''): ?>
                                    Current
                                <?php else: ?>
                                    New
                                <?php endif; ?>
                            </td>
                            <td class=""><?php echo e($value['first_name']); ?></td>
                            <td class=""><?php echo e($value['last_name']); ?></td>
                            
                            <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                            <td class=""><?php echo e($value['current_school']); ?></td>
                            <td class="hidezone"><?php echo e($value['zoned_school']); ?></td>
                            <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                            <td class="text-center"></td>
                            <td class="">
                                    <?php $sibling_id = $value['first_sibling'] ?>
                                <?php if($sibling_id  != ''): ?>
                                    <div class="alert1 alert-success p-10 text-center"><?php echo e($sibling_id); ?></div>
                                <?php else: ?>
                                    <div class="alert1 alert-warning p-10 text-center">NO</div>
                                <?php endif; ?>
                            </td>
                            <td class=""><?php echo e($value['lottery_number']); ?></td>
                            
                            <td class="text-center committee_score-col">
                                    <div class="alert1 alert-success">
                                        <?php echo $value['student_profile']; ?>

                                    </div>
                            </td>
                            <td class="text-center">Waitlisted<br>[No Availability]</td>
                            <td class="text-center"><?php echo $value['percent_status'] ?? ''; ?>	</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php $__currentLoopData = $second_magnet_processing['no_availability_arr']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class=""><?php echo e($value['id']); ?></td>
                            <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                            <td class="hiderace"><?php echo e($value['race']); ?></td>
                            <td class="">
                                <?php if($value['student_id'] != ''): ?>
                                    Current
                                <?php else: ?>
                                    New
                                <?php endif; ?>
                            </td>
                            <td class=""><?php echo e($value['first_name']); ?></td>
                            <td class=""><?php echo e($value['last_name']); ?></td>
                            
                            <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                            <td class=""><?php echo e($value['current_school']); ?></td>
                            <td class="hidezone"><?php echo e($value['zoned_school']); ?></td>
                            <td class="text-center"></td>
                            <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                            
                            <td class="">
                                    <?php $sibling_id = $value['second_sibling'] ?>
                                <?php if($sibling_id  != ''): ?>
                                    <div class="alert1 alert-success p-10 text-center"><?php echo e($sibling_id); ?></div>
                                <?php else: ?>
                                    <div class="alert1 alert-warning p-10 text-center">NO</div>
                                <?php endif; ?>
                            </td>
                            <td class=""><?php echo e($value['lottery_number']); ?></td>
                            
                            <td class="text-center committee_score-col">
                                    <div class="alert1 alert-success">
                                        <?php echo $value['student_profile']; ?>

                                    </div>
                            </td>
                            <td class="text-center">Waitlisted<br>[No Availability]</td>
                            <td class="text-center"><?php echo $value['percent_status'] ?? ''; ?>	</td>

                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                     

                    <?php if(isset($first_magnet_processing['in_eligible'])): ?>
                        <?php $__currentLoopData = $first_magnet_processing['in_eligible']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class=""><?php echo e($value['id']); ?></td>
                                <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                                <td class="hiderace"><?php echo e($value['race']); ?></td>
                                <td class="">
                                    <?php if($value['student_id'] != ''): ?>
                                        Current
                                    <?php else: ?>
                                        New
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['first_name']); ?></td>
                                <td class=""><?php echo e($value['last_name']); ?></td>
                                
                                <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                                <td class=""><?php echo e($value['current_school']); ?></td>
                                <td class="hidezone"><?php echo e($value['zoned_school']); ?></td>
                                <?php if($value['choice'] == "first"): ?>
                                    <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                    <td class="text-center"></td>
                                <?php else: ?>
                                    <td class="text-center"></td>
                                    <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                <?php endif; ?>
                                <td class="">
                                    <?php $sibling_id = $value[$value['choice'].'_sibling'] ?>
                                    <?php if($sibling_id  != ''): ?>
                                        <div class="alert1 alert-success p-10 text-center"><?php echo e($sibling_id); ?></div>
                                    <?php else: ?>
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['lottery_number']); ?></td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            <?php echo $value['student_profile']; ?>

                                        </div>
                                </td>
                                <td class="text-center text-danger">Denied Due to Ineligibility</td>
                                <td class="text-center"><?php echo $value['percent_status'] ?? ''; ?>	</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                    <?php if(isset($second_magnet_processing['in_eligible'])): ?>
                        <?php $__currentLoopData = $second_magnet_processing['in_eligible']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class=""><?php echo e($value['id']); ?></td>
                                <td class="text-center"><?php echo e($value['submission_status']); ?></td>
                                <td class="hiderace"><?php echo e($value['race']); ?></td>
                                <td class="">
                                    <?php if($value['student_id'] != ''): ?>
                                        Current
                                    <?php else: ?>
                                        New
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['first_name']); ?></td>
                                <td class=""><?php echo e($value['last_name']); ?></td>
                                        
                                <td class="text-center"><?php echo e($value['next_grade']); ?></td>
                                <td class=""><?php echo e($value['current_school']); ?></td>
                                <td class="hidezone"><?php echo e($value['zoned_school']); ?></td>
                                <?php if($value['choice'] == "first"): ?>
                                    <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                    <td class="text-center"></td>
                                <?php else: ?>
                                    <td class="text-center"></td>
                                    <td class=""><?php echo e(getProgramName($value['program_id'])); ?></td>
                                <?php endif; ?>
                                <td class="">
                                    <?php $sibling_id = $value[$value['choice'].'_sibling'] ?>
                                    <?php if($sibling_id  != ''): ?>
                                        <div class="alert1 alert-success p-10 text-center"><?php echo e($sibling_id); ?></div>
                                    <?php else: ?>
                                        <div class="alert1 alert-warning p-10 text-center">NO</div>
                                    <?php endif; ?>
                                </td>
                                <td class=""><?php echo e($value['lottery_number']); ?></td>
                                
                                <td class="text-center committee_score-col">
                                        <div class="alert1 alert-success">
                                            <?php echo $value['student_profile']; ?>

                                        </div>
                                </td>
                                <td class="text-center text-danger">Denied Due to Ineligibility</td>
                                <td class="text-center"><?php echo $value['percent_status'] ?? ''; ?>	</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>                                                            
                </tbody>
            </table>
            
        </div>
    </div>
</div>
</div>
</div>
