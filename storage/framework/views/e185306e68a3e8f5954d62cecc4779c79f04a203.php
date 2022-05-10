<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Proposal Detail')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).on('change', '.status_change', function () {
            var status = this.value;
            var url = $(this).data('url');
            $.ajax({
                url: url + '?status=' + status,
                type: 'GET',
                cache: false,
                success: function (data) {
                },
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send proposal')): ?>
        <?php if($proposal->status!=4): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="timeline timeline-one-side" data-timeline-content="axis" data-timeline-axis-style="dashed">
                                <div class="timeline-block">
                                    <span class="timeline-step timeline-step-sm bg-primary border-primary text-white"><i class="fas fa-plus"></i></span>
                                    <div class="timeline-content">
                                        <div class="text-sm h6"><?php echo e(__('Create Proposal')); ?></div>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit proposal')): ?>
                                            <div class="Action">
                                                <a href="<?php echo e(route('proposal.edit',\Crypt::encrypt($proposal->id))); ?>" class="edit-icon float-right" data-toggle="tooltip" data-original-title="<?php echo e(__('Edit')); ?>"><i class="fas fa-pencil-alt"></i></a>
                                            </div>
                                        <?php endif; ?>
                                        <small><i class="fas fa-clock mr-1"></i><?php echo e(__('Created on ')); ?> <?php echo e(\Auth::user()->dateFormat($proposal->issue_date)); ?></small>
                                    </div>
                                </div>
                                <div class="timeline-block">
                                    <span class="timeline-step timeline-step-sm bg-warning border-warning text-white"><i class="fas fa-envelope"></i></span>
                                    <div class="timeline-content">
                                        <div class="text-sm h6 "><?php echo e(__('Send Proposal')); ?></div>
                                        <?php if($proposal->status==0): ?>
                                            <div class="Action">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send proposal')): ?>
                                                    <a href="<?php echo e(route('proposal.sent',$proposal->id)); ?>" class="send-icon float-right" data-toggle="tooltip" data-original-title="<?php echo e(__('Mark Sent')); ?>"><i class="fa fa-paper-plane"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($proposal->status!=0): ?>
                                            <small><?php echo e(__('Sent on')); ?> <?php echo e(\Auth::user()->dateFormat($proposal->send_date)); ?></small>
                                        <?php else: ?>
                                            <small><?php echo e(__('Status')); ?> : <?php echo e(__('Not Sent')); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="timeline-block">
                                    <span class="timeline-step timeline-step-sm bg-info border-info text-white"><i class="far fa-money-bill-alt"></i></span>
                                    <div class="timeline-content">
                                        <span class="text-sm h6 "><?php echo e(__('Proposal Status')); ?></span>
                                        <div class="float-right" data-toggle="tooltip" data-original-title="<?php echo e(__('Click to change status')); ?>">
                                            <select class="form-control status_change select2" name="status" data-url="<?php echo e(route('proposal.status.change',$proposal->id)); ?>">
                                                <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($k); ?>" <?php echo e(($proposal->status==$k)?'selected':''); ?>><?php echo e($val); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <br>
                                        <small>
                                            <?php if($proposal->status == 0): ?>
                                                <span class="badge badge-pill badge-primary"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                            <?php elseif($proposal->status == 1): ?>
                                                <span class="badge badge-pill badge-info"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                            <?php elseif($proposal->status == 2): ?>
                                                <span class="badge badge-pill badge-success"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                            <?php elseif($proposal->status == 3): ?>
                                                <span class="badge badge-pill badge-warning"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                            <?php elseif($proposal->status == 4): ?>
                                                <span class="badge badge-pill badge-danger"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if(\Auth::user()->type=='company'): ?>
        <?php if($proposal->status!=0): ?>
            <div class="row justify-content-between align-items-center mb-3">
                <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                    <div class="all-button-box mx-2">
                        <a href="<?php echo e(route('proposal.resent',$proposal->id)); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><?php echo e(__('Resend Proposal')); ?></a>
                    </div>
                    <div class="all-button-box">
                        <a href="<?php echo e(route('proposal.pdf', Crypt::encrypt($proposal->id))); ?>" class="btn btn-xs btn-white btn-icon-only width-auto" target="_blank"><?php echo e(__('Download')); ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="row justify-content-between align-items-center mb-3">
            <div class="col-md-12 d-flex align-items-center justify-content-between justify-content-md-end">
                <div class="all-button-box">
                    <a href="<?php echo e(route('proposal.pdf', Crypt::encrypt($proposal->id))); ?>" class="btn btn-xs btn-white btn-icon-only width-auto" target="_blank"><?php echo e(__('Download')); ?></a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row invoice-title mt-2">
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12">
                                    <h2><?php echo e(Auth::user()->proposalNumberFormat($proposal->proposal_id)); ?></h2>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-nd-6 col-lg-6 col-12 text-right">

                                    <div class="float-right mr-3">
                                        <?php echo DNS2D::getBarcodeHTML( route('proposal.link.copy',\Illuminate\Support\Facades\Crypt::encrypt($proposal->id)), "QRCODE",2,2); ?>

                                    </div>

                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <?php if(!empty($customer->billing_name)): ?>
                                    <div class="col-md-6">
                                        <small class="font-style">
                                            <strong><?php echo e(__('Billed To')); ?> :</strong><br>
                                            <?php echo e(!empty($customer->billing_name)?$customer->billing_name:''); ?><br>
                                            <?php echo e(!empty($customer->billing_phone)?$customer->billing_phone:''); ?><br>
                                            <?php echo e(!empty($customer->billing_address)?$customer->billing_address:''); ?><br>
                                            <?php echo e(!empty($customer->billing_zip)?$customer->billing_zip:''); ?><br>
                                            <?php echo e(!empty($customer->billing_city)?$customer->billing_city:'' .', '); ?> <?php echo e(!empty($customer->billing_state)?$customer->billing_state:'',', '); ?> <?php echo e(!empty($customer->billing_country)?$customer->billing_country:''); ?><br>
                                            <strong><?php echo e(__('Tax Number ')); ?> : </strong><?php echo e(!empty($customer->tax_number)?$customer->tax_number:''); ?><br>

                                        </small>
                                    </div>
                                <?php endif; ?>

                                <?php if(\Utility::getValByName('shipping_display')=='on'): ?>
                                    <div class="col-md-6 text-md-right">
                                        <small>
                                            <strong><?php echo e(__('Shipped To')); ?> :</strong><br>
                                            <?php echo e(!empty($customer->shipping_name)?$customer->shipping_name:''); ?><br>
                                            <?php echo e(!empty($customer->shipping_phone)?$customer->shipping_phone:''); ?><br>
                                            <?php echo e(!empty($customer->shipping_address)?$customer->shipping_address:''); ?><br>
                                            <?php echo e(!empty($customer->shipping_zip)?$customer->shipping_zip:''); ?><br>
                                            <?php echo e(!empty($customer->shipping_city)?$customer->shipping_city:'' . ', '); ?> <?php echo e(!empty($customer->shipping_state)?$customer->shipping_state:'' .', '); ?>,<?php echo e(!empty($customer->shipping_country)?$customer->shipping_country:''); ?><br>
                                            <strong><?php echo e(__('Tax Number ')); ?> : </strong><?php echo e(!empty($customer->tax_number)?$customer->tax_number:''); ?><br>

                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <small>
                                        <strong><?php echo e(__('Status')); ?> :</strong><br>
                                        <?php if($proposal->status == 0): ?>
                                            <span class="badge badge-pill badge-primary"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                        <?php elseif($proposal->status == 1): ?>
                                            <span class="badge badge-pill badge-info"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                        <?php elseif($proposal->status == 2): ?>
                                            <span class="badge badge-pill badge-success"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                        <?php elseif($proposal->status == 3): ?>
                                            <span class="badge badge-pill badge-warning"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                        <?php elseif($proposal->status == 4): ?>
                                            <span class="badge badge-pill badge-danger"><?php echo e(__(\App\Models\Proposal::$statues[$proposal->status])); ?></span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="col text-md-right">
                                    <small>
                                        <strong><?php echo e(__('Issue Date')); ?> :</strong><br>
                                        <?php echo e(\Auth::user()->dateFormat($proposal->issue_date)); ?><br><br>
                                    </small>
                                </div>

                                <?php if(!empty($customFields) && count($proposal->customField)>0): ?>
                                    <?php $__currentLoopData = $customFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col text-md-right">
                                            <small>
                                                <strong><?php echo e($field->name); ?> :</strong><br>
                                                <?php echo e(!empty($proposal->customField)?$proposal->customField[$field->id]:'-'); ?>

                                                <br><br>
                                            </small>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="font-weight-bold"><?php echo e(__('Product Summary')); ?></div>
                                    <small><?php echo e(__('All items here cannot be deleted.')); ?></small>
                                    <div class="table-responsive mt-2">
                                        <table class="table mb-0 table-striped">
                                            <tr>
                                                <th class="text-dark" data-width="40">#</th>
                                                <th class="text-dark"><?php echo e(__('Product')); ?></th>
                                                <th class="text-dark"><?php echo e(__('Quantity')); ?></th>
                                                <th class="text-dark"><?php echo e(__('Rate')); ?></th>
                                                <th class="text-dark"><?php echo e(__('Tax')); ?></th>
                                                <th class="text-dark"> <?php if($proposal->discount_apply==1): ?><?php echo e(__('Discount')); ?><?php endif; ?></th>
                                                <th class="text-dark"><?php echo e(__('Description')); ?></th>
                                                <th class="text-right text-dark" width="12%"><?php echo e(__('Price')); ?><br>
                                                    <small class="text-danger font-weight-bold"><?php echo e(__('before tax & discount')); ?></small>
                                                </th>
                                            </tr>
                                            <?php
                                                $totalQuantity=0;
                                                $totalRate=0;
                                                $totalTaxPrice=0;
                                                $totalDiscount=0;
                                                $taxesData=[];
                                            ?>

                                            <?php $__currentLoopData = $iteams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>$iteam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(!empty($iteam->tax)): ?>
                                                    <?php
                                                        $taxes=\Utility::tax($iteam->tax);
                                                        $totalQuantity+=$iteam->quantity;
                                                        $totalRate+=$iteam->price;
                                                        $totalDiscount+=$iteam->discount;
                                                        foreach($taxes as $taxe){
                                                            $taxDataPrice=\Utility::taxRate($taxe->rate,$iteam->price,$iteam->quantity);
                                                            if (array_key_exists($taxe->name,$taxesData))
                                                            {
                                                                $taxesData[$taxe->name] = $taxesData[$taxe->name]+$taxDataPrice;
                                                            }
                                                            else
                                                            {
                                                                $taxesData[$taxe->name] = $taxDataPrice;
                                                            }
                                                        }
                                                    ?>
                                                <?php endif; ?>
                                                <tr>
                                                    <td><?php echo e($key+1); ?></td>
                                                    <td><?php echo e(!empty($iteam->product)?$iteam->product->name:''); ?></td>
                                                    <td><?php echo e($iteam->quantity); ?></td>
                                                    <td><?php echo e(\Auth::user()->priceFormat($iteam->price)); ?></td>
                                                    <td>
                                                        <?php if(!empty($iteam->tax)): ?>
                                                            <table>
                                                                <?php $totalTaxRate = 0;?>
                                                                <?php $__currentLoopData = $taxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tax): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $taxPrice=\Utility::taxRate($tax->rate,$iteam->price,$iteam->quantity);
                                                                        $totalTaxPrice+=$taxPrice;
                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo e($tax->name .' ('.$tax->rate .'%)'); ?></td>
                                                                        <td><?php echo e(\Auth::user()->priceFormat($taxPrice)); ?></td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </table>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($proposal->discount_apply==1): ?>
                                                            <?php echo e(\Auth::user()->priceFormat($iteam->discount)); ?>

                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e(!empty($iteam->description)?$iteam->description:'-'); ?></td>
                                                    <td class="text-right"><?php echo e(\Auth::user()->priceFormat(($iteam->price*$iteam->quantity))); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <tfoot>
                                            <tr>
                                                <td></td>
                                                <td><b><?php echo e(__('Total')); ?></b></td>
                                                <td><b><?php echo e($totalQuantity); ?></b></td>
                                                <td><b><?php echo e(\Auth::user()->priceFormat($totalRate)); ?></b></td>
                                                <td><b><?php echo e(\Auth::user()->priceFormat($totalTaxPrice)); ?></b></td>
                                                <td>
                                                    <?php if($proposal->discount_apply==1): ?>
                                                        <b><?php echo e(\Auth::user()->priceFormat($totalDiscount)); ?></b>
                                                    <?php endif; ?>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="text-right"><b><?php echo e(__('Sub Total')); ?></b></td>
                                                <td class="text-right"><?php echo e(\Auth::user()->priceFormat($proposal->getSubTotal())); ?></td>
                                            </tr>
                                            <?php if($proposal->discount_apply==1): ?>
                                                <tr>
                                                    <td colspan="6"></td>
                                                    <td class="text-right"><b><?php echo e(__('Discount')); ?></b></td>
                                                    <td class="text-right"><?php echo e(\Auth::user()->priceFormat($proposal->getTotalDiscount())); ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <?php if(!empty($taxesData)): ?>
                                                <?php $__currentLoopData = $taxesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxName => $taxPrice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td colspan="6"></td>
                                                        <td class="text-right"><b><?php echo e($taxName); ?></b></td>
                                                        <td class="text-right"><?php echo e(\Auth::user()->priceFormat($taxPrice)); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                            <tr>
                                                <td colspan="6"></td>
                                                <td class="blue-text text-right"><b><?php echo e(__('Total')); ?></b></td>
                                                <td class="blue-text text-right"><?php echo e(\Auth::user()->priceFormat($proposal->getTotal())); ?></td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/proposal/view.blade.php ENDPATH**/ ?>