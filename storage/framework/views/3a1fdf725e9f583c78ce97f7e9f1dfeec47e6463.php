<table class="table table-striped mb-0" id="grade-table">
    <thead>
        <tr> 
            <th class="align-middle">#</th>
            <th class="align-middle">Old Status</th>
            <th class="align-middle">New Status</th>
            <th class="align-middle">Comment</th>
            <th class="align-middle">Updated By</th>
            <th class="align-middle">Updated At</th>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($data['status_logs'])): ?>
            <?php $__currentLoopData = $data['status_logs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr> 
                    <td class="align-middle"><?php echo e($loop->iteration); ?></td>
                    <td class="align-middle"><?php echo e($value->old_status); ?></td>
                    <td class="align-middle"><?php echo e($value->new_status); ?></td>
                    <td class="align-middle"><?php echo $value->comment; ?></td>
                    <td class="align-middle"><?php if($value->updated_by == 0): ?> <?php echo e("By Parent"); ?> <?php else: ?> <?php echo e(getUserName($value->updated_by)); ?> <?php endif; ?></td>
                    <td class="align-middle"><?php echo e(getDateTimeFormat($value->updated_at)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No records found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>