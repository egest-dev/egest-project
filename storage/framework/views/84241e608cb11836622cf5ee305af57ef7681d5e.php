<div class="card bg-none card-box">
    <?php echo e(Form::open(array('url' => 'sources'))); ?>

    <div class="row">
        <div class="form-group col-12">
            <?php echo e(Form::label('name', __('Source Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name', '', array('class' => 'form-control','required'=>'required'))); ?>

        </div>
        <div class="col-12 pt-5 text-right">
            <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home/egestma/public_html/resources/views/sources/create.blade.php ENDPATH**/ ?>