@extends('layouts.container')

@section('title', 'Health Facilities')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#health-facilities-menu').addClass('active');

            var oTable = $('#healthFacilityTable').DataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.health.facilities.index') }}",
                columns: [
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'province',
                        name: 'province'
                    },
                    {
                        data: 'district',
                        name: 'district'
                    },
                    {
                        data: 'local_level',
                        name: 'local_level'
                    },
                    {
                        data: 'ward',
                        name: 'ward'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
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

            $('#healthFacilityTable').on('click', '.delete-record', function(e) {
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


            $(document).on('click', '.open-health-facility-modal-form', function(e) {
                e.preventDefault();
                $('#openModal').find('.modal-content').html('');
                $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                    const form = document.getElementById('healthFacilityForm');
                    $(form).find(".select2").select2({
                        dropdownParent: $('.modal'),
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                    const fv = FormValidation.formValidation(form, {
                        fields: {
                            title: {
                                validators: {
                                    notEmpty: {
                                        message: 'Health facility name is required',
                                    },
                                },
                            },
                            province_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Province is required',
                                    },
                                },
                            },
                            district_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'District is required',
                                    },
                                },
                            },
                            local_level_id: {
                                validators: {
                                    notEmpty: {
                                        message: 'Palika level is required',
                                    },
                                },
                            },
                            ward: {
                                validators: {
                                    notEmpty: {
                                        message: 'Ward is required',
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

                    $(form).on('change', '[name="province_id"]', function (e) {
                        $element = $(this);
                    var provinceId = $element.val();
                    var htmlToReplace = '<option value="">Select District</option>';
                    if (provinceId) {
                        var url = baseUrl + '/api/master/provinces/' + provinceId;
                        var successCallback = function(response) {
                            response.districts.forEach(function(district) {
                                htmlToReplace += '<option value="' + district.id +
                                    '">' + district.district_name  + '</option>';
                            });
                            $($element).closest('form').find('[name="district_id"]').html(
                                htmlToReplace).trigger('change');
                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                        } else {
                            $($element).closest('form').find('[name="district_id"]').html(
                                htmlToReplace);
                        }
                    // fv.revalidateField('activity_code_id');
                    }).on('change', '[name="district_id"]', function(e) {
                        $element = $(this);
                        var districtId = $element.val();

                        var htmlToReplace = '<option value="">Select Palika</option>';
                        if (districtId) {
                            var url = baseUrl + '/api/master/districts/' + districtId;
                            var successCallback = function(response) {
                                response.localLevels.forEach(function(localLevel) {
                                    htmlToReplace += '<option value="'  + localLevel.id +
                                        '">' + localLevel.local_level_name + '</option>';
                                });
                                $($element).closest('form').find('[name="local_level_id"]').html(
                                    htmlToReplace);
                            }
                            var errorCallback = function(error) {
                                console.log(error);
                            }
                            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                        } else {
                            $($element).closest('form').find('[name="local_level_id"]').html(
                                htmlToReplace);

                        }
                        // fv.revalidateField('activity_code_id');
                    })
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
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">{{ __('label.home') }}</a></li>
                            <li class="breadcrumb-item"><a href="#"
                                    class="text-decoration-none">{{ __('label.master') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-health-facility-modal-form"
                        href="{!! route('master.health.facilities.create') !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="healthFacilityTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">Health Facility Name</th>
                                    <th scope="col">{{__('label.province')}}</th>
                                    <th scope="col">{{__('label.district')}}</th>
                                    <th scope="col">{{__('label.palika')}}</th>
                                    <th scope="col">{{__('label.ward')}}</th>
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
