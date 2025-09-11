@extends('layouts.container')

@section('title', 'Probation Review')
@section('page_css')
    <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css">
@endsection
@section('page_js')
    <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#probation-review-request-menu').addClass('active');

        });

        var oTable = $('#probationReviewTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('probation.review.requests.list') }}",
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
                    data: 'department_name',
                    name: 'department_name'
                },
                {
                    data: 'designation_name',
                    name: 'designation_name'
                },
                {
                    data: 'probation_end_date',
                    name: 'probation_end_date'
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
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page"><a href="#"
                                    class="text-decoration-none text-dark">HR</a></li>
                            <li class="breadcrumb-item" aria-current="page">Probation Review</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Probation Review</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-modal-form"
                        href="{!! route('probation.review.requests.create') !!}" rel="tooltip" title="Probation Review">
                        <i class="bi-form"></i>Add New
                    </button>
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
                                    <th class="">{{ __('label.department-name') }}</th>
                                    <th class="">{{ __('label.designation-name') }}</th>
                                    <th class="">{{ __('label.probation-end-date') }}</th>
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
