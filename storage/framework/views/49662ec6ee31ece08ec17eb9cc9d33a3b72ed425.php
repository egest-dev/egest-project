<?php $__env->startPush('script-page'); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Contract')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('title'); ?>
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0 "><?php echo e(__('Contract')); ?></h5>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Contract')); ?></li>
<?php $__env->stopSection(); ?>





<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="<?php echo e(route('contract.index')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                <span class="btn-inner--icon"><?php echo e(__('List View')); ?></span>
            </a>
        </div>


        <?php if(\Auth::user()->type=='company'): ?>

            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('contract.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Contract')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
            </div>
        <?php endif; ?>

    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('filter'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo e($contract->subject); ?></h6>
                            </div>
                            <?php if(\Auth::user()->type=='company'): ?>
                                <div class="text-right">
                                    <div class="actions">
                                        <div class="dropdown action-item">
                                            <a href="#" class="action-item" data-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a href="#" data-url="<?php echo e(route('contract.edit',$contract->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Contract')); ?>" class="dropdown-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>">
                                                    <?php echo e(__('Edit')); ?>

                                                </a>
                                                <a href="#!" class="dropdown-item" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-<?php echo e($contract->id); ?>').submit();">
                                                    <?php echo e(__('Delete')); ?>

                                                </a>
                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['contract.destroy', $contract->id],'id'=>'delete-form-'.$contract->id]); ?>

                                                <?php echo Form::close(); ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body py-3 flex-grow-1">

                        <p class="text-sm mb-0">
                            <?php echo e($contract->description); ?>

                        </p>
                    </div>
                    <div class="card-footer py-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <span class="form-control-label"><?php echo e(__('Contract Type')); ?>:</span>
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="badge badge-secondary badge-pill"><?php echo e(!empty($contract->types)?$contract->types->name:''); ?></span>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <span class="form-control-label"><?php echo e(__('Contract Value')); ?>:</span>
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="badge badge-secondary badge-pill"><?php echo e(\Auth::user()->priceFormat($contract->value)); ?></span>
                                    </div>
                                </div>
                            </li>
                            <?php if(\Auth::user()->type!='client'): ?>
                                <li class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <span class="form-control-label"><?php echo e(__('Client')); ?>:</span>
                                        </div>
                                        <div class="col-6 text-right">
                                            <?php echo e(!empty($contract->clients)?$contract->clients->name:''); ?>

                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                            <li class="list-group-item px-0">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <small><?php echo e(__('Start Date')); ?>:</small>
                                        <div class="h6 mb-0"><?php echo e(\Auth::user()->dateFormat($contract->start_date )); ?></div>
                                    </div>
                                    <div class="col-6">
                                        <small><?php echo e(__('End Date')); ?>:</small>
                                        <div class="h6 mb-0"><?php echo e(\Auth::user()->dateFormat($contract->end_date )); ?></div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/contract/grid.blade.php ENDPATH**/ ?>