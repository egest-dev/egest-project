<div class="card bg-none card-box">
    <?php echo e(Form::model($taskStage, array('route' => array('project-task-stages.update', $taskStage->id), 'method' => 'PUT'))); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Project Task Stage Title'),['class'=>'form-control-label'])); ?>

                <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter project stage title')))); ?>

            </div>
        </div>
        <div class="form-group col-12">
            <?php echo e(Form::label('color', __('Color'),['class'=>'form-control-label'])); ?>

            <input class="jscolor form-control " value="<?php echo e($taskStage->color); ?>" name="color" id="color" required>
            <small class="small"><?php echo e(__('For chart representation')); ?></small>
        </div>
        <div class="col-12">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home/egestma/public_html/resources/views/task_stage/edit.blade.php ENDPATH**/ ?>