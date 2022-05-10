<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Deals')); ?> <?php if($pipeline): ?> - <?php echo e($pipeline->name); ?> <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css-page'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/summernote/summernote-bs4.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('assets/libs/summernote/summernote-bs4.js')); ?>"></script>
    <script>
        $(document).on("change", "#change-pipeline select[name=default_pipeline_id]", function () {
            $('#change-pipeline').submit();
        });
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="row d-flex justify-content-end">
        <?php if($pipeline): ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-12">
                <div class="all-select-box">
                    <div class="btn-box">
                        <?php echo e(Form::open(array('route' => 'deals.change.pipeline','id'=>'change-pipeline','class'=>'mr-2'))); ?>

                        <?php echo e(Form::select('default_pipeline_id', $pipelines,$pipeline->id, array('class' => 'form-control select2','id'=>'default_pipeline_id'))); ?>

                        <?php echo e(Form::close()); ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create deal')): ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-12">
                <div class="all-button-box">
                    <a href="#" data-url="<?php echo e(route('deals.create')); ?>" data-ajax-popup="true" data-size="lg" data-title="<?php echo e(__('Create Deal')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?></a>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-12">
            <div class="all-button-box">
                <a href="<?php echo e(route('deals.index')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-table"></i> <?php echo e(__('Kanban View')); ?> </a>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if($pipeline): ?>
        <div class="row">
            <div class="col">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0"><?php echo e(__('Total Deals')); ?></h5>
                    <h5 class="report-text mb-0"><?php echo e($cnt_deal['total']); ?></h5>
                </div>
            </div>
            <div class="col">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0"><?php echo e(__('This Month Total Deals')); ?></h5>
                    <h5 class="report-text mb-0"><?php echo e($cnt_deal['this_month']); ?></h5>
                </div>
            </div>
            <div class="col">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0"><?php echo e(__('This Week Total Deals')); ?></h5>
                    <h5 class="report-text mb-0"><?php echo e($cnt_deal['this_week']); ?></h5>
                </div>
            </div>
            <div class="col">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0"><?php echo e(__('Last 30 Days Total Deals')); ?></h5>
                    <h5 class="report-text mb-0"><?php echo e($cnt_deal['last_30days']); ?></h5>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body py-0">
                        <div class="table-responsive">
                            <table class="table table-striped dataTable">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                    <th><?php echo e(__('Stage')); ?></th>
                                    <th><?php echo e(__('Tasks')); ?></th>
                                    <th><?php echo e(__('Users')); ?></th>
                                    <?php if(Gate::check('edit deal') ||  Gate::check('delete deal')): ?>
                                        <th width="300px"><?php echo e(__('Action')); ?></th>
                                    <?php endif; ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(count($deals) > 0): ?>
                                    <?php $__currentLoopData = $deals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($deal->name); ?></td>
                                            <td><?php echo e(\Auth::user()->priceFormat($deal->price)); ?></td>
                                            <td><?php echo e($deal->stage->name); ?></td>
                                            <td><?php echo e(count($deal->tasks)); ?>/<?php echo e(count($deal->complete_tasks)); ?></td>
                                            <td>
                                                <?php $__currentLoopData = $deal->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                        <img alt="image" data-toggle="tooltip" data-original-title="<?php echo e($user->name); ?>" <?php if($user->avatar): ?> src="<?php echo e(asset('/storage/uploads/avatar/'.$user->avatar)); ?>" <?php else: ?> src="<?php echo e(asset('assets/img/avatar/avatar-1.png')); ?>" <?php endif; ?> class="rounded-circle " width="25" height="25">
                                                    </a>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </td>
                                            <?php if(\Auth::user()->type != 'Client'): ?>
                                                <td class="Action">
                                                    <span>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view deal')): ?>
                                                            <?php if($deal->is_active): ?>
                                                                <a href="<?php echo e(route('deals.show',$deal->id)); ?>" class="bg-warning edit-icon"><i class="fas fa-eye"></i></a>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit deal')): ?>
                                                            <a href="#" data-url="<?php echo e(URL::to('deals/'.$deal->id.'/edit')); ?>" data-size="lg" data-ajax-popup="true" data-title="<?php echo e(__('Edit Deal')); ?>" class="edit-icon" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>"><i class="fas fa-pencil-alt"></i></a>
                                                        <?php endif; ?>
                                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete deal')): ?>
                                                            <a href="#" data-title="<?php echo e(__('Delete Deal')); ?>" class="delete-icon" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($deal->id); ?>').submit();"><i class="fas fa-trash"></i></a>
                                                            <?php echo Form::open(['method' => 'DELETE', 'route' => ['deals.destroy', $deal->id],'id'=>'delete-form-'.$deal->id]); ?>

                                                            <?php echo Form::close(); ?>

                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr class="font-style">
                                        <td colspan="6" class="text-center"><?php echo e(__('No data available in table')); ?></td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/deals/list.blade.php ENDPATH**/ ?>