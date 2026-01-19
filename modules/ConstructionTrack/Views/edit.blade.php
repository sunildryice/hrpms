@extends('layouts.container')

@section('title', 'Edit Construction Track')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#construction-index').addClass('active');

            const form = document.getElementById('constructionAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    signed_date: {
                        validators: {
                            notEmpty: {
                                message: 'Signed date is required.'
                            }
                        }
                    },
                    health_facility_name: {
                        validators: {
                            notEmpty: {
                                message: 'Health facility name is required.'
                            }
                        }
                    },
                    facility_type: {
                        validators: {
                            notEmpty: {
                                message: 'Facility type is required.'
                            }
                        }
                    },
                    type_of_work: {
                        validators: {
                            notEmpty: {
                                message: 'Type of work is required.'
                            }
                        }
                    },
                    province_id: {
                        validators: {
                            notEmpty: {
                                message: 'Province is required',
                            },
                        },
                    },
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'District is required',
                            },
                        },
                    },
                    local_level_id: {
                        validators: {
                            notEmpty: {
                                message: 'Local Level is required',
                            },
                        },
                    },
                    engineer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Engineer name is required.'
                            }
                        }
                    },
                    effective_date_from: {
                        validators: {
                            notEmpty: {
                                message: 'Effective Date From is required',
                            },
                        },
                    },
                    effective_date_to: {
                        validators: {
                            notEmpty: {
                                message: 'Effective Date To is required',
                            },
                        },
                    },
                    ohw_contribution: {
                        validators: {
                            notEmpty: {
                                message: 'Ohw Contribution Amount is required',
                            },
                            // lessThan: {
                            //     message: 'The value must be less than or equal to 100',
                            //     max: 100,
                            // },
                            greaterThan: {
                                message: 'The value must be greater than or equal to 0',
                                min: 0,
                            }
                        },
                    },
                    work_start_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    work_completion_date: {
                        validators: {
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
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

                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'effective_date_from',
                            message: 'Effective Date From must be a valid date and earlier than Effective Date To.',
                        },
                        endDate: {
                            field: 'effective_date_to',
                            message: 'Effective Date To must be a valid date and later than Effective Date From.',
                        },
                    }),

                    signedDateEffectiveDateFrom: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'signed_date',
                            message: 'Signed Date must be a valid date and earlier than Effective Date From.',
                        },
                        endDate: {
                            field: 'effective_date_from',
                            message: 'Effective Date From must be a valid date and later than signed date.',
                        },
                    }),

                    workStartDateWorkCompletionDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'work_start_date',
                            message: 'Work start date must be a valid date and earlier than work completion date.',
                        },
                        endDate: {
                            field: 'work_completion_date',
                            message: 'Work end date must be a valid date and later than work start date.',
                        },
                    }),

                    signedDateWorkStartDateFrom: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'signed_date',
                            message: 'Signed date must be a valid date and earlier than work start date.',
                        },
                        endDate: {
                            field: 'work_start_date',
                            message: 'Work start date must be a valid date and later than signed date.',
                        },
                    }),
                },
            });

            $(form).on('change', '[name="district_id"]', function (e) {
                $element = $(this);
                var districtId = $element.val();
                var htmlToReplace = '<option value="">Select a Local Level</option>';
                if (districtId) {
                    var url = baseUrl + '/api/master/districts/' + districtId;
                    var successCallback = function (response) {
                        response.localLevels.forEach(function (localLevel) {
                            htmlToReplace += '<option value="' + localLevel.id + '">' +
                                localLevel.local_level_name + '</option>';
                        });
                        $($element).closest('form').find('[name="local_level_id"]').html(htmlToReplace)
                            .trigger('change');
                        $($element).closest('form').find('[name="local_level_id"]').select2('destroy')
                            .select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="local_level_id"]').html(htmlToReplace);
                }
                fv.revalidateField('district_id');
            }).on('change', '[name="province_id"]', function (e) {
                $element = $(this);
                var provinceId = $element.val();
                var htmlToReplace = '<option value="">Select a District</option>';
                if (provinceId) {
                    var url = baseUrl + '/api/master/provinces/' + provinceId;
                    var successCallback = function (response) {
                        response.districts.forEach(function (district) {
                            htmlToReplace += '<option value="' + district.id + '">' + district
                                .district_name + '</option>';
                        });
                        $($element).closest('form').find('[name="district_id"]').html(htmlToReplace)
                            .trigger('change');
                        $($element).closest('form').find('[name="district_id"]').select2('destroy')
                            .select2();
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="district_id"]').html(htmlToReplace);
                }
                fv.revalidateField('province_id');
            }).on('change', '[name="local_level_id"]', function (e) {
                fv.revalidateField('local_level_id');
            }).on('change', '[name="engineer_id"]', function (e) {
                fv.revalidateField('engineer_id');
            });;


            $('[name="signed_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{{ date('Y-m-d') }}',
            });
            $('[name="effective_date_from"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                    {{-- startDate: '{{ date('Y - m - d') }}', --}}
                }).on('change', function (e) {
                    fv.revalidateField('effective_date_from');
                });

        $('[name="effective_date_to"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
                    {{-- startDate: '{{ date('Y - m - d') }}', --}}
                }).on('change', function (e) {
                fv.revalidateField('effective_date_to');
            });

        $('[name="settlement_date"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
                    {{-- startDate: '{{ date('Y - m - d') }}', --}}
                }).on('change', function (e) {
                fv.revalidateField('settlement_date');
            });

        $('[name="work_start_date"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
            startDate: '{{ date('Y-m-d', strtotime(date($construction->signed_date) . ' + 1 days')) }}',
            zIndex: 2048,
        }).on('change', function (e) {
            fv.revalidateField('work_start_date');
        });

        $('[name="work_completion_date"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
            startDate: '{{ date('Y-m-d', strtotime(date($construction->signed_date) . ' + 1 days')) }}',
            zIndex: 2048,
        }).on('change', function (e) {
            fv.revalidateField('work_completion_date');
        });
            });

        $(document).on('click', '.open-contribution-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('constructionPartyForm');
                $(form).find(".select2").each(function () {
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
                        party_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Party Name is required',
                                },
                            },
                        },
                        contribution_amount: {
                            validators: {
                                notEmpty: {
                                    message: 'Contribution Amount is required',
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
                }).on('core.form.valid', function (event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });

                        constructionPartyTable.ajax.reload();

                        document.getElementById('ohw_contribution').value = new Number(response.construction.ohw_contribution);
                        document.getElementById('total_contribution_amount').value = new Number(response.construction.total_contribution_amount);
                        document.getElementById('total_contribution_percentage').value = new Number(response.construction.total_contribution_percentage);

                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
            });
        });

        var constructionPartyTable = $('#constructionParyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.parties.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                data: 'party_name',
                name: 'party_name'
            },
            {
                data: 'contribution_amount',
                name: 'contribution_amount'
            },
            {
                data: 'contribution_percentage',
                name: 'contribution_percentage'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            ]
        });

        $('#constructionParyTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });

                constructionPartyTable.ajax.reload();

                document.getElementById('ohw_contribution').value = new Number(response.construction.ohw_contribution);
                document.getElementById('total_contribution_amount').value = new Number(response.construction.total_contribution_amount);
                document.getElementById('total_contribution_percentage').value = new Number(response.construction.total_contribution_percentage);
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        var constructionAttachmentTable = $('#constructionAttachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.attachment.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#constructionAttachmentTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                constructionAttachmentTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-construction-attachment-create-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('constructionAttachmentCreateForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        //attachment: {
                        //    validators: {
                        //        notEmpty: {
                        //            message: 'The attachment is required.',
                        //        },
                        //        file: {
                        //            extension: 'jpeg,jpg,png,pdf',
                        //            type: 'image/jpeg,image/png,application/pdf',
                        //            maxSize: '5097152',
                        //            message: 'The selected file is not valid file or must not be greater than 5 MB.',
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
                }).on('core.form.valid', function (event) {
                    let form = document.getElementById('constructionAttachmentCreateForm');
                    let data = new FormData(form);
                    let url = form.getAttribute('action');

                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        constructionAttachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });

        $(document).on('click', '.open-construction-attachment-edit-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('constructionAttachmentEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The attachment title is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
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
                }).on('core.form.valid', function (event) {
                    let form = document.getElementById('constructionAttachmentEditForm');
                    let data = new FormData(form);
                    let url = form.getAttribute('action');

                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        constructionAttachmentTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
            });
        });


        var oTable = $('#constructionAmendmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.amendment.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'effective_date',
                    name: 'effective_date'
                },
                {
                    data: 'extension_to_date',
                    name: 'extension_to_date'
                },
                {
                    data: 'total_estimate_cost',
                    name: 'total_estimate_cost'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#constructionAmendmentTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-construction-amendment-create-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('constructionAmendmentCreateForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        effective_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Effective date is required.',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        // extension_to_date: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'Extension to date is required.',
                        //         },
                        //         date: {
                        //             format: 'YYYY-MM-DD',
                        //             message: 'The value is not a valid date',
                        //         },
                        //     },
                        // },
                        total_estimate_cost: {
                            validators: {
                                notEmpty: {
                                    message: 'The total estimate cost is required.',
                                },
                            },
                        },
                        // attachment: {
                        //     validators: {
                        //         notEmpty: {
                        //             message: 'The attachment is required.',
                        //         },
                        //         file: {
                        //             extension: 'jpeg,jpg,png,pdf',
                        //             type: 'image/jpeg,image/png,application/pdf',
                        //             maxSize: '5097152',
                        //             message: 'The selected file is not valid file or must not be greater than 5 MB.',
                        //         },
                        //     },
                        // },
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
                }).on('core.form.valid', function (event) {
                    let form = document.getElementById('constructionAmendmentCreateForm');
                    let data = new FormData(form);

                    let url = form.getAttribute('action');

                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        oTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
                $('[name="effective_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });
                $('[name="extension_to_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });
            });
        });

        $(document).on('click', '.open-construction-amendment-edit-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('constructionAmendmentEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        effective_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Effective date is required.',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        total_estimate_cost: {
                            validators: {
                                notEmpty: {
                                    message: 'The total estimate cost is required.',
                                },
                            },
                        },
                        attachment: {
                            validators: {
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
                }).on('core.form.valid', function (event) {
                    let form = document.getElementById('constructionAmendmentEditForm');
                    let data = new FormData(form);
                    let url = form.getAttribute('action');

                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        oTable.ajax.reload();
                    }
                    ajaxSubmitFormData(url, 'POST', data, successCallback);
                });
                $('[name="effective_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });
                $('[name="extension_to_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });
            });
        });
    </script>
