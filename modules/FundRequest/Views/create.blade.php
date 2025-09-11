@extends('layouts.container')

@section('title', 'Add New Fund Request')

@section('page_js')
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function (e) {
            $('#navbarVerticalMenu').find('#fund-requests-menu').addClass('active');
            const form = document.getElementById('fundRequestAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    // district_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'District is required',
                    //         },
                    //     },
                    // },
                    request_for_office_id: {
                        validators: {
                            notEmpty: {
                                message: 'Office (requested for) is required',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            //notEmpty: {
                            //    message: 'Attachment is required.'
                            //},
                            file: {
                                extension: 'jpeg,jpg,png,pdf,doc,docx,dot,xlsx,xls,xlm,xla,xlc,xlt,xlw',
                                type: 'image/jpeg,image/png,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,application/vnd.ms-office,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                maxSize: '2097152',
                                message: 'The selected file is not valid or must not be greater than 2 MB.',
                            },
                        },
                    },
                    year_month: {
                        validators: {
                            notEmpty: {
                                message: 'The month is required',
                            },
                        },
                    },
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The project code is required',
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

            $(form)
            // .on('change', '[name="district_id"]', function(e){
            //     fv.revalidateField('district_id');
            // })
            .on('change', '[name="request_for_office_id"]', function(e){
                fv.revalidateField('request_for_office_id');
            })
            .on('change', '[name="project_code_id"]', function(e){
                fv.revalidateField('project_code_id');
            });

            $(form.querySelector('[name="year_month"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm',
                startDate: '{!! date('Y-m',strtotime("-1 day")) !!}',
            }).on('change', function (e){
                // console.log('{!! date('Y-m',strtotime("-1 day")) !!}');
                fv.revalidateField('year_month');
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
                                    <a href="{{ route('fund.requests.index') }}" class="text-decoration-none text-dark">Fund
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
                <div class="card">
                    <div class="card-header fw-bold">Add Fund Request</div>
                      <form action="{{ route('fund.requests.store') }}" id="fundRequestAddForm" method="post"
                            enctype="multipart/form-data" autocomplete="off">
                          <div class="card-body">
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="validationfundtype"
                                                 class="form-label required-label">Year-Month</label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <input type = "text" class="form-control @if($errors->has('year_month')) is-invalid @endif" name="year_month" value="{!! date('Y-m',strtotime("+1 month")) !!}" readonly>
                                      @if($errors->has('year_month'))
                                          <div class="fv-plugins-message-container invalid-feedback">
                                              <div data-field="year_month">
                                                  {!! $errors->first('year_month') !!}
                                              </div>
                                          </div>
                                      @endif
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="request_for_office_id" class="form-label required-label">Office (requested for)</label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <select name="request_for_office_id" class="select2 form-control" data-width="100%">
                                          <option value="">Select an Office</option>
                                          @foreach($offices as $office)
                                              <option value="{{ $office->id }}" data-fund="{{ $office->id }}"
                                                  {{ $office->id ==  auth()->user()?->employee?->latestTenure?->office_id ? "selected":"" }}>
                                                  {{ $office->getOfficeName() }}
                                              </option>
                                          @endforeach
                                      </select>
                                      @if($errors->has('request_for_office_id'))
                                          <div class="fv-plugins-message-container invalid-feedback">
                                              <div data-field="request_for_office_id">
                                                  {!! $errors->first('request_for_office_id') !!}
                                              </div>
                                          </div>
                                      @endif
                                  </div>
                              </div>
                              {{-- <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="validationfundtype" class="form-label required-label">District</label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <select name="district_id" class="select2 form-control" data-width="100%">
                                          <option value="">Select a District</option>
                                          @foreach($districts as $district)
                                              <option value="{{ $district->id }}" data-fund="{{ $district->id }}"
                                                  {{ $district->id == old('district_id')? "selected":"" }}>
                                                  {{ $district->getDistrictName() }}
                                              </option>
                                          @endforeach
                                      </select>
                                      @if($errors->has('district_id'))
                                          <div class="fv-plugins-message-container invalid-feedback">
                                              <div data-field="district_id">
                                                  {!! $errors->first('district_id') !!}
                                              </div>
                                          </div>
                                      @endif
                                  </div>
                              </div> --}}

                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="validationfundtype" class="form-label required-label">Project</label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <select name="project_code_id" class="select2 form-control" data-width="100%">
                                          <option value="">Select a Project</option>
                                          @foreach($projectCodes as $project)
                                              <option value="{{ $project->id }}" data-fund="{{ $project->id }}"
                                                  {{ $project->id == old('project_code_id')? "selected":"" }}>
                                                  {{ $project->getProjectCodeWithDescription() }}
                                              </option>
                                          @endforeach
                                      </select>
                                      @if($errors->has('project_code_id'))
                                          <div class="fv-plugins-message-container invalid-feedback">
                                              <div data-field="project_code_id">
                                                  {!! $errors->first('project_code_id') !!}
                                              </div>
                                          </div>
                                      @endif
                                  </div>
                              </div>

                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="validationRemarks" class="m-0">Remarks</label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <textarea type="text"
                                                class="form-control @if($errors->has('remarks')) is-invalid @endif"
                                                name="remarks">{{ old('remarks') }}</textarea>
                                      @if($errors->has('remarks'))
                                          <div class="fv-plugins-message-container invalid-feedback">
                                              <div
                                                  data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                          </div>
                                      @endif
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="validationRemarks" class="form-label">Attachment</label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <input type="file" name="attachment"
                                             class="form-control js-document-upload @if($errors->has('attachment')) is-invalid @endif"/>
                                      <small>Supported file types jpeg/jpg/png/pdf/doc/xls and file size of upto
                                          2MB.</small>
                                      @if($errors->has('attachment'))
                                          <div class="fv-plugins-message-container invalid-feedback">
                                              <div
                                                  data-field="attachment">{!! $errors->first('attachment') !!}</div>
                                          </div>
                                      @endif
                                  </div>
                              </div>
                              {!! csrf_field() !!}
                          </div>
                          <div class="card-footer border-0 justify-content-end d-flex gap-2">
                              <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                              </button>
                              <a href="{!! route('fund.requests.index') !!}"
                                 class="btn btn-danger btn-sm">Cancel</a>
                          </div>
                      </form>
                  </div>
            </section>

@stop
