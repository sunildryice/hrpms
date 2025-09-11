@extends('layouts.container')

@section('title', 'Edit Performance Review')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#performance-index').addClass('active');

            const form = document.getElementById('performanceReviewForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    review_from: {
                        validators: {
                            notEmpty: {
                                message: 'The review from date is required.'
                            }
                        }
                    },
                    review_to: {
                        validators: {
                            notEmpty: {
                                message: 'The review to date is required.'
                            }
                        }
                    },
                    deadline_date: {
                        validators: {
                            notEmpty: {
                                message: 'The deadline date is required.'
                            }
                        }
                    },
                    // review_type_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Review type is required.'
                    //         }
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
                        validating: 'bi bi-arrow-repeat'
                    }),
                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'review_from',
                            message: 'From date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'review_to',
                            message: 'To date must be a valid date and later than from date.',
                        },
                    }),
                }
            });

            $('[name="review_from"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                // startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('review_from');
                fv.revalidateField('review_to');
            });

            $('[name="review_to"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                // startDate: '{{ date('Y-m-d', strtotime(date('Y-m-d'). ' + 1 days')) }}',
            }).on('change', function (e) {
                fv.revalidateField('review_from');
                fv.revalidateField('review_to');
            });

            $('[name="deadline_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                // startDate: '{{ date('Y-m-d') }}',
            }).on('change', function (e) {
                fv.revalidateField('deadline_date');
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
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('performance.index') }}"
                                    class="text-decoration-none text-dark">Performance Review</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('performance.update', $performanceReview->id)}}" method="POST" id="performanceReviewForm">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header fw-bold"> Edit performance review</div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label class="form-label" for="employee">Employee</label>
                            </div>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" value="{{ $performanceReview->getEmployeeName() }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="review_type_id" class="form-label">Review Type</label>
                            </div>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" value="{{ $performanceReview->getReviewType() }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="fiscal_year_id" class="form-label">Fiscal Year</label>
                            </div>
                            <div class="col-lg-9">
                                <input class="form-control" type="text" value="{{ $performanceReview->getFiscalYear() }}" disabled>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="review_from" class="form-label required-label">Review From</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control @if($errors->has('review_from')) is-invalid @endif"
                                name="review_from" id="review_from" value="{{ $performanceReview->review_from->format('Y-m-d') }}" autocomplete="off">
                                @if ($errors->has('review_from'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="review_from">
                                            {!! $errors->first('review_from') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="review_to" class="form-label required-label">Review To</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control @if($errors->has('review_to')) is-invalid @endif"
                                name="review_to" id="review_to" value="{{ $performanceReview->review_to->format('Y-m-d') }}" autocomplete="off">
                                @if ($errors->has('review_to'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="review_to">
                                            {!! $errors->first('review_to') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <label for="deadline_date" class="form-label required-label">Deadline</label>
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control @if($errors->has('deadline_date')) is-invalid @endif"
                                name="deadline_date" id="deadline_date" value="{{ $performanceReview->deadline_date?->format('Y-m-d') }}" autocomplete="off">
                                @if ($errors->has('deadline_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div data-field="deadline_date">
                                            {!! $errors->first('deadline_date') !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        <a href="{{route('performance.index')}}" role="button" class="btn btn-sm btn-danger">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

@stop
