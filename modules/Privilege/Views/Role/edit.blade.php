@extends('layouts.container')

@section('title', 'Edit Role')


@section('page_css')

    <style>
        .table tr td {
            vertical-align: top;
        }
    </style>


@endsection

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {
            $('#navbarVerticalMenu').find('#roles-menu').addClass('active');
            $('.check-permission').on('change', function(e) {
                if ($('.check-permission:checked').length == $('.check-permission').length) {
                    $(this).closest('table').find('.check-all').prop('checked', true);
                } else {
                    $(this).closest('table').find('.check-all').prop('checked', false);
                }
            });

            $('.check-all').on('change', function(e) {
                var checked = $(this).is(':checked');
                if (checked) {
                    $(this).closest('table').find('.check-permission').prop('checked', true);
                } else {
                    $(this).closest('table').find('.check-permission').prop('checked', false);
                }
            });

            const form = document.getElementById('roleForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    role: {
                        validators: {
                            notEmpty: {
                                message: 'Role is required',
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
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none">{!! __('label.home') !!}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('privilege.roles.index') }}"
                                        class="text-decoration-none">{!! __('label.roles') !!}</a>
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
                            <form action="{{ route('privilege.roles.update', $role->id) }}" id="roleForm" method="post"
                                enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="form-label required-label">Role Name</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input class="form-control @if ($errors->has('role')) is-invalid @endif"
                                                type="text" name="role" value="{{ old('role') ?: $role->role }}" />
                                            @if ($errors->has('role'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="role">
                                                        {!! $errors->first('role') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table" id="permissionsTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th scope="col" colspan="2">{{ __('label.permissions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="2">
                                                        <label> <input type="checkbox" class="check-all" value="all" />
                                                            Give All Permissions</label>

                                                    </td>
                                                </tr>
                                                @foreach ($permissions as $permission)
                                                    <tr>
                                                        <td>
                                                            <label> <input type="checkbox" name="permissions[]"
                                                                    class="check-permission" value="{{ $permission->id }}"
                                                                    @if (in_array($permission->id, $selectedPermissions)) checked @endif />
                                                                {{ $permission->permission_name }}
                                                            </label>


                                                        </td>
                                                        <td style="width: 80%;">
                                                            @if ($permission->childrens)
                                                                <ul class="list-unstyled row">
                                                                    {{-- @foreach ($permission->childrens->chunk(3) as $chunks) --}}

                                                                    @foreach ($permission->childrens as $child)
                                                                        <li class="col-lg-4">
                                                                            <label>
                                                                                <input type="checkbox" name="permissions[]"
                                                                                    class="check-permission"
                                                                                    value="{{ $child->id }}"
                                                                                    @if (in_array($child->id, $selectedPermissions)) checked @endif />
                                                                                {{ $child->permission_name }}
                                                                            </label>

                                                                        </li>
                                                                    @endforeach

                                                                    {{-- @endforeach --}}
                                                                </ul>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="save"
                                        class="btn btn-primary btn-sm">Update
                                    </button>
                                    <a href="{!! route('privilege.roles.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                                {!! csrf_field() !!}
                                {!! method_field('PUT') !!}
                            </form>
                        </div>

                    </div>
                </div>
            </section>

        </div>
    </div>

@stop
