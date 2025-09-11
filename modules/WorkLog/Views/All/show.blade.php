@extends('layouts.container')

@section('title', 'View All Work Plan')

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
            $('#navbarVerticalMenu').find('#all-work-logs-menu').addClass('active');
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
            columns: [{
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
                    className: 'activity-col'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
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
                        <li class="breadcrumb-item" aria-current="page">
                            <a href="{!! route('all.monthly.work.logs.index') !!}" class="text-decoration-none text-dark">All Worklog</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            Monthly Worklog View</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">View Monthly Worklog</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <div class="card-header fw-bold"> Monthly Worklog Information</div>
            <div class="card-body">
                @include('WorkLog::Partials.detail')
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive table-container">
                    <table class="table table-borderedless" id="workPlanDailyLogTable">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th style="width: 100px;">{{ __('label.date') }}</th>
                                <th style="width:25%;">{{ __('label.major-activities') }}</th>
                                <th class="">{{ __('label.activity-area') }}</th>
                                <th class="">{{ __('label.priority') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.other-activities') }}</th>
                                <th>{{ __('label.remarks') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('WorkLog::Partials.logs')
    </section>
@stop
