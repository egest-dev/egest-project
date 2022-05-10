<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('js/jquery-ui.min.js')); ?>"></script>
    <?php if(\Auth::user()->type=='company'): ?>
        <script>
            $(function () {
                $(".sortable").sortable();
                $(".sortable").disableSelection();
                $(".sortable").sortable({
                    stop: function () {
                        var order = [];
                        $(this).find('li').each(function (index, data) {
                            order[index] = $(data).attr('data-id');
                        });

                        $.ajax({
                            url: "<?php echo e(route('project-task-stages.order')); ?>",
                            data: {order: order, _token: $('meta[name="csrf-token"]').attr('content')},
                            type: 'POST',
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                toastr('Error', data.error, 'error')
                            }
                        })
                    }
                });
            });
        </script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Project Task Stages')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('action-button'); ?>
<div class="all-button-box row d-flex justify-content-end">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create project task stage')): ?>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="#" data-url="<?php echo e(route('project-task-stages.create')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="<?php echo e(__('Create Project Task Stage')); ?>">
                <i class="fa fa-plus"></i> <?php echo e(__('Create')); ?>

            </a>
        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="tab-content tab-bordered">
                <div class="tab-pane fade show active" role="tabpanel">
                    <ul class="list-group sortable">
                        <?php $__currentLoopData = $task_stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task_stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item" data-id="<?php echo e($task_stage->id); ?>">
                                <?php echo e($task_stage->name); ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit project task stage')): ?>
                                    <span class="float-right">
                                      <a href="#" data-url="<?php echo e(URL::to('project-task-stages/'.$task_stage->id.'/edit')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Bug Status')); ?>" class="edit-icon">
                                          <i class="fas fa-pencil-alt"></i>
                                      </a>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete project task stage')): ?>
                                      <a href="#!" class="delete-icon" data-toggle="tooltip" data-original-title="<?php echo e(__('Delete')); ?>" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="document.getElementById('delete-form-<?php echo e($task_stage->id); ?>').submit();">
                                            <i class="fas fa-trash"></i>
                                      </a>
                                            <?php echo Form::open(['method' => 'DELETE', 'route' => ['project-task-stages.destroy', $task_stage->id],'id'=>'delete-form-'.$task_stage->id]); ?>

                                            <?php echo Form::close(); ?>

                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
            <p class="text-muted mt-4"><strong><?php echo e(__('Note')); ?> : </strong><?php echo e(__('You can easily change order of project task stage using drag & drop.')); ?></p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/task_stage/index.blade.php ENDPATH**/ ?>