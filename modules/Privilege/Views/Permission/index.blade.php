@extends('layouts.container')

@section('title', __('label.permissions'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#permissions-menu').addClass('active');

            var oTable = $('#permissionTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('privilege.permissions.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'parent',
                        name: 'parent'
                    },
                    {
                        data: 'permission_name',
                        name: 'permission_name'
                    },
                    {
                        data: 'guard_name',
                        name: 'guard_name'
                    },
                    {
                        data: 'active',
                        name: 'active'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            $(document).on('shown.bs.modal', '#openModal', function(e) {
                const form = document.getElementById('permissionForm');
                $(form).find(".select2").select2({
                    dropdownParent: $('.modal'),
                    width: '100%',
                    dropdownAutoWidth: true
                });
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        permission_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Permission name is required',
                                },
                            },
                        },
                        guard_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Guard name is required',
                                },
                            },
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
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
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-modal-form"
                        href="{!! route('privilege.permissions.create') !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="permissionTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.sn') }}</th>
                                    <th scope="col">{{ __('label.parent') }}</th>
                                    <th scope="col">{{ __('label.permission-name') }}</th>
                                    <th scope="col">{{ __('label.guard-name') }}</th>
                                    <th scope="col">{{ __('label.active') }} ?</th>
                                    <th style="width: 150px">{{ __('label.action') }}</th>
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
