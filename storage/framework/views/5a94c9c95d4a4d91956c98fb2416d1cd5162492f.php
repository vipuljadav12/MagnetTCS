<div class="tab-pane fade show active" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
    <div class="<?php if($display_outcome > 0): ?> d-none <?php endif; ?>" style="height: 657px; overflow-y: auto;">
        <div class="table-responsive " id="table-wrap" style="height: 704px; overflow-y: auto;">
            <table class="table" id="tbl_population_changes">
                <thead>
                    <tr>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program Name</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Original Entered Available Seats</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Starting Available Slot</th>
                        <?php if(isset($race_ary)): ?>
                            <?php $__currentLoopData = $race_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $race=>$tmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important"><?php echo e($race); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Total Offered</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Ending Available Slots</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($data_ary)): ?>
                        <?php $__currentLoopData = $data_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <?php
                                $available_seats = $value['available_seats'] ?? 0;
                            ?>
                            
                            <td class=""><?php echo e(getProgramName($value['program_id'])); ?> - Grade <?php echo e($value['grade']); ?></td>
                            <td class="text-center"><?php echo e($value['total_seats'] ?? 0); ?></td>
                            <td class="text-center"><?php echo e($available_seats); ?></td>
                            <?php if(isset($race_ary)): ?>
                                <?php
                                    $total_offered = 0;
                                ?>
                                <?php $__currentLoopData = $race_ary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $race=>$tmp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $offered_value = $value['race_count'][$race] ?? 0;
                                        $total_offered += $offered_value;
                                    ?>
                                    <td class="text-center"><?php echo e($offered_value); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <td class="text-center"><?php echo e($total_offered); ?></td>
                            <td class="text-center"><?php echo e($available_seats - $total_offered); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
    <div class="d-flex flex-wrap justify-content-between mt-20"><a href="javascript:void(0);" class="btn btn-secondary" title="" id="ExportReporttoExcel">Download Population Changes</a> <?php if($display_outcome == 0): ?> <a href="javascript:void(0);" class="btn btn-success" title="" onclick="updateFinalStatus()">Accept Outcome and Commit Result</a> <?php else: ?> <a href="javascript:void(0);" class="btn btn-danger" title="" onclick="alert('Already Outcome Commited')">Accept Outcome and Commit Result</a>  <?php endif; ?>
           </div>
</div>

