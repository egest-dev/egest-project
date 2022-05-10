<div class="card bg-none card-box">
    <?php echo e(Form::model($plan, array('route' => array('plans.update', $plan->id), 'method' => 'PUT', 'enctype' => "multipart/form-data"))); ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('name',__('Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter Plan Name'),'required'=>'required'))); ?>

        </div>
        <?php if($plan->price >0): ?>
            <div class="form-group col-md-6">
                <?php echo e(Form::label('price',__('Price'),['class'=>'form-control-label'])); ?>

                <?php echo e(Form::number('price',null,array('class'=>'form-control','placeholder'=>__('Enter Plan Price'),'required'=>'required'))); ?>

            </div>
        <?php endif; ?>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('duration', __('Duration'),['class'=>'form-control-label'])); ?>

            <?php echo Form::select('duration', $arrDuration, null,array('class' => 'form-control select2','required'=>'required')); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('max_users',__('Maximum Users'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('max_users',null,array('class'=>'form-control','required'=>'required'))); ?>

            <span class="small"><?php echo e(__('Note: "-1" for Unlimited')); ?></span>
        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('max_customers',__('Maximum Customers'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('max_customers',null,array('class'=>'form-control','required'=>'required'))); ?>

            <span class="small"><?php echo e(__('Note: "-1" for Unlimited')); ?></span>
        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('max_venders',__('Maximum Venders'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('max_venders',null,array('class'=>'form-control','required'=>'required'))); ?>

            <span class="small"><?php echo e(__('Note: "-1" for Unlimited')); ?></span>
        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('max_clients',__('Maximum Clients'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('max_clients',null,array('class'=>'form-control','required'=>'required'))); ?>

            <span class="small"><?php echo e(__('Note: "-1" for Unlimited')); ?></span>
        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('description', __('Description'),['class'=>'form-control-label'])); ?>

            <?php echo Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']); ?>

        </div>
        <div class="form-group col-md-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="enable_crm" id="enable_crm" <?php echo e($plan['crm'] == 1 ? 'checked="checked"' : ''); ?>>
                <label class="custom-control-label form-control-label" for="enable_crm"><?php echo e(__('CRM')); ?></label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="enable_project" id="enable_project" <?php echo e($plan['project'] == 1 ? 'checked="checked"' : ''); ?>>
                <label class="custom-control-label form-control-label" for="enable_project"><?php echo e(__('Project')); ?></label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="enable_hrm" id="enable_hrm" <?php echo e($plan['hrm'] == 1 ? 'checked="checked"' : ''); ?>>
                <label class="custom-control-label form-control-label" for="enable_hrm"><?php echo e(__('HRM')); ?></label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" name="enable_account" id="enable_account" <?php echo e($plan['account'] == 1 ? 'checked="checked"' : ''); ?>>
                <label class="custom-control-label form-control-label" for="enable_account"><?php echo e(__('Account')); ?></label>
            </div>
        </div>
        <div class="form-group col-md-12">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home/egestma/public_html/resources/views/plan/edit.blade.php ENDPATH**/ ?>