@extends('layouts.container')

@section('title', 'Meeting Hall Booking Request')

@section('page_js')
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#meeting-hall-requests-menu').addClass('active');

            // $(".select2").select2({
            //     width: '100%',
            //     dropdownAutoWidth: true
            // });
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('hallBookingAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    meeting_hall_id: {
                        validators: {
                            notEmpty: {
                                message: 'Meeting Hall is required',
                            },
                        },
                    },
                    purpose: {
                        validators: {
                            notEmpty: {
                                message: 'The Purpose of Meeting is required',
                            },
                        },
                    },
                    meeting_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Meeting date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    start_time: {
                        validators: {
                            notEmpty: {
                                message: 'The start time is required',
                            }
                        },
                    },
                    end_time: {
                        validators: {
                            notEmpty: {
                                message: 'The end time is required',
                            }
                        },
                    },
                    number_of_attendees: {
                        validators: {
                            notEmpty: {
                                message: 'The no. of attendees is required',
                            },
                            numeric: {
                                message: 'The no. of attendees should be number.',
                            },
                            between: {
                                inclusive: true,
                                min:2,
                                max:127,
                                message: 'The no. of attendees must be between 2 and 127',
                            }
                        },
                    },
                    remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
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

            $(form.querySelector('[name="meeting_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
            }).on('change', function(e) {
                fv.revalidateField('meeting_date');
            });

            $('[name="start_time"]').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 1,
                locale: {
                    format: 'HH:mm'
                }
            }).on('show.daterangepicker', function(ev, picker) {
                picker.container.find(".calendar-table").hide();
            });


            $('[name="end_time"]').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 1,
                locale: {
                    format: 'HH:mm'
                }
            }).on('show.daterangepicker', function(ev, picker) {
                picker.container.find(".calendar-table").hide();
            });

            $(form).on('change','[name="meeting_hall_id"]', function (e){
                fv.revalidateField('meeting_hall_id');
            });
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
                                <li class="breadcrumb-item"><a href="{{ route('meeting.hall.bookings.index') }}"
                                        class="text-decoration-none text-dark">Meeting Hall Booking</a></li>
                                <li class="breadcrumb-item" aria-current="page">Add Meeting Hall Booking</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">New Meeting Hall Booking</h4>
                    </div>
                </div>
            </div>
            <section class="registration">
                <div class="card">
                    <form action="{{ route('meeting.hall.bookings.store') }}" id="hallBookingAddForm"
                        method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center h-100 required-label">
                                        <label for="validationProject" class="form-label">Meeting Hall
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <select name="meeting_hall_id"
                                        class="select2 form-control
                                        @if ($errors->has('meeting_hall_id')) is-invalid @endif"
                                        placeholder="Select a Meeting Hall" autocomplete="off" data-width="100%">
                                        <option value="">Select a Meeting Hall</option>
                                        @foreach ($meetingHalls as $meetingHall)
                                            <option value="{{ $meetingHall->id }}"
                                                {{ $meetingHall->id == old('meeting_hall_id') ? 'selected' : '' }}>
                                                {{ $meetingHall->title }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('meeting_hall_id'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="meeting_hall_id">
                                                {!! $errors->first('meeting_hall_id') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center h-100 required-label">
                                        <label for="validationtraveltype" class="form-label">Meeting Date
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text"
                                        class="form-control
                                        @if ($errors->has('meeting_date')) is-invalid @endif"
                                        name="meeting_date" value="{{ old('meeting_date') }}" />
                                    @if ($errors->has('meeting_date'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="meeting_date">{!! $errors->first('meeting_date') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center h-100 required-label">
                                        <label for="validationstarttime" class="form-label">Start Time
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text"
                                        class="form-control
                                        @if ($errors->has('start_time')) is-invalid @endif"
                                        name="start_time" value="{{ old('start_time') }}" readonly/>
                                    @if ($errors->has('start_time'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="start_time">{!! $errors->first('start_time') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center h-100 required-label">
                                        <label for="validationendtime" class="form-label">End time
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text"
                                        class="form-control
                                        @if ($errors->has('end_time')) is-invalid @endif"
                                        name="end_time" value="{{ old('end_time') }}" readonly/>
                                    @if ($errors->has('end_time'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="end_time">{!! $errors->first('end_time') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center h-100 required-label">
                                        <label for="validationPurpose" class="form-label">Purpose </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text"
                                        class="form-control @if ($errors->has('purpose_of_travel')) is-invalid @endif"
                                        name="purpose" value="{{ old('purpose') }}"
                                        placeholder="Purpose of Meeting">
                                    @if ($errors->has('purpose'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="purpose">{!! $errors->first('purpose') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center h-100 required-label">
                                        <label class="form-label">No. of Attendees </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="number"
                                        class="form-control @if ($errors->has('number_of_attendees')) is-invalid @endif"
                                        name="number_of_attendees" value="{{ old('number_of_attendees') }}"
                                        min="2">
                                    @if ($errors->has('number_of_attendees'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="number_of_attendees">{!! $errors->first('number_of_attendees') !!}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-center h-100 required-label">
                                        <label for="validationRemarks" class="form-label">Remarks </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea type="text"
                                    class="form-control
                                    @if ($errors->has('remarks')) is-invalid @endif"
                                    name="remarks">@if (old('remarks')){{ old('remarks') }}@endif</textarea>
                                    @if ($errors->has('remarks'))
                                        <div class="fv-plugins-message-container invalid-feedback">
                                            <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {!! csrf_field() !!}

                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save"
                                class="btn btn-primary btn-sm">Save</button>
                            <button type="submit" name="btn" value="submit"
                                class="btn btn-primary btn-sm">Book</button>
                            <a href="{!! route('meeting.hall.bookings.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>

@stop
