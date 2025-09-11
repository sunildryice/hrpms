@extends('layouts.container')

@section('title', 'Fund Release/MFR Approval')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#agreement-index').addClass('active');

            var oTable = $('#agreementTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('mfr.agreement.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'partner_organization',
                        name: 'partner_organization'
                    },
                    {
                        data: 'district',
                        name: 'district'
                    },
                    {
                        data: 'effective_from',
                        name: 'effective_from'
                    },
                    {
                        data: 'effective_to',
                        name: 'effective_to'
                    },
                    {
                        data: 'approved_budget',
                        name: 'approved_budget'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: "sticky-col"
                    },
                ]
            });

          $('#agreementTable').on('click', '.delete-record', function(e) {
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



            let error = {!! $errors !!};
            // console.log(error);
            if (error.attendance_file) {
                $('#importAttendanceModal').modal('show');
            }

            // $('#importAttendanceModal').show();

        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                        </li>
                        {{-- <li class="breadcrumb-item"><a href="#"
                                        class="text-decoration-none">{{ __('label.attendance') }}</a></li> --}}
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            {{-- @if (auth()->user()->can('import-attendance')) --}}
                <div class="mb-2">
                    <a type="button" class="btn btn-primary btn-sm" href="{{route('mfr.agreement.create')}}">
                        Add New
                    </a>
                </div>
            {{-- @endif --}}
        </div>
    </div>



    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderedless" id="agreementTable">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>Partner Organization</th>
                            <th>{{ __('label.district') }}</th>
                            <th>Period (from)</th>
                            <th>Period (to)</th>
                            <th>Budget</th>
                            <th style="width:95px;">{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@stop
