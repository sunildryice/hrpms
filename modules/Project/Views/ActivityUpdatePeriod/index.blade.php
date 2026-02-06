@extends('layouts.container')

@section('title', 'Activity Update Periods')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            var disabledRanges = @json($ranges ?? []);
            var todayStr = "{{ now()->format('Y-m-d') }}";

            $('#navbarVerticalMenu').find('#activity-update-periods-index').addClass('active');
            var oTable = $('#activityUpdatePeriodTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('activity-update-periods.index') }}",
                bFilter: false,
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
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


            $('#activityUpdatePeriodTable').on('click', '.cancel-record', function(e) {
                e.preventDefault();
                let url = $(this).attr('data-href');
                let number = $(this).attr('data-number');
                let successCallback = function(response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000
                    });
                    oTable.ajax.reload();
                };
                ajaxTextSweetAlert(url, 'POST', `Cancel ${number}?`, 'Remarks', 'log_remarks',
                    successCallback);
            })

            $('#activityUpdatePeriodTable').on('click', '.delete-record', function(e) {
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

            $(document).on('click', '.open-activity-update-period-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('activityUpdatePeriodCreateForm') ?
                        document.getElementById('activityUpdatePeriodCreateForm') :
                        document.getElementById('activityUpdatePeriodEditForm');
                    if (!form) return;

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            start_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'Start date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'Invalid date format'
                                    }
                                }
                            },
                            end_date: {
                                validators: {
                                    notEmpty: {
                                        message: 'End date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'Invalid date format'
                                    }
                                }
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
                    }).on('core.form.valid', function() {
                        const $url = fv.form.action;
                        const data = $(fv.form).serialize();
                        const successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message || 'Saved successfully');
                            if (fv.form && fv.form.id ===
                                'activityUpdatePeriodEditForm') {
                                window.location.reload();
                            } else {
                                oTable.ajax.reload();
                            }
                        };
                        ajaxSubmit($url, 'POST', data, successCallback);
                    });

                    const actionUrl = form.action || '';
                    const idMatch = actionUrl.match(/activity-update-periods\/(\d+)\/update/);
                    const excludeId = idMatch ? idMatch[1] : null;

                    const today = new Date(todayStr + 'T00:00:00');
                    const ranges = disabledRanges
                        .filter(function(r) {
                            return !excludeId || String(r.id) !== String(excludeId);
                        })
                        .map(function(r) {
                            return {
                                start: new Date(r.start_date + 'T00:00:00'),
                                end: new Date(r.end_date + 'T23:59:59')
                            };
                        });

                    const allowDate = function(date) {
                        if (date < today) return false;
                        for (let i = 0; i < ranges.length; i++) {
                            if (date >= ranges[i].start && date <= ranges[i].end) {
                                return false;
                            }
                        }
                        return true;
                    };

                    $('#openModal').find('[name="start_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                        filter: allowDate,
                    }).on('change', function() {
                        fv.revalidateField('start_date');
                    });

                    $('#openModal').find('[name="end_date"]').datepicker({
                        language: 'en-GB',
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 2048,
                        filter: allowDate,
                    }).on('change', function() {
                        fv.revalidateField('end_date');
                    });
                });
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-activity-update-period-modal-form"
                        href="{{ route('activity-update-periods.create') }}" title="Add Update Period">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @if (isset($currentActiveRanges) && $currentActiveRanges->count() > 0)
            <div class="card mb-3 border-success shadow-sm">
                <div class="card-header bg-light">
                    <span class="text-uppercase fw-bold small">Current Update Period</span>
                </div>
                <div class="card-body py-4 d-flex align-items-center justify-content-between" style="min-height: 90px;">
                    <div>
                        <span class="badge bg-success me-3">Active Today</span>
                        @foreach ($currentActiveRanges as $range)
                            <span
                                class="text-dark fw-bold">{{ \Carbon\Carbon::parse($range->start_date)->format('M j, Y') }}
                                - {{ \Carbon\Carbon::parse($range->end_date)->format('M j, Y') }}</span>
                            @if (!$loop->last)
                                <span class="mx-3">|</span>
                            @endif
                        @endforeach
                    </div>
                    <small class="text-secondary">{{ now()->format('M j, Y') }}</small>
                </div>
            </div>
        @endif
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="activityUpdatePeriodTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.start-date') }}</th>
                                <th>{{ __('label.end-date') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@stop
