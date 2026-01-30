@extends('layouts.container')

@section('title', 'Approve Travel Report')

@section('page_css')
    <style>
        .recommend-col {
            max-width: 350px;
            white-space: pre-line;
        }
    </style>

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-travel-report-menu').addClass('active');
        });
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('travelReportApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'Remarks is required',
                            },
                        },
                    }
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

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
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
                            <a href="{{ route('approve.travel.reports.index') }}"
                                class="text-decoration-none text-dark">Travel
                                Report</a>
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
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Information
                    </div>
                    <div class="card-body">
                        @include('TravelRequest::Partials.detail')
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Report Summary
                    </div>
                    <div class="card-body">
                        @include('TravelRequest::Partials.report-detail')
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">Travel Report </div>
                    <div class="card-body">

                        <div class="card mb-3">
                            <div class="card-header fw-bold">General Objective/Purpose of Travel</div>
                            <div class="card-body">{{ nl2br(e($travelReport->objectives)) }}</div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Major Achievement</div>
                            <div class="card-body">{{ nl2br(e($travelReport->major_achievement)) }}</div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Daily Carried Activities / Completed Tasks</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                {{-- <th style="width: 10%">Day</th> --}}
                                                <th style="width: 15%">Date</th>
                                                {{-- <th>{{ __('label.activity') }}</th> --}}
                                                <th>Planned Activities</th>
                                                <th>Carried Activities / Completed Tasks</th>
                                                <th style="width: 20%">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $itineraries =
                                                    $travelRequest?->travelRequestDayItineraries ?? collect();
                                            @endphp

                                            @forelse($itineraries as $index => $itinerary)
                                                @php
                                                    $date = \Carbon\Carbon::parse($itinerary->date);
                                                    $weekday = $date->format('l');
                                                    $formattedDate = $date->format('d M Y');
                                                @endphp

                                                <tr>
                                                    {{-- <td class="text-center fw-bold">{{ $weekday }}</td> --}}
                                                    <td class="text-nowrap">{{ $formattedDate }}</td>
                                                    {{-- <td class="text-nowrap">{{ $itinerary?->activity?->title }}</td> --}}
                                                    <td class="text-nowrap">{{ $itinerary?->planned_activities }}</td>
                                                    <td>{!! $itinerary->completed_tasks ? nl2br(e($itinerary->completed_tasks)) : '<em class="text-muted">Not filled</em>' !!}</td>
                                                    <td>{!! $itinerary->remarks ? nl2br(e($itinerary->remarks)) : '' !!}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        No itinerary days available for this travel request.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Not Completed Activities & Reasons</div>
                            <div class="card-body">{{ nl2br(e($travelReport->not_completed_activities)) }}</div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header fw-bold">Conclusion & Recommendations</div>
                            <div class="card-body">{{ nl2br(e($travelReport->conclusion_recommendations)) }}</div>
                        </div>

                        <div class="card mb-3">
                            @include('Attachment::list', [
                                'modelType' => 'Modules\TravelRequest\Models\TravelReport',
                                'modelId' => $travelReport->id,
                            ])
                        </div>
                    </div>

                    <form action="{{ route('approve.travel.reports.store', $travelReport->id) }}"
                        id="travelReportApproveForm" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationleavetype"
                                                    class="form-label required-label">Status</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select name="status_id" class="select2 form-control" data-width="100%">
                                                <option value="">Select a Status</option>
                                                <option value="2">Return to Requester</option>
                                                <option value="6">Approve</option>
                                            </select>
                                            @if ($errors->has('status_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="status_id">
                                                        {!! $errors->first('status_id') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationRemarks"
                                                    class="form-label required-label">Remarks</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea type="text" class="form-control @if ($errors->has('log_remarks')) is-invalid @endif" name="log_remarks">{{ old('log_remarks') }}</textarea>
                                            @if ($errors->has('log_remarks'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="log_remarks">{!! $errors->first('log_remarks') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            @if (!$authUser->can('submit', $travelReport))
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                            @endif
                            <a href="{!! route('approve.travel.reports.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop
