@extends('layouts.container')

@section('title', __('label.users'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#users-menu').addClass('active');

            var oTable = $('#userTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('privilege.users.index') }}",
                columns: [
                    {data: 'employee_code', name: 'employee_code'},
                    {data: 'user_id', name: 'user_id'},
                    {data: 'employee_id', name: 'employee_id'},
                    {data: 'full_name', name: 'full_name'},
                    {data: 'email_address', name: 'email_address'},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'tenure_office_name', name: 'tenure_office_name'},
                    {data: 'office_name', name: 'office_name'},
                    {data: 'activated_at', name: 'activated_at'},
                    {data: 'roles', name: 'roles', orderable: false, searchable: true},
                ]
            });
        });
    </script>
@endsection
@section('page-content')

    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}"
                                   class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="userTable">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('label.staff-code') }}</th>
                                <th scope="col">{{ __('label.user-id') }}</th>
                                <th scope="col">{{ __('label.employee-id') }}</th>
                                <th scope="col">{{ __('label.name') }}</th>
                                <th scope="col">{{ __('label.email-address') }}</th>
                                <th scope="col">{{ __('label.employee-name') }}</th>
                                <th scope="col">{{ __('label.tenure') .' '. __('label.office') }}</th>
                                <th scope="col">{{ __('label.office') }}</th>
                                <th scope="col">{{ __('label.active') }}</th>
                                <th style="width: 150px">{{ __('label.roles') }}</th>
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

@stop
