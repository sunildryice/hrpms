@extends('layouts.container')

@section('title', 'Edit Good Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#good-requests-menu').addClass('active');
            const form = document.getElementById('goodRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    purpose: {
                        validators: {
                            notEmpty: {
                                message: 'The purpose of good request is required',
                            },
                        },
                    },
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
        });

        var oTable = $('#goodRequestItemTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('good.requests.items.index', $goodRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {data: 'item_name', name: 'item_name'},
                {data: 'unit', name: 'unit'},
                {data: 'quantity', name: 'quantity'},
                {data: 'specification', name: 'specification'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className:'sticky-col'},
            ]
        });

        $('#goodRequestItemTable').on('click', '.delete-record', function (e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function (response) {
                toastr.success(response.message, 'Success', {timeOut: 5000});
                oTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-item-modal-form', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function () {
                const form = document.getElementById('goodRequestItemForm');
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
                        item_name: {
                            validators: {
                                notEmpty: {
                                    message: 'Item name is required',
                                },
                            },
                        },
                        unit_id: {
                            validators: {
                                notEmpty: {
                                    message: 'Unit is required',
                                },
                            },
                        },
                        quantity: {
                            validators: {
                                notEmpty: {
                                    message: 'Quantity is required',
                                },
                                greaterThan: {
                                    message: 'The value must be greater than or equal to 1',
                                    min: 1,
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
                        toastr.success(response.message, 'Success', {timeOut: 5000});
                        oTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                $(form).on('change','[name="unit_id"]', function (e){
                    fv.revalidateField('unit_id');
                });
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="container-fluid">
            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('good.requests.index') }}" class="text-decoration-none">Good
                                        Request</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>

            <section class="registration">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <form action="{{ route('good.requests.update', $goodRequest->id) }}"
                                  id="goodRequestEditForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks"
                                                       class="form-label required-label">Purpose</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text" rows="10"
                                                      class="form-control @if($errors->has('purpose')) is-invalid @endif"
                                                      name="purpose">{{ old('purpose') ?: $goodRequest->purpose }}</textarea>
                                            @if($errors->has('purpose'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="purpose">{!! $errors->first('purpose') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="reviewer_id" class="m-0">
                                                    Reviewer
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedReviewerId = old('reviewer_id') ?: $goodRequest->reviewer_id; @endphp
                                            <select name="reviewer_id" class="select2 form-control
                                                @if($errors->has('reviewer_id')) is-invalid @endif" data-width="100%">
                                                <option value="">Select a reviewer</option>
                                                @foreach($reviewers as $reviewer)
                                                    <option
                                                        value="{{ $reviewer->id }}" {{$reviewer->id == $selectedReviewerId ? "selected":""}}>{{ $reviewer->getFullName() }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('reviewer_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="reviewer_id">
                                                        {!! $errors->first('reviewer_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="approver_id" class="form-label required-label">
                                                    Approver
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedApproverId = old('approver_id') ?: $goodRequest->approver_id; @endphp
                                            <select name="approver_id" class="select2 form-control
                                                @if($errors->has('approver_id')) is-invalid @endif" data-width="100%">
                                                <option value="">Select an approver</option>
                                                @foreach($approvers as $approver)
                                                    <option
                                                        value="{{ $approver->id }}" {{$approver->id == $selectedApproverId ? "selected":""}}>{{ $approver->getFullName() }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('approver_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="approver_id">
                                                        {!! $errors->first('approver_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                    {!! method_field('PUT') !!}
                                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                                            Update
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="card">
                                        <div class="card-header fw-bold">
                                            Good Request Items
                                        </div>
                                        <div class="p-2">
                                            <div class="d-flex align-items-center add-info justify-content-end">
                                                <button data-toggle="modal"
                                                        class="btn btn-primary btn-sm open-item-modal-form"
                                                        href="{!! route('good.requests.items.create', $goodRequest->id) !!}"
                                                ><i class="bi-plus"></i> Add New Item
                                                </button>
                                            </div>
                                        </div>
                                        <div class="container-fluid-s">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table" id="goodRequestItemTable">
                                                            <thead class="thead-light">
                                                            <tr>
                                                                <th scope="col">{{ __('label.item-name') }}</th>
                                                                <th scope="col">{{ __('label.unit') }}</th>
                                                                <th scope="col">{{ __('label.quantity') }}</th>
                                                                <th scope="col">{{ __('label.specification') }}</th>
                                                                <th style="width: 150px" class="sticky-col">{{ __('label.action') }}</th>
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
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                        Submit
                                    </button>
                                    <a href="{!! route('good.requests.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
