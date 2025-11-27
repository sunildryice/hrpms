@extends('layouts.container')

@section('title', 'Add Travel Request')

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-request-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            // $("#substitutes").select2({
            //     width: '100%',
            //     dropdownAutoWidth: true
            // });
            const sevenDaysAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
            const form = document.getElementById('travelRequestAddForm');
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
                                message: 'The Departure date is required',
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
                                message: 'The Return date is required',
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

            $(form.querySelector('[name="departure_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}',
            }).on('change', function(e) {
                fv.revalidateField('departure_date');
                fv.revalidateField('return_date');
            });

            $(form.querySelector('[name="return_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}',
            }).on('change', function(e) {
                fv.revalidateField('departure_date');
                fv.revalidateField('return_date');
            });

            $(form).on('change', '[name="project_code_id"]', function(e) {
                fv.revalidateField('project_code_id');
            });

            // Passport Section Toggle
            const travelTypeSelect = document.querySelector('select[name="travel_type_id"]');
            const passportSection = document.getElementById('passportSection');

            function togglePassportSection() {
                if (travelTypeSelect.value == '2') {
                    passportSection.style.display = 'block';
                } else {
                    passportSection.style.display = 'none';
                }
            }
            togglePassportSection();
            travelTypeSelect.addEventListener('change', togglePassportSection);
            $(travelTypeSelect).on('change', togglePassportSection);


            // External Travelers Dynamic Rows
            const countInput = document.getElementById('external_traveler_count');
            const container = document.getElementById('external-travelers-container');

            function generateRows() {
                const count = parseInt(countInput.value) || 0;
                if (count < 0) countInput.value = 0;

                container.innerHTML = '';

                for (let i = 0; i < count; i++) {
                    const row = document.createElement('div');
                    row.className = 'row mb-2 align-items-end external-traveler-row';

                    let rowHTML = `
                    <div class="col-lg-3"></div>
                    <div class="col-md-4">
                        <input type="text" name="external_travelers[${i}][name]" class="form-control" placeholder="Full Name required" required>
                    </div>
                    <div class="col-md-4">
                        <input type="email" name="external_travelers[${i}][email]" class="form-control" placeholder="Email (optional)">
                    </div>
                    <div class="col-md-1">
                    <div class="btn-group" role="group">
                    <button type="button" class="btn btn-danger btn-sm remove-row" title="Remove">
                        <i class="bi bi-trash"></i>
                    </button>`;

                    if (i === count - 1) {
                        rowHTML += `
                    <button type="button" class="btn btn-success btn-sm add-row ms-1" title="Add another">
                        <i class="bi bi-plus-lg"></i>
                    </button>`;
                    }

                    rowHTML += `</div></div>`;
                    row.innerHTML = rowHTML;
                    container.appendChild(row);
                }

                document.querySelectorAll('.remove-row').forEach(btn => {
                    btn.onclick = function() {
                        this.closest('.external-traveler-row').remove();
                        countInput.value = container.children.length;
                        generateRows(); 
                    };
                });

                document.querySelectorAll('.add-row').forEach(btn => {
                    btn.onclick = function() {
                        countInput.value = parseInt(countInput.value || 0) + 1;
                        generateRows();
                    };
                });
            }

            countInput.addEventListener('input', () => {
                if (countInput.value < 0) countInput.value = 0;
                generateRows();
            });
            countInput.addEventListener('change', generateRows);

            if (countInput.value > 0) {
                generateRows();
            }
        });
    </script>

