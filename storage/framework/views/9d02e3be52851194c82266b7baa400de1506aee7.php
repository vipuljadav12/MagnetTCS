<?php
    $subjects_ary_conf = config('variables.ag_eligibility_subjects');
?>
<div class="form-group">
    <div class="row pl-20 pr-20">
        <?php for($i=0; $i<2; $i++): ?>
            <?php
                $part = $i+1;
            ?>
            <div class="col-sm-6 col-md-6" style="display: inline-block !important;">
                <div class="card">
                     <div class="card-header">Part <?php echo e($part); ?></div>
                    <div class="card-body">
                        <?php
                            $ts_fieds = $data['test_scores'];
                            if (isset($data['eligibility']->academic_grades_data)) {
                                $ag_data = json_decode($data['eligibility']->academic_grades_data, 1);
                                $rs_data = isset($ag_data['part_'.$part]['rangeselection']) ? $ag_data['part_'.$part]['rangeselection'] : [];
                                $rs_method = isset($rs_data['abc']) ? 'abc' : (isset($rs_data['3s2s']) ? '3s2s' : '');
                                $ag_ts_scores = isset($ag_data['part_'.$part]['ts_scores']) ? $ag_data['part_'.$part]['ts_scores'] : [];
                            } else {
                                $rs_data = [];
                                $ag_ts_scores = [];
                                $rs_method = '';
                            }
                        ?>
                        <?php $__currentLoopData = $data['eligibility_subjects']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="<?php echo e($val); ?>-<?php echo e($part); ?>" value="<?php echo e($val); ?>" name="academic_grades_data[part_<?php echo e($part); ?>][ts_scores][]" <?php if(in_array($val, $ag_ts_scores)): ?> checked <?php endif; ?>>
                                <label for="<?php echo e($val); ?>-<?php echo e($part); ?>" class="custom-control-label"><?php echo e($subjects_ary_conf[$val]); ?></label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        

                        <div class="form-control mt-10" style="border: none !important;">
                            <label class="control-label" style="display: inline-block;"><strong>Range Selection :</strong></label>
                            <select class="form-control custom-select template-type-select-new" id="range_selection_<?php echo e($part); ?>" style="display: inline-block; width: 74%; float: right;">
                                <option>Select Calculation Method</option>
                                <option value="3s2s" <?php if($rs_method == '3s2s'): ?> selected <?php endif; ?>>3s - 2s Calculation</option>
                                <option value="abc" <?php if($rs_method == 'abc'): ?> selected <?php endif; ?>>A/B/C Criteria</option>
                            </select>
                        </div>

                        <div class="mt-10 row pt-20 d-none" style="border: none !important;" id="abc_criteria_<?php echo e($part); ?>">
                            <div class="col-9 pt-10" style="margin: 0 auto !important;">
                                <label class="control-label" style="display: inline-block;"><strong>A</strong></label>
                                <input type="text" class="form-control abc_criteria_<?php echo e($part); ?>" name="academic_grades_data[part_<?php echo e($part); ?>][rangeselection][abc][A]" value="<?php echo e($rs_data[$rs_method]['A'] ?? ''); ?>" style="display: inline-block; width: 74%; float: right;">
                            </div>
                            <div class="col-9 pt-10" style="margin: 0 auto !important;">
                                <label class="control-label text-right" style="display: inline-block;"><strong>B</strong></label>
                                <input type="text" class="form-control abc_criteria_<?php echo e($part); ?>" name="academic_grades_data[part_<?php echo e($part); ?>][rangeselection][abc][B]" value="<?php echo e($rs_data[$rs_method]['B'] ?? ''); ?>" style="display: inline-block; width: 74%; float: right;">
                            </div>
                            <div class="col-9 pt-10" style="margin: 0 auto !important;">
                                <label class="control-label" style="display: inline-block;"><strong>C</strong></label>
                                <input type="text" class="form-control abc_criteria_<?php echo e($part); ?>" name="academic_grades_data[part_<?php echo e($part); ?>][rangeselection][abc][C]" value="<?php echo e($rs_data[$rs_method]['C'] ?? ''); ?>" style="display: inline-block; width: 74%; float: right;">
                            </div>
                        </div>

                        <div class="mt-10 row pt-20 d-none" style="border: none !important;" id="3s2s_criteria_<?php echo e($part); ?>">
                            <div class="col-9 pt-10" style="margin: 0 auto !important;">
                                <label class="control-label" style="display: inline-block;"><strong>All 3s</strong> = </label>
                                <label class="control-label" style="display: inline-block;">12.5 points</label>
                                <input type="hidden" class="form-control 3s2s_criteria_<?php echo e($part); ?>" name="academic_grades_data[part_<?php echo e($part); ?>][rangeselection][3s2s][All 3s]" style="display: inline-block; width: 74%; float: right;" value="<?php echo e(($rs_data[$rs_method]['All 3s']) ?? "12.5"); ?>">
                            </div>
                            <div class="col-9 pt-10" style="margin: 0 auto !important;">
                                <label class="control-label" style="display: inline-block;"><strong>3s & 2s or 2s & 2s</strong> = </label>
                                <label class="control-label" style="display: inline-block;">6.25 points</label>
                                <input type="hidden" class="form-control 3s2s_criteria_<?php echo e($part); ?>" name="academic_grades_data[part_<?php echo e($part); ?>][rangeselection][3s2s][3s & 2s or 2s & 2s]" style="display: inline-block; width: 74%; float: right;" value="<?php echo e(($rs_data[$rs_method]['3s & 2s or 2s & 2s']) ?? "6.25"); ?>">
                            </div>
                            <div class="col-9 pt-10" style="margin: 0 auto !important;">
                                <label class="control-label" style="display: inline-block;"><strong>3s & 1s or 2s & 1s</strong> = </label>
                                <label class="control-label" style="display: inline-block;">0 points</label>
                                <input type="hidden" class="form-control 3s2s_criteria_<?php echo e($part); ?>" name="academic_grades_data[part_<?php echo e($part); ?>][rangeselection][3s2s][3s & 1s or 2s & 1s]" style="display: inline-block; width: 74%; float: right;" value="<?php echo e(($rs_data[$rs_method]['3s & 1s or 2s & 1s']) ?? "0"); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>       
</div>