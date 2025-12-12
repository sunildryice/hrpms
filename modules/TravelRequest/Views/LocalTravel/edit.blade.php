@extends('layouts.container')

@section('title', 'Edit Local Travel Reimbursement')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#local-travel-reimbursements-menu').addClass('active');
            const form = document.getElementById('localTravelEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Project is required',
                            },
                        },
                    },
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'Purpose is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'The approver is required',
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

        var oTable = $('#localTravelItineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('local.travel.reimbursements.itineraries.index', $localTravel->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'travel_date',
                    name: 'travel_date'
                },
                {
                    data: 'travel_mode',
                    name: 'travel_mode'
                },
                {
                    data: 'pickup_location',
                    name: 'pickup_location'
                },
                {
                    data: 'total_fare',
                    name: 'total_fare'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                }
            ],
        });

        $('#localTravelItineraryTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-itinerary-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('localTravelItineraryForm');
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
                        travel_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Travel date is required',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        travel_mode: {
                            validators: {
                                notEmpty: {
                                    message: 'Travel mode is required',
                                },
                            },
                        },
                        total_fare: {
                            validators: {
                                notEmpty: {
                                    message: 'Total fare is required',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 0.01',
                                    min: 0.01,
                                },
                            },
                        },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/jpg,image/png,application/pdf',
                                    maxSize: 5097152,
                                    message: 'File must be jpeg, jpg, png or pdf and less than 2MB'
                                }
                            }
                        }
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
                }).on('core.form.valid', function() {
                    const $url = fv.form.action;
                    const formData = new FormData(form);

                    const successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message || 'Saved successfully');
                        oTable.ajax.reload();
                    };

                    ajaxSubmitFormData($url, 'POST', formData, successCallback);
                });

                $('[name="travel_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    endDate: '{{ date('Y-m-d') }}',
                    zIndex: 2048,
                }).on('change', function(e) {
                    fv.revalidateField('travel_date');
                });

                const $travelerContainer = $('#travelers-names-container');
                const $numberInput = $('#number_of_travelers');

                function renderTravelerRows() {
                    const count = parseInt($numberInput.val()) || 0;
                    const existing = window.currentTravelersData || [];

                    $travelerContainer.empty();

                    if (count <= 0) return;

                    for (let i = 0; i < count; i++) {
                        const name = existing[i]?.name || '';

                        let rowHTML = `
                        <div class="row mb-2 align-items-end traveler-row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-8 col-xl-6">
                                <input type="text" 
                                       name="names_of_travelers[${i}][name]" 
                                       class="form-control" 
                                       placeholder="Full Name *" 
                                       value="${name}" 
                                       required>
                            </div>
                            <div class="col-md-1">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-danger btn-sm remove-traveler-row" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>`;

                        if (i === count - 1) {
                            rowHTML += `
                                    <button type="button" class="btn btn-success btn-sm add-traveler-row ms-1" title="Add another traveler">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>`;
                        }
                        rowHTML += `
                                </div>
                            </div>
                        </div>`;

                        $travelerContainer.append(rowHTML);
                    }
                }

                // When number of travelers changes
                $numberInput.on('input change', function() {
                    let val = parseInt($(this).val()) || 0;
                    if (val < 0) val = 0;
                    if (val > 50) val = 50; // optional max
                    $(this).val(val);
                    renderTravelerRows();
                });

                // Click + button → increase count by 1
                $travelerContainer.on('click', '.add-traveler-row', function() {
                    const current = parseInt($numberInput.val()) || 0;
                    $numberInput.val(current + 1).trigger('change');
                });

                // Click trash → remove that row and re-index
                $travelerContainer.on('click', '.remove-traveler-row', function() {
                    $(this).closest('.traveler-row').remove();

                    // Rebuild with correct indexing
                    const remainingCount = $travelerContainer.find('.traveler-row').length;
                    $numberInput.val(remainingCount);

                    // Re-render to fix indexes and + button position
                    const tempData = [];
                    $travelerContainer.find('input[name^="names_of_travelers"]').each(function() {
                        tempData.push({
                            name: $(this).val()
                        });
                    });
                    window.currentTravelersData = tempData;

                    renderTravelerRows();
                });

                // Initial render when modal opens
                renderTravelerRows();

                $(form).on('change', '[name="approver_id"]', function(e) {
                    fv.revalidateField('approver_id');
                });
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="page-nlocal/header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('local.travel.reimbursements.index') }}"
                                class="text-decoration-none text-dark">
                                Local Travel Reimbursements
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">

        <form action="{{ route('local.travel.reimbursements.update', $localTravel->id) }}" id="localTravelEditForm"
            method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="card">
                <div class="card-header fw-bold">
                    Local Travel Reimbursement
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProject" class="form-label required-label">Project
                                </label>
                            </div>
                        </div>
                        @php $selectedProjectCodeId =  old('project_code_id') ?: $localTravel->project_code_id  @endphp
                        <div class="col-lg-10">
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
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Purpose</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <input type="text" value="{{ old('title') ?: $localTravel->title }}"
                                class="form-control @if ($errors->has('title')) is-invalid @endif" name="title" />
                            @if ($errors->has('title'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="title">{!! $errors->first('title') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- <div class="mb-2 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProject" class="form-label">Request For
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <select name="employee_id"
                                class="select2 form-control
                                        @if ($errors->has('employee_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select Employee/Consultant</option>
                                @foreach ($consultants as $consultant)
                                    <option value="{{ $consultant->id }}"
                                        {{ $consultant->id == (old('employee_id') ?: $localTravel->employee_id) ? 'selected' : '' }}>
                                        {{ $consultant->getFullName() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('employee_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="employee_id">
                                        {!! $errors->first('employee_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> --}}

                    @php
                        $selectedTravelRequest = old('travel_request_id') ?: $localTravel->travel_request_id;
                        $selectedApproverId = old('approver_id') ?: $localTravel->approver_id;
                    @endphp

                    {{-- <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpurchasetype" class="form-label">Travel Request (If
                                    any)</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <select name="travel_request_id" class="select2 form-control" data-width="100%">
                                <option value="">Select Travel Request</option>
                                @foreach ($travelRequests as $travelRequest)
                                    <option value="{{ $travelRequest->id }}" data-purchase="{{ $travelRequest->id }}"
                                        {{ $travelRequest->id == $selectedTravelRequest ? 'selected' : '' }}>
                                        {{ $travelRequest->getTravelRequestNumber() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('travel_request_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="travel_request_id">
                                        {!! $errors->first('travel_request_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div> --}}

                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Reason For Travel</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') ?: $localTravel->remarks }}</textarea>
                            @if ($errors->has('remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label class="form-label required-label">Approver</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <select name="approver_id" class="select2 form-control" data-width="100%">
                                <option value="">Select Approver</option>
                                @foreach ($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}" data-purchase="{{ $supervisor->id }}"
                                        {{ $supervisor->id == $selectedApproverId ? 'selected' : '' }}>
                                        {{ $supervisor->getFullName() }}
                                    </option>
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


                </div>
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Travel Details</span>
                        <button data-toggle="modal" class="btn btn-primary btn-sm open-itinerary-modal-form"
                            href="{!! route('local.travel.reimbursements.itineraries.create', $localTravel->id) !!}"><i class="bi-plus"></i> Add New Detail
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="localTravelItineraryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.date') }}</th>
                                    <th scope="col">{{ __('label.mode') }}</th>
                                    <th scope="col">Pickup Location</th>
                                     <th scope="col">{{ __('label.fare') }}</th>
                                    <th scope="col">{{ __('label.reason') }}</th>
                                    <th scope="col">{{ __('label.attachment') }}</th>
                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="justify-content-end d-flex gap-2 mt-4">
                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                    Submit
                </button>
                <a href="{!! route('local.travel.reimbursements.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>

    </section>



@stop
