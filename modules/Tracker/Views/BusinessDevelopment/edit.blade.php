@extends('layouts.container')

@section('title', 'Edit Business Development')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#business-development-index').addClass('active');

            const form = document.getElementById('businessDevelopmentUpdateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    date: {
                        validators: {
                            notEmpty: {
                                message: 'The date is required.'
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'Date must be a valid date.'
                            }
                        }
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
                        validating: 'bi bi-arrow-repeat'
                    }),
                }
            });

            $('[name="date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function (e) {
                fv.revalidateField('date');
            });

        });

    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a href="{{route('business-development.index')}}" class="text-decoration-none">Business Development</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('business-development.update', $businessDevelopment->id)}}" method="POST" id="businessDevelopmentUpdateForm">
                @csrf
                @method('put')
                <div class="card">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">
                            Edit Business Development
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="thematic_area_id">Thematic Area</label>
                                <select class="form-select" name="thematic_area_id" id="thematic_area_id">
                                    <option value="">-- Select --</option>
                                    @foreach($thematicAreas as $area)
                                        <option value="{{$area->id}}" {{$businessDevelopment->thematic_area_id == $area->id ? 'selected' : ''}}>{{$area->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="date" id="date" value="{{$businessDevelopment->date?->format('Y-m-d')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="call_name">Call Name</label>
                                <input class="form-control" type="text" name="call_name" id="call_name" value="{{$businessDevelopment->call_name}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="donor_name">Donor Name</label>
                                <input class="form-control" type="text" name="donor_name" id="donor_name" value="{{$businessDevelopment->donor_name}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="project_type">Project Type</label>
                                <select class="form-select" name="project_type" id="project_type">
                                    <option value="">-- Select --</option>
                                    <option value="Research" {{$businessDevelopment->project_type == 'Research' ? 'selected' : ''}}>Research</option>
                                    <option value="Implementation" {{$businessDevelopment->project_type == 'Implementation' ? 'selected' : ''}}>Implementation</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="">-- Select --</option>
                                    <option value="Scanned" {{$businessDevelopment->status == 'Scanned' ? 'selected' : ''}}>Scanned</option>
                                    <option value="Submitted" {{$businessDevelopment->status == 'Submitted' ? 'selected' : ''}}>Submitted</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="result">Result</label>
                                <select class="form-select" name="result" id="result">
                                    <option value="">-- Select --</option>
                                    <option value="Rejected" {{$businessDevelopment->result == 'Rejected' ? 'selected' : ''}}>Rejected</option>
                                    <option value="Awaiting Result" {{$businessDevelopment->result == 'Awaiting Result' ? 'selected' : ''}}>Awaiting Result</option>
                                    <option value="Awarded" {{$businessDevelopment->result == 'Awarded' ? 'selected' : ''}}>Awarded</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        <a href="{{route('business-development.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
