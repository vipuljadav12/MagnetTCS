<header>
    <div class="p-10 bg-secondary text-center" style="background: <?php echo e(Session::get("theme_color")); ?> !important">
    	<a href="javascript:void(0);" class="d-inline-block" title="" id="logo">
    		<img src="<?php echo e((isset($application_data) ? getDistrictLogo($application_data->display_logo) : getDistrictLogo())); ?>" title="" alt="" style="max-height: 100px;">
    	</a>
    	<div class="align-middle d-inline-block ml-20 text-left d-none">
    		<div><?php echo $district->address ?? ''; ?></div>
    		<div><?php echo $district->city ?? ''; ?>, <?php echo $district->state  ?? ''; ?> <?php echo $district->zipcode ?? ''; ?></div>
    		<div><?php echo e($district->magnet_point_contact_phone ?? ''); ?></div>
    	</div>
    </div>
</header>