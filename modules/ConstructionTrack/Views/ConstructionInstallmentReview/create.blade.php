@extends('layouts.container')

@section('title', 'Review Construction Installment')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#construction-installment-review').addClass('active');
        });


        var oTable = $('#constructionParyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.parties.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'party_name',
                    name: 'party_name'
                },
                {
                    data: 'contribution_amount',
                    name: 'contribution_amount'
                },
                {
                    data: 'contribution_percentage',
                    name: 'contribution_percentage'
                },
                @if ($authUser->can('hideAction', $construction))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                @endif
            ]
        });


        var oTable = $('#constructionProgressTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.progress.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'report_date',
                    name: 'report_date'
                },
                {
                    data: 'progress_percentage',
                    name: 'progress_percentage'
                },
                {
                    data: 'estimate',
                    name: 'estimate'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
            ]
        });


        var oTable = $('#constructionInstallmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.installment.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'installment_number',
                    name: 'installment_number'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'advance_release_date',
                    name: 'advance_release_date'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'status',
                    name: 'status'
                },
            ],
        });

        $(function() {
            $.ajax({
                url: "{{route('construction.installment.totalAmount')}}",
                method: 'POST',
                data: {
                    '_token': "{{csrf_token()}}",
                    'constructionId': "{{$construction->id}}"
                },
                success: function(data) {
                    let totalSum = data.sum;
                    document.getElementById('totalSumOfInstallment').innerText = totalSum;
                },
                error: function(error) {
                    //
                }
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
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('construction.index') }}"
                                        class="text-decoration-none">Construction</a>
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
                                <div class="row mb-2">
                                    <div class="col-lg-12">
                                        <label for="validationRemarks" class="m-0">General Information</label>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    {{-- <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Year</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('construction_year')) is-invalid @endif"
                                               type="year"  name="construction_year" value="{{ old('construction_year')?: $construction->construction_year->format('Y') }}"/>
                                        @if ($errors->has('construction_year'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="construction_year">
                                                    {!! $errors->first('construction_year') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div> --}}

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Signed Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control" name="signed_date" readonly
                                            value="{{ $construction->signed_date ? $construction->signed_date->format('Y-m-d') : '' }}" />
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Health Facility
                                                Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('health_facility_name')) is-invalid @endif"
                                            type="text" name="health_facility_name" readonly
                                            value="{{ old('health_facility_name') ?: $construction->health_facility_name }}" />
                                        @if ($errors->has('health_facility_name'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="health_facility_name">
                                                    {!! $errors->first('health_facility_name') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Nepali Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('nepali_date')) is-invalid @endif"
                                            type="text" readonly name="nepali_date"
                                            value="{{ $construction->getSignedBsDate() }}" />
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Type of
                                                Facility</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('facility_type')) is-invalid @endif"
                                            type="text" name="facility_type" readonly
                                            value="{{ old('facility_type') ?: $construction->facility_type }}" />
                                        @if ($errors->has('facility_type'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="facility_type">
                                                    {!! $errors->first('facility_type') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Effective Date AD From</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('effective_date_from')) is-invalid @endif"
                                            name="effective_date_from" readonly
                                            value="{{ $construction->getEffectiveFromDate() }}" />
                                        @if ($errors->has('effective_date_from'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="effective_date_from">
                                                    {!! $errors->first('effective_date_from') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Type of Work</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('type_of_work')) is-invalid @endif"
                                            type="text" name="type_of_work" readonly
                                            value="{{ old('type_of_work') ?: $construction->type_of_work }}" />
                                        @if ($errors->has('type_of_work'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="type_of_work">
                                                    {!! $errors->first('type_of_work') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Effective Date BS From</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control" readonly name="effective_date_from_bs"
                                            value="{{ $construction->getEffectiveFromBsDate() }}" />
                                    </div>
                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Province</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedProvinceId = $construction->province_id; @endphp
                                        <select name="province_id" disabled class="select2 form-control"
                                            data-width="100%">
                                            <option value="">Select a Province</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}" data-purchase="{{ $province->id }}"
                                                    {{ $province->id == $selectedProvinceId ? 'selected' : '' }}>
                                                    {{ $province->getProvinceName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('province_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="province_id">
                                                    {!! $errors->first('province_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Effective Date AD to</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text" class="form-control" name="effective_date_from" readonly
                                            value="{{ $construction->getEffectiveToDate() }}" />
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">District</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedDistrictId = $construction->district_id; @endphp
                                        <select name="district_id" disabled class="select2 form-control"
                                            data-width="100%">
                                            <option value="">Select a District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}" data-purchase="{{ $district->id }}"
                                                    {{ $district->id == $selectedDistrictId ? 'selected' : '' }}>
                                                    {{ $district->getDistrictName() }}
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
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Effective Date BS To</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <input class="form-control" type="text" readonly name="effective_date_bs_to"
                                            value="{{$construction->getEffectiveToBsDate()}}" />
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Local Level</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        @php $selectedLocalId = $construction->local_level_id; @endphp
                                        <select name="local_level_id" disabled class="select2 form-control"
                                            data-width="100%">
                                            <option value="">Select a Local Level</option>
                                            @foreach ($localLevels as $local)
                                                <option value="{{ $local->id }}" data-purchase="{{ $local->id }}"
                                                    {{ $local->id == $selectedLocalId ? 'selected' : '' }}>
                                                    {{ $local->getLocalLevelName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('local_level_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="local_level_id">
                                                    {!! $errors->first('local_level_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">OHW Contribution Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('ohw_contribution')) is-invalid @endif"
                                            type="number" name="ohw_contribution" readonly
                                            value="{{ $construction->ohw_contribution }}" />
                                        @if ($errors->has('ohw_contribution'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="ohw_contribution">
                                                    {!! $errors->first('ohw_contribution') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Engineer Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="hidden" name="engineer_id" id="engineer_id" value="{{$construction->engineer_id}}">
                                        <select class="select2" name="engineer_id_2" id="engineer_id_2" disabled>
                                            <option value="" selected disabled>Select engineer</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}" {{$employee->id == $construction->engineer_id ? 'selected' : ''}}>{{$employee->getFullName()}}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('engineer_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="engineer_id">
                                                    {!! $errors->first('engineer_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">Approval</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" disabled type="checkbox" role="switch"
                                                id="physicallyabled" name="approval"
                                                @if ($construction->approval == 1) checked @endif disabled>
                                            <label class="form-check-label" for="physicallyabled"></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0 ">Total Contribution</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="number" readonly name="total_contribution_amount" readonly
                                            class="form-control" value="{{ $construction->total_contribution_amount }}">
                                        @if ($errors->has('total_contribution_amount') ?: $construction->total_contribution_amount)
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="total_contribution_amount">
                                                    {!! $errors->first('total_contribution_amount') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="m-0">OHW Contribution Percentage</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control @if ($errors->has('total_contribution_percentage')) is-invalid @endif"
                                            type="number" readonly name="total_contribution_percentage"
                                            value="{{ $construction->total_contribution_percentage }}" />
                                        @if ($errors->has('total_contribution_percentage'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="total_contribution_percentage">
                                                    {!! $errors->first('total_contribution_percentage') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <hr>

                                <div class="card">
                                    <div class="card-header fw-bold">
                                        Parties
                                    </div>
                                    <div class="card-body p-0 m-0">
                                        <div class="table-responsive">
                                            <table class="table" id="constructionParyTable" style="margin: 0px !important">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col">Party Name</th>
                                                        <th scope="col">Contribution</th>
                                                        <th scope="col">C%</th>
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
                                        Progress
                                    </div>
                                    <div class="card-body p-0 m-0">
                                        <div class="table-responsive">
                                            <table class="table" id="constructionProgressTable" style="margin: 0px !important">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col">Report Date</th>
                                                        <th scope="col">Progress Percentage</th>
                                                        <th scope="col">Estimate</th>
                                                        <th scope="col">Remarks</th>
                                                        <th scope="col">Attachment</th>
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
                                        Installment
                                    </div>
                                    <div class="card-body p-0 m-0">
                                        <div class="table-responsive">
                                            <table class="table" id="constructionInstallmentTable" style="margin: 0px !important">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col">Installment</th>
                                                        <th scope="col">Amount</th>
                                                        <th scope="col">Date</th>
                                                        <th scope="col">Remarks</th>
                                                        <th scope="col">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                            <div class="m-2 text-end">
                                                <span class="fw-bold">Total Sum of Installment: </span><span id="totalSumOfInstallment"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="card">
                    <div class="card-header fw-bold">
                        Process Construction Installment
                    </div>
                    <form action="{{route('construction.installment.review.store', $installment->id)}}"
                          id="constructionInstallmentProcessForm" method="POST" autocomplete="off">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    @foreach($installment->logs as $log)
                                        <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                            <div width="40" height="40"
                                                class="rounded-circle mr-3 user-icon">
                                                <i class="bi-person-circle fs-5"></i>
                                            </div>
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex flex-row align-items-center">
                                                        <span class="me-2">{{ $log->createdBy->getFullName() }}</span>
                                                        <span class="badge bg-primary c-badge">
                                                            {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                                        </span>
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

                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="approver_id" class="form-label required-label">Approver</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="approver_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select approver</option>
                                                @foreach ($approvers as $user)
                                                    <option value="{{$user->id}}" {{old('approver_id') == $user->id ? 'selected' : ''}}>{{$user->getFullName()}}</option>
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
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="status_id" class="form-label required-label">Status </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="status_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select Status</option>
                                                <option value="{{ config('constant.RETURNED_STATUS') }}" {{old('status_id') == config('constant.RETURNED_STATUS') ? 'selected' : ''}}>Return to Employee</option>
                                                <option value="{{ config('constant.VERIFIED_STATUS') }}" {{old('status_id') == config('constant.VERIFIED_STATUS') ? 'selected' : ''}}>Verify</option>
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
                                                <label for="remarks" class="form-label required-label">Remarks </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text"
                                                      class="form-control @if ($errors->has('remarks')) is-invalid @endif"
                                                      name="remarks">{{ old('remarks') }}</textarea>
                                            @if ($errors->has('remarks'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="remarks">{!! $errors->first('remarks') !!}</div>
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
                            <a href="{{ route('construction.installment.review.index') }}"
                               class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>



        </div>
    </div>

@stop
