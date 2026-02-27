@extends('layouts.container')

@section('title', 'Assigned Assets')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#assets-assigned-menu').addClass('active');

            var assetsTable = $('#assetsTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('assets.assigned.index') }}",
                bFilter: true,
                bPaginate: true,
                bInfo: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'asset_number',
                        name: 'asset_number',
                        orderable: false,
                    },
                    {
                        data: 'assigned_user',
                        name: 'assigned_user',
                        orderable: false,
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                        orderable: false,
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'assigned_location',
                        name: 'assigned_location'
                    },
                    {
                        data: 'asset_condition',
                        name: 'asset_condition'
                    },
                    {
                        data: 'specification',
                        name: 'specification'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'sticky-col'
                    },
                ]
            });

            $(document).on('click', '.open-asset-edit-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                    const form = document.getElementById('assetEditForm');
                    $(form).find(".select2").each(function() {
                        $(this)
                            .wrap("<div class=\"position-relative\"></div>")
                            .select2({
                                dropdownParent: $(this).parent(),
                                width: '100%',
                                dropdownAutoWidth: true
                            });
                    });

                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            assigned_office_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'The assigned office is required.',
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
                        let data = new FormData(form);
                        let url = form.getAttribute('action');
                        var successCallback = function(response) {
                            $('#openModal').modal('hide');
                            toastr.success(response.message, 'Success', {
                                timeOut: 5000
                            });
                            assetsTable.ajax.reload();
                        }
                        ajaxSubmitFormData(url, 'POST', data, successCallback);
                    });
                });
            });


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
                        <table class="table" id="assetsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('label.sn') }}</th>
                                    <th scope="col">{{ __('label.asset-number') }}</th>
                                    <th scope="col">Assigned User</th>
                                    <th scope="col">{{ __('label.purchase-date') }}</th>
                                    <th scope="col">{{ __('label.item') }}</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">{{ __('label.condition') }}</th>
                                    <th scope="col">{{ __('label.specification') }}</th>
                                    <th>{{ __('label.action') }}</th>
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
