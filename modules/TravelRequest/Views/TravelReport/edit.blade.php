@extends('layouts.container')

@section('title', 'Travel Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-report-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = {{ $travelReport->travelReportRecommendations->count() }};

            const form = document.getElementById('travelReportAddForm');
            const template = document.getElementById('template');

            // const subjectValidators = {
            //     validators: {
            //         notEmpty: {
            //             message: 'Day is required'
            //         }
            //     }
            // };
            // const dateValidators = {
            //     validators: {
            //         notEmpty: {
            //             message: 'Date is required'
            //         }
            //     }
            // };
            // const taskValidators = {
            //     validators: {
            //         notEmpty: {
            //             message: 'Activities are required'
            //         }
            //     }
            // };
            // const remarkValidators = {
            //     validators: {}
            // };

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
                    'conclusion_recommendations': {
                        validators: {
                            notEmpty: {
                                message: 'Conclusion is required'
                            }
                        }
                    },
                    'completed_tasks': {
                        selector: '#activitiesErrorContainer',
                        validators: {
                            callback: {
                                message: 'Activities are required',
                                callback: function(input) {
                                    const fields = form.querySelectorAll(
                                        'textarea[name^="itinerary[completed_tasks]"]');
                                    let allFilled = true;

                                    fields.forEach(function(field) {
                                        if (field.value.trim() === '') {
                                            allFilled = false;
                                            field.closest('tr').classList.add('table-danger');
                                        } else {
                                            field.closest('tr').classList.remove(
                                                'table-danger');
                                        }
                                    });

                                    return allFilled;
                                }
                            }
                        }
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
                    declarative: new FormValidation.plugins.Declarative({
                        html5Input: false
                    }),
                    message: new FormValidation.plugins.Message({
                        clazz: 'invalid-feedback',
                        container: function(field, element) {
                            if (field === 'completed_tasks') {
                                return '#activitiesErrorContainer';
                            }
                            return FormValidation.plugins.Message.getParent(element);
                        }
                    })
                },
            });

            form.querySelectorAll('textarea[name^="itinerary[completed_tasks]"]').forEach(function(field) {
                field.addEventListener('input', function() {
                    fv.revalidateField('completed_tasks');
                });
            });

            @if ($errors->any())
                fv.revalidateField('completed_tasks');
            @endif

        });
    </script>
@endsection

@section('page-content')
    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('travel.requests.index') }}"
                                class="text-decoration-none text-dark">Travel Requests</a></li>
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
                    <div class="card-header fw-bold">Travel Request Details</div>
                    @include('TravelRequest::Partials.detail')
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">Update Travel Report</div>

                    <form action="{{ route('travel.reports.update', $travelReport->id) }}" id="travelReportAddForm"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">

                                    <div class="row mb-2">
                                        <div class="col-lg-3 d-flex align-items-start h-100">
                                            <label class="form-label required-label">General Objective/Purpose of
                                                travel</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="objectives" class="form-control" rows="8">{{ old('objectives', $travelReport->objectives) }}</textarea>
                                            @error('objectives')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3 d-flex align-items-start h-100">
                                            <label class="form-label required-label">Major Achievement</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="major_achievement" class="form-control" rows="8">{{ old('major_achievement', $travelReport->major_achievement) }}</textarea>
                                            @error('major_achievement')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th colspan="4">Daily Carried Activities /
                                                            Completed Tasks</th>
                                                    </tr>
                                                    <tr>
                                                        {{-- <th style="width: 10%">Day</th> --}}
                                                        <th style="width: 15%">{{ __('label.date') }}</th>
                                                        <th style="width: 15%">{{ __('label.activity') }}</th>
                                                        <th style="width: 25%">Planned Activities</th>
                                                        <th style="width: 25%">Carried Activities / Completed Tasks</th>
                                                        <th style="width: 20%">{{ __('label.remarks') }}</th>
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
                                                            {{-- <td class="text-center">
                                                                <input type="text" class="form-control fw-bold text-center" value="{{ $weekday }}" readonly>
                                                            </td> --}}

                                                            <td class="text-center">
                                                                {{ $formattedDate }}
                                                                <input type="hidden"
                                                                    name="itinerary[itinerary_id][{{ $index }}]"
                                                                    value="{{ $itinerary->id }}">
                                                            </td>

                                                            <td>
                                                                {{ $itinerary?->activity?->title }}
                                                            </td>

                                                            <td>
                                                                <textarea class="form-control" rows="3" readonly>{{ $itinerary->planned_activities ?? '' }}</textarea>
                                                            </td>

                                                            <td>
                                                                <textarea name="itinerary[completed_tasks][{{ $index }}]" rows="3"
                                                                    class="form-control @error("itinerary.completed_tasks.{$index}") is-invalid @enderror">{{ old("itinerary.completed_tasks.{$index}", $itinerary->completed_tasks ?? '') }}</textarea>
                                                            </td>

                                                            <td>
                                                                <textarea name="itinerary[remarks][{{ $index }}]" rows="3"
                                                                    class="form-control @error("itinerary.remarks.{$index}") is-invalid @enderror">{{ old("itinerary.remarks.{$index}", $itinerary->remarks ?? '') }}</textarea>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-danger">
                                                                No itinerary days found for this travel request.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            <div id="activitiesErrorContainer"
                                                class="fv-plugins-message-container invalid-feedback d-block"></div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3 d-flex align-items-start h-100">
                                            <label class="form-label">Not Completed Activities &
                                                Reasons</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="not_completed_activities" class="form-control" rows="8">{{ old('not_completed_activities', $travelReport->not_completed_activities) }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3 d-flex align-items-start h-100">
                                            <label class="form-label required-label">Conclusion & Recommendations</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="conclusion_recommendations" class="form-control">{{ old('conclusion_recommendations', $travelReport->conclusion_recommendations) }}</textarea>
                                            @error('conclusion_recommendations')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    @include('Attachment::index', [
                                        'modelType' => 'Modules\TravelRequest\Models\TravelReport',
                                        'modelId' => $travelReport->id,
                                    ])

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="" class="m-0">
                                                    {{ __('label.approval') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedApproverId = old('approver_id') ?: $travelReport->approver_id; @endphp
                                            <select name="approver_id"
                                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                                data-width="100%">
                                                @if ($supervisors->count() !== 1)
                                                    <option value="">Select an Approver</option>
                                                @endif
                                                @foreach ($supervisors as $approver)
                                                    <option value="{{ $approver->id }}"
                                                        {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                        {{ $approver->getFullName() }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('approver_id'))
                                                <div class="fv-plugins-message-container invalid-feedback">
                                                    <div data-field="approver_id">{!! $errors->first('approver_id') !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {!! csrf_field() !!}
                                    {!! method_field('PUT') !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update
                            </button>
                            @if ($authUser->can('submit', $travelReport))
                                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                    Submit
                                </button>
                            @endif
                            <a href="{!! route('travel.reports.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
        </div>
    </section>
@endsection
