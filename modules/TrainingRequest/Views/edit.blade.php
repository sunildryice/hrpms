@extends('layouts.container')

@section('title', 'Training Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#training-requests-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('trainingEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    title: {
                        validators: {
                            notEmpty: {
                                message: 'Course title field is required.',
                            },
                        },
                    },
                    activity_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Activity Code field is required.',
                            },
                        },
                    },
                    account_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'Account Code field is required.',
                            },
                        },
                    },
                    course_fee: {
                        validators: {
                            notEmpty: {
                                message: 'Course fee field is required.',
                            },
                        },
                    },
                    duration: {
                        validators: {
                            notEmpty: {
                                message: 'Course duration field is required.',
                            },
                        },
                    },
                    own_time: {
                        validators: {
                            notEmpty: {
                                message: 'Own time field is required.',
                            },
                            numeric: {
                                message: 'Own time should be a number.'
                            },
                        },
                    },
                    work_time: {
                        validators: {
                            notEmpty: {
                                message: 'Work time Code field is required.',
                            },
                            numeric: {
                                message: 'Work time should be a number.'
                            },
                        },
                    },
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'Training start date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    end_date: {
                        validators: {
                            notEmpty: {
                                message: 'Training end date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    description: {
                        validators: {
                            notEmpty: {
                                message: 'Description field is required.',
                            },
                        },
                    },
                    attachment: {
                        validators: {
                            file: {
                                extension: 'jpeg,jpg,png,pdf',
                                type: 'image/jpeg,image/png,application/pdf',
                                maxSize: '2097152',
                                message: 'The selected file is not valid type or must not be greater than 2 MB.',
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

                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'start_date',
                            message: 'Start date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'end_date',
                            message: 'End date must be a valid date and later than from date.',
                        },
                    }),
                },
            });

            $(form).find('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                var start_date = $(this).val();
                $(form).find('[name="end_date"]').datepicker("option", "startDate", start_date);
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $(form).find('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $('[name="start_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '2022-04-02',
            }).on('change', function(e) {
                var start_date = $(this).val();
                $('[name="end_date"]').datepicker("option", "startDate", start_date);
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $('[name="end_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            }).on('change', function(e) {
                fv.revalidateField('start_date');
                fv.revalidateField('end_date');
            });

            $(form).on('change','[name="activity_code_id"]', function (e){
                $element = $(this);
                var activityCodeId = $element.val();
                var htmlToReplace = '<option value="">Select Account Code</option>';
                if (activityCodeId) {
                    var url = baseUrl + '/api/master/activity-codes/' + activityCodeId;
                    var successCallback = function (response) {
                        response.accountCodes.forEach(function (accountCode) {
                            htmlToReplace += '<option value="' + accountCode.id + '">' + accountCode.title +' '+ accountCode.description + '</option>';
                        });
                        $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace).trigger('change');
                    }
                    var errorCallback = function (error) {
                        console.log(error);
                    }
                    ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                } else {
                    $($element).closest('form').find('[name="account_code_id"]').html(htmlToReplace);
                }
                fv.revalidateField('activity_code_id');
            }).on('change','[name="account_code_id"]', function (e){
                fv.revalidateField('account_code_id');
            });
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                    class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Training Request</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Training Request</h4>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header fw-bold">
                            Edit Training Request
                        </div>
                        <form action="{!! route('training.requests.update',$trainingRequest->id) !!}" method="post"
                            enctype="multipart/form-data" id="trainingEditForm" autocomplete="off">
                          <div class="modal-body">
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="activitycode" class="form-label required-label">Name of Course(s) or Training requested
                                              Training Institution Name
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <input type="text" name="title" class="form-control" id="" value="{{$trainingRequest->title}}">
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="atvcde" class="form-label required-label">Date of course
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <div class="row">
                                          <div class="col-lg-6">
                                              <div class="input-group has-validation">
                                                  <div class="input-group-append">
                                                      <span class="input-group-text required-label">Start</span>
                                                  </div>
                                                  <input type="text" class="form-control" name="start_date" value="{{$trainingRequest->start_date}}" readonly />
                                              </div>
                                          </div>
                                          <div class="col-lg-6">
                                              <div class="input-group has-validation">
                                                  <div class="input-group-append">
                                                      <span class="input-group-text required-label" >End</span>
                                                  </div>
                                                  <input type="text" class="form-control" name="end_date" value="{{$trainingRequest->end_date}}" readonly />
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="atvcde" class="form-label required-label">Time (in Hours)
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <div class="row">
                                          <div class="col-lg-6">
                                              <div class="input-group has-validation">
                                                  <div class="input-group-append">
                                                      <span class="input-group-text required-label">Own</span>
                                                  </div>
                                                  <input type="number" class="form-control" name="own_time" value="{{$trainingRequest->own_time}}" autofocus="" min=0/>
                                              </div>
                                          </div>
                                          <div class="col-lg-6">
                                              <div class="input-group has-validation">
                                                  <div class="input-group-append">
                                                      <span class="input-group-text required-label" >Work</span>
                                                  </div>
                                                  <input type="number" class="form-control" name="work_time" value="{{$trainingRequest->work_time}}" autofocus="" min=0/>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="atvcde" class="form-label required-label">Course Duration
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <input type="text" name="duration" class="form-control" id="" value="{{$trainingRequest->duration}}">
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="atvcde" class="form-label required-label">Course Fee
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <input type="number" name="course_fee" class="form-control" id="" value="{{$trainingRequest->course_fee}}" min=0>
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="atvcde" class="form-label required-label">{{ __('label.activity-code') }}
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <select name="activity_code_id" class="form-select select2" data-width="100%">
                                          <option value="">Select an Activity Code</option>
                                          @foreach($activityCodes as $activityCode)
                                              <option value="{{ $activityCode->id }}" @if($trainingRequest->activity_code_id == $activityCode->id) selected @endif>
                                                  {{ $activityCode->getActivityCodeWithDescription() }}
                                              </option>
                                          @endforeach
                                      </select>
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="acccde" class="form-label required-label">{{ __('label.account-code') }}
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <select name="account_code_id" class="form-select select2" data-width="100%">
                                          <option value="">Select an Account Code</option>
                                          @foreach($accountCodes as $accountCode)
                                              <option value="{{ $accountCode->id }}" @if($trainingRequest->account_code_id == $accountCode->id) selected @endif>
                                                  {{ $accountCode->getAccountCodeWithDescription() }}
                                              </option>
                                          @endforeach
                                      </select>
                                  </div>
                              </div>

                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                          <label for="prblmdsc" class="form-label required-label">Brief Descriptions
                                          </label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                      <textarea name="description" id="prblmdsc" cols="30" rows="5"
                                          class="form-control">@if($trainingRequest->description){{$trainingRequest->description}}@endif</textarea>
                                  </div>
                              </div>
                              <div class="row mb-2">
                                  <div class="col-lg-3">
                                      <div class="d-flex align-items-start h-100">
                                            <label for="fileattch" class="m-0">Attach a copy of course(s)</label>
                                      </div>
                                  </div>
                                  <div class="col-lg-9">
                                        <input type="file" class="form-control" id="fileattch" name="attachment">
                                        <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                                  </div>
                              </div>
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="{{ route('training.requests.details',$trainingRequest->id) }}" class="btn btn-danger">Cancel</a>
                            </div>
                            {!! csrf_field() !!}
                            {!! method_field('PUT') !!}
                      </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
