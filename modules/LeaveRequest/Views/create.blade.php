@extends('layouts.container')

@section('title', 'Add New Leave Request')

@section('page_js')
    <script type="text/javascript">
        var formValidationInstance = null;
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#leave-requests-menu').addClass('active');
        });

        var employeeId = '{{ auth()->user()->employee_id }}';
        var holidays = '{!! str_replace('&quot;', '', json_encode($holidays)) !!}';
        let leaveModes = (JSON.parse('{!! json_encode($leaveModes->map->only(['id', 'title', 'hours'])) !!}'));

        $('.submit-btn').attr('disabled', false)

        var getDateArray = function(start, end) {
            var arr = [];
            var dt = new Date(start);
            while (dt <= end) {
                arr.push(new Date(dt));
                dt.setDate(dt.getDate() + 1);
            }
            return arr;
        }
        var checkOverlapLeave = function($element) {
            startDate = $($element).closest('form').find('[name="start_date"]').val();
            endDate = $($element).closest('form').find('[name="end_date"]').val();
            if (startDate && endDate) {
                var url = baseUrl + '/api/employee/' + employeeId + '/check-leaves';
                data = {
                    start_date: startDate,
                    end_date: endDate
                };
                var successCallback = function(response) {
                    if (response.leaveDaysCount) {
                        toastr.warning(response.message, 'Warning', {
                            timeOut: 9000
                        });
                    }
                }
                var errorCallback = function(error) {
                    console.log(error);
                }
                ajaxNativeSubmit(url, 'POST', data, 'json', successCallback, errorCallback);
            }
        }

        var showHideAttachment = function($element) {
            let days = 0;
            var leaveType = $($element).closest('form').find('[name="leave_type_id"] option:selected').text();
            startDate = new Date($($element).closest('form').find('[name="start_date"]').val());
            endDate = new Date($($element).closest('form').find('[name="end_date"]').val());
            if (startDate instanceof Date && isFinite(startDate) && endDate instanceof Date && isFinite(endDate)) {
                diffTime = Math.abs(endDate - startDate);
                days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                days = isNaN(days) ? 0 : days + 1;
            }
            if (days > 3) {
                $($element).closest('form').find('#attachmentBlock').show();
            } else {
                $($element).closest('form').find('#attachmentBlock').hide();
            }
        }

        var generateLeaveTable = function($element) {
            leaveTypeId = $($element).closest('form').find('[name="leave_type_id"]').val();
            startDate = $($element).closest('form').find('[name="start_date"]').val();
            endDate = $($element).closest('form').find('[name="end_date"]').val();
            leaveBasis = $($element).closest('form').find('[name="leave_basis"]').val();
            includeWeekends = $($element).closest('form').find('[name="include_weekends"]').val();
            if (startDate && endDate && leaveTypeId) {
                var dateArr = getDateArray(new Date(startDate), new Date(endDate));
                var table = '<table class="table">' +
                    '<thead>' +
                    '<tr>' +
                    '<th>Day</th>' +
                    '<th>Date</th>' +
                    '<th>Leave Slots</th>' +
                    '<th>Time</th>' +
                    '</tr>' +
                    '</thead><tbody>';

                selectBox = '<select class="form-control leave_mode" name="leave_mode_id[]">';
                leaveModes.forEach(function(leaveMode, index) {
                    if (leaveBasis == 2) {
                        selectBox += '<option value="' + leaveMode.id + '">' + leaveMode.title + '</option>';
                    } else {
                        if (leaveMode.hours == 8) {
                            selectBox += '<option value="' + leaveMode.id + '">' + leaveMode.title +
                                '</option>';
                        }
                    }
                });
                selectBox += '</select>';

                count = 1;
                dateArr.forEach(function(date, index) {
                    var leaveDate = new Date(date).toLocaleDateString('sv');
                    if (holidays.includes(leaveDate)) {
                        if (includeWeekends == '1') {
                            table += '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td><input type="hidden" name="leave_days[]" value="' + leaveDate + '">' +
                                leaveDate + '</td>' +
                                '<td>' + selectBox + '</td>' +
                                '<td><input type="text" class="form-control" name="leave_time[]" readonly/></td>' +
                                '</tr>';
                            count++;
                        }
                    } else {
                        table += '<tr>' +
                            '<td>' + count + '</td>' +
                            '<td><input type="hidden" name="leave_days[]" value="' + leaveDate + '">' +
                            leaveDate + '</td>' +
                            '<td>' + selectBox + '</td>' +
                            '<td><input type="text" class="form-control" name="leave_time[]" readonly/></td>' +
                            '</tr>';
                        count++;
                    }
                });
                table += '</tbody></table>';
                $(document).find('#leaveDays').html(table);

                $(document).find('#leaveDays').find('.leave_mode').on('change', function(e) {
                    let leaveModeId = ($(this).find(':selected').val());
                    let leaveMode = leaveModes.find(x => x.id == leaveModeId);
                    if (leaveMode?.hours == 2) {
                        $(this).closest('tr').find('[name="leave_time[]"]').daterangepicker({
                            timePicker: true,
                            timePickerSeconds: false,
                            locale: {
                                format: 'HH:mm:ss'
                            },
                            endDate: '00:00:00',
                        }).on('show.daterangepicker', function(ev, picker) {
                            picker.container.find(".calendar-table").hide();
                        });
                    } else {
                        $(this).closest('tr').find('[name="leave_time[]"]').val('').data('daterangepicker')
                            ?.remove();
                    }
                });

            } else {
                $(document).find('#leaveDays').html('');
            }
            showHideAttachment($element);
        }

        $(document).ready(function() {
            $(".select2").select2({
                width: '100%',
                dropdownAutoWidth: true
            });
        });

        function getLeaveDays() {
            const form = document.getElementById('leaveRequestAddForm');
            const start = form.querySelector('[name="start_date"]').value;
            const end = form.querySelector('[name="end_date"]').value;

            if (!start || !end) {
                return 0;
            }

            const startDate = new Date(start);
            const endDate = new Date(end);

            if (isNaN(startDate) || isNaN(endDate)) {
                return 0;
            }

            let diff = Math.abs(endDate - startDate);
            let days = Math.ceil(diff / (1000 * 60 * 60 * 24)) + 1;

            return days;
        }


        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('leaveRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    leave_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Leave Type is required',
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
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'The start date is required',
                            },
                        },
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'The end date is required',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            callback: {
                                message: 'Prescription is required',
                                callback: function(input) {
                                    let days = getLeaveDays();

                                    if (days > 3) {
                                        return input.value !== ''; // Required
                                    }

                                    return true; // Not required
                                }
                            },

                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: '2097152',
                                message: 'The selected file must be valid and under 2 MB.',
                            }
                        }
                    }

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
                            field: 'start_date',
                            message: 'Start date must be a valid date and earlier than end date.',
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'End date must be a valid date and later than start date.',
                        },
                    }),
                },
            }).on('core.form.valid', function() {
                $('.submit-btn').attr('disabled', true)
            });


            $(form).on('change', '[name="leave_type_id"]', function(e) {
                $element = $(this);
                var leaveTypeId = $element.val();
                var employeeLeaveId = $($element).find(':selected').attr('data-leave');
                if (leaveTypeId) {
                    var url = baseUrl + '/api/employee/' + employeeId + '/leaves/' + employeeLeaveId +
                        '/show';
                    var successCallback = function(response) {
                        var balance = 'Balance : ' + response.leave.balance + ' - ' + response
                            .leaveBasis;
                        $($element).closest('form').find('[name="balance"]').val(balance);
                        $($element).closest('form').find('[name="leave_basis"]').val(response.leave
                            .leave_type.leave_basis);
                        $($element).closest('form').find('[name="include_weekends"]').val(response.leave
                            .leave_type.include_weekends);
                        generateLeaveTable($element);
                    }
                    var errorCallback = function(error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="balance"]').val(0);
                    $($element).closest('form').find('[name="leave_basis"]').val(1);
                    generateLeaveTable(this);
                }
                {{-- if(leaveType.includes('Sick')){ --}}
                {{--    $('[name="start_date"]').datepicker('destroy').datepicker({ --}}
                {{--        language: 'en-GB', --}}
                {{--        autoHide: true, --}}
                {{--        format: 'yyyy-mm-dd', --}}
                {{--    }); --}}
                {{--    $('[name="end_date"]').datepicker('destroy').datepicker({ --}}
                {{--        language: 'en-GB', --}}
                {{--        autoHide: true, --}}
                {{--        format: 'yyyy-mm-dd', --}}
                {{--    }); --}}
                {{-- } else { --}}
                {{--    $('[name="start_date"]').datepicker('destroy').datepicker({ --}}
                {{--        language: 'en-GB', --}}
                {{--        autoHide: true, --}}
                {{--        format: 'yyyy-mm-dd', --}}
                {{--        startDate: '{!! date('Y-m-d') !!}', --}}
                {{--    }).val(''); --}}
                {{--    $('[name="end_date"]').datepicker('destroy').datepicker({ --}}
                {{--        language: 'en-GB', --}}
                {{--        autoHide: true, --}}
                {{--        format: 'yyyy-mm-dd', --}}
                {{--        startDate: '{!! date('Y-m-d') !!}', --}}
                {{--    }).val(''); --}}
                {{-- } --}}
                fv.revalidateField('leave_type_id');
            }).on('change', '[name="approver_id"]', function(e) {
                fv.revalidateField('approver_id');
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                {{-- startDate: '{!! date('Y-m-d') !!}', --}}
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
                generateLeaveTable(this);
                checkOverlapLeave(this);
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                {{-- startDate: '{!! date('Y-m-d') !!}', --}}
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
                generateLeaveTable(this);
                checkOverlapLeave(this);
            });

            selectedLeaveModeIds = [];
            @if ($errors->count() || session('warning_message'))
                $(form).find('[name="leave_type_id"]').trigger('change');
                @if (old('leave_mode_id'))
                    @foreach (old('leave_mode_id') as $key => $value)
                        selectedLeaveModeIds.push({
                            key: '{{ $key }}',
                            value: '{{ $value }}',
                        });
                    @endforeach
                @endif
            @endif
        });
    </script>
