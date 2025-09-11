@extends('layouts.container')

@section('title', 'Probation Review')
@section('page_css')
    <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css">
@endsection
@section('page_js')
    <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
            $('#navbarVerticalMenu').find('#probation-review-request-menu').addClass('active');

        });

        var oTable = $('#probationReviewTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('probation.review.requests.index') }}",
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

        $('#probationReviewTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $($object).closest('tr').remove();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-probation-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function (){
                const form = document.getElementById('probationReviewAddForm') ? document.getElementById(
                    'probationReviewAddForm') : document.getElementById('probationReviewEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        employee_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Employee field is required.',
                                },
                            },
                        },
                        date: {
                            validators: {
                                notEmpty: {
                                    message: 'Review date is required.',
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The value is not a valid year',
                                },
                            },
                        },
                        review_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Review Type is required.',
                                },
                            },
                        },
                        reviewer_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Reviewer is required.',
                                },
                            },
                        },
                        approver_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Approver is required.',
                                },
                            },
                        },
                        remarks: {
                            validators: {
                                notEmpty: {
                                    message: 'Remarks field is required.',
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

                $(form).find('[name="date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: '{!! date('Y-m-d') !!}',
                    zIndex: 2048,
                }).on('change', function(e) {
                    fv.revalidateField('date');
                });

                $(form).on('change', '[name="employee_id"]', function(e) {
                    $element = $(this);
                    var employeeId = $element.val();
                    var htmlToReplace = '<option value="">Select a Reviewer</option>';
                    if (employeeId) {
                        var url = baseUrl + '/api/employee/supervisor/' + employeeId;
                        var successCallback = function(response) {
                            response.supervisors.forEach(function(supervisor) {
                                htmlToReplace += '<option value="' + supervisor.id + '">' +
                                    supervisor.full_name + '</option>';
                            });
                            $($element).closest('form').find('[name="reviewer_id"]').html(htmlToReplace)
                                .val(null).trigger('change');
                            //  $($element).closest('form').find('[name="reviewer_id"]').select2("destroy").select2();
                            console.log(response);

                        }
                        var errorCallback = function(error) {
                            console.log(error);
                        }
                        ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                    } else {
                        $($element).closest('form').find('[name="reviewer_id"]').html(htmlToReplace);
                    }
                    fv.revalidateField('reviewer_id');
                    fv.revalidateField('employee_id');
                }).on('change', '[name="reviewer_id"]', function(e) {
                    fv.revalidateField('reviewer_id');
                }).on('change', '[name="review_id"]', function(e) {
                    fv.revalidateField('review_id');
                });
            });
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
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-probation-modal-form"
                        href="{!! route('probation.review.requests.create') !!}" rel="tooltip" title="Probation Review">
                        <i class="bi-plus"></i>Add New
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
                                    <th class="">{{ __('label.review-type') }}</th>
                                    <th class="">{{ __('label.review-date') }}</th>
                                    <th class="">{{ __('label.reviewer') }}</th>
                                    <th class="">{{ __('label.remarks') }}</th>
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
