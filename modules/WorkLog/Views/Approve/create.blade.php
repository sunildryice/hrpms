@extends('layouts.container')

@section('title', 'Approve Worklog')

@section('page_css')
     <style>
        .table-container {
            overflow: auto;
        }
        .activity-col {
            min-width: 500px;
            max-width: 500px;
            white-space: pre-line;
        }
     </style>
@endsection
@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-work-logs-menu').addClass('active');
            $(".select2").select2({
                width: '100%',
                dropdownAutoWidth: true
            });
        });
        var oTable = $('#workPlanDailyLogTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('daily.work.logs.index', $workPlan->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'log_date',
                    name: 'log_date',
                    orderable: false,
                    className: 'first-col'
                },
                {
                    data: 'major_activities',
                    name: 'major_activities',
                    className: 'activity-col'
                },
                {
                    data: 'activity_area',
                    name: 'activity_area'
                },
                {
                    data: 'priority',
                    name: 'priority'
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'other_activities',
                    name: 'other_activities',
                    className: 'activity-col'
                },
                {
                    data: 'remarks',
                    name: 'remarks',
                },
            ],
            initComplete: () => {
                const table = $('#workPlanDailyLogTable');
                const tableContainer = $('.table-container');
                const tableHeight = table[0].clientHeight;
                if (tableHeight > 682) {
                    tableContainer.css('height', 'calc(100vh - 215px)');
                }
            }
        });
        // console.log(oTable.rows().count());
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('approveWorkLogForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    log_remarks: {
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

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
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
                                <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                        class="text-decoration-none text-dark">Home</a></li>
                                {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                                <li class="breadcrumb-item" aria-current="page">
                                    <a href="{!! route('approve.work.logs.index') !!}" class="text-decoration-none">Worklog</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">
                                    <a href="#" class="text-decoration-none">Monthly Worklog Approval</a>
                                </li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Approve Monthly Worklog</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header fw-bold">
                                    Monthly Worklog Information
                                </div>
                                <div class="card-body">
                                    @include('WorkLog::Partials.detail')
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header fw-bold">Daily Work Logs</div>
                                <div class="card-body">
                                    <div class="table-responsive table-container">
                                        <table class="table table-borderedless" id="workPlanDailyLogTable">
                                            <thead class="bg-light sticky-top">
                                                <tr>
                                                    <th class="first-col">{{ __('label.date') }}</th>
                                                    <th  class="wrap-cell">{{ __('label.major-activities') }}</th>
                                                    <th class="">{{ __('label.activity-area') }}</th>
                                                    <th class="">{{ __('label.priority') }}</th>
                                                    <th>{{ __('label.status') }}</th>
                                                    <th class="wrap-cell">{{ __('label.other-activities') }}</th>
                                                    <th>{{ __('label.remarks') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header fw-bold">Work Log Process</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            @foreach ($workPlan->logs as $log)
                                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                                    <div width="40" height="40"
                                                        class="rounded-circle mr-3 user-icon">
                                                        <i class="bi-person"></i>
                                                    </div>
                                                    <div class="w-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                                <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                                                <span
                                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                            </div>
                                                            <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                                        </div>
                                                        <p class="text-justify comment-text mb-0 mt-1">
                                                            {{ $log->log_remarks }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-lg-12">
                                            <form action="{{ route('approve.work.logs.store', $workPlan->id) }}"
                                                id="approveWorkLogForm" method="post" enctype="multipart/form-data"
                                                autocomplete="off">
                                                <div class="card-body">
                                                    <div class="row mb-2">
                                                        <div class="col-lg-3">
                                                            <div class="d-flex align-items-start h-100">
                                                                <label for="validationleavetype"
                                                                    class="form-label required-label">Status</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <select name="status_id" class="select2 form-control"
                                                                data-width="100%">
                                                                <option value="">Select a Status</option>
                                                                <option value="2"
                                                                    @if (old('status_id') == '2') selected @endif>Return
                                                                    to Requester</option>
                                                                {{-- <option value="8" @if (old('status_id') == '8') selected @endif>Reject</option> --}}
                                                                {{-- <option value="4" @if (old('status_id') == '4') selected @endif>Recommend</option> --}}
                                                                <option value="6"
                                                                    @if (old('status_id') == '6') selected @endif>
                                                                    Approve</option>
                                                            </select>
                                                            @if ($errors->has('status_id'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="status_id">
                                                                        {!! $errors->first('status_id') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="row mb-2">
                                                        <div class="col-lg-3">
                                                            <div class="d-flex align-items-start h-100">
                                                                <label for="validationRemarks"
                                                                    class="form-label required-label">Remarks</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                            @if ($errors->has('log_remarks'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    {!! csrf_field() !!}
                                                </div>
                                                <div class=" justify-content-end d-flex gap-2">
                                                    {{-- <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save --}}
                                                    {{-- </button> --}}
                                                    <button type="submit" name="btn" value="submit"
                                                        class="btn btn-success btn-sm">
                                                        Submit
                                                    </button>
                                                    <a href="{!! route('approve.leave.requests.index') !!}"
                                                        class="btn btn-danger btn-sm">Cancel</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
@stop
