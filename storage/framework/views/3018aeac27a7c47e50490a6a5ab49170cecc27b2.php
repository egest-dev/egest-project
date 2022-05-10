<?php $__env->startSection('page-title'); ?>
       <?php echo e(__('Reset Password')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom-scripts'); ?>
    <?php if(env('RECAPTCHA_MODULE') == 'yes'): ?>
        <?php echo NoCaptcha::renderJs(); ?>

    <?php endif; ?>
<?php $__env->stopPush(); ?>

 <?php $__env->startSection('content'); ?>
     <div class="login-contain">
         <div class="login-inner-contain">
             <div class="login-form">
        <div class="page-title"><h5><?php echo e(__('Réinitialiser le mot de passe')); ?></h5></div>
        <?php if(session('status')): ?>
            <div class="alert alert-primary">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>
        <p class="text-xs text-muted"><?php echo e(__('')); ?></p>
        <form method="POST" action="<?php echo e(route('password.email')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label for="email" class="form-control-label"><?php echo e(__('Merci de contacter cette email pour Réinitialiser le mot de passe :')); ?></label>
                
            </div>

            
            <?php if(env('RECAPTCHA_MODULE') == 'yes'): ?>
                <div class="form-group col-lg-12 col-md-12 mt-3">
                    <?php echo NoCaptcha::display(); ?>

                    <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="small text-danger" role="alert">
                                <strong><?php echo e($message); ?></strong>
                            </span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            <?php endif; ?>
            


            <button href="mailto:contact@egest.ma" class="btn-login"><?php echo e(__('contact@egest.ma')); ?></button>
            <div class="or-text"><?php echo e(__('OR')); ?></div>
            <div class="text-xs text-muted text-center">
                <?php echo e(__("Back to")); ?> <a href="<?php echo e(route('login')); ?>"><?php echo e(__('Login')); ?></a>
            </div>
        </form>
    </div>
         </div>
     </div>
<?php $__env->stopSection(); ?>





<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/auth/forgot-password.blade.php ENDPATH**/ ?>