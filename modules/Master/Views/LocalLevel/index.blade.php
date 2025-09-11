@extends('layouts.container')

@section('title', __('label.local-levels'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#local-levels-menu').addClass('active');

            var oTable = $('#districtTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.local.levels.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'province',
                        name: 'province',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'district',
                        name: 'district',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'local_level_name',
                        name: 'local_level_name'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'sticky-col'
                    },
                ]
            });


            $(document).on('click', '.open-locallevel-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('localLevelForm');
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            district_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'District is required',
                                    },
                                },
                            },
                            local_level_name: {
                                validators: {
                                    notEmpty: {
                                        message: 'Local level name is required',
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
            }):
        });
    </script>
@endsection
@section('page-content')

    <div class="p-3 m-content">
        <div class="pb-3 mb-3 border-bottom">
            <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="m-0 breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a></li>
                            <li class="breadcrumb-item"><a href="#"
                                    class="text-decoration-none">{{ __('label.master') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="districtTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.sn') }}</th>
                                    <th scope="col">{{ __('label.province') }}</th>
                                    <th scope="col">{{ __('label.district') }}</th>
                                    <th scope="col">{{ __('label.local-level') }}</th>
                                    <th scope="col">{{ __('label.created-by') }}</th>
                                    <th scope="col">{{ __('label.updated-on') }}</th>
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
