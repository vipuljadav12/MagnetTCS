<?php $__env->startSection("title"," Login | ".env("APP_NAME")); ?>
<?php $__env->startSection('content'); ?>
<div class="row align-items-center full-height">
    <div class="col-12 col-xl-5 mx-auto m-w-600">
        <div class="bg-white rounded pt-50 pb-50 pt-lg-30">
            <div class="logo-wrapper text-center mb-15 mb-lg-30 mt-10 mt-lg-5 px-15 px-md-30">
                <h2 class="text-uppercase text-center"><a href="" class="text-success"> <span><img src="<?php echo e(url('/')); ?>/resources/assets/admin/images/login.png" alt=""></span></a> </h2>
            </div>
            <hr>
            <div class="p-30 px-md-80">
                <?php if(Session::has("mess")): ?>
                    <div class="alert alert-danger"> <?php echo e(Session::get("mess")); ?></div>
                <?php endif; ?>
                <form class="form-horizontal" method="POST" id="loginform"  action="<?php echo e(route('login')); ?>">
                    <?php echo e(csrf_field()); ?>

                    <div class="form-group position-relative pl-50">
                        <label for="" class="font-18 <?php echo e($errors->has('email') ? ' has-error' : ''); ?>">Username</label>
                        <input class="form-control" type="text"  name="email" id="emailaddress" required="" placeholder="" value="<?php echo e(old('email')); ?>" maxlength="255" autofocus>
                        <div class="pre-icon"> <i class="far fa-user-circle"></i></div>
                        <?php if($errors->has('email')): ?>
                            <span class="error">
                                <strong><?php echo e($errors->first('email')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group position-relative pl-50">
                        <label for="password" class="font-18">Password</label>
                        <input class="form-control" type="password" required="" name="password" id="password" placeholder=""  autocomplete="off" maxlength="255">
                        <div class="pre-icon fingerprint"><i class="fas fa-fingerprint"></i></div>
                        <?php if($errors->has('password')): ?>
                            <span class="error">
                                <strong><?php echo e($errors->first('password')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="buttons-w">
                        <div class="form-group position-relative text-right mb-0 pt-10">
                            <button type="submit" class="btn-secondary btn-lg w-120 font-18 d-inline-block text-center rounded-0">Login</button> 
                            <div class="form-check-inline"> 
                                <a href="<?php echo e(url('')); ?>/password/reset" title="">Reset Password</a>
                                
                            </div> 
                        </div>
                    </div>
                </form>
            </div>
            <!-- end card-box--> 
        </div>
        <!-- end wrapper --> 
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content1'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo e(csrf_field()); ?>


                        <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required autofocus>

                                <?php if($errors->has('email')): ?>
                                    <span class="error">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                <?php if($errors->has('password')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>

                                <a class="btn btn-link" href="<?php echo e(route('password.request')); ?>">
                                    Forgot Your Password?
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('auth.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>