@extends('layouts.container')

@section('title', 'Probation Review')
@section('page_css')
@endsection
@section('page_js')
    <script>
        $(document).ready(function() {
            $('.step-item').click(function() {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-probation-review-request-menu').addClass('active');
        });

        var oTable = $('#probationReviewTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('approve.probation.review.requests.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'employee_name',
                    name: 'employee_name'
                },
                {
                    data: 'review_type',
                    name: 'review_type'
                },
                {
                    data: 'review_date',
                    name: 'review_date'
                },
                {
                    data: 'reviewer',
                    name: 'reviewer'
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
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page"><a href="#"
                                    class="text-decoration-none text-dark">HR</a></li>
                            <li class="breadcrumb-item" aria-current="page">Approve Probation Review Request</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Approve Probation Review Request</h4>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderedless" id="probationReviewTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width:45px;"></th>
                                    <th class="">{{ __('label.for') }}</th>
                                    <th class="">{{ __('label.review-type') }}</th>
                                    <th class="">{{ __('label.date') }}</th>
                                    <th class="">{{ __('label.reviewer') }}</th>
                                    <th style="width:40%; ">{{ __('label.remarks') }}</th>
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
        </section>
    @stop
