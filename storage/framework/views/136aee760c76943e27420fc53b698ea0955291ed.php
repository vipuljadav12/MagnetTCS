<table class="table table-striped mb-0" id="Email-table">
    <thead>
        <tr> 
            <th class="align-middle">#</th>
            <th class="align-middle">Event</th>
            <th class="align-middle">Email Address</th>
            <th class="align-middle">Email Subject</th>
            <th class="align-middle">Sent By</th>
            <th class="align-middle">Sent On</th>
            <th class="align-middle">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($data['email_communication'])): ?>
            <?php $__currentLoopData = $data['email_communication']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr> 
                    <td class="align-middle"><?php echo e($loop->iteration); ?></td>
                    <td class="align-middle"><?php echo e($value->module); ?></td>
                    <td class="align-middle"><?php echo e($value->email_to); ?></td>
                    <td class="align-middle"><?php echo e($value->email_subject); ?></td>
                    <td class="align-middle"><?php if($value->user_id == 0): ?> <?php echo e("System"); ?> <?php else: ?> <?php echo e(getUserName($value->user_id)); ?> <?php endif; ?></td>
                    <td class="align-middle"><?php echo e(getDateTimeFormat($value->created_at)); ?></td>
                    <td class="text-center">
                        <a href="javascript:void(0);" class="font-18 ml-5 mr-5 email_communication" id = "<?php echo e($value->id); ?>" title="Preview" onclick="previewCommunicationEmail(<?php echo e($value->id); ?>)"><i class="fas fa-eye"></i></a>
                        <a href="<?php echo e(url('admin/Submissions/resendEmail',$value->id)); ?>" class="font-18 ml-5 mr-5" title="Resend"><i class="fas fa-redo"></i></a>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No records found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="modal fade" id="emailCommunication" tabindex="-1" role="dialog" aria-labelledby="ModalLabel1" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel1">Email Communication Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="Communication_modal">

            </div>
        </div>
</div>
</div>

