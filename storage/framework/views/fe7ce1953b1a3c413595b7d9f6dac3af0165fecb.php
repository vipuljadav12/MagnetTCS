<div class="tab-pane fade show active" id="preview04" role="tabpanel" aria-labelledby="preview04-tab">
    <div class=" <?php if($display_outcome > 0): ?> d-none <?php endif; ?>">
        <div class="table-responsive">
                    <div class="row col-md-12" id="submission_filters"></div>
        <div style="height: 704px; overflow-y: auto;">
            <table class="table" id="tbl_submission_results">
                <thead>
                    <tr>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Submission ID</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Student Name</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Next Grade</th>
                        <th class="notexport" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program</th>
                        <th class="notexport" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Outcome</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Race</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">School</th>
                        <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Choice</th>
                        <th class="text-center" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Outcome</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $final_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class=""><?php echo e($value['id']); ?></td>
                            <td class=""><?php echo e($value['name']); ?></td>
                            <td class="text-center"><?php echo e($value['grade']); ?></td>
                            <td class=""><?php echo e($value['program_name']); ?></td>
                            <td class=""><?php echo e($value['offered_status']); ?></td>
                            <td class=""><?php echo e($value['race']); ?></td>
                            <td class=""><?php echo e($value['school']); ?></td>
                            <td class=""><?php echo e($value['program']); ?></td>
                            <td class="text-center"><?php echo e($value['choice']); ?></td>
                            <td class=""><?php echo $value['outcome']; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        </div>
        
    </div>
    <div class="d-flex flex-wrap justify-content-between pt-20"><a href="javascript:void(0);" class="btn btn-secondary" title="" id="ExportReporttoExcel">Download Submissions Result</a><?php if($display_outcome == 0): ?> <a href="javascript:void(0);" class="btn btn-success" title="" onclick="updateFinalStatus()">Accept Outcome and Commit Result</a> <?php else: ?> <a href="javascript:void(0);" class="btn btn-danger" title="" onclick="alert('Already Outcome Commited')">Accept Outcome and Commit Result</a>  <?php endif; ?></div>
</div>