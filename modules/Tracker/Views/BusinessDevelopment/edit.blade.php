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

            function togglePartnershipFields() {
                var val = $('[name="partnership_type"]').val();
                if (val === 'Consortium') {
                    $('#consortiumLeadRow').show();
                    $('#consortiumPartnersRow').show();
                } else {
                    $('#consortiumLeadRow').hide();
                    $('#consortiumPartnersRow').hide();
                }
            }

            $('[name="partnership_type"]').on('change', togglePartnershipFields);
            togglePartnershipFields();

        });

    </script>
@endsection

@section('page-content')
<div class="m-content">
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
            <form action="{{route('business-development.update', $businessDevelopment->id)}}" method="POST" id="businessDevelopmentUpdateForm" enctype="multipart/form-data">
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
                                <label class="form-label required-label" for="date">Date </label>
                                <input class="form-control" type="text" name="date" id="date" value="{{$businessDevelopment->date?->format('Y-m-d')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="call_name">Call Name</label>
                                <input class="form-control" type="text" name="call_name" id="call_name" value="{{$businessDevelopment->call_name}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="url">URL</label>
                                <input class="form-control" type="text" name="url" id="url" value="{{$businessDevelopment->url}}" placeholder="https://">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="thematic_area_id">Thematic Area</label>
                                <select class="form-select select2" name="thematic_area_id" id="thematic_area_id">
                                    <option value="">Select Thematic Area</option>
                                    @foreach($thematicAreas as $area)
                                        <option value="{{$area->id}}" {{$businessDevelopment->thematic_area_id == $area->id ? 'selected' : ''}}>{{$area->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="project_name">Project Name</label>
                                <input class="form-control" type="text" name="project_name" id="project_name" value="{{$businessDevelopment->project_name}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="funding_agency">Funding Agency</label>
                                <input class="form-control" type="text" name="funding_agency" id="funding_agency" value="{{$businessDevelopment->funding_agency}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="contracting_agency">Contracting Agency</label>
                                <input class="form-control" type="text" name="contracting_agency" id="contracting_agency" value="{{$businessDevelopment->contracting_agency}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="partnership_type">Partnership Type</label>
                                <select class="form-select select2" name="partnership_type" id="partnership_type">
                                    <option value="">Select Partnership Type</option>
                                    <option value="Consortium" {{$businessDevelopment->partnership_type == 'Consortium' ? 'selected' : ''}}>Consortium</option>
                                    <option value="Single Organization" {{$businessDevelopment->partnership_type == 'Single Organization' ? 'selected' : ''}}>Single Organization</option>
                                </select>
                            </div>
                            <div class="col-lg-4" id="consortiumLeadRow" style="display: none;">
                                <label class="form-label" for="consortium_lead">Consortium Lead</label>
                                <input class="form-control" type="text" name="consortium_lead" id="consortium_lead" value="{{$businessDevelopment->consortium_lead}}">
                            </div>
                            <div class="col-lg-4" id="consortiumPartnersRow" style="display: none;">
                                <label class="form-label" for="consortium_partners">Consortium Partners</label>
                                <input class="form-control" type="text" name="consortium_partners" id="consortium_partners" value="{{$businessDevelopment->consortium_partners}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="project_type">Project Type</label>
                                <select class="form-select select2" name="project_type" id="project_type">
                                    <option value="">Select Project Type</option>
                                    <option value="Research" {{$businessDevelopment->project_type == 'Research' ? 'selected' : ''}}>Research</option>
                                    <option value="Implementation" {{$businessDevelopment->project_type == 'Implementation' ? 'selected' : ''}}>Implementation</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-select select2" name="status" id="status">
                                    <option value="">Select Status</option>
                                    <option value="Scanned" {{$businessDevelopment->status == 'Scanned' ? 'selected' : ''}}>Scanned</option>
                                    <option value="Submitted" {{$businessDevelopment->status == 'Submitted' ? 'selected' : ''}}>Submitted</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="result">Result</label>
                                <select class="form-select select2" name="result" id="result">
                                    <option value="">Select Result</option>
                                    <option value="Rejected" {{$businessDevelopment->result == 'Rejected' ? 'selected' : ''}}>Rejected</option>
                                    <option value="Awaiting Result" {{$businessDevelopment->result == 'Awaiting Result' ? 'selected' : ''}}>Awaiting Result</option>
                                    <option value="Awarded" {{$businessDevelopment->result == 'Awarded' ? 'selected' : ''}}>Awarded</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-12">
                                <label class="form-label" for="remarks">Remarks</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="3">{{$businessDevelopment->remarks}}</textarea>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-6">
                                <label class="form-label" for="attachment">Attachment</label>
                                <input class="form-control" type="file" name="attachment" id="attachment">
                                <small class="text-muted">Supported files: pdf, jpg, jpeg, png, doc, docx, xlsx (Max 2MB)</small>
                                @if ($businessDevelopment->attachment)
                                    <a href="{{asset('storage/'.$businessDevelopment->attachment)}}" target="_blank" class="ms-2 fs-5" title="View Attachment">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @endif
                                @if ($errors->has('attachment'))
                                    <span class="text-danger">{{$errors->first('attachment')}}</span>
                                @endif
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
