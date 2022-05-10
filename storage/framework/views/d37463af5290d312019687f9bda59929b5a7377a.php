<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Delivery Note')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <script>


        $('.copy_link').click(function (e) {
            e.preventDefault();
            var copyText = $(this).attr('href');


            document.addEventListener('copy', function (e) {
                e.clipboardData.setData('text/plain', copyText);
                e.preventDefault();
            }, true);

            document.execCommand('copy');
            show_toastr('Success', 'Url copied to clipboard', 'success');
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create delivery note')): ?>
        <div class="col-0" >
            <?php if(!\Auth::guard('customer')->check()): ?>
                <?php echo e(Form::open(array('route' => array('delivery-note.index'),'method' => 'GET','id'=>'customer_submit'))); ?>

            <?php else: ?>
                <?php echo e(Form::open(array('route' => array('customer.invoice'),'method' => 'GET','id'=>'customer_submit'))); ?>

            <?php endif; ?>
        </div>
        <div class="row d-flex justify-content-end">
          <div class="col-2">
            <div class="all-select-box">
                <div class="btn-box">
                    <?php echo e(Form::label('issue_date', __('Date'),['class'=>'text-type'])); ?>

                    <?php echo e(Form::text('issue_date', isset($_GET['issue_date'])?$_GET['issue_date']:null, array('class' => 'form-control month-btn datepicker-range'))); ?>

                </div>
            </div>
          </div>

          <?php if(!\Auth::guard('customer')->check()): ?>
              <div class="col-auto">
                  <div class="all-select-box">
                      <div class="btn-box">
                          <?php echo e(Form::label('customer', __('Customer'),['class'=>'text-type'])); ?>

                          <?php echo e(Form::select('customer',$customer,isset($_GET['customer'])?$_GET['customer']:'', array('class' => 'form-control select2'))); ?>

                      </div>
                  </div>
              </div>
          <?php endif; ?>

          <div class="col-2">
              <div class="all-select-box">
                  <div class="btn-box">
                      <?php echo e(Form::label('status', __('Status'),['class'=>'text-type'])); ?>

                      <?php echo e(Form::select('status', [''=>'All']+$status,isset($_GET['status'])?$_GET['status']:'', array('class' => 'form-control select2'))); ?>

                  </div>
              </div>
          </div>
          <div class="col-auto my-custom">
              <a href="#" class="apply-btn" onclick="document.getElementById('customer_submit').submit(); return false;" data-toggle="tooltip" data-original-title="<?php echo e(__('apply')); ?>">
                  <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
              </a>
              <?php if(!\Auth::guard('customer')->check()): ?>
                  <a href="<?php echo e(route('delivery-note.index')); ?>" class="reset-btn" data-toggle="tooltip" data-original-title="<?php echo e(__('Reset')); ?>">
                      <span class="btn-inner--icon"><i class="fas fa-trash-restore-alt"></i></span>
                  </a>
              <?php else: ?>
                  <a href="<?php echo e(route('customer.index')); ?>" class="reset-btn" data-toggle="tooltip" data-original-title="<?php echo e(__('Reset')); ?>">
                      <span class="btn-inner--icon"><i class="fas fa-trash-restore-alt"></i></span>
                  </a>
              <?php endif; ?>
          </div>
          <?php echo e(Form::close()); ?>

          <div class="col-2 my-custom-btn">
              <div class="all-button-box">
                  <a href="<?php echo e(route('delivery-note.create',0)); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                      <i class="fas fa-plus"></i> <?php echo e(__('Create')); ?>

                  </a>
              </div>
          </div>
            <div class="col-2 my-custom-btn">
                <div class="all-button-box">
                    <a href="<?php echo e(route('delivery-note.export')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                        <i class="fa fa-file-excel"></i> <?php echo e(__('Export')); ?>

                    </a>
                </div>
            </div>
      </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <div class="">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body py-0 mt-2">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 dataTable">
                            <thead>
                            <tr>
                                <th> <?php echo e(__('Delivery Note')); ?></th>
                                <?php if(!\Auth::guard('customer')->check()): ?>
                                    <th><?php echo e(__('Customer')); ?></th>
                                <?php endif; ?>
                                <th><?php echo e(__('Issue Date')); ?></th>
                                <th><?php echo e(__('Due Date')); ?></th>
                                <th><?php echo e(__('Due Amount')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <?php if(Gate::check('edit delivery note') || Gate::check('delete delivery note') || Gate::check('show delivery note')): ?>
                                    <th><?php echo e(__('Action')); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>

                            <tbody>
                            <?php $__currentLoopData = $deliveryNotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deliveryNote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="Id">



                                            <a href="<?php echo e(route('delivery-note.show',\Crypt::encrypt($deliveryNote->id))); ?>"><?php echo e(AUth::user()->deliveryNoteNumberFormat($deliveryNote->invoice_id)); ?></a>

                                    </td>



                                    <td><?php echo e(Auth::user()->dateFormat($deliveryNote->issue_date)); ?></td>
                                    <td>
                                        <?php if(($deliveryNote->due_date < date('Y-m-d'))): ?>
                                            <p class="text-danger"> <?php echo e(\Auth::user()->dateFormat($deliveryNote->due_date)); ?></p>
                                        <?php else: ?>
                                            <?php echo e(\Auth::user()->dateFormat($deliveryNote->due_date)); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e(\Auth::user()->priceFormat($deliveryNote->getDue())); ?></td>
                                    <td>
                                        <?php if($deliveryNote->status == 0): ?>
                                            <span class="badge badge-pill badge-primary"><?php echo e(__(\App\Models\DeliveryNote::$statues[$deliveryNote->status])); ?></span>
                                        <?php elseif($deliveryNote->status == 1): ?>
                                            <span class="badge badge-pill badge-warning"><?php echo e(__(\App\Models\DeliveryNote::$statues[$deliveryNote->status])); ?></span>
                                        <?php elseif($deliveryNote->status == 2): ?>
                                            <span class="badge badge-pill badge-danger"><?php echo e(__(\App\Models\DeliveryNote::$statues[$deliveryNote->status])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if(Gate::check('edit delivery note') || Gate::check('delete delivery note') || Gate::check('show delivery note')): ?>
                                        <td class="Action">
                                            <span><?php $deliveryNoteID= Crypt::encrypt($deliveryNote->id); ?>



                                              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('duplicate delivery note')): ?>
                                              <a href="#" class="edit-icon bg-success" data-toggle="tooltip" data-original-title="<?php echo e(__('Duplicate')); ?>" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="You want to confirm this action. Press Yes to continue or Cancel to go back" data-confirm-yes="document.getElementById('duplicate-form-<?php echo e($deliveryNote->id); ?>').submit();">
                                                    <i class="fas fa-copy"></i>
                                                    <?php echo Form::open(['method' => 'get', 'route' => ['delivery-note.duplicate', $deliveryNote->id],'id'=>'duplicate-form-'.$deliveryNote->id]); ?>

                                                        <?php echo Form::close(); ?>

                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show delivery note')): ?>
                                                    <?php if(\Auth::guard('customer')->check()): ?>

                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('delivery-note.show',\Crypt::encrypt($deliveryNote->id))); ?>" class="edit-icon bg-info" data-toggle="tooltip" data-original-title="<?php echo e(__('Detail')); ?>">
                                                        <i class="fas fa-eye"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit delivery note')): ?>
                                                    <a href="<?php echo e(route('delivery-note.edit',Crypt::encrypt($deliveryNote->id))); ?>" class="edit-icon" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete delivery note')): ?>
                                                    <a href="#" class="delete-icon " data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($deliveryNote->id); ?>').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['delivery-note.destroy', $deliveryNote->id],'id'=>'delete-form-'.$deliveryNote->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/deliverynote/index.blade.php ENDPATH**/ ?>