<div class="form-group row">
	<?php if(isset($data['label'])): ?>
		<label class="control-label col-12 col-md-4 col-xl-3"><?php echo e($data['label']); ?><?php if(isset($data['required']) && $data['required']=='yes'): ?><span class="text-danger">*</span><?php endif; ?>
			</label>
		
	<?php endif; ?>
	<div class="col-12 col-md-6 col-xl-6">
		<input type="email" class="form-control" <?php if(isset($data['label'])): ?> thisname="<?php echo e($data['label']); ?>" <?php endif; ?>  name="formdata[<?php echo e($field_id); ?>]" <?php if(isset($data['placeholder'])): ?> placeholder="<?php echo e($data['placeholder']); ?>" <?php endif; ?> <?php if(isset($data['required']) && $data['required']=='yes'): ?> required <?php endif; ?> value="<?php echo e(Session::get("form_data")[0]['formdata'][$field_id]  ?? ''); ?>"  id="<?php echo e($data['db_field']); ?>">
	</div>
	<?php if(isset($data['help_text'])): ?>
		<div class="col-12 col-md-2 col-xl-3">
			<span class="help" data-toggle="tooltip" data-html="true" title="<?php echo e($data['help_text']); ?>">
		        <?php if($data['help_text'] != ""): ?>
		            <i class="fas fa-question"></i>
		        <?php endif; ?>
		    </span>
		</div>
	<?php endif; ?>
	
</div>