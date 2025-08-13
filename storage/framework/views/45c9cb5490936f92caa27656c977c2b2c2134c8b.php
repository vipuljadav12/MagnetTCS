<form class="form" id="submission_comment_form" method="post" action="<?php echo e(url('admin/Submissions/store/comments/'.$submission->id)); ?>">
    <?php echo e(csrf_field()); ?>

    <div class="card shadow">
        <div class="card-header">Comments</div>
        <div class="card-body">
            <div class="form-group">
                <label for="">Comment : </label>
                <div class="">
                    <textarea name="comment" class="form-control"></textarea>
                    <?php if($errors->has('comment')): ?>
                        <div class="error"><?php echo e($errors->first('comment')); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-secondary" title="Submit">Submit</button>
            </div>
            <div class="border-top mt-30 pb-10">
            <?php if(isset($data['comments'])): ?>
                <?php $__currentLoopData = $data['comments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $user_name = getUserName($value->user_id);
                        $explode_name = explode(" ", $user_name);
                        $name_initials = "";
                        foreach ($explode_name as $word) {
                            $name_initials .= $word[0] ?? '';
                        }
                    ?>
                    <div class="d-flex mt-20 mb-0 card p-15 flex-row">
                        <div class="mr-20">
                            <div class="bg-secondary text-white rounded-circle comment-short-name"><?php echo e($name_initials); ?></div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-10">
                                <div class=""><?php echo e($user_name); ?></div>
                                <div class=""><?php echo e(getDateTimeFormat($value->created_at)); ?></div>
                            </div>
                            <div class=""><?php echo e($value->comment); ?></div>
                            <div class=""><?php echo $value->submission_event; ?></div>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
            </div>
        </div>
    </div>
</form>