@extends('layouts.container')

@section('title', 'Show Employee Exit Handover Note')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $('#navbarVerticalMenu').find('#approved-exit-handover-note').addClass('active');

        var oTable = $('#handoverProjectTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'project',
                    name: 'project'
                },
                {
                    data: 'action_needed',
                    name: 'action_needed'
                },
                {
                    data: 'partners',
                    name: 'partners'
                },
                {
                    data: 'budget',
                    name: 'budget'
                },
                {
                    data: 'critical_issues',
                    name: 'critical_issues'
                },
                {
                    data: 'project_status',
                    name: 'project_status'
                },
                @if ($authUser->can('update', $exitHandOverNote))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                @endif
            ]
        });

        var oTable = $('#handoverActivityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('activity.exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'activity',
                    name: 'activity'
                },
                {
                    data: 'organization',
                    name: 'organization'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'comments',
                    name: 'comments'
                },
            ]
        });

        var oTable = $('#handoverDocumentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('document.exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'attachment_name',
                    name: 'attachment_name'
                },
                {
                    data: 'attachment_type',
                    name: 'attachment_type'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
            ]
        });

        $('#handoverDocumentTable').on('click', '.delete-record', function(e) {
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
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('approved.exit.handover.note.index') }}"
                                    class="text-decoration-none">Approved Handover Note</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for=""> Brief description of duties</label>
                                </div>
                                <div class="col-lg-9 ">
                                    <textarea rows="3" class="form-control" readonly>{{ $exitHandOverNote->duty_description }}</textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for=""> Reporting procedures</label>
                                </div>

                                <div class="col-lg-9 ">
                                    <textarea rows="3" class="form-control" readonly>{{ $exitHandOverNote->reporting_procedures }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for=""> Meeting Description </label>
                                </div>

                                <div class="col-lg-9 ">
                                    <textarea rows="3" class="form-control" readonly>{{ $exitHandOverNote->meeting_description }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for=""> Contact After Exit </label>
                                </div>

                                <div class="col-lg-3">
                                    <input type="text" readonly value="{{ $exitHandOverNote->contact_after_exit }}"
                                        class="form-control">
                                </div>
                                <div class="col-lg-3">
                                    <label for=""> Approver </label>
                                </div>

                                <div class="col-lg-3">
                                    <input type="text" readonly value="{{ $exitHandOverNote->getApproverName() }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-borderedless" id="handoverProjectTable">
                                    <thead class="bg-light">
                                        <tr>

                                            <!-- <th style="width:45px;"></th> -->
                                            <th class="">Name of project</th>
                                            <th>Action needed</th>
                                            <th>Partners</th>
                                            <th>Budget</th>
                                            <th>Critical issues</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>


                            <div class="table-responsive mb-3">
                                <table class="table table-borderedless" id="handoverActivityTable">
                                    <thead class="bg-light">
                                        <tr>

                                            <!-- <th style="width:45px;"></th> -->
                                            <th class="">Name</th>
                                            <th>Organization</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Comments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-borderedless" id="handoverDocumentTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Document Name</th>
                                            <th>Document Type</th>
                                            <th>Document</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header fw-bold">
                            Exit Handover Note Process
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    @foreach ($exitHandOverNote->logs as $log)
                                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                            <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                                <i class="bi-person"></i>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex flex-row align-items-center">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
