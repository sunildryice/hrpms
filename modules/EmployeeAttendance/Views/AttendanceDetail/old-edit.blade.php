@extends('layouts.container')

@section('title', 'Attendance Detail')
@section('page_css')
    <style>
        .table tr th,
        .table tr td {
            font-size: 10px;
            padding: 0.55rem 0.55rem;
        }

        .holiday {
            color: red !important;
        }

        .table thead th {
            text-transform: capitalize;
        }

        input,
        input:focus-visible {
            outline: none;
            padding: 0.3rem 0.5rem;
        }

        .input-custom-width {
            width: 68px;
            border: 1px solid #e4e4e4;
        }


        .wrapper {
            position: relative;
            width: 100%;
            z-index: 1;
            margin: auto;
            overflow: scroll;
            height: 700px;
        }

        .table thead {
            position: -webkit-sticky;
            position: sticky;
            top: 0 !important;
            z-index: 99 !important;
            background: rgb(249, 249, 249) !important;
        }

        .table th:first-child {
            position: sticky;
            left: 0;
            background: rgb(249, 249, 249) !important;
        }

        table tr:nth-child(1) th,
        table tr:nth-child(2) th {
            z-index: 1;
        }

        table tr:nth-child(1) th,
        table tr:nth-child(1) td {
            top: 0px;
            position: sticky;
        }

        table tr:nth-child(2) th,
        table tr:nth-child(2) td {
            top: 34px;
            position: sticky;
        }

        /* For Horizontal Sticky CSS */
        table tr:nth-child(1) th {
            top: 0px;
            position: sticky;
        }

        /* For Vertical CSS */
        th,
        table tr:nth-child(1) td {
            top: 0px;
            position: sticky;
        }

        .input-custom-width[readonly] {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .input-custom-width[readonly]:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .input-custom-width[readonly]:active {
            background-color: #dee2e6;
            border-color: #adb5bd;
            box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            transform: scale(0.98);
        }

        .input-custom-width[readonly]:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }
    </style>

