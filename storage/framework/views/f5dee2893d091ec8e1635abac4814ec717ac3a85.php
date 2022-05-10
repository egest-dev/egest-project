<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Sources')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create source')): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('sources.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create Source')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-0">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable">
                            <thead>
                            <tr>
                                <th><?php echo e(__('Source')); ?></th>
                                <th width="250px"><?php echo e(__('Action')); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($source->name); ?></td>
                                    <td class="Active">
                                        <span>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit source')): ?>
                                                <a href="#" data-url="<?php echo e(URL::to('sources/'.$source->id.'/edit')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Source')); ?>" class="edit-icon" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>"><i class="fas fa-pencil-alt"></i></a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete source')): ?>
                                                <a href="#" class="delete-icon" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($source->id); ?>').submit();"><i class="fas fa-trash"></i></a>
                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['sources.destroy', $source->id],'id'=>'delete-form-'.$source->id]); ?>

                                                <?php echo Form::close(); ?>

                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/sources/index.blade.php ENDPATH**/ ?>