@endsection
@section('page-content')


    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('leave.requests.index') }}" class="text-decoration-none text-dark">Leave
                                Requests</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="rounded border-0 shadow-sm card">
            <form action="{{ route('leave.requests.store') }}" id="leaveRequestAddForm" method="post"
                enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <div class="mb-3 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationleavetype" class="form-label required-label">Leave
                                    Type</label>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-8 mb-lg-0">
                            <select name="leave_type_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Leave Type</option>
                                @foreach ($leaveTypes as $leave)
                                    <option value="{{ $leave->leave_type_id }}" data-leave="{{ $leave->id }}"
                                        {{ $leave->leave_type_id == old('leave_type_id') ? 'selected' : '' }}>
                                        {{ $leave->leaveType->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('leave_type_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="leave_type_id">
                                        {!! $errors->first('leave_type_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control" disabled name="balance" value="" />
                            <input type="hidden" class="form-control" disabled name="leave_basis" value="" />
                            <input type="hidden" class="form-control" disabled name="include_weekends" value="" />
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label required-label">Start Date</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <input type="text"
                                class="form-control
                                                @if ($errors->has('start_date')) is-invalid @endif"
                                readonly name="start_date" value="{{ old('start_date') }}" />
                            @if ($errors->has('start_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="start_date">{!! $errors->first('start_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationreturndate" class="form-label required-label">End
                                    Date</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <input type="text"
                                class="form-control
                                                @if ($errors->has('end_date')) is-invalid @endif"
                                readonly name="end_date" value="{{ old('end_date') }}" />
                            @if ($errors->has('end_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="end_date">{!! $errors->first('end_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationSubstitutes" class="form-label">Substitutes</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <select name="substitutes[]"
                                class="select2
                                                @if ($errors->has('substitutes')) is-invalid @endif"
                                data-width="100%" multiple>
                                <option value="">Select substitutes</option>
                                @foreach ($substitutes as $staff)
                                    <option value="{{ $staff->id }}">
                                        {{ $staff->getFullNameWithCode() }}
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
                    <div class="mb-3 row" id="attachmentBlock" style="display: none;">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Upload Prescription</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <input type="file" name="attachment"
                                class="form-control js-document-upload @if ($errors->has('attachment')) is-invalid @endif" />
                            <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                            @if ($errors->has('attachment'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="attachment">{!! $errors->first('attachment') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label">Reason For the Leave</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif" name="remarks">{{ old('remarks') }}</textarea>
                            @if ($errors->has('remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Send
                                    To</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <select name="approver_id"
                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select an Approver</option>
                                @foreach ($supervisors as $approver)
                                    <option value="{{ $approver->id }}"
                                        {{ $approver->id == old('approver_id') ? 'selected' : '' }}>
                                        {{ $approver->getFullName() }}</option>
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
                    <div id="leaveDays">

                    </div>
                    {!! csrf_field() !!}
                </div>
                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                    </button>
                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm submit-btn">
                        Submit
                    </button>
                    <a href="{!! route('leave.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>



@stop
