<?php
    $student_profile = app('\app\Modules\Submissions\Controllers\SubmissionsController')->calculateStudentProfile($submission->id);

    //dd($student_profile);
?>

<form class="form" id="" action="">
    <?php echo e(csrf_field()); ?>

    <?php if($student_profile['profile']['recommendation']['status'] == "Y" && $student_profile['profile']['test_score']['status'] == "Y" && $student_profile['profile']['grade']['status'] == "Y"): ?>
        <div class="card shadow">
            <div class="card-header">Student Profile</div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12 font-weight-bold">Student Profile Score</label>
                    <div class="col-12 col-md-12">
                        <input id="" type="text" class="form-control" value="<?php echo e($student_profile['profile']['student_score'] ?? 0); ?>" disabled="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12 font-weight-bold">Student Profile Percentage</label>
                    <div class="col-12 col-md-12">
                        <input id="" type="text" class="form-control" value="<?php echo e($student_profile['profile']['final_percent'] ?? 0); ?>" disabled="">
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">Print Form</div>
            <div class="card-body">
                <div class="form-group row col-12">
                    <div class="">
                        <a class="btn btn-sm btn-success" href="<?php echo e(url('')); ?>/admin/Submissions/student/profile/pdf/<?php echo e($submission->id); ?>" title="Print Student Profile"><i class="fas fa-file-pdf"></i> Print Student Profile</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow">
                
                <div class="card-body">
                    <div class="form-group row col-12">
                        <div class="">
                            <label class="text-danger">Following information are pending for this submission:<br>
                                <ul>
                                <?php if($student_profile['profile']['recommendation']['status'] == "N"): ?>
                                    <li>Learners Profile Section</li>
                                <?php endif; ?>
                                <?php if($student_profile['profile']['test_score']['status'] == "N"): ?>
                                    <li>Test Score Section</li>
                                <?php endif; ?>
                                <?php if($student_profile['profile']['grade']['status'] == "N"): ?>
                                    <li>Test Score Section</li>
                                <?php endif; ?>  
                                </ul>  
                                    
                        </div>
                    </div>
                </div>
            </div>
    <?php endif; ?>

</form>