@endsection
@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}"
                                    class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('construction.index') }}"
                                    class="text-decoration-none">Construction</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section class="registration">
            <div class="row">

                <div class="col-lg-12">
                    <div class="card">
                        <form action="{{ route('construction.update', $construction->id) }}" id="constructionAddForm"
                            method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">


                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="validationRemarks" class="m-0">General Information</label>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Signed
                                                Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('signed_date')) is-invalid @endif"
                                            type="text" readonly name="signed_date"
                                            value="{{ $construction->signed_date ? $construction->signed_date->format('Y-m-d') : ''  }}" />
                                        @if ($errors->has('signed_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="signed_date">
                                                    {!! $errors->first('signed_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Nepali Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control signed_date_nepali @if ($errors->has('nepali_date')) is-invalid @endif"
                                            type="text" readonly name="nepali_date"
                                            value="{{ $construction->getSignedBsDate() }}" />
                                        @if ($errors->has('nepali_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="nepali_date">
                                                    {!! $errors->first('nepali_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Health Facility
                                                Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('health_facility_name')) is-invalid @endif"
                                            type="text" name="health_facility_name"
                                            value="{{ old('health_facility_name') ?: $construction->health_facility_name }}" />
                                        @if ($errors->has('health_facility_name'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="health_facility_name">
                                                    {!! $errors->first('health_facility_name') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Effective Date
                                                AD From</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('effective_date_from')) is-invalid @endif"
                                            readonly name="effective_date_from"
                                            value="{{$construction->getEffectiveFromDate()}}" />
                                        @if ($errors->has('effective_date_from'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_from">
                                                    {!! $errors->first('effective_date_from') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Type of
                                                Facility</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('facility_type')) is-invalid @endif"
                                            type="text" name="facility_type"
                                            value="{{ old('facility_type') ?: $construction->facility_type }}" />
                                        @if ($errors->has('facility_type'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="facility_type">
                                                    {!! $errors->first('facility_type') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Effective Date BS From</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('effective_date_from_bs')) is-invalid @endif"
                                            type="text" readonly name="effective_date_from_bs"
                                            value="{{ $construction->getEffectiveFromBsDate() }}" />
                                        @if ($errors->has('effective_date_from_bs'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_from_bs">
                                                    {!! $errors->first('effective_date_from_bs') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Type of
                                                Work</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('type_of_work')) is-invalid @endif"
                                            type="text" name="type_of_work"
                                            value="{{ old('type_of_work') ?: $construction->type_of_work }}" />
                                        @if ($errors->has('type_of_work'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="type_of_work">
                                                    {!! $errors->first('type_of_work') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Effective Date
                                                AD to</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('effective_date_to')) is-invalid @endif"
                                            readonly name="effective_date_to"
                                            value="{{ $construction->getEffectiveToDate() }}" />
                                        @if ($errors->has('effective_date_to'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_to">
                                                    {!! $errors->first('effective_date_to') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Province</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedProvinceId = $construction->province_id; @endphp
                                        <select name="province_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Province</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}" data-purchase="{{ $province->id }}" {{ $province->id == $selectedProvinceId ? 'selected' : '' }}>
                                                    {{ $province->getProvinceName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('province_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="province_id">
                                                    {!! $errors->first('province_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Effective Date BS To</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('effective_date_bs_to')) is-invalid @endif"
                                            type="text" readonly name="effective_date_bs_to"
                                            value="{{ $construction->getEffectiveToBsDate() }}" />
                                        @if ($errors->has('effective_date_bs_to'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_bs_to">
                                                    {!! $errors->first('effective_date_bs_to') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>





                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">District</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedDistrictId = $construction->district_id; @endphp
                                        <select name="district_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}" data-purchase="{{ $district->id }}" {{ $district->id == $selectedDistrictId ? 'selected' : '' }}>
                                                    {{ $district->getDistrictName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('district_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="district_id">
                                                    {!! $errors->first('district_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">OHW
                                                Contribution Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('ohw_contribution')) is-invalid @endif"
                                            type="number" name="ohw_contribution" id="ohw_contribution"
                                            value="{{ old('ohw_contribution') ?: $construction->ohw_contribution }}"
                                            readonly />
                                        @if ($errors->has('ohw_contribution'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="ohw_contribution">
                                                    {!! $errors->first('ohw_contribution') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>




                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Local
                                                Level</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedLocalId = $construction->local_level_id; @endphp
                                        <select name="local_level_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Local Level</option>
                                            @foreach ($localLevels as $local)
                                                <option value="{{ $local->id }}" data-purchase="{{ $local->id }}" {{ $local->id == $selectedLocalId ? 'selected' : '' }}>
                                                    {{ $local->getLocalLevelName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('local_level_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="local_level_id">
                                                    {!! $errors->first('local_level_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">OHW Contribution Percentage</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input
                                            class="form-control @if ($errors->has('total_contribution_percentage')) is-invalid @endif"
                                            type="number" readonly name="total_contribution_percentage"
                                            id="total_contribution_percentage"
                                            value="{{ $construction->total_contribution_percentage }}" />
                                        @if ($errors->has('total_contribution_percentage'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="total_contribution_percentage">
                                                    {!! $errors->first('total_contribution_percentage') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>



                                </div>



                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Engineer
                                                Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        {{-- <input type="text" class="form-control" name="engineer_name"
                                            value="{{ old('engineer_name') ?: $construction->engineer_name }}"> --}}

                                        <select class="select2" name="engineer_id" id="engineer_id">
                                            <option value="" selected disabled>Select engineer</option>
                                            @foreach ($engineers as $engineer)
                                                <option value="{{$engineer->id}}"
                                                    {{$engineer->id == $construction->engineer_id ? 'selected' : ''}}>
                                                    {{$engineer->getFullName()}}</option>
                                            @endforeach
                                        </select>

                                        @if ($errors->has('engineer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="engineer_id">
                                                    {!! $errors->first('engineer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Approval</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class=" form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="physicallyabled" name="approval" @if ($construction->approval == 1)
                                            checked @endif disabled>
                                            <label class="form-check-label" for="physicallyabled"></label>
                                        </div>
                                    </div> --}}

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0 ">Total Estimate Cost</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="number" readonly name="total_contribution_amount"
                                            id="total_contribution_amount" {{--
                                            value="{{ $construction->getTotalContributionAmount() }}" --}}
                                            value="{{ $construction->total_contribution_amount }}" class="form-control">
                                        @if ($errors->has('total_contribution_amount'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="total_contribution_amount">
                                                    {!! $errors->first('total_contribution_amount') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="work_start_date" class="m-0">Work Start Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text"
                                            class="form-control @if($errors->has('work_start_date')) is-invalid @endif"
                                            name="work_start_date"
                                            value="{{$construction->work_start_date?->format('Y-m-d')}}"
                                            placeholder="Work start date">
                                        @if($errors->has('work_start_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="work_start_date">
                                                    {!! $errors->first('work_start_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="work_completion_date" class="m-0">Work Completion Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text"
                                            class="form-control @if($errors->has('work_completion_date')) is-invalid @endif"
                                            name="work_completion_date"
                                            value="{{$construction->work_completion_date?->format('Y-m-d')}}"
                                            placeholder="Work completion date">
                                        @if($errors->has('work_completion_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="work_completion_date">
                                                    {!! $errors->first('work_completion_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="donor" class="m-0">Donors</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">

                                        {{-- <select
                                            class="form-control select2 @if($errors->has('donor_codes')) is-invalid @endif"
                                            name="donor_codes[]" id="donor_codes" multiple>
                                            @foreach ($donors as $donor)
                                            <option value="{{$donor->id}}" {{in_array($donor->id,
                                                $construction->donors->pluck('id')->toArray()) ? 'selected' :
                                                ''}}>{{$donor->getDonorCodeWithDescription()}}</option>
                                            @endforeach
                                        </select> --}}

                                        <input type="text"
                                            class="form-control @if($errors->has('donor')) is-invalid @endif"
                                            name="donor" value="{{$construction->donor}}" placeholder="Donor">

                                        @if($errors->has('donor_codes'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="donor_codes">
                                                    {!! $errors->first('donor_codes') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="metal_plaque_text" class="m-0">Metal Plaque Text</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        {{-- <input type="text"
                                            class="form-control @if($errors->has('metal_plaque_text')) is-invalid @endif"
                                            name="metal_plaque_text" value="{{$construction->metal_plaque_text}}"
                                            placeholder="Metal Plaque Text"> --}}

                                        <textarea
                                            class="form-control @if($errors->has('metal_plaque_text')) is-invalid @endif"
                                            placeholder="Metal Plaque Text" name="metal_plaque_text"
                                            id="metal_plaque_text"
                                            rows="2">{{ $construction->metal_plaque_text }}</textarea>

                                        @if($errors->has('metal_plaque_text'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="metal_plaque_text">
                                                    {!! $errors->first('metal_plaque_text') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if (auth()->user()->can('manage-cluster'))
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="form-label required-label">Cluster</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            @php
                                                $selected = old('office_id') ?? $construction->office_id;
                                            @endphp
                                            <select name="office_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select an Office</option>
                                                @foreach ($offices as $local)
                                                    <option value="{{ $local->id }}" {{ $local->id == $selected ? 'selected' : '' }}>
                                                        {{ $local->office_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('local_level_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="local_level_id">
                                                        {!! $errors->first('local_level_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif




                                {!! csrf_field() !!}
                                {!! method_field('PUT') !!}

                                <div>
                                    <div class="card">
                                        <div class="card-header fw-bold"
                                            style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                            <span>
                                                Parties
                                            </span>
                                            <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-contribution-modal-form"
                                                href="{{ route('construction.parties.create', $construction->id) }}"><i
                                                    class="bi-plus"></i> Add Parties
                                            </button>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div>
                                                <div class="card-body">
                                                    <!-- constructionParyTable -->
                                                    <div class="table-responsive">
                                                        <table class="table" id="constructionParyTable">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th scope="col">Party Name</th>
                                                                    <th scope="col">Contribution</th>
                                                                    <th scope="col">C%</th>
                                                                    <th style="width: 150px">{{ __('label.action') }}
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>




                                <div>
                                    <div class="card">
                                        <div class="card-header fw-bold"
                                            style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                            <span>
                                                Attachments
                                            </span>
                                            <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-construction-attachment-create-modal-form"
                                                href="{{ route('construction.attachment.create', $construction->id) }}"><i
                                                    class="bi-plus"></i> Add Attachment
                                            </button>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table" id="constructionAttachmentTable">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col">Title</th>
                                                                <th scope="col" style="width: 150px">Attachment</th>
                                                                <th scope="col" style="width: 150px">
                                                                    {{ __('label.action') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="card">
                                        <div class="card-header fw-bold"
                                            style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                            <span>
                                                Amendments
                                            </span>
                                            <button data-toggle="modal"
                                                class="btn btn-primary btn-sm open-construction-amendment-create-modal-form"
                                                href="{{ route('construction.amendment.create', $construction->id) }}"><i
                                                    class="bi-plus"></i> Add Amendment
                                            </button>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table" id="constructionAmendmentTable">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col">Effective Date</th>
                                                                <th scope="col">Extension To Date</th>
                                                                <th scope="col">Total Estimate Cost</th>
                                                                <th scope="col" style="width: 150px">Attachment</th>
                                                                <th scope="col" style="width: 150px">
                                                                    {{ __('label.action') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                                </button>
                                {{-- <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button> --}}
                                <a href="{!! route('construction.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </section>

    </div>
</div>

@stop