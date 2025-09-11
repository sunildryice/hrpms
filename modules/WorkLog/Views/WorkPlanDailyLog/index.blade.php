@extends('layouts.container')

@section('title', 'Work Log')

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
            $('#navbarVerticalMenu').find('#work-logs-menu').addClass('active');
        });

        var oTable = $('#workPlanDailyLogTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('daily.work.logs.index', $workLog) }}",
            bPaginate: false,
            bInfo: false,
            bFilter: false,
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'log_date',
                    name: 'log_date'
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
                    name: 'status'
                },
                {
                    data: 'other_activities',
                    name: 'other_activities',
                    className: 'activity-col',
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: "sticky-col"
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

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('workPlanLogAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    planned: {
                        validators: {
                            notEmpty: {
                                message: 'Planned field is required',
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

        $('#workPlanDailyLogTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.workPlanDailyLogCount) {
                    $('.submit-btn').show();
                } else {
                    $('.submit-btn').hide();
                }
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });
    </script>
@endsection

@section('page-content')

    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a href="{!! route('monthly.work.logs.index') !!}"
                                class="text-decoration-none text-dark">Worklog</a></li>
                        <li class="breadcrumb-item" aria-current="page">Daily Worklog</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Daily Worklog</h4>
            </div>
            <div class="add-info justify-content-end">
                @if ($authUser->can('addEditDailyLog', $workPlan))
                    <a class="btn btn-primary btn-sm" href="{!! route('daily.work.logs.create', $workPlan->id) !!}" rel="tooltip" title="Daily Worklog">
                        <i class="bi-form"></i>Add New
                    </a>
                @endif
            </div>
        </div>
    </div>
    <section class="registration">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive table-container">
                    <table class="table table-borderedless" id="workPlanDailyLogTable">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th style="width:45px;">SN</th>
                                <th style="width: 100px;">{{ __('label.date') }}</th>
                                <th style="width:25%;">{{ __('label.major-activities') }}</th>
                                <th class="">{{ __('label.activity-area') }}</th>
                                <th class="">{{ __('label.priority') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.other-activities') }}</th>
                                <th>{{ __('label.remarks') }}</th>
                                <th style="width: 140px;">{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <div class="card">
            <form class="g-3 needs-validation" action="{{ route('monthly.work.logs.submit', $workPlan->id) }}"
                id="workPlanLogAddForm" method="post" enctype="multipart/form-data" autocomplete="off" novalidate>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label class="m-0">{{ __('label.summary-of-major-tasks') }}</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea rows="5" class="form-control @if ($errors->has('summary')) is-invalid @endif" name="summary">
@if ($workPlan->status_id)
{{ $workPlan->summary }}
@endif
</textarea>
                            @if ($errors->has('summary'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="summary">{!! $errors->first('summary') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label class="form-label required-label">{{ __('label.planned') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" name="planned" value="{{ $workPlan->planned }}" id=""
                                class="form-control @if ($errors->has('planned')) is-invalid @endif">
                            @if ($errors->has('planned'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="planned">{!! $errors->first('planned') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label class="m-0">{{ __('label.completed') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" name="completed"
                                value="@if ($workPlan->status_id) {{ $workPlan->completed }} @endif" id=""
                                class="form-control @if ($errors->has('completed')) is-invalid @endif">
                            @if ($errors->has('completed'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="completed">{!! $errors->first('completed') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($authUser->can('submit', $workPlan))
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label class="m-0">Send To
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                @php $selectedApproverId = old('approver_id') ?: $workPlan->approver_id; @endphp
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
                    @endif

                    @if ($workPlan->status_id == config('constant.RETURNED_STATUS'))
                        <hr>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header fw-bold">
                                    <span>
                                        Remarks
                                    </span>
                                    <span class="{{ $workPlan->getStatusClass() }}"
                                        style="float: right">{{ $workPlan->getStatus() }}</span>
                                </div>
                                <div class="card-body">
                                    <div>
                                        {{ $workPlan->logs->where('status_id', config('constant.RETURNED_STATUS'))->last()?->log_remarks }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                </div>
                {!! csrf_field() !!}
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    @if (in_array($workPlan->status_id, [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]))
                        <button type="submit" name="btn" value="save"
                            class="btn btn-primary btn-sm next submit-btn">Save
                        </button>
                        @if ($authUser->can('submit', $workPlan))
                            <button type="submit" name="btn" value="submit"
                                class="btn btn-success btn-sm next submit-btn">Submit
                            </button>
                        @endif
                        <button type="reset" class="btn btn-danger btn-sm">Reset</button>
                    @endif
                </div>
            </form>
        </div>
    </section>
@stop
