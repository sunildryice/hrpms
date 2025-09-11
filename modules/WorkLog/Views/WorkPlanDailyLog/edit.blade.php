@extends('layouts.container')

@section('title', 'Work Log')
@section('page_css')
    <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css">
@endsection
@section('page_js')
    <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('[data-toggle="datepicker"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'YYYY-MM-DD',
            endDate: "{!! date('Y-m-d') !!}",
        });

        $(document).ready(function() {
            $('.step-item').click(function() {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#work-logs-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function (e) {
            const form = document.getElementById('workPlanDailyLogAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    log_date: {
                        validators: {
                            notEmpty: {
                                message: 'The log date field is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The log date is not a valid date',
                            },
                        },
                    },

                    major_activities: {
                        validators: {
                            notEmpty: {
                                message: 'Major Activities/ Planned Tasks field is required',
                            },
                        },
                    },

                    // activity_area_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Activity Area field is required',
                    //         },
                    //     },
                    // },

                    // priority_id: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Priority field is required',
                    //         },
                    //     },
                    // },

                    // status: {
                    //     validators: {
                    //         notEmpty: {
                    //             message: 'Status field is required',
                    //         },
                    //     },
                    // },
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

            $(form.querySelector('[name="log_date"]')).datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! $startDate !!}',
                endDate: '{!! $endDate !!}',
            }).on('change', function (e){
                fv.revalidateField('log_date');
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
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a></li>
                            {{-- <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li> --}}
                            <li class="breadcrumb-item" aria-current="page"><a href="{!! route('daily.work.logs.index', $workPlanDailyLog->work_plan_id) !!}" class="text-decoration-none text-dark">Worklog</a></li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a href="{!! route('daily.work.logs.index',$workPlanDailyLog->work_plan_id) !!}" class="text-decoration-none text-dark">Daily Worklog</a></li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Edit Daily Worklog</h4>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="card">
                <form class="g-3 needs-validation" action="{{ route('daily.work.logs.update', $workPlanDailyLog->id) }}" id="workPlanDailyLogAddForm" method="post"
                    enctype="multipart/form-data" autocomplete="off" novalidate>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationfullname" class="form-label required-label">{{ __('label.date') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input
                                    {{-- data-toggle="datepicker" --}}
                                    type="text"
                                    name="log_date"
                                    value="{{ $workPlanDailyLog->log_date }}"
                                    id=""
                                    class="form-control @if($errors->has('log_date')) is-invalid @endif" readonly>
                                @if($errors->has('log_date'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div
                                            data-field="log_date">{!! $errors->first('log_date') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">{{ __('label.major-activities') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea rows="5"
                                class="form-control @if($errors->has('major_activities')) is-invalid @endif"
                                name="major_activities">@if($workPlanDailyLog->major_activities){{ $workPlanDailyLog->major_activities}}@endif</textarea>
                                @if($errors->has('major_activities'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div
                                            data-field="major_activities">{!! $errors->first('major_activities') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- <div class="row mb-2"> --}}
                        {{--     <div class="col-lg-3"> --}}
                        {{--         <div class="d-flex align-items-start h-100"> --}}
                        {{--             <label for="donor_id" class="m-0">{{ __('label.donor') }} --}}
                        {{--             </label> --}}
                        {{--         </div> --}}
                        {{--     </div> --}}
                        {{--     <div class="col-lg-9"> --}}
                        {{--         <select id="donor_id" class="form-control select2 @if($errors->has('donor_id')) is-invalid @endif" --}}
                        {{--             placeholder="Select an Donor" --}}
                        {{--             name="donor_id" autocomplete="off"> --}}
                        {{--             <option value="">Select a Donor</option> --}}
                        {{--         @foreach($donors as $donor) --}}
                        {{--             <option value="{{ $donor->id }}" {{$donor->id == $workPlanDailyLog->donor_id? "selected":""}}> --}}
                        {{--                 {{ $donor->getDonorCodeWithDescription() }} --}}
                        {{--             </option> --}}
                        {{--         @endforeach --}}
                        {{--         </select> --}}
                        {{--         @if($errors->has('activity_area_id')) --}}
                        {{--             <div class="fv-plugins-message-container invalid-feedback"> --}}
                        {{--                 <div --}}
                        {{--                     data-field="activity_area_id">{!! $errors->first('activity_area_id') !!}</div> --}}
                        {{--             </div> --}}
                        {{--         @endif --}}
                        {{--     </div> --}}
                        {{-- </div> --}}
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="Activityarea" class="m-0">{{ __('label.activity-area') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select id="Activityarea" class="form-control select2 @if($errors->has('activity_area_id')) is-invalid @endif"
                                    placeholder="Select an Activity Area"
                                    name="activity_area_id" autocomplete="off">
                                    <option value="">Select an Activity Area</option>
                                @foreach($activityAreas as $activityarea)
                                    <option value="{{ $activityarea->id }}" @if($activityarea->id == $workPlanDailyLog->activity_area_id) selected @endif>
                                        {{ $activityarea->title }}
                                    </option>
                                @endforeach
                                </select>
                                @if($errors->has('activity_area_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div
                                            data-field="activity_area_id">{!! $errors->first('activity_area_id') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="Priority" class="m-0">{{ __('label.priority') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select id="priority" class="form-control select2 @if($errors->has('priority_id')) is-invalid @endif"
                                    placeholder="Select a Priority"
                                    name="priority_id" autocomplete="off">
                                    <option value="">Select a Priority</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority->id }}" {{$priority->id == $workPlanDailyLog->priority_id? "selected":""}}>
                                        {{ $priority->title }}
                                    </option>
                                @endforeach
                                </select>
                                @if($errors->has('priority_id'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div
                                            data-field="priority_id">{!! $errors->first('priority_id') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validateusers" class="m-0">{{ __('label.status') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text"
                                    name="status"
                                    value="{{$workPlanDailyLog->status}}"
                                    id=""
                                    class="form-control @if($errors->has('status')) is-invalid @endif">
                                @if($errors->has('status'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div
                                            data-field="status">{!! $errors->first('status') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="m-0">{{ __('label.other-activities') }}</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text"
                                name="other_activities"
                                id=""
                                value="{{$workPlanDailyLog->other_activities}}"
                                class="form-control @if($errors->has('other_activities')) is-invalid @endif">
                                @if($errors->has('other_activities'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div
                                            data-field="other_activities">{!! $errors->first('other_activities') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="m-0">{{ __('label.remarks') }} </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea rows="5"
                                    class="form-control @if($errors->has('remarks')) is-invalid @endif"
                                    name="remarks">@if($workPlanDailyLog->remarks){{$workPlanDailyLog->remarks }}@endif</textarea>
                                @if($errors->has('remarks'))
                                    <div class="fv-plugins-message-container invalid-feedback">
                                        <div
                                            data-field="remarks">{!! $errors->first('remarks') !!}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    {!! csrf_field() !!}
                    {!! method_field('PUT') !!}
                    <div class="card-footer border-0 justify-content-end d-flex gap-2">
                        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm next">Save</button>
                        <button type="reset" class="btn btn-danger btn-sm">Reset</button>
                    </div>
                </form>
            </div>
        </section>
    @stop
