<div class="form-group row" <?php if($data['db_field']=="employee_id" || $data['db_field']=="work_location" || $data['db_field']=="employee_first_name" || $data['db_field']=="employee_last_name" || $data['db_field']=="zoned_school"): ?> id="<?php echo e($data['db_field']); ?>" style="display: none" <?php endif; ?>>
	<?php if(isset($data['label'])): ?>
		<label class="control-label col-12 col-md-4 col-xl-3"><?php echo e($data['label']); ?><?php if(isset($data['required']) && $data['required']=='yes'): ?><span class="text-danger">*</span><?php endif; ?>

			</label>
		
	<?php endif; ?>
	<div class="col-12 col-md-6 col-xl-6">
		<?php if($data['db_field'] == "address"): ?>
			<div class="mb-10">
		<?php endif; ?>
		<input type="text" class="form-control" <?php if(isset($data['label'])): ?> thisname="<?php echo e($data['label']); ?>" <?php endif; ?> name="formdata[<?php echo e($field_id); ?>]"  <?php if(isset($data['required']) && $data['required']=='yes' && $data['db_field'] != "zoned_school"): ?> required <?php endif; ?> value="<?php echo e(Session::get("form_data")[0]['formdata'][$field_id]  ?? ''); ?>" <?php if(isset($data['db_field']) && ($data['db_field']=="phone_number" || $data['db_field']=="alternate_number")): ?> placeholder="(___) ___-____" <?php else: ?> <?php if(isset($data['placeholder'])): ?> placeholder="<?php echo e($data['placeholder']); ?>" <?php endif; ?> <?php endif; ?> <?php if($data['db_field']=="student_id"): ?> onblur="checkStudentID(this)" <?php endif; ?> id="<?php echo e($data['db_field']); ?>" > <?php if($data['db_field']=="student_id"): ?> <span class="hidden">
                        Checking Student ID <img src="<?php echo e(url('/resources/assets/front/images/loader.gif')); ?>"></span> <?php endif; ?>
		

		<?php if($data['db_field'] == "address"): ?>
			</div>
		<?php endif; ?>
		<?php if($data['db_field'] == "address"): ?>
			<div class="alert alert-danger d-none" id="address_text">
				<p><?php echo getConfig()['address_selection_option']; ?></p>
				<div class=""  id="address_options">
				</div>
				
			</div>
		<?php endif; ?>
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