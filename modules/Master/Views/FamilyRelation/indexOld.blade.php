@extends('layouts.container')

@section('title', __('label.family-relations'))

@section('page_js')
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#family-relations-menu').addClass('active');

            $('#familyRelationTable').on('click', '.delete-record', function (e) {
                e.preventDefault();
                $object = $(this);
                var $url = $object.attr('data-href');
                var successCallback = function (response) {
                    toastr.success(response.message, 'Success', {timeOut: 5000});
                }
                ajaxDeleteSweetAlert($url, successCallback);
            });

            $('#familyRelationTable tbody').sortable({
                update: function (event, ui) {
                    var $object = $(this);
                    var relations = $(this).sortable('serialize');
                    var count = parseInt($object.children().first().children('td:nth-child(2)').html());
                    $object.children('tr').each(function () {
                        var sn = parseInt($(this).children('td:nth-child(2)').html());
                        if (sn < count) {
                            count = sn;
                        }
                    });
                    $object.children('tr').each(function () {
                        $(this).children('td:nth-child(2)').html(count);
                        count++;
                    });
                    var $url = '{!! route('master.family.relations.sort.order') !!}';
                    var data = {relations: relations};
                    successCallback = function (response) {
                        toastr.success(response.message);
                    };
                    ajaxSubmit($url, 'POST', data, successCallback);
                }
            });


            $(document).on('shown.bs.modal', '#openModal', function (e) {
                const form = document.getElementById('familyRelationForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'Expense type is required',
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
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
            });
        })
        ;
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
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ __('label.master') }}</a></li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                    <button data-toggle="modal" class="btn btn-primary btn-sm open-modal-form"
                            href="{!! route('master.family.relations.create') !!}">
                        <i class="bi-plus"></i> Add New
                    </button>
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <table class="table" id="familyRelationTable">
                        <thead class="thead-light">
                        <tr">
                            <th>{{ __('label.sn') }}</th>
                            <th>{{ __('label.family-type') }}</th>
                            <th>{{ __('label.created-by') }}</th>
                            <th>{{ __('label.updated-on') }}</th>
                            <th style="width: 150px">{{ __('label.action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($familyRelations as $relation)
                            <tr id="row_{!! $relation->id !!}">
                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td class="title">
                                    {{ $relation->title }}
                                </td>
                                <td class="created_by">
                                    {{ $relation->getCreatedBy() }}
                                </td>
                                <td>
                                    {{ $relation->getUpdatedAt() }}
                                </td>
                                <td class="options">
                                    <a href="{{ route('master.family.relations.edit', $relation->id) }}"
                                       data-toggle="modal" class="btn btn-outline-primary btn-sm open-modal-form"
                                       title="Edit Relation"><i class="bi-pencil-square"></i>
                                    </a>&nbsp;&nbsp;
                                    <a href="javascript:;" class="btn btn-danger btn-sm delete-record"
                                       data-href="{{ route('master.family.relations.destroy', $relation->id) }}"
                                       title="Delete Relation"><i class="bi-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@stop
