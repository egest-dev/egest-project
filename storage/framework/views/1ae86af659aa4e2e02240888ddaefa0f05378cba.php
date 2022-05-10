<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Bug Report')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('assets/libs/dragula/dist/dragula.min.js')); ?>"></script>
    <script>
        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {

                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');
                        var stage_id = $(target).attr('data-id');

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);

                        $.ajax({
                            url: '<?php echo e(route('bug.kanban.order')); ?>',
                            type: 'POST',
                            data: {bug_id: id, status_id: stage_id, order: order, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('<?php echo e(__("Error")); ?>', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);
    </script>
    <script>
        $(document).on('click', '#form-comment button', function (e) {
            var comment = $.trim($("#form-comment textarea[name='comment']").val());
            var name = '<?php echo e(\Auth::user()->name); ?>';
            if (comment != '') {
                $.ajax({
                    url: $("#form-comment").data('action'),
                    data: {comment: comment, "_token": $('meta[name="csrf-token"]').attr('content')},
                    type: 'POST',
                    success: function (data) {
                        data = JSON.parse(data);
                        var html = "<li class='media mb-20'>" +
                            "                    <div class='media-body'>" +
                            "                    <div class='d-flex justify-content-between align-items-end'><div>" +
                            "                        <h5 class='mt-0'>" + name + "</h5>" +
                            "                        <p class='mb-0 text-xs'>" + data.comment + "</p></div>" +
                            "                           <div class='comment-trash' style=\"float: right\">" +
                            "                               <a href='#' class='btn btn-outline btn-sm text-danger delete-comment' data-url='" + data.deleteUrl + "' >" +
                            "                                   <i class='fa fa-trash'></i>" +
                            "                               </a>" +
                            "                           </div>" +
                            "                           </div>" +
                            "                    </div>" +
                            "                </li>";
                        $("#comments").prepend(html);
                        $("#form-comment textarea[name='comment']").val('');
                        show_toastr('<?php echo e(__("Success")); ?>', '<?php echo e(__("Comment Added Successfully!")); ?>', 'success');
                    },
                    error: function (data) {
                        show_toastr('<?php echo e(__("Error")); ?>', '<?php echo e(__("Some Thing Is Wrong!")); ?>', 'error');
                    }
                });
            } else {
                show_toastr('<?php echo e(__("Error")); ?>', '<?php echo e(__("Please write comment!")); ?>', 'error');
            }
        });

        $(document).on("click", ".delete-comment", function () {
            if (confirm('Are You Sure ?')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'JSON',
                    success: function (data) {
                        show_toastr('<?php echo e(__("Success")); ?>', '<?php echo e(__("Comment Deleted Successfully!")); ?>', 'success');
                        btn.closest('.media').remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('<?php echo e(__("Error")); ?>', data.message, 'error');
                        } else {
                            show_toastr('<?php echo e(__("Error")); ?>', '<?php echo e(__("Some Thing Is Wrong!")); ?>', 'error');
                        }
                    }
                });
            }
        });

        $(document).on('submit', '#form-file', function (e) {
            e.preventDefault();
            $.ajax({
                url: $("#form-file").data('url'),
                type: 'POST',
                data: new FormData(this),
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    show_toastr('<?php echo e(__("Success")); ?>', '<?php echo e(__("File Added Successfully!")); ?>', 'success');
                    var delLink = '';

                    $('.file_update').html('');
                    $('#file-error').html('');

                    if (data.deleteUrl.length > 0) {
                        delLink = "<a href='#' class='text-danger text-muted delete-comment-file'  data-url='" + data.deleteUrl + "'>" +
                            "                                        <i class='dripicons-trash'></i>" +
                            "                                    </a>";
                    }

                    var html = '<div class="col-8 mb-2 file-' + data.id + '">' +
                        '                                    <h5 class="mt-0 mb-1 font-weight-bold text-sm"> ' + data.name + '</h5>' +
                        '                                    <p class="m-0 text-xs">' + data.file_size + '</p>' +
                        '                                </div>' +
                        '                                <div class="col-4 mb-2 file-' + data.id + '">' +
                        '                                    <div class="comment-trash" style="float: right">' +
                        '                                        <a download href="<?php echo e(asset(Storage::url('bugs'))); ?>/' + data.file + '" class="btn btn-outline btn-sm text-primary m-0 px-2">' +
                        '                                            <i class="fa fa-download"></i>' +
                        '                                        </a>' +
                        '                                        <a href="#" class="btn btn-outline btn-sm red text-danger delete-comment-file m-0 px-2" data-id="' + data.id + '" data-url="' + data.deleteUrl + '">' +
                        '                                            <i class="fas fa-trash"></i>' +
                        '                                        </a>' +
                        '                                    </div>' +
                        '                                </div>';

                    $("#comments-file").prepend(html);
                },
                error: function (data) {
                    data = data.responseJSON;
                    if (data.message) {
                        $('#file-error').text(data.errors.file[0]).show();
                    } else {
                        show_toastr('<?php echo e(__("Error")); ?>', '<?php echo e(__("Some Thing Is Wrong!")); ?>', 'error');
                    }
                }
            });
        });

        $(document).on("click", ".delete-comment-file", function () {
            if (confirm('Are You Sure ?')) {
                var btn = $(this);
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'DELETE',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    dataType: 'JSON',
                    success: function (data) {
                        show_toastr('<?php echo e(__("Success")); ?>', '<?php echo e(__("File Deleted Successfully!")); ?>', 'success');
                        $('.file-' + btn.attr('data-id')).remove();
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('<?php echo e(__("Error")); ?>', data.message, 'error');
                        } else {
                            show_toastr('<?php echo e(__("Error")); ?>', '<?php echo e(__("Some Thing Is Wrong!")); ?>', 'error');
                        }
                    }
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <?php if($view == 'grid'): ?>
                <a href="<?php echo e(route('bugs.view', 'list')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <span class="btn-inner--text"><i class="fas fa-list"></i><?php echo e(__('List View')); ?></span>
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('bugs.view', 'grid')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <span class="btn-inner--text"><i class="fas fa-table"></i><?php echo e(__('Card View')); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage project')): ?>

                <a href="<?php echo e(route('projects.index')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <span class="btn-inner--icon"><i class="fas fa-arrow-left"></i><?php echo e(__('Back')); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="row">
                    <?php if(count($bugs) > 0): ?>
                        <?php $__currentLoopData = $bugs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-4 col-lg-3">
                                <div class="card m-3 card-progress border shadow-none" id="<?php echo e($bug->id); ?>" style="<?php echo e(!empty($bug->priority_color) ? 'border-left: 2px solid '.$bug->priority_color.' !important' :''); ?>;">
                                    <div class="card-body p-0">
                                        <div class="pl-2 pt-0 pr-2 pb-2">
                                            <div class="my-2">
                                      <span>
                                        <a href="<?php echo e(route('task.bug.kanban',$bug->project_id)); ?>" class="text-body h6"><?php echo e($bug->title); ?></a>
                                      </span>
                                                <?php if($bug->priority =='low'): ?>
                                                    <span class="font-weight-600 badge badge-xs badge-success"><?php echo e(ucfirst($bug->priority)); ?></span>
                                                <?php elseif($bug->priority =='medium'): ?>
                                                    <span class="font-weight-600 badge badge-xs badge-warning"><?php echo e(ucfirst($bug->priority)); ?></span>
                                                <?php elseif($bug->priority =='high'): ?>
                                                    <span class="font-weight-600 badge badge-xs badge-danger"><?php echo e(ucfirst($bug->priority)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-0">
                                                <span class="text-nowrap mb-2 d-inline-block text-xs"><?php echo e((!empty($bug->description)) ? $bug->description : '-'); ?></span>
                                            </p>
                                            <div class="row">
                                                <div class="col-6 text-xs">
                                                    <i class="far fa-clock"></i>
                                                    <span><?php echo e(\Auth::user()->dateFormat($bug->start_date)); ?></span>
                                                </div>
                                                <div class="col-6 text-right text-xs font-weight-bold">
                                                    <i class="far fa-clock"></i>
                                                    <span><?php echo e(\Auth::user()->dateFormat($bug->due_date)); ?></span>
                                                </div>
                                                <div class="col-12 pt-2">
                                                    <p class="mb-0">
                                                        <?php $users = $bug->users(); ?>

                                                        <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <img alt="image" data-toggle="tooltip" data-original-title="<?php echo e(!empty($user)? $user->name:''); ?>" <?php if(!empty($user) && $user->avatar): ?> src="<?php echo e(asset('/storage/uploads/avatar/'.!empty($user) ?$user->avatar:'avatar-1.png')); ?>" <?php else: ?> src="<?php echo e(asset('assets/img/avatar/avatar-1.png')); ?>" <?php endif; ?> class="rounded-circle " width="25" height="25">
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </a>

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="col-md-12">
                            <h6 class="text-center m-3"><?php echo e(__('No tasks found')); ?></h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/egestma/public_html/resources/views/projects/allBugGridView.blade.php ENDPATH**/ ?>