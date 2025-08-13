<div class="form-group row">
	<?php if(isset($data['label'])): ?>
		<label class="control-label col-12 col-md-4 col-xl-3"><?php echo e($data['label']); ?><?php if(isset($data['required']) && $data['required']=='yes'): ?><span class="text-danger">*</span><?php endif; ?>
			</label>
		
	<?php endif; ?>
	
	

	<div class="col-12 col-md-6 col-xl-6">
		<input type="hidden" class="form-control" id="birthdayFiller" <?php if(isset($data['label'])): ?> thisname="<?php echo e($data['label']); ?>" <?php endif; ?> name="formdata[<?php echo e($field_id); ?>]" <?php if(isset($data['placeholder'])): ?> placeholder="<?php echo e($data['placeholder']); ?>" <?php endif; ?> <?php if(isset($data['required']) && $data['required']=='yes'): ?> required <?php endif; ?> value="<?php echo e(Session::get("form_data")[0]['formdata'][$field_id]  ?? ''); ?>">
		<?php if($data['db_field'] == "birthday"): ?>
			<div class="row">
				
				<?php
					if(isset(Session::get("form_data")[0]['formdata'][$field_id]) && Session::get("form_data")[0]['formdata'][$field_id] != '')
					{
						$birthday_cut_off = Session::get("form_data")[0]['formdata'][$field_id];
					}
					else
					{
						$birthday_cut_off = getApplicationDetailsById(Session::get('application_id'));
					}
					//$birthday_cut_off = date("Y-m-d");
					$year = date("Y",strtotime($birthday_cut_off));
					$month = date("m",strtotime($birthday_cut_off));
					$day = date("d",strtotime($birthday_cut_off));

				?> 
				<input type="hidden" name="" value="<?php echo e($day); ?>" id='limitDay'>
				<input type="hidden" name="" value="<?php echo e($year); ?>" id='limitYear'>
				<input type="hidden" name="" value="<?php echo e($month); ?>" id='limitMonth'>
				
				<div class="col-4">
					<select class="form-control changeDate" id="month">
						
					</select>
					<div class="text-right hidden">	
						<label class="text-right action"><?php echo e(date("F",strtotime($birthday_cut_off))); ?>	</label>
					</div>
				</div>

				<div class="col-4">
					<select class="form-control changeDate" id="day">
						
					</select>
					<div class="text-right hidden">	
						<label class="text-right action"><?php echo e($day); ?>	</label>
					</div>
				</div>
				<div class="col-4">
					<select class="form-control changeDate" id="year">
						
					</select>
					<div class="text-right hidden">	
						<label class="text-right action"><?php echo e($year); ?>	</label>
					</div>
				</div>
			</div>
		<?php else: ?>
			<input type="text" class="form-control mydatepicker" <?php if(isset($data['label'])): ?> thisname="<?php echo e($data['label']); ?>" <?php endif; ?> name="formdata[<?php echo e($field_id); ?>]" <?php if(isset($data['placeholder'])): ?> placeholder="<?php echo e($data['placeholder']); ?>" <?php endif; ?> <?php if(isset($data['required']) && $data['required']=='yes'): ?> required <?php endif; ?>>
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

