@extends('layouts.container')

@section('title', 'Edit Exit Hand Over Note')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#update-employees-exit-menu').addClass('active');
            const form = document.getElementById('exitHandOverNoteEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    duty_description: {
                        validators: {
                            notEmpty: {
                                message: 'Duty Description is required',
                            },
                        },
                    },
                    reporting_procedures: {
                        validators: {
                            notEmpty: {
                                message: 'Reporting Procedures is required',
                            },
                        },
                    },

                    meeting_description: {
                        validators: {
                            notEmpty: {
                                message: 'Meeting Description is required',
                            },
                        },
                    },
                    contact_after_exit: {
                        validators: {
                            notEmpty: {
                                message: 'Contact After Exit is required',
                            },
                            regexp: {
                                regexp: /^(?:[9][7-8]\d{8}|[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,})$/,
                                message: 'Please enter a valid phone number or email address.',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });
        });

        var projectTable = $('#handoverProjectTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'project',
                    name: 'project'
                },
                {
                    data: 'action_needed',
                    name: 'action_needed'
                },
                {
                    data: 'partners',
                    name: 'partners'
                },
                {
                    data: 'budget',
                    name: 'budget'
                },
                {
                    data: 'critical_issues',
                    name: 'critical_issues'
                },
                {
                    data: 'project_status',
                    name: 'project_status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ]
        });

        $(document).on('click', '.open-handover-project-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('exitProjectForm');
                $(form).find(".select2").each(function() {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        //project_code_id: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Project  code is required',
                        //        },
                        //    },
                        //},
                        //project_status: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Project Status is required',
                        //        },
                        //    },
                        //},
                        //action_needed: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Action Needed is required',
                        //        },
                        //    },
                        //},
                        //partners: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Partners code is required',
                        //        },
                        //    },
                        //},
                        //critical_issues: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Critical Issues code is required',
                        //        },
                        //    },
                        //},
                        //budget: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Budget  is required',
                        //        },
                        //        greaterThan: {
                        //            message: 'The value must be greater than or equal to 0.01',
                        //            min: 0.01,
                        //        },
                        //    },
                        //},
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        projectTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
            });
        });

        $('#handoverProjectTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                projectTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        // $(document).on('click', '.open-activity-modal-form', function(e) {
        //     e.preventDefault();
        //     $('#activityModal').modal('show').find('.modal-content').load($(this).attr('href'));
        // });

        $(document).on('click', '.open-activity-modal-form', function(e) {
            e.preventDefault();
            $('#activityModal').find('.modal-content').html('');
            $('#activityModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const activityForm = document.getElementById('activityForm');
                $(activityForm).find(".select2").each(function() {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });
                const activityFv = FormValidation.formValidation(activityForm, {
                    fields: {
                        //activity_code_id: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Activity  code is required',
                        //        },
                        //    },
                        //},
                        //organization: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Organization is required',
                        //        },
                        //    },
                        //},
                        //email: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Email is required',
                        //        },
                        //        emailAddress: {
                        //            message: 'Please enter valid email address.',
                        //        }
                        //    },
                        //},
                        //phone: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Phone is required',
                        //        },
                        //        regexp: {
                        //            regexp: /^[9][7-8]\d{8}$/,
                        //            message: 'The phone number is not valid',
                        //        },
                        //    },
                        //},
                        //comments: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'Comment is required',
                        //        },
                        //    },
                        //},
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    $url = activityFv.form.action;
                    $form = activityFv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#activityModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        activityTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(activityForm).on('change', '[name="activity_code_id"]', function(e) {
                    activityFv.revalidateField('activity_code_id');
                });
            });
        });

        var activityTable = $('#handoverActivityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('activity.exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                {
                    data: 'organization',
                    name: 'organization'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'comments',
                    name: 'comments'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ]
        });

        $('#handoverActivityTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                activityTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        // $(document).on('click', '.open-document-modal-form', function(e) {
        //     e.preventDefault();
        //     $('#documentModal').modal('show').find('.modal-content').load($(this).attr('href'));
        // });

        $(document).on('click', '.open-document-modal-form', function(e) {
            e.preventDefault();
            $('#documentModal').find('.modal-content').html('');
            $('#documentModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const documentForm = document.getElementById('documentForm');
                const documentFv = FormValidation.formValidation(documentForm, {
                    fields: {
                        attachment_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Attachment name is required',
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                notEmpty: {
                                    message: 'Attachment is required',
                                },
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.',
                                },
                            },
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    $url = documentFv.form.action;
                    $form = documentFv.form;
                    data = $($form).serialize();
                    var formData = new FormData();
                    $('#documentForm input, #documentForm select, #documentForm textarea').each(
                        function(
                            index) {
                            var input = $(this);
                            formData.append(input.attr('name'), input.val());
                        });
                    var attachmentFiles = documentForm.querySelector('[name="attachment"]').files;
                    if (attachmentFiles.length > 0) {
                        formData.append('attachment', attachmentFiles[0]);
                    }

                    var successCallback = function(response) {
                        $('#documentModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        documentTable.ajax.reload();
                    }
                    ajaxSubmitFormData($url, 'POST', formData, successCallback);
                });

                $(documentFv).on('change', '[name="activity_code_id"]', function(e) {
                    documentFv.revalidateField('activity_code_id');
                });
            });
        });

        var documentTable = $('#handoverDocumentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('document.exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'attachment_name',
                    name: 'attachment_name'
                },
                {
                    data: 'attachment_type',
                    name: 'attachment_type'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ]
        });

        $('#handoverDocumentTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                documentTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">@yield('title')</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                {{-- <div class="add-info justify-content-end">
                     <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                             class="bi-wrench-adjustable me-1"></i> New Employee Exit</button>
                 </div> --}}
            </div>
        </div>
        <section class="registration">

            <div class="row">
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                        <ul class="m-0 list-unstyled">
                            <li class="nav-item">
                                <a href="@if ($authUser->can('update', $exitHandOverNote)) {{ route('exit.employee.handover.note.edit') }} @else
                                    {{ route('exit.handover.note.requests.show') }} @endif"
                                    class="nav-link active text-decoration-none"><i class="nav-icon bi-info-circle"></i>
                                    Handover Note</a>
                            </li>
                            <li class="nav-item"><a
                                href="@if($authUser->can('update', $exitAssetHandover)) {{route('exit.employee.handover.asset.edit')}} @else {{route('exit.employee.handover.asset.show')}} @endif"
                                class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i>
                                Asset Handover</a></li>
                            <li class="nav-item"><a
                                    href= "@if ($authUser->can('update', $exitInterview)) {{ route('exit.employee.interview.edit') }} @else
                                    {{ route('exit.employee.interview.show') }} @endif"
                                    class="nav-link text-decoration-none"><i class="nav-icon bi-people"></i> Exit
                                    interview</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <form action="{{ route('exit.employee.handover.note.update', $exitHandOverNote->id) }}"
                            id="exitHandOverNoteEditForm" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-lg-3">
                                        <label for=""> Brief description of duties</label>
                                    </div>

                                    <div class="col-lg-9 ">
                                        <textarea rows="3" class="form-control" name="duty_description">{{ old('duty_description') ? old('duty_description') : $exitHandOverNote->duty_description }}</textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-lg-3">
                                        <label for=""> Reporting procedures</label>
                                    </div>

                                    <div class="col-lg-9 ">
                                        <textarea rows="3" class="form-control" name="reporting_procedures">{{ old('reporting_procedures') ? old('reporting_procedures') : $exitHandOverNote->reporting_procedures }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3">
                                        <label for=""> Regular/re-occurring meetings </label>
                                    </div>

                                    <div class="col-lg-9 ">
                                        <textarea rows="3" class="form-control" name="meeting_description">{{ old('meeting_description') ? old('meeting_description') : $exitHandOverNote->meeting_description }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3">
                                        <label for=""> Contact After Exit </label>
                                    </div>

                                    <div class="col-lg-9 ">
                                        <input type="text" name="contact_after_exit"
                                            value="{{ old('contact_after_exit') ? old('contact_after_exit') : $exitHandOverNote->contact_after_exit }}"
                                            class="form-control">
                                        @if ($errors->has('contact_after_exit'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="contact_after_exit">
                                                    {!! $errors->first('contact_after_exit') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Send To</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        @php $selectedSupervisorId = old('approver_id') ?: $exitHandOverNote->approver_id; @endphp
                                        <select name="approver_id"
                                            class="select2 form-control
                                            @if ($errors->has('approver_id')) is-invalid @endif"
                                            data-width="100%">
                                            <option value="">Select an approver</option>
                                            @foreach ($supervisors as $supervisor)
                                                <option value="{{ $supervisor->id }}"
                                                    {{ $supervisor->id == $selectedSupervisorId ? 'selected' : '' }}>
                                                    {{ $supervisor->full_name }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('approver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="approver_id">
                                                    {!! $errors->first('approver_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                {!! csrf_field() !!}
                                {!! method_field('PUT') !!}

                                <div class="row mb-3">
                                    <div class="py-2 d-flex ">
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <span class="fw-bold">@lang('label.project-status')</span>
                                            <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-handover-project-modal-form"
                                                href="{!! route('project.exit.handover.note.create', [$exitHandOverNote->id]) !!}"><i class="bi-plus"></i> Add New HandOver
                                                Project
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-borderedless" id="handoverProjectTable">
                                        <thead class="bg-light">
                                            <tr>

                                                <!-- <th style="width:45px;"></th> -->
                                                <th class="">Name of project</th>
                                                <th>Action needed</th>
                                                <th>Partners</th>
                                                <th>Budget</th>
                                                <th>Critical issues</th>
                                                <th>Status</th>
                                                <th style="width: 130px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mb-3">
                                    <div class="py-2 d-flex ">
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <span class="fw-bold">Major Activities</span>
                                            <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-activity-modal-form"
                                                href="{!! route('exit.handover.activity.note.create', [$exitHandOverNote->id]) !!}"><i class="bi-plus"></i> Add New HandOver
                                                Activity
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-borderedless" id="handoverActivityTable">
                                        <thead class="bg-light">
                                            <tr>

                                                <!-- <th style="width:45px;"></th> -->
                                                <th class="">Name</th>
                                                <th>Organization</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Comments</th>
                                                <th style="width: 130px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mb-3">
                                    <div class="py-2 d-flex ">
                                        <div class="d-flex justify-content-between flex-grow-1">
                                            <span class="fw-bold">Attachments</span>
                                            <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-document-modal-form"
                                                href="{!! route('document.exit.handover.note.create', [$exitHandOverNote->id]) !!}"><i class="bi-plus"></i> Add New Documents
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive mb-3">
                                    <table class="table table-borderedless" id="handoverDocumentTable">
                                        <thead class="bg-light">
                                            <tr>

                                                <!-- <th style="width:45px;"></th> -->
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Document</th>
                                                <th style="width: 130px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <span class="fw-bold">Suggested attachments:</span>
                                    <ul>
                                        <li>TOR/Job description</li>
                                        <li>Key documents relevant for the position</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="save"
                                    class="btn btn-primary btn-sm">Update
                                </button>
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="activityModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="activityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <div class="modal fade" id="documentModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
@stop
