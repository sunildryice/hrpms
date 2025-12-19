@extends('layouts.container-report')

@section('title', 'Office Asset Handover')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }

        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th,
        .table tr td {
            padding: 0.25rem 0.75rem;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <script type="text/javascript">
        window.print();
    </script>
    <!-- CSS only -->


    <section class="p-3 bg-white print-info" id="print-info">

        <div class="mb-3 text-center print-title fw-bold translate-middle">
            <div class="fs-5"> Office Asset Handover</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">

                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column justify-content-end">
                        <div class="mb-4 d-flex flex-column justify-content-end brand-logo flex-grow-1">
                            <div class="float-right d-flex flex-column justify-content-end">
                                <img src="{{ asset('img/logonp.png') }}" alt=""
                                    class="align-self-end pe-5 logo-img">
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="print-body">
            <div>
                <p class="fw-bolder text-decoration-underline">The following OHW office assets have been submitted as per
                    below.</p>
            </div>
            <table class="table table-bordered" style="width: 100%">
                <thead>
                    <tr>
                        <th style="width: 1%">{{ __('label.sn') }}</th>
                        <th style="width: 10%">{{ __('label.item-name') }}</th>
                        <th style="width: 10%">{{ __('label.asset-number') }}</th>
                        <th style="width: 10%">{{ __('label.specification') }}</th>
                        <th style="width: 10%">{{ __('label.condition') }}</th>
                        <th>{{ __('label.remarks') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assets as $index => $asset)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $asset->asset->inventoryItem->getItemName() }}</td>
                            <td>{{ $asset->getAssetNumber() }}</td>
                            <td>{{ $asset->getSpecification() }}</td>
                            <td>{{ $asset->getAssetCondition() }}</td>
                            <td>{{ $asset->getRemarks() }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

            <div class="mb-4 row">
                <div class="col-lg-6">
                    <div>Submitted By:</div>
                    <div><strong>Name</strong>: {{ $data->getEmployeeName() }} </div>
                    <div><strong>Position</strong>: {{ $data->employee->getDesignationName() }} </div>
                    <div><strong>Date:</strong> {{ $data->submittedLog?->getCreatedAt() }}</div>
                </div>
                <div class="col-lg-6">
                    <div>Approved By:</div>
                    <div> <strong>Name:</strong> {{ $data->approver?->getFullName() }}</div>
                    <div> <strong>Position:</strong> {{ $data->approver?->employee->getDesignationName() }}</div>
                    <div><strong>Date:</strong> {{ $data->approvedLog?->getCreatedAt() }}</div>
                </div>
            </div>
        </div>
    </section>

@endsection
