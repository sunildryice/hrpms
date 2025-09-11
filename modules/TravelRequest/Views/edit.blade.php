@extends('layouts.container')

@section('title', 'Edit Travel Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-request-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            // $("#substitutes").select2({
            //     width: '100%',
            //     dropdownAutoWidth: true
            // });
            const form = document.getElementById('travelRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    travel_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Travel Type is required',
                            },
                        },
                    },
                    purpose_of_travel: {
                        validators: {
                            notEmpty: {
                                message: 'The Purpose of Travel is required',
                            },
                        },
                    },
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The project code is required',
                            },
                        },
                    },
                    departure_date: {
                        validators: {
                            notEmpty: {
                                message: 'The departure date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    return_date: {
                        validators: {
                            notEmpty: {
                                message: 'The return date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    final_destination: {
                        validators: {
                            notEmpty: {
                                message: 'Destination is required',
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
                            field: 'departure_date',
                            message: 'Departure date must be a valid date and earlier than return date.',
                        },
                        endDate: {
                            field: 'return_date',
                            message: 'Return date must be a valid date and later than departure date.',
                        },
                    }),
                },
            });

            $(form).find('[name="departure_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! $travelRequest->parentTravelRequest
                    ? $travelRequest->parentTravelRequest->departure_date->subDays(14)->format('Y-m-d')
                    : date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('departure_date');
                fv.revalidateField('return_date');
            });

            $(form).find('[name="return_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! $travelRequest->parentTravelRequest
                    ? $travelRequest->parentTravelRequest->departure_date->subDays(14)->format('Y-m-d')
                    : date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('departure_date');
                fv.revalidateField('return_date');
            });

            $(form).on('change', '[name="project_code_id"]', function(e) {
                fv.revalidateField('project_code_id');
            });

            @if (!$authUser->can('submit', $travelRequest))
                $('.estimateDiv').hide();
                $('.submit-record').hide();
            @endif
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.requests.itinerary.index', $travelRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'departure_date',
                    name: 'departure_date'
                },
                {
                    data: 'departure_place',
                    name: 'departure_place'
                },
                {
                    data: 'arrival_date',
                    name: 'arrival_date'
                },
                {
                    data: 'arrival_place',
                    name: 'arrival_place'
                },
                {
                    data: 'mode_of_travel',
                    name: 'mode_of_travel',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'dsa_category',
                    name: 'dsa_category',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'dsa_unit_price',
                    name: 'dsa_unit_price'
                },
                {
                    data: 'dsa_total_price',
                    name: 'dsa_total_price'
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

        $('#itineraryTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.itineraryCount) {
                    $('.estimateDiv').show();
                    $('.submit-record').show();
                } else {
                    $('.estimateDiv').hide();
                    $('.submit-record').hide();
                }
                itineraryTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        var estimateTable = $('#estimationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.requests.estimate.index', $travelRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'estimated_dsa',
                    name: 'estimated_dsa'
                },
                {
                    data: 'estimated_air_fare',
                    name: 'estimated_air_fare'
                },
                {
                    data: 'estimated_vehicle_fare',
                    name: 'estimated_vehicle_fare'
                },
                {
                    data: 'miscellaneous_amount',
                    name: 'miscellaneous_amount'
                },
                {
                    data: 'miscellaneous_remarks',
                    name: 'miscellaneous_remarks'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'advance_amount',
                    name: 'advance_amount'
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

        $('#estimationTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var tRow = '<tr><td colspan="7" class="text-center">Record not found.</td></tr>';
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.estimateCount) {
                    $('.estimateAddBlock').hide();
                } else {
                    $('.estimateAddBlock').show();
                }
                estimateTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-estimation-modal-form', function(e) {
            e.preventDefault();
            $('#estimateAddModal').find('.modal-content').html('');
            $('#estimateAddModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('estimateForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        estimated_dsa: {
                            validators: {
                                notEmpty: {
                                    message: 'Estimated DSA is required',
                                },
                                numeric: {
                                    message: 'The Estimated Air Fare should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        estimated_air_fare: {
                            validators: {
                                numeric: {
                                    message: 'The Estimated Air Fare should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        estimated_vehicle_fare: {
                            validators: {
                                numeric: {
                                    message: 'The Estimated Vehicle Fare should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        advance_amount: {
                            validators: {
                                numeric: {
                                    message: 'The Advance Amount should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        miscellaneous_amount: {
                            validators: {
                                numeric: {
                                    message: 'The Miscellaneous Amount should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
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
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#estimateAddModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        if (response.estimateCount) {
                            $('.estimateAddBlock').hide();
                        } else {
                            $('.estimateAddBlock').show();
                        }
                        estimateTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
            });
        });

        $(document).on('click', '.open-itinerary-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const itineraryForm = document.getElementById('itineraryForm');
                $(itineraryForm).find(".select2").each(function() {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });

                const fv = FormValidation.formValidation(itineraryForm, {
                    fields: {
                        departure_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Departure Date is required',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        arrival_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Arrival date is required',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        departure_place: {
                            validators: {
                                notEmpty: {
                                    message: 'Departure place is required',
                                },
                            },
                        },
                        arrival_place: {
                            validators: {
                                notEmpty: {
                                    message: 'Arrival place is required',
                                },
                            },
                        },
                        dsa_category_id: {
                            validators: {
                                notEmpty: {
                                    message: 'DSA Category is required',
                                },
                            },
                        },
                        dsa_unit_price: {
                            validators: {
                                notEmpty: {
                                    message: 'DSA Rate is required',
                                },
                                numeric: {
                                    message: 'The DSA rate should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        activity_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Activity code is required',
                                },
                            },
                        },
                        account_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Account code is required',
                                },
                            },
                        },
                        description: {
                            validators: {
                                notEmpty: {
                                    message: 'Description is required',
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
                        startEndDate: new FormValidation.plugins.StartEndDate({
                            format: 'YYYY-MM-DD',
                            startDate: {
                                field: 'departure_date',
                                message: 'Departure date must be a valid date and earlier than arrival date.',
                            },
                            endDate: {
                                field: 'arrival_date',
                                message: 'Arrival date must be a valid date and later than departure date.',
                            },
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
                        if (response.itineraryCount) {
                            $('.estimateDiv').show();
                            $('.submit-record').show();
                        } else {
                            $('.estimateDiv').hide();
                            $('.submit-record').hide();
                        }
                        itineraryTable.ajax.reload();
                        estimateTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(itineraryForm).find('.travel-mode').on('change', function() {
                    let selectedValues = $(this).val();
                    if(selectedValues.includes('7')){
                        $(itineraryForm).find('.other-travel-mode').show();
                    }else {
                        $(itineraryForm).find('.other-travel-mode').hide();
                    }
                })

                $(itineraryForm).find('[name="departure_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: '{!! $travelRequest->departure_date !!}',
                    endDate: '{!! $travelRequest->return_date !!}',
                    zIndex: 2048,
                }).on('change', function(e) {
                    fv.revalidateField('departure_date');
                    fv.revalidateField('arrival_date');
                });

                $(itineraryForm).find('[name="arrival_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: '{!! $travelRequest->departure_date !!}',
                    endDate: '{!! $travelRequest->return_date !!}',
                    zIndex: 2048,
                }).on('change', function(e) {
                    fv.revalidateField('departure_date');
                    fv.revalidateField('arrival_date');
                });


                $(itineraryForm).on('change', '[name="activity_code_id"]', function(e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function(response) {
                            response.accountCodes.forEach(function(accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id +
                                    '">' + accountCode.title + ' ' + accountCode
                                    .description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(
                                htmlToReplace);
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(
                            htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                }).on('change', '[name="account_code_id"]', function(e) {
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="dsa_category_id"]', function(e) {
                    $element = $(this);
                    var categoryId = $element.val();
                    if (categoryId) {
                        var url = baseUrl + '/api/master/dsa-categories/' + categoryId;
                        var successCallback = function(response) {
                            $($element).closest('form').find('[name="dsa_unit_price"]').val(
                                response.dsaCategory.rate);
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="dsa_unit_price"]').val(0);
                    }
                    fv.revalidateField('dsa_category_id');
                }).on('change', '[name="travel_mode_id"]', function(e) {
                    fv.revalidateField('travel_mode_id');
                });
            });
        });
    </script>
    <link href="{{ asset('plugins/slim-select/dist/slimselect.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/slim-select/dist/slimselect.min.js') }}"></script>
    <script>
        new SlimSelect({
            select: '#accompanying_staff',
            placeholder: 'Select accompanying staffs'
        });
        new SlimSelect({
            select: '#substitutes',
            placeholder: 'Select substitutes'
        });
    </script>
@endsection
@section('page-content')


    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('travel.requests.index') }}"
                                class="text-decoration-none text-dark">Travel Request</a></li>
                        <li class="breadcrumb-item" aria-current="page">Edit Travel Request</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="registration">
        <form action="{{ route('travel.requests.update', $travelRequest->id) }}" id="travelRequestEditForm" method="post"
            enctype="multipart/form-data" autocomplete="off">

            <div class="card">
                <div class="card-body">
                    @php $selectedAccompanyingStaffs = $travelRequest->accompanyingStaffs->pluck('id')->toArray(); @endphp
                    @php $travelSubstitutes = $travelRequest->substitutes->pluck('id')->toArray(); @endphp
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationtraveltype" class="form-label required-label">Travel
                                    Type
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="travel_type_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Travel Type</option>
                                @foreach ($travelTypes as $travelType)
                                    <option value="{{ $travelType->id }}"
                                        {{ $travelType->id == $travelRequest->travel_type_id ? 'selected' : '' }}>
                                        {{ $travelType->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('travel_type_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="travel_type_id">
                                        {!! $errors->first('travel_type_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationProject" class="form-label">Request For
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="employee_id" class="select2 form-control
                                        @if($errors->has('employee_id')) is-invalid @endif"
                                    data-width="100%">
                                    <option value="">Select Consultant</option>
                                    @foreach($consultants as $consultant)
                                      <option value="{{ $consultant->id }}" {{$consultant->id == (old('employee_id') ?? $travelRequest->employee_id)?
                                        "selected":""}}>
                                        {{ $consultant->getFullName() }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($errors->has('employee_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="employee_id">
                                        {!! $errors->first('employee_id') !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationAccompanyingStaffs" class="m-0">Accompanying
                                    Staff</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="accompanying_staff[]" id="accompanying_staff"
                                class="@if ($errors->has('accompanying_staff')) is-invalid @endif" data-width="100%" multiple>
                                <option value="">Select Accompanying Staff</option>
                                @foreach ($substitutes as $staff)
                                    <option value="{{ $staff->id }}" @if (in_array($staff->id, $selectedAccompanyingStaffs)) selected @endif>
                                        {{ $staff->getFullNameWithCode() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('accompanying_staff'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="accompanying_staff">
                                        {!! $errors->first('accompanying_staff') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationPurposeofTravel" class="form-label required-label">Purpose
                                    of Travel </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control @if ($errors->has('purpose_of_travel')) is-invalid @endif"
                                name="purpose_of_travel" value="{{ $travelRequest->purpose_of_travel }}"
                                placeholder="Purpose of travel">
                            @if ($errors->has('purpose_of_travel'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="purpose_of_travel">{!! $errors->first('purpose_of_travel') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProject" class="form-label required-label">Project
                                </label>
                            </div>
                        </div>
                        @php $selectedProjectCodeId =  old('project_code_id') ?: $travelRequest->project_code_id  @endphp
                        <div class="col-lg-9">
                            <select name="project_code_id"
                                class="select2 form-control
                                                    @if ($errors->has('project_code_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select a Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ $project->id == $selectedProjectCodeId ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('project_code_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="project_code_id">
                                        {!! $errors->first('project_code_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label required-label">Departure Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                                    @if ($errors->has('departure_date')) is-invalid @endif"
                                name="departure_date"
                                value="{{ old('departure_date') ?: ($travelRequest->departure_date ? $travelRequest->departure_date->format('Y-m-d') : '') }}"
                                data-toggle="datepicker" />
                            @if ($errors->has('departure_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="departure_date">{!! $errors->first('departure_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationreturndate" class="form-label required-label">Return
                                    Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                                    @if ($errors->has('return_date')) is-invalid @endif"
                                name="return_date"
                                value="{{ old('return_date') ?: ($travelRequest->return_date ? $travelRequest->return_date->format('Y-m-d') : '') }}"
                                data-toggle="datepicker" />
                            @if ($errors->has('return_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="return_date">{!! $errors->first('return_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProject" class="m-0">Substitutes
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="substitutes[]" id="substitutes" data-width="100%" multiple
                                class="@if ($errors->has('substitutes')) is-invalid @endif">
                                @foreach ($substitutes as $substitute)
                                    <option value="{{ $substitute->id }}"
                                        {{ in_array($substitute->id, $travelSubstitutes) ? 'selected' : '' }}>
                                        {{ $substitute->getFullNameWithCode() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('substitutes'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="substitutes">
                                        {!! $errors->first('substitutes') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Destination
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                value="{{ old('final_destination') ?: $travelRequest->final_destination }}"
                                class="form-control @if ($errors->has('final_destination')) is-invalid @endif"
                                name="final_destination" />
                            @if ($errors->has('final_destination'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="final_destination">{!! $errors->first('final_destination') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="m-0">Remarks </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{!! old('remarks') ?: $travelRequest->remarks !!}</textarea>
                            @if ($errors->has('remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Send
                                    to </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php $selectedApproverId = old('approver_id') ?: $travelRequest->approver_id; @endphp
                            <select name="approver_id"
                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select an Approver</option>
                                @foreach ($supervisors as $approver)
                                    <option value="{{ $approver->id }}"
                                        {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                        {{ $approver->getFullName() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('approver_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="approver_id">{!! $errors->first('approver_id') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                        Update
                    </button>
                </div>
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}
            </div>
            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Travel Itinerary</span>
                        <button data-toggle="modal" class="btn btn-primary btn-sm open-itinerary-modal-form"
                            href="{!! route('travel.requests.itinerary.create', $travelRequest->id) !!}">
                            <i class="bi-plus"></i> Add New Itinerary
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="itineraryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.from-date') }}</th>
                                    <th scope="col">{{ __('label.from-place') }}</th>
                                    <th scope="col">{{ __('label.to-date') }}</th>
                                    <th scope="col">{{ __('label.to-place') }}</th>
                                    <th scope="col">{{ __('label.mode-of-travel') }}</th>
                                    <th scope="col">{{ __('label.dsa-category') }}</th>
                                    <th scope="col">{{ __('label.dsa-rate') }}</th>
                                    <th scope="col">{{ __('label.total-dsa') }}</th>
                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="card estimateDiv">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-end">
                        <span> Travel Cost Estimation</span>
                        <div class="estimateAddBlock" @if ($travelRequest->travelRequestEstimate) style="display:none;" @endif>
                            <button data-toggle="modal" class="btn btn-primary btn-sm open-estimation-modal-form"
                                href="{!! route('travel.requests.estimate.create', $travelRequest->id) !!}">
                                <i class="bi-plus"></i> Add Estimation
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body estimate">
                    <div class="table-responsive">
                        <table class="table" id="estimationTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.estimated-dsa') }}</th>
                                    <th scope="col">{{ __('label.estimated-air-fare') }}</th>
                                    <th scope="col">{{ __('label.estimated-vehicle-fare') }}</th>
                                    <th scope="col">{{ __('label.miscellaneous-amount') }}</th>
                                    <th scope="col">{{ __('label.miscellaneous-remarks') }}</th>
                                    <th scope="col">{{ __('label.total-amount') }}</th>
                                    <th scope="col">{{ __('label.advance-amount') }}</th>
                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="justify-content-end d-flex gap-2" id="submitRequest">
                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm submit-record"
                    @if (!$authUser->can('submit', $travelRequest)) style="display:none;" @endif>
                    Submit
                </button>
                <a href="{!! route('travel.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>
    </section>

    <div class="modal fade" id="estimateAddModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="estimateAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

@stop
