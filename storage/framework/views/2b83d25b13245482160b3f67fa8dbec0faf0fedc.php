<div class="form-group row">
	
	<?php
		$sel_value = "";
		if(Session::has("form_data"))
		{
			$formdata = Session::get("form_data");
			if(isset($formdata[0]['formdata'][$field_id]))
				$sel_value = $formdata[0]['formdata'][$field_id];
		}
	?>
	<?php if(isset($data['radio_1']) && $data['radio_1']!=''): ?>

		<?php if($sel_value=="Yes" && $data['db_field'] == "mcp_employee"): ?>
			<input type="hidden" id="mcp_employee_enable" value="1">
			<input type="hidden" id="mcp_employee_element" value="Yes">
		<?php elseif($data['db_field'] == "mcp_employee"): ?>
			<input type="hidden" id="mcp_employee_enable" value="0">
			<input type="hidden" id="mcp_employee_element" value="">
		<?php endif; ?>
		<?php if(isset($data['label'])): ?>
			<label class="control-label col-12 col-md-4 col-xl-3"><?php echo e($data['label']); ?><?php if(isset($data['required']) && $data['required']=='yes'): ?><span class="text-danger">*</span><?php endif; ?> 
			</label>
				
		<?php endif; ?>
		<div class="col-12 col-md-6 col-xl-6">
				<div class="custom-control custom-radio custom-control-inline">
				<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if(substr($key, 0, 6)=="radio_"): ?>
						<input type="radio" style="left: 0 !important; opacity: 1 !important; position: inherit !important;" value="<?php echo e($value); ?>" name="formdata[<?php echo e($field_id); ?>]" <?php if($sel_value==$value): ?> checked="checked" <?php endif; ?> <?php if($data['db_field']=="mcp_employee"): ?> onchange="showHideEmployee(this)" <?php endif; ?>>&nbsp;<?php echo e($value); ?>&nbsp;&nbsp;
					<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
	<!--<fieldset>-->
		
    <?php endif; ?>
</div>