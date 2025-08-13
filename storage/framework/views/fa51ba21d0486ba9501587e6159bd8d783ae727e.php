<?php
    $incidents = app('\app\Modules\Submissions\Controllers\SubmissionsController')->checkConductDisplay($submission->id);

   // dd($incidents);
?>

<form class="form" id="" action="<?php echo e(url('admin/Submissions/update/ConductDisciplinaryInfo/'.$submission->id)); ?>" method="post">
    <?php echo e(csrf_field()); ?>



        <div class="card shadow">
            <div class="card-header">Student Conduct Criteria</div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12 font-weight-bold">Incident 1</label>
                    <div class="col-12 col-md-12">
                        <textarea class="form-control" name="incidents[]" ><?php echo $incidents[0] ?? ''; ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12 font-weight-bold">Incident 2</label>
                    <div class="col-12 col-md-12">
                        <textarea class="form-control" name="incidents[]"><?php echo $incidents[1] ?? ''; ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-12 col-md-12 font-weight-bold">Incident 3</label>
                    <div class="col-12 col-md-12">
                        <textarea class="form-control" name="incidents[]"><?php echo $incidents[2] ?? ''; ?></textarea>
                    </div>
                </div> 
                <div class="text-right"> 
                        <button type="submit" class="btn btn-success">    
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>               
            </div>
        </div>


</form>