@endsection
@section('page_js')
    <script>
        $(document).ready(function() {
            const detailForm = document.getElementById('attendanceProcessForm');
            const fv = FormValidation.formValidation(detailForm, {
                fields: {
                    reviewer_id: {
                        validators: {
                            notEmpty: {
                                message: 'Hr reviewer is required',
                            },
                        },
                    },
                    approver_id: {
                        validators: {
                            notEmpty: {
                                message: 'Approver is required',
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
            })

            $('[name="check_in_time"]').daterangepicker({
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

            $('[name="check_out_time"]').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 1,
                locale: {
                    format: ' HH:mm'
                }
            }).on('show.daterangepicker', function(ev, picker) {
                picker.container.find(".calendar-table").hide();
            });

            //allow only number in textbox using jQuery.
            // $('[name="donor"]').keypress(function (e) {
            // var charCode = (e.which) ? e.which : event.keyCode;
            // if (String.fromCharCode(charCode).match(/[^0-9]/g))
            //     return false;
            // });

            function updateAttendanceDetail(
                attendanceId,
                attendanceDate,
                checkInTime = null,
                checkOutTime = null,
                donorId = null,
                chargedHours = null
            ) {
                $.ajax({
                    url: "{{ route('attendance.detail.store') }}",
                    method: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'attendanceId': attendanceId,
                        'attendanceDate': attendanceDate,
                        'checkInTime': checkInTime,
                        'checkOutTime': checkOutTime,
                        'donorId': donorId,
                        'chargedHours': chargedHours,
                    },
                    success: function(data) {
                        console.log(data);
                        document.getElementById(attendanceDate + '.workedhours').innerText = data
                            .time_in_time_out_interval;
                        document.getElementById(attendanceDate + '.hoursCharged').innerText = data
                            .current_attendance_detail_charged_hours;
                        document.getElementById('totalHoursWorked').innerText = data.total_worked_hours;

                        document.getElementById('totalUnrestrictedHours').innerText = data
                            .total_unrestricted_hours;
                        document.getElementById('totalUnrestrictedPercentage').innerText = data
                            .total_unrestricted_percentage + '%';
                        document.getElementById('totalChargedHours').innerText = data
                            .total_charged_hours;
                        document.getElementById('totalChargedPercentage').innerText = data
                            .total_charged_percentage + '%';

                        data.donor_charges.forEach(donor_charge => {
                            let chargedHours = donor_charge.charged_hours;
                            let chargedPercentage = donor_charge.charged_percentage;
                            document.getElementById(donor_charge.donor_id + '.chargedHours')
                                .innerText =
                                chargedHours;
                            document.getElementById(donor_charge.donor_id +
                                    '.chargedPercentage')
                                .innerText = chargedPercentage + '%';
                        });

                        document.getElementById(attendanceDate + '.currentUnrestrictedHour').value =
                            data
                            .current_attendance_detail_unrestricted_hours;


                        console.log(data);
                    },
                    error: function(data) {
                        toastr.error(data.responseJSON.failure, 'Error', {
                            timeOut: 4000
                        });
                    }
                });
            }

            let hours_worked = 0;

            $("input[name=check_in_time]").on('change', function(e) {
                let attendanceId = "{{ $attendanceId }}";
                let attendanceDate = e.target.getAttribute('data-attendance-date');
                let checkInTime = e.target.value || 0;
                let checkOutTime = null;
                updateAttendanceDetail(attendanceId, attendanceDate, checkInTime, checkOutTime);
            });

            $("input[name=check_out_time]").on('change', function(e) {
                let attendanceId = "{{ $attendanceId }}";
                let attendanceDate = e.target.getAttribute('data-attendance-date');
                let checkInTime = null;
                let checkOutTime = e.target.value || 0;
                updateAttendanceDetail(attendanceId, attendanceDate, checkInTime, checkOutTime);
            });

            $("input[name=donor]").on('change', function(e) {
                let attendanceId = "{{ $attendanceId }}";
                let attendanceDate = e.target.getAttribute('data-attendance-date');
                let checkInTime = null;
                let checkOutTime = null;
                let donorId = e.target.getAttribute('data-donor-id');
                let chargedHours = parseFloat(e.target.value) || 0;
                whole = parseInt(chargedHours);
                fraction = chargedHours - whole;
                if (fraction > 0.59) {
                    fraction = fraction + 0.4
                }
                chargedHours = parseFloat(whole + fraction).toFixed(2);
                $(this).val(chargedHours);
                updateAttendanceDetail(attendanceId, attendanceDate, checkInTime, checkOutTime, donorId,
                    chargedHours);
            });

            $(document).on('click', '.open-donor-modal-form', function(e) {
                let donorInput = $(this);
                let attendanceDate = donorInput.attr('data-attendance-date');
                let donorId = donorInput.attr('data-donor-id');
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('data-href'),
                    function() {
                        const donorForm = document.getElementById('donorForm');
                        $(donorForm).find(".select2").each(function() {
                            $(this)
                                .wrap("<div class=\"position-relative\"></div>")
                                .select2({
                                    dropdownParent: $(this).parent(),
                                    width: '100%',
                                    dropdownAutoWidth: true
                                });
                        });

                        //
                        $(donorForm).on('input', '[name="chargedHours"]', function() {
                            donorInput.val($(this).val());
                            let chHr = this.value;
                            let chHrArray = chHr.split('.');
                            let hr = chHrArray[0];
                            if (chHrArray.length > 1) {
                                let min = chHrArray[1];
                                min = min.padEnd(2, '0');
                                $('#hours').text(hr);
                                $('#minutes').text(min);
                            } else {
                                $('#hours').text(hr);
                                $('#minutes').text('00');
                            }
                        })

                        const fv = FormValidation.formValidation(donorForm, {
                            fields: {
                                activities: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Description of Activities are required',
                                        },
                                    },
                                },
                                project_id: {
                                    validators: {
                                        notEmpty: {
                                            message: 'Project is required',
                                        },
                                    },
                                },
                                chargedHours: {
                                    validators: {
                                        callback: {
                                            callback: function(input) {
                                                let value = input.element.value;
                                                let minute = (value + "").split(".")[1];
                                                minute = minute?.padEnd(2, '0') || 0;
                                                return (minute >= 60 || minute.length > 2) ?
                                                    false : true;
                                            },
                                            message: 'Invalid hours format. Minutes should be less than 60',
                                        },
                                        greaterThan: {
                                            min: 0,
                                            message: 'Invalid hours format',
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
                            var successCallback = function(data) {
                                $('#openModal').modal('hide');
                                toastr.success(data.message, 'Success', {
                                    timeOut: 5000
                                });
                                oTable?.ajax?.reload();
                                //donorInput.val(data.donor_charges.find(item => item
                                //    .donor_id == donorId)?.charged_hours);
                                document.getElementById(attendanceDate + '.workedhours')
                                    .innerText = data
                                    .time_in_time_out_interval;
                                document.getElementById(attendanceDate + '.hoursCharged')
                                    .innerText = data
                                    .current_attendance_detail_charged_hours;
                                document.getElementById('totalHoursWorked').innerText = data
                                    .total_worked_hours;

                                document.getElementById('totalUnrestrictedHours')
                                    .innerText = data
                                    .total_unrestricted_hours;
                                document.getElementById('totalUnrestrictedPercentage')
                                    .innerText = data
                                    .total_unrestricted_percentage + '%';
                                document.getElementById('totalChargedHours').innerText =
                                    data
                                    .total_charged_hours;
                                document.getElementById('totalChargedPercentage')
                                    .innerText = data
                                    .total_charged_percentage + '%';

                                data.donor_charges.forEach(donor_charge => {
                                    let chargedHours = donor_charge.charged_hours;
                                    let chargedPercentage = donor_charge
                                        .charged_percentage;
                                    document.getElementById(donor_charge.donor_id +
                                            '.chargedHours')
                                        .innerText =
                                        chargedHours;
                                    document.getElementById(donor_charge.donor_id +
                                            '.chargedPercentage')
                                        .innerText = chargedPercentage + '%';
                                });

                                document.getElementById(attendanceDate +
                                        '.currentUnrestrictedHour').value =
                                    data
                                    .current_attendance_detail_unrestricted_hours;


                            }
                            let errorCallBack = function(err) {
                                showErrorMessageInSweatAlert(err);
                            }
                            ajaxSubmit($url, 'POST', data, successCallback, errorCallBack);
                        });
                    });
            });
        });
    </script>
@endsection




@section('page-content')


    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ route('profile.show') }}"
                                class="text-decoration-none text-dark">Profile</a>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.show', $attendance->employee->id) }}"
                                class="text-decoration-none text-dark">{{ __('label.attendance') }}</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            <div class="d-flex justify-content-between">
                @if (in_array($attendance->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]))
                    <a href="{{ route('attendance.detail.recalculate', $attendance->id) }}"
                        class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-clockwise"></i> Recalculate</a>
                @endif
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6 col-lg-8">
                    <div class="mb-3 print-code fs-6 fw-bold">
                        Staff Attendance Record
                    </div>
                    <div class="mb-3 print-header-info">
                        <ul class="p-0 m-0 list-unstyled fs-7">
                            <li><span class="fw-bold me-2">Staff
                                    Name:</span><span>{{ $attendance->employee->getFullName() }}</span>
                            </li>
                            <li><span
                                    class="fw-bold me-2">Title:</span><span>{{ $attendance->employee->latestTenure->getDesignationName() }}</span>
                            </li>
                            <li><span class="fw-bold me-2">Duty
                                    station:</span><span>{{ $attendance->employee->latestTenure->getDutyStation() }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <ul class="mt-4 list-unstyled fs-7">
                        <li class="mb-2 d-flex justify-content-end align-items-center">
                            <span
                                class="fw-bold me-2">Month:</span><span>{{ date('F', mktime(0, 0, 0, $attendance->month, 10)) }}</span>
                        </li>
                        <li class="d-flex justify-content-end align-items-center">
                            <span class="fw-bold me-2">Year:</span><span>{{ $attendance->year }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="wrapper table-responsive">
                <table class="table mb-0 table-borderless table-bordered">
                    <thead>
                        <tr>
                            <th class="sticky-col first-col" scope="row">Days</th>
                            @foreach ($dates as $date)
                                @if ($date->get('holiday'))
                                    <th scope="column" class="holiday">{{ $date->get('day_name') }}</th>
                                @else
                                    <th scope="column">{{ $date->get('day_name') }}</th>
                                @endif
                            @endforeach
                            <th scope="column">Total</th>
                            <th scope="column">Charge</th>
                        </tr>
                        <tr>
                            <th class="sticky-col first-col" scope="row">Date</th>
                            @foreach ($dates as $date)
                                @if ($date->get('holiday'))
                                    <th class="holiday">{{ $date->get('day') }}</th>
                                @else
                                    <th scope="column">{{ $date->get('day') }}</th>
                                @endif
                            @endforeach
                            <th scope="column">hh.mm</th>
                            <th scope="column">%</th>
                        </tr>
                        <tr>
                            <th scope="row" class="">Attendance</th>
                            @foreach ($dates as $date)
                                @if ($date->get('holiday'))
                                    <th class="holiday">
                                        @if ($date->get('is_annual_holiday'))
                                            @if ($date->get('leave'))
                                                {{ $date->get('in_travel') ? 'H / ' . $date->get('leave')['leave_abbreviation'] . '/ T' : 'H / ' . $date->get('leave')['leave_abbreviation'] }}
                                            @else
                                                {{ $date->get('in_travel') ? 'H / T' : 'H' }}
                                            @endif
                                        @else
                                            @if ($date->get('leave'))
                                                {{ $date->get('in_travel') ? 'X / ' . $date->get('leave')['leave_abbreviation'] . '/ T' : 'X / ' . $date->get('leave')['leave_abbreviation'] }}
                                            @else
                                                {{ $date->get('in_travel') ? 'X / T' : 'X' }}
                                            @endif
                                        @endif
                                    </th>
                                @else
                                    @if ($date->get('leave'))
                                        <th>{{ $date->get('leave')['leave_abbreviation'] }}</th>
                                    @else
                                        @if ($date->get('check_in_time') && $date->get('check_out_time'))
                                            <th>{{ $date->get('in_travel') ? 'T' : 'P' }}</th>
                                        @else
                                            <th>{{ $date->get('in_travel') ? 'T' : '' }}</th>
                                        @endif
                                    @endif
                                @endif

                            @endforeach
                            <th scope="column"></th>
                            <th scope="column"></th>
                        </tr>



                        <tr>
                            <th scope="row" class="">Time In (hh:mm)</th>
                            @foreach ($dates as $date)
                                @if (!$date->get('holiday'))
                                    <td>
                                        <input data-toggle="datepicker-time" type="text" name="check_in_time"
                                            data-attendance-date="{{ $date->get('date') }}"
                                            value="{{ $date->get('check_in_time') }}" class="input-custom-width"
                                            {{ $editable ? '' : 'disabled' }}
                                            {{ $date->get('disabled') ? 'disabled' : '' }}>
                                    </td>
                                @else
                                    <td></td>
                                @endif
                            @endforeach
                            <th scope="column"></th>
                            <th scope="column"></th>
                        </tr>

                        <tr>
                            <th scope="row" class="">Time Out (hh:mm)</th>
                            @foreach ($dates as $date)
                                @if (!$date->get('holiday'))
                                    <td>
                                        <input data-toggle="datepicker-time" type="text" name="check_out_time"
                                            data-attendance-date="{{ $date->get('date') }}"
                                            value="{{ $date->get('check_out_time') }}" class="input-custom-width"
                                            {{ $editable ? '' : 'disabled' }}
                                            {{ $date->get('disabled') ? 'disabled' : '' }}>
                                    </td>
                                @else
                                    <td></td>
                                @endif
                            @endforeach
                            <th scope="column"></th>
                            <th scope="column"></th>
                        </tr>

                        <tr>
                            <th scope="row" class=""> Hours Worked (hh.hh)</th>
                            @php
                                $totalHoursWorked = 0;
                            @endphp
                            @foreach ($dates as $date)
                                <td class="fw-bold" id="{{ $date->get('date') . '.workedhours' }}">
                                    {{ $date->get('worked_hours') }}</td>
                            @endforeach
                            <th scope="column" id="totalHoursWorked">{{ $total_worked_hours }}</th>
                            <th scope="column"></th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            <th></th>
                        </tr>

                        <tr>
                            <th scope="row" class=""><strong>Time Charge (hh.mm)</strong></th>
                        </tr>

                        @php
                            if (!empty($attendance->getDonorCodes())) {
                                $donors = $donors->whereIn('id', $attendance->getDonorCodes());
                            }
                        @endphp

                        @foreach ($donors as $donor)
                            <tr>
                                <th scope="row" class="text-wrap">{{ $donor->description }}</th>
                                @foreach ($dates as $date)
                                    @php
                                        $donor_lists = $date->get('donor_list');
                                        $hour_charged = '';
                                        foreach ($donor_lists as $donor_list) {
                                            if ($donor_list['donor_id'] == $donor->id) {
                                                $hour_charged = $donor_list['worked_hours'];
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if (!$date->get('holiday'))
                                        <td>
                                            <input type="number" min="0" max="8"
                                                class="input-custom-width open-donor-modal-form" data-toggle="modal"
                                                data-href="{!! route('attendance.detail.donor.create', [
                                                    $attendanceId,
                                                    $donor->id,
                                                    'attendance_date' => $date->get('date'),
                                                ]) !!}" data-bs-toggle="modal" name="donor"
                                                data-attendance-date="{{ $date->get('date') }}"
                                                data-donor-id="{{ $donor->id }}" value="{{ $hour_charged }}"
                                                {{ $editable ? '' : 'disabled' }} readonly
                                                {{ $date->get('disabled') ? 'disabled' : '' }}>
                                        </td>
                                    @else
                                        <td>
                                            <input type="number" min="0" max="8"
                                                class="input-custom-width open-donor-modal-form" data-toggle="modal"
                                                data-href="{!! route('attendance.detail.donor.create', [
                                                    $attendanceId,
                                                    $donor->id,
                                                    'attendance_date' => $date->get('date'),
                                                ]) !!}" data-bs-toggle="modal" name="donor"
                                                data-attendance-date="{{ $date->get('date') }}"
                                                data-donor-id="{{ $donor->id }}" value="{{ $hour_charged }}"
                                                {{ $editable ? '' : 'disabled' }} readonly
                                                {{ $date->get('disabled') ? 'disabled' : '' }}
                                                style="border: 1px solid #f77065;">
                                        </td>
                                    @endif
                                @endforeach

                                <th scope="column" id="{{ $donor->id . '.chargedHours' }}">
                                    @foreach ($donor_charges as $donor_charge)
                                        @if ($donor_charge['donor_id'] == $donor->id)
                                            {{ $donor_charge['charged_hours'] }}
                                        @endif
                                    @endforeach
                                </th>
                                <th scope="column" id="{{ $donor->id . '.chargedPercentage' }}">
                                    @foreach ($donor_charges as $donor_charge)
                                        @if ($donor_charge['donor_id'] == $donor->id)
                                            {{ $donor_charge['charged_percentage'] }} %
                                        @endif
                                    @endforeach
                                </th>
                            </tr>
                        @endforeach

                        <tr>
                            <th scope="row" class="">{{ $unrestrictedDonor?->description ?: 'Unrestricted' }}
                            </th>
                            @foreach ($dates as $date)
                                <td>
                                    <input type="number" min="0" max="8"
                                        class="input-custom-width open-donor-modal-form" data-toggle="modal"
                                        data-href="{!! $unrestrictedDonor
                                            ? route('attendance.detail.donor.create', [
                                                $attendanceId,
                                                $unrestrictedDonor->id,
                                                'attendance_date' => $date->get('date'),
                                            ])
                                            : '#' !!}" data-bs-toggle="modal" name="donor"
                                        name="unrestricted" data-attendance-date="{{ $date->get('date') }}"
                                        data-donor-id="{{ $unrestrictedDonor?->id }}"
                                        id="{{ $date->get('date') . '.currentUnrestrictedHour' }}" readonly
                                        value="{{ $date->get('unrestricted_hours') }}">
                                </td>
                            @endforeach
                            <th scope="column" id="totalUnrestrictedHours">{{ $total_unrestricted_hours }}</th>
                            <th scope="column" id="totalUnrestrictedPercentage">{{ $total_unrestricted_percentage }}%
                            </th>
                        </tr>

                    </tbody>
                    <tfoot>
                        <tr>
                        <tr class="total">
                            <th scope="row" class=""><strong>Hours Charged (hh.mm)</strong>
                            </th>
                            @foreach ($dates as $date)
                                <td id="{{ $date->get('date') . '.hoursCharged' }}">{{ $date->get('hours_charged') }}
                                </td>
                            @endforeach
                            <th scope="column" id="totalChargedHours">{{ $total_charged_hours }}</th>
                            <th scope="column" id="totalChargedPercentage">{{ $total_charged_percentage }} %</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        {{-- <div class="gap-2 border-0 card-footer justify-content-end d-flex"> --}}
        {{--    <a as="button"  title="Save attendance details" class="btn btn-primary btn-sm" href="{{route('attendance.detail.update', $attendance->id)}}"> --}}
        {{-- onclick="location.reload()"> --}}
        {{-- <i class="bi bi-save"></i> Save --}}
        {{-- </a> --}}
        {{-- </div> --}}
    </div>

    @include('EmployeeAttendance::Partials.sub-worklogs', [
        'attendance' => $attendance,
        'enabledDonors' => $enabledDonors->push($unrestrictedDonor),
    ])

    @if (auth()->user()->can('submit', $attendance))
        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    Attendance Process
                </div>
                <form action="{{ route('attendance.submit') }}" id="attendanceProcessForm" method="post"
                    enctype="multipart/form-data" autocomplete="off">

                    <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                @if ($attendance->status_id == config('constant.RETURNED_STATUS'))
                                    <section>
                                        <div class="m-2 col-lg-6">
                                            <div class="p-3 mb-2 border row">
                                                <div>
                                                    <div class="d-flex align-items-start h-100">
                                                        <span class="fw-bold"
                                                            style="text-decoration: underline">Remarks:</span>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <span>{{ $attendance->getLatestRemark() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-2 row">
                                    <div class="col-lg-5">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="status_id" class="m-0">Remarks</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <textarea class="form-control" name="remarks" id="remarks" rows="2">{{ $attendance->remarks ?: old('remarks') }}</textarea>
                                        @if ($errors->has('remarks'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="remarks">
                                                    {!! $errors->first('remarks') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <div class="col-lg-5">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="approver_id" class="form-label required-label">Select
                                                Approver</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <select name="approver_id" id="approver_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select Approver</option>
                                            @foreach ($approvers as $reviewer)
                                                <option value="{{ $reviewer->id }}">{{ $reviewer->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('reviewer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reviewer_id">
                                                    {!! $errors->first('reviewer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2 row">
                                    <div class="col-lg-5">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="status_id" class="form-label required-label">Submit to HR for
                                                verification </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <select name="reviewer_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select HR</option>
                                            @foreach ($reviewers as $reviewer)
                                                <option value="{{ $reviewer->id }}">{{ $reviewer->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('reviewer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="reviewer_id">
                                                    {!! $errors->first('reviewer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                {!! csrf_field() !!}
                            </div>
                        </div>
                    </div>
                    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                        <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm"
                            {{ $editable ? '' : 'disabled' }}>
                            Submit
                        </button>
                        <a href="{!! route('attendance.detail.show', $attendance->id) !!}" class="btn btn-danger btn-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </section>
    @endif
@endsection
