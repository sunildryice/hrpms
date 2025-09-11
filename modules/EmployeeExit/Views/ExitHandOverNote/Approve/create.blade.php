@extends('layouts.container')

@section('title', 'Show Employee Exit Hand Over Note')
@section('page_css')
@endsection
@section('page_js')
    <script type="text/javascript">
        $('#navbarVerticalMenu').find('#approve-exit-handover-note').addClass('active');
        var oTable = $('#handoverProjectTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'project', name: 'project'},
                {data: 'action_needed', name: 'action_needed'},
                {data: 'partners', name: 'partners'},
                {data: 'budget', name: 'budget'},
                {data: 'critical_issues', name: 'critical_issues'},
                {data: 'project_status', name: 'project_status'},
                 @if($authUser->can('update', $exitHandOverNote))
                {data: 'action', name: 'action', orderable: false, searchable: false},
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
            columns: [
                {data: 'activity', name: 'activity'},
                {data: 'organization', name: 'organization'},
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'comments', name: 'comments'},
                 @if($authUser->can('update', $exitHandOverNote))
                {data: 'action', name: 'action', orderable: false, searchable: false},
                 @endif
            ]
        });

         var oTable = $('#handoverDocumentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('document.exit.handover.note.index', $exitHandOverNote->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'attachment_name', name: 'attachment_name'},
                {data: 'attachment_type', name: 'attachment_type'},
                {data: 'attachment', name: 'attachment'},
               @if($authUser->can('update', $exitHandOverNote))
                {data: 'action', name: 'action', orderable: false, searchable: false},
               @endif
            ]
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            //
            const form = document.getElementById('handoverApproveForm');
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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
{{--                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">@yield('title')</a></li>--}}
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
               {{-- <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-wrench-adjustable me-1"></i> New Employee Exit</button>
                </div> --}}
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

                                <div class="col-lg-9 ">
                                    <input type="number" readonly value = "{{ $exitHandOverNote->contact_after_exit }}" class="form-control">
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
                                            @if($authUser->can('update', $exitHandOverNote))
                                        <th style="width: 130px;">Action</th>
                                            @endif
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
                                            @if($authUser->can('update', $exitHandOverNote))
                                        <th style="width: 130px;">Action</th>
                                            @endif
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

                                        <!-- <th style="width:45px;"></th> -->
                                        <th>Document Name</th>
                                        <th>Document Type</th>
                                        <th>Document</th>
                                        @if($authUser->can('update', $exitHandOverNote))
                                        <th style="width: 130px;">Action</th>
                                        @endif
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
                        <form action="{{ route('approve.exit.handover.note.store', $exitHandOverNote->id) }}"
                              id="handoverApproveForm" method="post"
                              enctype="multipart/form-data" autocomplete="off">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        @foreach($exitHandOverNote->logs as $log)
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
                                    <div class="col-lg-7">
                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label for="validationleavetype" class="form-label required-label">Status</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select name="status_id" class="select2 form-control"
                                                        data-width="100%">
                                                    <option value="">Select a Status</option>
                                                    <option value="2">Return to Requester</option>
                                                    <option value="6">Approve</option>
                                                    {{-- <option value="8">Reject</option> --}}
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
                                                    <label for="validationRemarks" class="form-label required-label">Remarks</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <textarea type="text"
                                                          class="form-control @if ($errors->has('log_remarks')) is-invalid @endif"
                                                          name="log_remarks">{{ old('log_remarks') }}</textarea>
                                                @if ($errors->has('log_remarks'))
                                                    <div class="fv-plugins-message-container invalid-feedback">
                                                        <div
                                                            data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        {!! csrf_field() !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                                <a href="{!! route('approve.exit.handover.note.index') !!}"
                                   class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </section>
@stop
