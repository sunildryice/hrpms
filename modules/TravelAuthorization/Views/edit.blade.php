@extends('layouts.container')

@section('title', 'Edit Travel Authorization Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#ta-request-menu').addClass('active');
        });
        let officialsCount = '{{ $travel->officials()->count() }}';
        let itineraryCount = '{{ $travel->itineraries()->count() }}';
        let estimatesCount = '{{ $travel->estimates()->count() }}';

        function submitButton() {
            if (itineraryCount > 0 && officialsCount > 0 && estimatesCount > 0) {
                $('.submit-record').show();
            } else {
                $('.submit-record').hide();
            }
        }

        document.addEventListener('DOMContentLoaded', function (e) {
            submitButton();
            const form = document.getElementById('travelForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office is required',
                            },
                        },
                    },
                    objectives: {
                        validators: {
                            notEmpty: {
                                message: 'Objectives is required',
                            },
                        },
                    },
                    outcomes: {
                        validators: {
                            notEmpty: {
                                message: 'Outcome of Travel is required',
                            },
                        },
                    },
                    // approver_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Approver is required',
                    //         },
                    //     }
                    // }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            @if (!$authUser->can('submit', $travel))
            $('.estimateDiv').hide();
            $('.submit-record').hide();
            @endif
        });

        var officialTable = $('#officialTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ta.requests.official.index', $travel->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                data: 'name',
                name: 'name'
            },
                {
                    data: 'post',
                    name: 'post'
                },
                {
                    data: 'level',
                    name: 'level'
                },
                {
                    data: 'office',
                    name: 'office'
                },
                {
                    data: 'district',
                    name: 'district',
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
        $('#officialTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                officialsCount = response.officialsCount;
                submitButton();
                officialTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        var itineraryTable = $('#itineraryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ta.requests.itinerary.index', $travel->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                data: 'travel_date',
                name: 'travel_date'
            },
                {
                    data: 'place_from',
                    name: 'place_from'
                },
                {
                    data: 'place_to',
                    name: 'place_to'
                },
                {
                    data: 'activities',
                    name: 'activities'
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

        $('#itineraryTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                itineraryCount = response.itineraryCount;
                submitButton();
                itineraryTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        var estimateTable = $('#estimateTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ta.requests.estimate.index', $travel->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                data: 'particulars',
                name: 'particulars'
            },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'days',
                    name: 'days'
                },
                {
                    data: 'unit_price',
                    name: 'unit_price'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ],
            drawCallback: function () {
                let table = this[0]
                let footer = table.getElementsByTagName('tfoot')[0];
                if (!footer) {
                    footer = document.createElement('tfoot');
                    table.appendChild(footer);
                }

                let total_amount = this.api().column(4).data().reduce(function (a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                total_amount = new Intl.NumberFormat('en-US').format(total_amount);

                footer.innerHTML = '';
                footer.innerHTML = `<tr>
                    <td colspan='3'></td>
                    <td >Total Amount</td>
                    <td colspan='3'>${total_amount}</td>
                </tr>`

            }
        });

        $('#estimateTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var tRow = '<tr><td colspan="7" class="text-center">Record not found.</td></tr>';
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                estimatesCount = response.estimatesCount;
                submitButton();
                estimateTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-estimation-modal-form', function (e) {
            e.preventDefault();
            $('#estimateAddModal').find('.modal-content').html('');
            $('#estimateAddModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {

                const form = document.getElementById('estimateForm');
                $(form).find(".select2").each(function () {
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
                        particulars: {
                            validators: {
                                notEmpty: {
                                    message: 'Particulars is required',
                                },
                            },
                        },
                        quantity: {
                            validators: {
                                notEmpty: {
                                    message: 'Quantity is required',
                                },
                            },
                        },
                        days: {
                            validators: {
                                notEmpty: {
                                    message: 'Days is required',
                                },
                            },
                        },
                        unit_price: {
                            validators: {
                                notEmpty: {
                                    message: 'Rate is required',
                                },
                            },
                        },
                        activity_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Activity code is required',
                                },
                            },
                        },
                        account_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Account code is required',
                                },
                            },
                        },
                        donor_code_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Donor code is required',
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
                }).on('core.form.valid', function (event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function (response) {
                        $('#estimateAddModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        estimatesCount = response.estimatesCount;
                        submitButton();
                        estimateTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                function calculateTotalAmount() {
                    var quantity = $('[name="quantity"]').val();
                    var days = $('[name="days"]').val();
                    var unitPrice = $('[name="unit_price"]').val();
                    //var totalAmount = quantity * unitPrice;
                    var totalAmount = quantity * days * unitPrice;
                    $('[name="total_amount"]').val(totalAmount);
                }


                $(form).on('change', '[name="activity_code_id"]', function (e) {
                    $element = $(this);
                    var activityCodeId = $element.val();
                    var htmlToReplace = '<option value="">Select Account Code</option>';
                    if (activityCodeId) {
                        var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                        var successCallback = function (response) {
                            response.accountCodes.forEach(function (accountCode) {
                                htmlToReplace += '<option value="' + accountCode.id +
                                    '">' + accountCode.title + ' ' + accountCode
                                        .description + '</option>';
                            });
                            $($element).closest('form').find('[name="account_code_id"]').html(
                                htmlToReplace);
                        }
                        var errorCallback = function (error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="account_code_id"]').html(
                            htmlToReplace);
                    }
                    fv.revalidateField('activity_code_id');
                }).on('change', '[name="account_code_id"]', function (e) {
                    fv.revalidateField('account_code_id');
                }).on('change', '[name="quantity"]', function (e) {
                    calculateTotalAmount();
                }).on('change', '[name="unit_price"]', function (e) {
                    calculateTotalAmount();
                }).on('change', '[name="days"]', function (e) {
                    calculateTotalAmount();
                });
            });
        });

        $(document).on('click', '.open-itinerary-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const itineraryForm = document.getElementById('itineraryForm');
                $(itineraryForm).find(".select2").each(function () {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });

                const fv = FormValidation.formValidation(itineraryForm, {
                    fields: {
                        travel_date: {
                            validators: {
                                notEmpty: {
                                    message: 'Travel Date is required',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid date',
                                },
                            },
                        },
                        place_from: {
                            validators: {
                                notEmpty: {
                                    message: 'Departure place is required',
                                },
                            },
                        },
                        place_to: {
                            validators: {
                                notEmpty: {
                                    message: 'Arrival place is required',
                                },
                            },
                        },
                        activities: {
                            validators: {
                                notEmpty: {
                                    message: 'Activities is required',
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
                }).on('core.form.valid', function (event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        itineraryCount = response.itineraryCount;
                        submitButton();
                        itineraryTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(itineraryForm).find('[name="travel_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                }).on('change', function (e) {
                    fv.revalidateField('travel_date');
                });
            });
        });
        $(document).on('click', '.open-official-modal-form', function (e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const officialForm = document.getElementById('officialForm');
                $(officialForm).find(".select2").each(function () {
                    $(this)
                        .wrap("<div class=\"position-relative\"></div>")
                        .select2({
                            dropdownParent: $(this).parent(),
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                });

                const fv = FormValidation.formValidation(officialForm, {
                    fields: {
                        name: {
                            validators: {
                                notEmpty: {
                                    message: "Official's name is required",
                                },
                            },
                        },
                        post: {
                            validators: {
                                //notEmpty: {
                                //    message: 'Post is required',
                                //},
                            },
                        },
                        office: {
                            validators: {
                                //notEmpty: {
                                //    message: 'Office is required',
                                //},
                            },
                        },
                        district_id: {
                            validators: {
                                notEmpty: {
                                    message: 'District is required',
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
                }).on('core.form.valid', function (event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function (response) {
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        officialsCount = response.officialsCount;
                        submitButton();
                        officialTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
            });
        });
    </script>
    <link href="{{ asset('plugins/slim-select/dist/slimselect.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/slim-select/dist/slimselect.min.js') }}"></script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 page-header border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('ta.requests.index') }}" class="text-decoration-none text-dark">Travel
                                Authorization</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <section class="registration">
        <form action="{{ route('ta.requests.update', $travel->id) }}" id="travelForm" method="post"
              enctype="multipart/form-data" autocomplete="off">

            <div class="card">
                <div class="card-body">
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Office</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php $selected = old('office_id') ?: $travel->office_id; @endphp
                            <select name="office_id"
                                    class="select2 form-control
                                                @if ($errors->has('office_id')) is-invalid @endif"
                                    data-width="100%">
                                <option value="">Select an Office</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}" {{ $office->id == $selected ? 'selected' : '' }}>
                                        {{ $office->getOfficeName() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('office_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="office_id">{!! $errors->first('office_id') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationPurposeofTravel" class="form-label required-label"> Objectives
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" rows="5"
                                      class="form-control @if ($errors->has('objectives')) is-invalid @endif"
                                      name="objectives">{!! old('objectives') ?: $travel->objectives !!}</textarea>
                            @if ($errors->has('objectives'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="objectives">{!! $errors->first('objectives') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationPurposeofTravel" class="form-label required-label"> Outcomes
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" rows="5"
                                      class="form-control @if ($errors->has('outcomes')) is-invalid @endif"
                                      name="outcomes">{!! old('outcomes') ?: $travel->outcomes !!}</textarea>
                            @if ($errors->has('outcomes'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="outcomes">{!! $errors->first('outcomes') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="m-0">Remarks </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea type="text" class="form-control @if ($errors->has('remarks')) is-invalid @endif"
                                      name="remarks">{!! old('remarks') ?: $travel->remarks !!}</textarea>
                            @if ($errors->has('remarks'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Send
                                    to </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php $selectedApproverId = old('approver_id') ?: $travel->approver_id; @endphp
                            <select name="approver_id"
                                    class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                    data-width="100%">
                                <option value="">Select an Approver</option>
                                @foreach ($supervisors as $approver)
                                    <option value="{{ $approver->id }}"
                                        {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                        {{ $approver->getFullName() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('approver_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="approver_id">{!! $errors->first('approver_id') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="gap-2 border-0 card-footer justify-content-end d-flex">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                        Update
                    </button>
                </div>
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}
            </div>

            @if ($returnRemarks)
                <div class="card">
                    <div class="card-header text-danger">Return Remarks</div>
                    <div class="card-body">{{ $returnRemarks }}</div>
                </div>
            @endif

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span>Details of visiting officials</span>
                        <button data-toggle="modal" class="btn btn-primary btn-sm open-official-modal-form"
                                href="{!! route('ta.requests.official.create', $travel->id) !!}">
                            <i class="bi-plus"></i> Add Official
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="officialTable">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Post</th>
                                <th scope="col">Level</th>
                                <th scope="col">Office</th>
                                <th scope="col">District</th>
                                <th style="width: 150px">{{ __('label.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>


            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Itinerary of visit</span>
                        <button data-toggle="modal" class="btn btn-primary btn-sm open-itinerary-modal-form"
                                href="{!! route('ta.requests.itinerary.create', $travel->id) !!}">
                            <i class="bi-plus"></i> Add Itinerary
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="itineraryTable">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('label.date') }}</th>
                                <th scope="col">{{ __('label.from-place') }}</th>
                                <th scope="col">{{ __('label.to-place') }}</th>
                                <th scope="col">{{ __('label.activity') }}</th>
                                <th style="width: 150px">{{ __('label.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Detail Cost Estimation</span>
                        <button data-toggle="modal" class="btn btn-primary btn-sm open-estimation-modal-form"
                                href="{!! route('ta.requests.estimate.create', $travel->id) !!}">
                            <i class="bi-plus"></i> Add Estimates
                        </button>
                    </div>
                </div>
                <div class="card-body estimate">
                    <div class="table-responsive">
                        <table class="table" id="estimateTable">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('label.particulars') }}</th>
                                <th scope="col">{{ __('label.quantity') }}</th>
                                <th scope="col">{{ __('label.days') }}</th>
                                <th scope="col">{{ __('label.rate') }}</th>
                                <th scope="col">{{ __('label.total-amount') }}</th>
                                <th style="width: 150px">{{ __('label.action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($travel->logs->count())
                <div class="card">
                    <div class="card-header fw-bold">Travel Authorization process</div>
                    <div class="card-body">
                        <div class="c-b">
                            @foreach ($travel->logs as $log)
                                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                        <i class="bi-person-circle fs-5"></i>
                                    </div>
                                    <div class="w-100">
                                        <div
                                            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                            <div
                                                class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                <span
                                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                            </div>
                                            <small>{{ $log->getCreatedAt() }}</small>
                                        </div>
                                        <p class="text-justify comment-text mb-0 mt-1">
                                            {{ $log->log_remarks }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="gap-2 justify-content-end d-flex" id="submitRequest">
                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm submit-record"
                        style="display:none;">
                    Submit
                </button>
                <a href="{!! route('ta.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>
    </section>

    <div class="modal fade" id="estimateAddModal" data-bs-backdrop="static" data-bs-keyboard="false"
         aria-labelledby="estimateAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

@stop