@endsection
@push('scripts')
    <link href="{{ asset('plugins/slim-select/dist/slimselect.css') }}" rel="stylesheet">
    </link>
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
@endpush
@section('page-content')



    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('travel.requests.index') }}"
                                class="text-decoration-none text-dark">Travel Request</a></li>
                        <li class="breadcrumb-item" aria-current="page">Add New Travel Request</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Add New Travel Request</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <div class="card-header fw-bold">Travel Request</div>
            <form action="{{ route('travel.requests.store') }}" id="travelRequestAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationtraveltype" class="form-label required-label">Travel Type
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="travel_type_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Travel Type</option>
                                @foreach ($travelTypes as $travelType)
                                    <option value="{{ $travelType->id }}"
                                        {{ $travelType->id == old('travel_type_id') ? 'selected' : '' }}>
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
                    <div id="passportSection" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label class="form-label">Passport Number</label>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-control bg-light border-0">
                                    @if ($employeePassportNumber)
                                        <strong class="text-dark">{{ $employeePassportNumber }}</strong>
                                    @else
                                        <span class="text-danger">
                                            <i class="bi bi-x-circle"></i> Not provided
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <label class="form-label">Passport Attachment</label>
                            </div>
                            <div class="col-lg-3">
                                <div
                                    class="form-control bg-light border-0 d-flex align-items-center justify-content-between">
                                    @if ($employeePassportAttachment && \Storage::disk('public')->exists($employeePassportAttachment))
                                        <a href="{{ asset('storage/' . $employeePassportAttachment) }}" target="_blank"
                                            class="btn btn-success btn-sm">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <small class="text-success ms-2">
                                            <i class="bi bi-check-circle-fill"></i> Uploaded
                                        </small>
                                    @else
                                        <span class="text-warning">
                                            <i class="bi bi-exclamation-triangle"></i> Not uploaded
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (!$employeePassportNumber && !$employeePassportAttachment)
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="alert alert-warning border small py-2 mb-3" role="alert">
                                        <i class="bi bi-info-circle"></i>
                                        <strong>Action Required:</strong> Please update your profile with passport details
                                        for international travel.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationProject" class="form-label">Request For
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="employee_id" class="select2 form-control
                                        @if ($errors->has('employee_id')) is-invalid @endif"
                                    data-width="100%">
                                    <option value="">Select Consultant</option>
                                    @foreach ($consultants as $consultant)
                                    <option value="{{ $consultant->id }}" {{$consultant->id == old('employee_id')?
                                        "selected":""}}>
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
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationaccompanyingStaffs" class="form-label">Accompanying
                                    Staff</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="accompanying_staff[]" id="accompanying_staff" data-select2-id="accompanying_staff"
                                class="@if ($errors->has('accompanying_staff')) is-invalid @endif" data-width="100%" multiple>
                                @foreach ($substitutes as $staff)
                                    <option value="{{ $staff->id }}">
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
                                <label for="validationProject" class="form-label required-label">Project
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="project_code_id"
                                class="select2 form-control
                                        @if ($errors->has('project_code_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ $project->id == old('project_code_id') ? 'selected' : '' }}>
                                        {{ $project->getProjectCodeWithDescription() }}
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
                                <label for="validationPurposeofTravel" class="form-label required-label">Purpose
                                    of
                                    Travel </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control @if ($errors->has('purpose_of_travel')) is-invalid @endif"
                                name="purpose_of_travel" value="{{ old('purpose_of_travel') }}"
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
                                <label for="validationdd" class="form-label required-label">Departure Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('departure_date')) is-invalid @endif"
                                readonly name="departure_date" value="{{ old('departure_date') }}" />
                            @if ($errors->has('departure_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="departure_date">{!! $errors->first('departure_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationreturndate" class="form-label required-label">Return Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('return_date')) is-invalid @endif"
                                readonly name="return_date" value="{{ old('return_date') }}" />
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
                                <label for="employee-sub" class="form-label">Substitutes</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="substitutes[]" id="substitutes"
                                class="@if ($errors->has('substitutes')) is-invalid @endif" data-width="100%" multiple>
                                @foreach ($substitutes as $substitute)
                                    <option value="{{ $substitute->id }}">{{ $substitute->getFullNameWithCode() }}
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
                            <input type="text" value="{{ old('final_destination') }}"
                                class="form-control @if ($errors->has('final_destination')) is-invalid @endif"
                                name="final_destination" />
                            @if ($errors->has('final_destination'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="final_destination">{!! $errors->first('final_destination') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Remarks </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">
                            {!! old('remarks') !!}</textarea>
                            @if ($errors->has('remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div> --}}

                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label class="form-label">Number of travelers (if any outside the organization)</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="number" min="0" id="external_traveler_count"
                                name="external_traveler_count" class="form-control"
                                value="{{ old('external_traveler_count') }}" placeholder="e.g. 0, 1, 2, 3...">
                            <small class="text-muted">Enter 0 if no external travelers</small>
                        </div>
                    </div>

                    <div id="external-travelers-container">
                        @if (old('external_traveler_count') || (isset($travelRequest) && $travelRequest->external_traveler_count > 0))
                            @php
                                $count = old('external_traveler_count', $travelRequest->external_traveler_count ?? 0);
                                $travelers = old('external_travelers', $travelRequest->external_travelers ?? []);
                            @endphp
                            @for ($i = 0; $i < $count; $i++)
                                <div class="row mb-2 align-items-end external-traveler-row">
                                    <div class="col-lg-3">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="external_travelers[{{ $i }}][name]"
                                            class="form-control" placeholder="Full Name *"
                                            value="{{ $travelers[$i]['name'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="email" name="external_travelers[{{ $i }}][email]"
                                            class="form-control" placeholder="Email (optional)"
                                            value="{{ $travelers[$i]['email'] ?? '' }}">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                    {!! csrf_field() !!}
                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Next</button>
                    <a href="{!! route('travel.requests.create') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>

@stop
