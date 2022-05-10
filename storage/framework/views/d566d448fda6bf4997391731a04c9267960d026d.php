<div class="card bg-none card-box">
<?php echo e(Form::open(array('route' => array('invoice.payment', $invoice->id),'method'=>'post','enctype' => 'multipart/form-data'))); ?>

<div class="row">
    <div class="form-group  col-md-6">
        <?php echo e(Form::label('date', __('Date'))); ?>

        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            <?php echo e(Form::text('date', '', array('class' => 'form-control datepicker','required'=>'required'))); ?>

        </div>
    </div>
    <div class="form-group  col-md-6">
        <?php echo e(Form::label('amount', __('Amount'))); ?>

        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-money-bill-alt"></i>
                </div>
            </div>
            <?php echo e(Form::text('amount',$invoice->getDue(), array('class' => 'form-control','required'=>'required'))); ?>

        </div>
    </div>
    <div class="form-group  col-md-6">
        <div class="input-group">
            <?php echo e(Form::label('account_id', __('Account'))); ?>

            <?php echo e(Form::select('account_id',$accounts,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
    </div>

    <div class="form-group  col-md-6">
        <?php echo e(Form::label('reference', __('Reference'))); ?>

        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <i class="far fa-sticky-note"></i>
                </div>
            </div>
            <?php echo e(Form::text('reference', '', array('class' => 'form-control'))); ?>

        </div>
    </div>
    <div class="form-group  col-md-12">
        <?php echo e(Form::label('description', __('Description'))); ?>

        <?php echo e(Form::textarea('description', '', array('class' => 'form-control','rows'=>3))); ?>

    </div>

    <div class="col-sm-12 col-md-12">
        <div class="form-group">
            <?php echo e(Form::label('add_receipt', __('Payment Receipt'), ['class' => 'form-control-label'])); ?>

            <input type="file" name="add_receipt" id="image" class="custom-input-file" accept="image/*, .txt, .rar, .zip" >
            <label for="image">
                <i class="fa fa-upload"></i>
                <span>Choose a file…</span>
            </label>
        </div>
    </div>
    <div class="col-md-12 px-0">
        <input type="submit" value="<?php echo e(__('Add')); ?>" class="btn-create badge-blue">
        <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
    </div>
</div>
<?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home/egestma/public_html/resources/views/invoice/payment.blade.php ENDPATH**/ ?>