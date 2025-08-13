<?php if(!Session::has("from_admin")): ?>
    <?php if(isset($data['checkbox_1']) && $data['checkbox_1']!=''): ?>
    	<?php if(isset($data['placeholder'])): ?>
    		<div class="form-group d-flex pl-30">
      			<?php echo $data['placeholder']; ?>

    		</div>
    	<?php endif; ?>
    	<div class="form-group d-flex">
    		<div class="mr-10">
            	<input type="checkbox"  name="formdata[<?php echo e($field_id); ?>]"  <?php if(isset($data['label'])): ?> thisname="<?php echo e($data['label']); ?>" <?php endif; ?> class="chkbox-style form-check-input styled-checkbox" id="table<?php echo e($field_id); ?>"  <?php if(isset($data['required']) && $data['required']=='yes'): ?> required <?php endif; ?> value="Yes">
            	<label for="table<?php echo e($field_id); ?>" class="label-xs check-secondary"></label>
            	
            </div>
            <div class=""><?php echo $data['checkbox_1']; ?>

            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
