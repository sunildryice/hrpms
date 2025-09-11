@extends('layouts.container')

@section('title', 'Approved Training Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approved-training-requests-menu').addClass('active');
        });

        var oTable = $('#trainingRequestTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('approved.training.requests.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'training_number',
                    name: 'training_number'
                },
                {
                    data: 'requester',
                    name: 'requester'
                },
                {
                    data: 'name_of_course',
                    name: 'name_of_course'
                },
                {
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
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
                    className:'sticky-col'
                },
            ]
        });
    </script>
@endsection
@section('page-content')

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Approved Training Request</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Approved Training Request</h4>
                </div>
            </div>

        </div>
        <section class="registration">
            <div class="card" id="training-request-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless" id="trainingRequestTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width:45px;"></th>
                                    <th>{{ __('label.training-number') }}</th>
                                    <th>{{ __('label.requester') }}</th>
                                    <th>{{ __('label.name-of-course') }}</th>
                                    <th>{{ __('label.duration') }}</th>
                                    <th>{{ __('label.remarks') }}</th>
                                    <th style="width: 100px;">{{ __('label.status') }}</th>
                                    <th style="width: 164px;">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>



@stop
