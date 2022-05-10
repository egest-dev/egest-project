<?php $__env->startPush('script-page'); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Vendor-Detail')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="row d-flex justify-content-end">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create bill')): ?>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="all-button-box">
                    <a href="<?php echo e(route('bill.create',$vendor->id)); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                        <?php echo e(__('Create Bill')); ?>

                    </a>
                </div>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit vender')): ?>
            <div class="col-xl-1 col-lg-2 col-md-2 col-sm-6 col-6">
                <div class="all-button-box">
                    <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-size="2xl" data-url="<?php echo e(route('vender.edit',$vendor['id'])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Vendor')); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete vender')): ?>
            <div class="col-xl-1 col-lg-2 col-md-2 col-sm-6 col-6">
                <div class="all-button-box">
                    <a href="#" class="btn btn-xs btn-white bg-danger btn-icon-only width-auto" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($vendor['id']); ?>').submit();">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
            <?php echo Form::open(['method' => 'DELETE', 'route' => ['vender.destroy', $vendor['id']],'id'=>'delete-form-'.$vendor['id']]); ?>

            <?php echo Form::close(); ?>

        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-4 col-lg-4 col-xl-4">
            <div class="card pb-0 customer-detail-box">
                <h3 class="small-title"><?php echo e(__('Vendor Info')); ?></h3>
                <div class="p-4">
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->name); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->email); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->contact); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-xl-4">
            <div class="card pb-0 customer-detail-box">
                <h3 class="small-title"><?php echo e(__('Billing Info')); ?></h3>
                <div class="p-4">
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->billing_name); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->billing_phone); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->billing_address); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->billing_zip); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->billing_city.', '. $vendor->billing_state .', '.$vendor->billing_country); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-xl-4">
            <div class="card pb-0 customer-detail-box">
                <h3 class="small-title"><?php echo e(__('Shipping Info')); ?></h3>
                <div class="p-4">
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->shipping_name); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->shipping_phone); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->shipping_address); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->shipping_zip); ?></h5>
                    <h5 class="report-text gray-text mb-0"><?php echo e($vendor->shipping_city.', '. $vendor->billing_state .', '.$vendor->billing_country); ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card pb-0">
                <h3 class="small-title"><?php echo e(__('Company Info')); ?></h3>
                <div class="row">
                    <?php
                        $totalBillSum=$vendor->vendorTotalBillSum($vendor['id']);
                        $totalBill=$vendor->vendorTotalBill($vendor['id']);
                        $averageSale=($totalBillSum!=0)?$totalBillSum/$totalBill:0;
                    ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <h5 class="report-text gray-text mb-0"><?php echo e(__('Vendor Id')); ?></h5>
                            <h5 class="report-text mb-3"><?php echo e(\Auth::user()->venderNumberFormat($vendor->vender_id)); ?></h5>
                            <h5 class="report-text gray-text mb-0"><?php echo e(__('Total Sum of Bills')); ?></h5>
                            <h5 class="report-text mb-0"><?php echo e(\Auth::user()->priceFormat($totalBillSum)); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <h5 class="report-text gray-text mb-0"><?php echo e(__('Date of Creation')); ?></h5>
                            <h5 class="report-text mb-3"><?php echo e(\Auth::user()->dateFormat($vendor->created_at)); ?></h5>
                            <h5 class="report-text gray-text mb-0"><?php echo e(__('Quantity of Bills')); ?></h5>
                            <h5 class="report-text mb-0"><?php echo e($totalBill); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <h5 class="report-text gray-text mb-0"><?php echo e(__('Balance')); ?></h5>
                            <h5 class="report-text mb-3"><?php echo e(\Auth::user()->priceFormat($vendor->balance)); ?></h5>
                            <h5 class="report-text gray-text mb-0"><?php echo e(__('Average Sales')); ?></h5>
                            <h5 class="report-text mb-0"><?php echo e(\Auth::user()->priceFormat($averageSale)); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="p-4">
                            <h5 class="report-text gray-text mb-0"><?php echo e(__('Overdue')); ?></h5>
                            <h5 class="report-text mb-3"><?php echo e(\Auth::user()->priceFormat($vendor->vendorOverdue($vendor->id))); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h5 class="h4 d-inline-block font-weight-400 mb-4"><?php echo e(__('Bills')); ?></h5>
            <div class="card">
                <div class="card-body py-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 dataTable">
                            <thead>
                            <tr>
                                <th><?php echo e(__('Bill')); ?></th>
                                <th><?php echo e(__('Bill Date')); ?></th>
                                <th><?php echo e(__('Due Date')); ?></th>
                                <th><?php echo e(__('Due Amount')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <?php if(Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill')): ?>
                                    <th> <?php echo e(__('Action')); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>

                            <tbody>
                            <?php $__currentLoopData = $vendor->vendorBill($vendor->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="font-style">
                                    <td class="Id">
                                        <?php if(\Auth::guard('vender')->check()): ?>
                                            <a href="<?php echo e(route('vender.bill.show',\Crypt::encrypt($bill->id))); ?>"><?php echo e(AUth::user()->billNumberFormat($bill->bill_id)); ?>

                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('bill.show',\Crypt::encrypt($bill->id))); ?>"><?php echo e(AUth::user()->billNumberFormat($bill->bill_id)); ?>

                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(Auth::user()->dateFormat($bill->bill_date)); ?></td>
                                    <td>
                                        <?php if(($bill->due_date < date('Y-m-d'))): ?>
                                            <p class="text-danger"> <?php echo e(\Auth::user()->dateFormat($bill->due_date)); ?></p>
                                        <?php else: ?>
                                            <?php echo e(\Auth::user()->dateFormat($bill->due_date)); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(\Auth::user()->priceFormat($bill->getDue())); ?></td>
                                    <td>
                                        <?php if($bill->status == 0): ?>
                                            <span class="badge badge-pill badge-primary"><?php echo e(__(\App\Models\Invoice::$statues[$bill->status])); ?></span>
                                        <?php elseif($bill->status == 1): ?>
                                            <span class="badge badge-pill badge-warning"><?php echo e(__(\App\Models\Invoice::$statues[$bill->status])); ?></span>
                                        <?php elseif($bill->status == 2): ?>
                                            <span class="badge badge-pill badge-danger"><?php echo e(__(\App\Models\Invoice::$statues[$bill->status])); ?></span>
                                        <?php elseif($bill->status == 3): ?>
                                            <span class="badge badge-pill badge-info"><?php echo e(__(\App\Models\Invoice::$statues[$bill->status])); ?></span>
                                        <?php elseif($bill->status == 4): ?>
                                            <span class="badge badge-pill badge-success"><?php echo e(__(\App\Models\Invoice::$statues[$bill->status])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if(Gate::check('edit bill') || Gate::check('delete bill') || Gate::check('show bill')): ?>
                                        <td class="Action">
                                            <span>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('duplicate bill')): ?>
                                                    <a href="#" class="edit-icon bg-success" data-toggle="tooltip" data-original-title="<?php echo e(__('Duplicate')); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back" data-confirm-yes="document.getElementById('duplicate-form-<?php echo e($bill->id); ?>').submit();">
                                                    <i class="fas fa-copy"></i>
                                                    <?php echo Form::open(['method' => 'get', 'route' => ['bill.duplicate', $bill->id],'id'=>'duplicate-form-'.$bill->id]); ?>

                                                        <?php echo Form::close(); ?>

                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show bill')): ?>
                                                    <?php if(\Auth::guard('vender')->check()): ?>
                                                        <a href="<?php echo e(route('vender.bill.show',\Crypt::encrypt($bill->id))); ?>" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="<?php echo e(__('Detail')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('bill.show',\Crypt::encrypt($bill->id))); ?>" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="<?php echo e(__('Detail')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit bill')): ?>
                                                    <a href="<?php echo e(route('bill.edit',\Crypt::encrypt($bill->id))); ?>" class="edit-icon" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete bill')): ?>
                                                    <a href="#" class="delete-icon " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($bill->id); ?>').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['bill.destroy', $bill->id],'id'=>'delete-form-'.$bill->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                <?php endif; ?>
                                            </span>
                                        </td>
                                    <?php endif; ?>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/vender/show.blade.php ENDPATH**/ ?>