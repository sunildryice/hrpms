@extends('layouts.container')

@section('title', 'Travel Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-report-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = 0;
            const form = document.getElementById('travelReportAddForm');

            const subjectValidators = {
                validators: {
                    notEmpty: {
                        message: 'Day is required'
                    }
                }
            };
            const dateValidators = {
                validators: {
                    notEmpty: {
                        message: 'Date is required'
                    }
                }
            };
            const taskValidators = {
                validators: {
                    notEmpty: {
                        message: 'Activities are required'
                    }
                }
            };
            const remarkValidators = {
                validators: {}
            };

            const fv = FormValidation.formValidation(form, {
                fields: {
                    'objectives': {
                        validators: {
                            notEmpty: {
                                message: 'Objectives are required'
                            }
                        }
                    },
                    'major_achievement': {
                        validators: {
                            notEmpty: {
                                message: 'Major achievement is required'
                            }
                        }
                    },
                    'not_completed_activities': {
                        validators: {
                            notEmpty: {
                                message: 'Not completed activities are required'
                            }
                        }
                    },
                    'conclusion_recommendations': {
                        validators: {
                            notEmpty: {
                                message: 'Conclusion is required'
                            }
                        }
                    },

                    'recommendation[day_number][0]': subjectValidators,
                    'recommendation[activity_date][0]': dateValidators,
                    'recommendation[completed_tasks][0]': taskValidators,
                    'recommendation[remarks][0]': remarkValidators,
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


    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('travel.requests.index') }}" class="text-decoration-none text-dark">Travel
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
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Travel Request Details
                    </div>
                    @include('TravelRequest::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Prepare Travel Report
                    </div>

                    <form action="{{ route('travel.reports.store', $travelRequest->id) }}" id="travelReportAddForm"
                        method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">
                                                    General Objective/Purpose of travel
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="objectives" class="form-control" rows="8">{{ old('objectives') }}</textarea>
                                            @if ($errors->has('objectives'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="objectives">
                                                        {!! $errors->first('objectives') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">
                                                    Major Achievement
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="major_achievement" class="form-control" rows="8" placeholder="">{{ old('major_achievement') }}</textarea>
                                            @if ($errors->has('major_achievement'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="major_achievement">
                                                        {!! $errors->first('major_achievement') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th colspan="4">Daily Carried Activities /
                                                            Completed Tasks</th>
                                                    </tr>
                                                    <tr>
                                                        <th style="width: 10%">Day</th>
                                                        <th style="width: 15%">Date</th>
                                                        <th>Carried Activities / Completed Tasks</th>
                                                        <th style="width: 25%">Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $startDate = \Carbon\Carbon::parse(
                                                            $travelRequest->departure_date,
                                                        );
                                                        $endDate = \Carbon\Carbon::parse($travelRequest->return_date);
                                                        $dates = collect();
                                                        for (
                                                            $date = $startDate->copy();
                                                            $date->lte($endDate);
                                                            $date->addDay()
                                                        ) {
                                                            $dates->push($date->copy());
                                                        }
                                                    @endphp

                                                    @forelse($dates as $index => $date)
                                                        @php
                                                            $weekdayName = $date->format('l');
                                                            $formattedDate = $date->format('d M Y');
                                                            $storeDate = $date->format('Y-m-d');
                                                            $dayNumber = $index + 1;
                                                        @endphp

                                                        <tr>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control text-center fw-bold"
                                                                    value="{{ $weekdayName }}" readonly>
                                                                <input type="hidden"
                                                                    name="recommendation[day_number][{{ $index }}]"
                                                                    value="{{ $dayNumber }}">
                                                            </td>

                                                            <td>
                                                                <input type="text" class="form-control"
                                                                    value="{{ $formattedDate }}" readonly>
                                                                <input type="hidden"
                                                                    name="recommendation[activity_date][{{ $index }}]"
                                                                    value="{{ $storeDate }}">
                                                            </td>

                                                            <td>
                                                                <textarea name="recommendation[completed_tasks][{{ $index }}]" rows="3" class="form-control">{{ old("recommendation.completed_tasks.{$index}") }}</textarea>

                                                                @error("recommendation.completed_tasks.{$index}")
                                                                    <div class="invalid-feedback d-block">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </td>

                                                            <td>
                                                                <textarea name="recommendation[remarks][{{ $index }}]" rows="3" class="form-control">{{ old("recommendation.remarks.{$index}") }}</textarea>

                                                                @error("recommendation.remarks.{$index}")
                                                                    <div class="invalid-feedback d-block">{{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-danger">
                                                                Invalid travel dates: Departure date must be before or equal
                                                                to Return date.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">
                                                    Not Completed Activities & Reasons
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="not_completed_activities" class="form-control" rows="8" placeholder="">{{ old('not_completed_activities') }}</textarea>
                                            @if ($errors->has('not_completed_activities'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="not_completed_activities">
                                                        {!! $errors->first('not_completed_activities') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="form-label required-label">
                                                    Conclusion & Recommendations
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="conclusion_recommendations" class="form-control @if ($errors->has('conclusion_recommendations')) is-invalid @endif"
                                                placeholder="">{{ old('conclusion_recommendations') }}</textarea>
                                            @if ($errors->has('conclusion_recommendations'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="conclusion_recommendations">
                                                        {!! $errors->first('conclusion_recommendations') !!}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    {!! csrf_field() !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Save
                            </button>
                            <a href="{!! route('travel.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                </div>
                </form>
            </div>

        </div>
        </div>
    </section>
@stop
