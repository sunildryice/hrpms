@extends('layouts.container')

@section('title', 'Edit Event Completion')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#event-completion-menu').addClass('active');
        })

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('eventCompletionEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    district_id: {
                        validators: {
                            notEmpty: {
                                message: 'The District is required',
                            },
                        },
                    },
                    activity_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The activity code is required',
                            },
                        },
                    },
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Program date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Program date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                            callback: {
                                message: 'The End date must be greater than Start date',
                                callback: function(value, validator, field) {
                                    const startDate = new Date(form.querySelector('[name="start_date"]').value);
                                    return new Date(value.value) >= startDate;
                                },
                            }
                        },
                    },
                    venue: {
                        validators: {
                            notEmpty: {
                                message: 'The venue is required',
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


            $(form.querySelector('[name="start_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                if(form.querySelector('[name="end_date"]').value){
                    fv.revalidateField('end_date');
                }
            });

            $(form.querySelector('[name="end_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('end_date');
            });

            @if (!$authUser->can('submit', $eventCompletion))
                $('.submit-record').hide();
            @endif

            var participantsTable = $('#participantsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('event.completion.participants.index', $eventCompletion->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'designation',
                        name: 'designation'
                    },
                    {
                        data: 'contact',
                        name: 'contact'
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

            $('#participantsTable').on('click', '.delete-record', function(e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 5000
                    });
                    if (response.participantCount) {
                        $('.submit-record').show();
                    } else {
                        $('.submit-record').hide();
                    }
                    participantsTable.ajax.reload();
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });


            $(document).on('click', '.open-participants-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const participantsForm = document.getElementById('participantsForm');


                    const fv = FormValidation.formValidation(participantsForm, {
                            fields: {
                                name: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Name of participant is required',
                                        },
                                    },
                                },
                                office: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Office of participant is required',
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
                        })
                        .on('core.form.valid', function(event) {
                            $url = fv.form.action;
                            $form = fv.form;
                            data = $($form).serialize();
                            var successCallback = function(response) {
                                $('#openModal').modal('hide');
                                toastr.success(response.message, 'Success', {
                                    timeOut: 5000
                                });
                                if (response.participantCount) {
                                    $('.submit-record').show();
                                } else {
                                    $('.submit-record').hide();
                                }
                                participantsTable.ajax.reload();
                            }
                            ajaxSubmit($url, 'POST', data, successCallback);
                        });
                })
            })
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
                        <li class="breadcrumb-item"><a href="{{ route('event.completion.index') }}"
                                class="text-decoration-none text-dark">Event Completion</a></li>
                        <li class="breadcrumb-item" aria-current="page">Edit Event Completion</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="registration">
        <form action="{{ route('event.completion.update', $eventCompletion->id) }}" id="eventCompletionEditForm"
            method="post" enctype="multipart/form-data" autocomplete="off">

            <div class="card">
                <div class="card-body">

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdistrict" class="form-label required-label">District
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="district_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a District</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}"
                                        {{ $district->id == $eventCompletion->district_id ? 'selected' : '' }}>
                                        {{ $district->district_name }}
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
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationActivity" class="form-label">Activity</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control select2" data-width="100%" name="activity_code_id">
                                <option value="">Select Activity</option>
                                @foreach ($activityCodes as $activityCode)
                                    <option value="{!! $activityCode->id !!}"
                                        {{ $activityCode->id == $eventCompletion->activity_code_id ? 'selected' : '' }}>
                                        {{ $activityCode->getActivityCodeWithDescription() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('activity_code_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="activity_code_id">
                                        {!! $errors->first('activity_code_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationVenue" class="form-label required-label">Venue:</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control @if ($errors->has('venue')) is-invalid @endif"
                                name="venue" value="{{ old('venue') ?: $eventCompletion->venue }}" placeholder="Venue">
                            @if ($errors->has('venue'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="venue">{!! $errors->first('venue') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Program Start Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('start_date')) is-invalid @endif"
                                readonly name="start_date"
                                value="{{ old('start_date') ?: $eventCompletion->start_date->format('Y-m-d') }}" />
                            @if ($errors->has('start_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="start_date">{!! $errors->first('start_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationpd" class="form-label required-label">Program End Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control
                                        @if ($errors->has('end_date')) is-invalid @endif"
                                readonly name="end_date"
                                value="{{ old('end_date') ?: $eventCompletion->end_date->format('Y-m-d') }}" />
                            @if ($errors->has('end_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="end_date">{!! $errors->first('end_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationBackground" class="form-label">Background</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('background')) is-invalid @endif" name="background">
{!! old('background') ?: $eventCompletion->background !!}
</textarea>
                            @if ($errors->has('background'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="background">{!! $errors->first('background') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationObjective" class="form-label">Objectives </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('objectives')) is-invalid @endif" name="objectives">
{{ old('objectives') ?: $eventCompletion->objectives }}
</textarea>
                            @if ($errors->has('objectives'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="objectives">{!! $errors->first('objectives') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProcess" class="form-label">Process</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('process')) is-invalid @endif" name="process">
{{ old('process') ?: $eventCompletion->process }}
</textarea>
                            @if ($errors->has('process'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="process">{!! $errors->first('process') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationClosing" class="form-label">Closing </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('closing')) is-invalid @endif" name="closing">
{{ old('closing') ?: $eventCompletion->closing }}
</textarea>
                            @if ($errors->has('closing'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="closing">{!! $errors->first('closing') !!}</div>
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
                            @php $selectedApproverId = old('approver_id') ?: $eventCompletion->approver_id; @endphp
                            <select name="approver_id"
                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select an Approver</option>
                                @foreach ($approvers as $approver)
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

            @include('Attachment::index', [
                'modelType' => 'Modules\EventCompletion\Models\EventCompletion',
                'modelId' => $eventCompletion->id,
            ])

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Event Participants</span>
                        <button data-toggle="modal" class="btn btn-primary btn-sm open-participants-modal-form"
                            href="{!! route('event.completion.participants.create', $eventCompletion->id) !!}">
                            <i class="bi-plus"></i> Add New Participant
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="participantsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.name') }}</th>
                                    <th scope="col">{{ __('label.office') }}</th>
                                    <th scope="col">{{ __('label.designation') }}</th>
                                    <th scope="col">{{ __('label.contact') }}</th>
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
                    @if (!$authUser->can('submit', $eventCompletion)) style="display:none;" @endif>
                    Submit
                </button>
                <a href="{!! route('event.completion.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>
    </section>



@stop
