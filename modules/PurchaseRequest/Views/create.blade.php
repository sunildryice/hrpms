@extends('layouts.container')

@section('title', 'Add New Purchase Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#purchase-requests-menu').addClass('active');
            const form = document.getElementById('purchaseRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    required_date: {
                        validators: {
                            notEmpty: {
                                message: 'The required date is required',
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

            $('[name="required_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('required_date');
            });
        });
    </script>
@endsection

@section('page-content')


            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('purchase.requests.index') }}" class="text-decoration-none text-dark">Purchase
                                        Requests</a>
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
                            <form action="{{ route('purchase.requests.store') }}" id="purchaseRequestAddForm" method="post"
                                  enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdd" class="form-label required-label">Required Date </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input class="form-control @if($errors->has('required_date')) is-invalid @endif"
                                                   type="text" readonly name="required_date" value="{{ old('required_date') }}"/>
                                            @if($errors->has('required_date'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="required_date">
                                                        {!! $errors->first('required_date') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks" class="form-label">Purpose</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text"
                                                      class="form-control @if($errors->has('purpose')) is-invalid @endif"
                                                      name="purpose">{{ old('purpose') }}</textarea>
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
                                                <label for="validationRemarks" class="form-label">Delivery Instructions</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text"
                                                      class="form-control @if($errors->has('delivery_instructions')) is-invalid @endif"
                                                      name="delivery_instructions">{{ old('delivery_instructions') }}</textarea>
                                            @if($errors->has('delivery_instructions'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div
                                                        data-field="delivery_instructions">{!! $errors->first('delivery_instructions') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdistributiontype"
                                                    class="form-label ">Procurement Officers</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php
                                                $selected = old('procurement_officer') ?? [];
                                            @endphp
                                            <select name="procurement_officer[]" class="select2 form-control"
                                                data-width="100%" multiple>
                                                @foreach ($officers as $officer)
                                                    <option value="{{ $officer->id }}"
                                                        data-distribution="{{ $officer->id }}"
                                                        {{ in_array($officer->id, $selected) ? 'selected' : '' }}>
                                                        {{ $officer->getFullNameWithEmpCode() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('district_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="district_id">
                                                        {!! $errors->first('district_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Next
                                    </button>
                                    <a href="{!! route('purchase.requests.index') !!}"
                                       class="btn btn-danger btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </section>

@stop
