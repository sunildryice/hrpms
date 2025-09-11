@extends('layouts.container')

@section('title', 'Show Construction Track')

@section('page_css')
    <style>

    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#construction-index').addClass('active');
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
            ],
            // drawCallback: function() {
            //     let table = this[0];
            //     let footer = table.getElementsByTagName('tfoot')[0];
            //     if (!footer) {
            //         footer = document.createElement("tfoot");
            //         table.appendChild(footer);
            //     }

            //     let totalProgressPercentage = this.api().column(1).data().reduce( function (a, b) {
            //         return parseFloat(b);
            //     },0 );

            //     let totalEstimate = this.api().column(2).data().reduce( function (a, b) {
            //         return parseFloat(a) + parseFloat(b);
            //     },0 );

            //     totalEstimate = new Intl.NumberFormat('en-US').format(totalEstimate);

            //     footer.innerHTML = '';
            //     footer.innerHTML = `<tr>
            //                             <td></td>
            //                             <td>Total Work Progress Percentage: ${totalProgressPercentage} %</td>
            //                             <td>Total Work Progress Amount: ${totalEstimate}</td>
            //                             <td></td>
            //                             <td></td>
            //                             <td colspan='3'></td>
            //                         </tr>`;
            // },
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
                    data: 'advance_release_date',
                    name: 'advance_release_date'
                },
                {
                    data: 'transaction_type',
                    name: 'transaction_type'
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
            ],
            drawCallback: function() {
                let data = this.api().ajax.json();
                let fundTransferredTotal = new Intl.NumberFormat('en-US').format(data.fund_transferred_total);
                let expenseSettledTotal = new Intl.NumberFormat('en-US').format(data.expense_settled_total);

                let table = this[0];
                let footer = table.getElementsByTagName('tfoot')[0];
                if (!footer) {
                    footer = document.createElement("tfoot");
                    table.appendChild(footer);
                }

                // let total_amount = this.api().column(2).data().reduce( function (a, b) {
                //     return parseFloat(a) + parseFloat(b);
                // },0 );
                // total_amount = new Intl.NumberFormat('en-US').format(total_amount);

                let difference_amount = Math.abs(data.fund_transferred_total - data.expense_settled_total);
                let formatted_difference_amount = new Intl.NumberFormat('en-US').format(difference_amount);
                let advance_or_payable = data.fund_transferred_total > data.expense_settled_total ? 'Advance NPR' : 'Payable NPR';

                footer.innerHTML = '';
                footer.innerHTML = `<tr>
                                        <td colspan='2'></td>
                                        <td colspan='3'>${advance_or_payable}: ${formatted_difference_amount} (Fund Transferred: ${fundTransferredTotal} , Expense Settled: ${expenseSettledTotal})</td>
                                    </tr>`;
            },
        });

        var oTable = $('#constructionAttachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.attachment.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        var oTable = $('#constructionAmendmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('construction.amendment.index', $construction->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'effective_date',
                    name: 'effective_date'
                },
                {
                    data: 'extension_to_date',
                    name: 'extension_to_date'
                },

                {
                    data: 'total_estimate_cost',
                    name: 'total_estimate_cost'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                }
            ]
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
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Signed Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input class="form-control" name="signed_date" readonly
                                            value="{{ $construction->signed_date ? $construction->signed_date->format('Y-m-d') : '' }}" />
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


                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdd" class="form-label required-label">Engineer Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <input type="text" class="form-control" disabled name="engineer_id" id="engineer_id" value="{{$construction->getEngineerName()}}">
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
                                            <label for="validationdd" class="m-0 ">Total Estimate Cost</label>
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

                                    {{-- <div class="col-lg-3">
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
                                    </div> --}}

                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="work_start_date" class="m-0">Work Start Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                       <input type="text" readonly class="form-control @if($errors->has('work_start_date')) is-invalid @endif" name="work_start_date" value="{{$construction->work_start_date?->format('Y-m-d')}}" placeholder="Work start date">
                                        @if($errors->has('work_start_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="work_start_date">
                                                    {!! $errors->first('work_start_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="work_completion_date" class="m-0">Work Completion Date</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                       <input type="text" readonly class="form-control @if($errors->has('work_completion_date')) is-invalid @endif" name="work_completion_date" value="{{$construction->work_completion_date?->format('Y-m-d')}}" placeholder="Work completion date">
                                        @if($errors->has('work_completion_date'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="work_completion_date">
                                                    {!! $errors->first('work_completion_date') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="donor_codes" class="m-0">Donors</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                       {{-- <select disabled class="form-control select2 @if($errors->has('donor_codes')) is-invalid @endif" name="donor_codes[]" id="donor_codes" multiple>
                                           @foreach ($donors as $donor)
                                               <option value="{{$donor->id}}" {{in_array($donor->id, $construction->donors->pluck('id')->toArray()) ? 'selected' : ''}}>{{$donor->getDonorCodeWithDescription()}}</option>
                                           @endforeach
                                       </select> --}}

                                        <span class="span-mimic-text-input">{{ $construction->donor }}</span>

                                       @if($errors->has('donor_codes'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="donor_codes">
                                                    {!! $errors->first('donor_codes') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="metal_plaque_text" class="m-0">Metal Plaque Text</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                       {{-- <input type="text" readonly class="form-control @if($errors->has('metal_plaque_text')) is-invalid @endif"
                                       name="metal_plaque_text" value="{{$construction->metal_plaque_text}}" placeholder="Metal Plaque Text"> --}}

                                       <span class="span-mimic-text-input">{{ $construction->metal_plaque_text }}</span>

                                        @if($errors->has('metal_plaque_text'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="metal_plaque_text">
                                                    {!! $errors->first('metal_plaque_text') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Parties
                                        </div>
                                        <div class="container-fluid-s">
                                            <div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table" id="constructionParyTable">
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
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="card">
                                        <div class="card-header fw-bold" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                            <span>
                                                Attachments
                                            </span>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table" id="constructionAttachmentTable">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col">Title</th>
                                                                <th scope="col" style="width: 150px">Attachment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="card">
                                        <div class="card-header fw-bold" style="display: flex; flex-direction: row; align-items: center; justify-content: space-between;">
                                            <span>
                                                Amendments
                                            </span>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table" id="constructionAmendmentTable">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th scope="col">Effective Date</th>
                                                                <th scope="col">Extension Date To</th>
                                                                <th scope="col">Total Estimate Cost</th>
                                                                <th scope="col" style="width: 150px">Attachment</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Project Progress
                                        </div>
                                        <div class="container-fluid-s">
                                            <div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table" id="constructionProgressTable">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th scope="col">Report Date</th>
                                                                    {{-- <th scope="col">Work Start Date</th>
                                                                    <th scope="col">Work Completion Date</th> --}}
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
                                        </div>
                                    </div>
                                </div>


                                <div>
                                    <div class="card">
                                        <div class="container-fluid-s">
                                            <div>
                                                <div class="card-header fw-bold">
                                                    Financial Transaction
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table" id="constructionInstallmentTable">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th scope="col">Transaction Date</th>
                                                                    <th scope="col">Transaction Type</th>
                                                                    <th scope="col">Amount</th>
                                                                    <th scope="col">Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                        {{-- <div class="m-2 text-end">
                                                            <span class="fw-bold">Total Sum of Installment: </span><span id="totalSumOfInstallment"></span>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <a href="{!! route('construction.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
