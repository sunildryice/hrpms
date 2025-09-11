@extends('layouts.container')

@section('title', 'Add Advance Settlement Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function(e) {

            $('#navbarVerticalMenu').find('[href="#navbarAdvanceRequest"]').addClass('active').attr('aria-expanded',
                'true');
            $('#navbarVerticalMenu').find('#navbarAdvanceRequest').addClass('show');
            $('#navbarVerticalMenu').find('#settlement-advance-requests-menu').addClass('active');


            const form = document.getElementById('settlementadvanceRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    completion_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Completion date is required',
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
            }).on('change', '[name="project_code_id"]', function(e) {
                fv.revalidateField('project_code_id');
            });

            $('[name="completion_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                endDate: '{{ date('Y-m-d') }}',
            }).on('change', function(e) {
                fv.revalidateField('completion_date');
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
                            <a href="{{ route('advance.settlement.index') }}" class="text-decoration-none text-dark">Advance
                                Settlement
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>

    <section class="registration">
        <div class="card">
            <form action="{{ route('advance.settlement.store', $advanceRequest->id) }}" id="settlementadvanceRequestAddForm"
                method="post" enctype="multipart/form-data" autocomplete="off">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label required-label">Project</label>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            @php $selectedProjectId = $advanceRequest->project_code_id; @endphp
                            <select class="select2 form-control" name="project_code_id" disabled>
                                <option value="">Select a Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" @if ($selectedProjectId == $project->id) selected @endif>
                                        {{ $project->getProjectCode() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('project_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="project_id">
                                        {!! $errors->first('project_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-2">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label required-label">Completion
                                    Date</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <input class="form-control @if ($errors->has('completion_date')) is-invalid @endif" type="text"
                                readonly name="completion_date" value="{{ old('completion_date') }}" />
                            @if ($errors->has('completion_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="completion_date">
                                        {!! $errors->first('completion_date') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {!! csrf_field() !!}
                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                    </button>
                    <a href="{!! route('advance.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </section>

@stop
