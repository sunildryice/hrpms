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

            $('input[name="recommendation[activity_date][0]"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}'
            }).on('change', function() {
                fv.revalidateField(this.name);
            });

            @foreach ($travelReport->travelReportRecommendations as $index => $rec)
                fv.addField('recommendation[day_number][{{ $index }}]', subjectValidators)
                    .addField('recommendation[activity_date][{{ $index }}]', dateValidators)
                    .addField('recommendation[completed_tasks][{{ $index }}]', taskValidators)
                    .addField('recommendation[remarks][{{ $index }}]', remarkValidators);

                $(document).ready(function() {
                    const input = document.querySelector(
                        'input[name="recommendation[activity_date][{{ $index }}]"]');
                    if (input && !$(input).hasClass('hasDatepicker')) {
                        $(input).datepicker({
                            language: 'en-GB',
                            autoHide: true,
                            format: 'yyyy-mm-dd',
                            startDate: '{{ date('Y-m-d') }}'
                        }).on('change', function() {
                            fv.revalidateField(this.name);
                        });
                    }
                });
            @endforeach

            const removeRow = function(button) {
                const row = button.closest('tr');
                const index = row.dataset.rowIndex;

                fv.removeField('recommendation[day_number][' + index + ']')
                    .removeField('recommendation[activity_date][' + index + ']')
                    .removeField('recommendation[completed_tasks][' + index + ']')
                    .removeField('recommendation[remarks][' + index + ']');

                row.remove();
            };

            document.querySelectorAll('.js-remove-button').forEach(btn => {
                btn.addEventListener('click', function() {
                    removeRow(this);
                });
            });

            // Add new row
            document.getElementById('addButton').addEventListener('click', function() {
                const clone = template.cloneNode(true);
                clone.removeAttribute('id');
                clone.style.display = 'table-row';
                clone.dataset.rowIndex = rowIndex;

                clone.querySelector('[data-name="recommendation.day_number"]').name =
                    'recommendation[day_number][' + rowIndex + ']';
                clone.querySelector('[data-name="recommendation.activity_date"]').name =
                    'recommendation[activity_date][' + rowIndex + ']';
                clone.querySelector('[data-name="recommendation.completed_tasks"]').name =
                    'recommendation[completed_tasks][' + rowIndex + ']';
                clone.querySelector('[data-name="recommendation.remarks"]').name =
                    'recommendation[remarks][' + rowIndex + ']';

                const dateInput = clone.querySelector('input[name="recommendation[activity_date][' +
                    rowIndex + ']"]');
                $(dateInput).datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: '{{ date('Y-m-d') }}'
                }).on('change', function() {
                    fv.revalidateField(this.name);
                });

                fv.addField('recommendation[day_number][' + rowIndex + ']', subjectValidators);
                fv.addField('recommendation[activity_date][' + rowIndex + ']', dateValidators);
                fv.addField('recommendation[completed_tasks][' + rowIndex + ']', taskValidators);
                fv.addField('recommendation[remarks][' + rowIndex + ']', remarkValidators);

                clone.querySelector('.js-remove-button').addEventListener('click', function() {
                    removeRow(this);
                });

                template.before(clone);
                rowIndex++;
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
                                                <thead>
                                                    <tr>
                                                        <th colspan="5">Daily Carried Activities / Completed Tasks</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Day</th>
                                                        <th>Date</th>
                                                        <th>Carried Activities / Completed Tasks</th>
                                                        <th style="width: 40%">Remarks</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($travelReport->travelReportRecommendations as $index => $rec)
                                                        <tr data-row-index="{{ $index }}">
                                                            <td>
                                                                <textarea name="recommendation[day_number][{{ $index }}]" rows="5" class="form-control">{{ old("recommendation.day_number.$index", $rec->day_number) }}</textarea>
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    name="recommendation[activity_date][{{ $index }}]"
                                                                    data-name="recommendation.activity_date"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="yyyy-mm-dd" onfocus="this.blur()"
                                                                    value="{{ old("recommendation.activity_date.$index", $rec->activity_date?->format('Y-m-d')) }}">
                                                            </td>
                                                            <td>
                                                                <textarea name="recommendation[completed_tasks][{{ $index }}]" rows="5" class="form-control">{{ old("recommendation.completed_tasks.$index", $rec->completed_tasks) }}</textarea>
                                                            </td>
                                                            <td>
                                                                <textarea name="recommendation[remarks][{{ $index }}]" rows="5" class="form-control">{{ old("recommendation.remarks.$index", $rec->remarks) }}</textarea>
                                                            </td>
                                                            <td>
                                                                @if ($loop->first)
                                                                    <button type="button" id="addButton"
                                                                        class="btn btn-primary btn-block">+</button>
                                                                @else
                                                                    <button type="button"
                                                                        class="btn btn-danger btn-block js-remove-button"
                                                                        data-row-index="{{ $index }}">−</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr data-row-index="0">
                                                            <td>
                                                                <textarea name="recommendation[day_number][0]" rows="5" class="form-control"></textarea>
                                                            </td>
                                                            <td><input type="text"
                                                                    name="recommendation[activity_date][0]"
                                                                    data-name="recommendation.activity_date"
                                                                    class="form-control form-control-sm"
                                                                    placeholder="yyyy-mm-dd" onfocus="this.blur()"></td>
                                                            <td>
                                                                <textarea name="recommendation[completed_tasks][0]" rows="5" class="form-control"></textarea>
                                                            </td>
                                                            <td>
                                                                <textarea name="recommendation[remarks][0]" rows="5" class="form-control"></textarea>
                                                            </td>
                                                            <td><button type="button" id="addButton"
                                                                    class="btn btn-primary btn-block">+</button></td>
                                                        </tr>
                                                    @endforelse

                                                    <tr id="template" style="display: none">
                                                        <td>
                                                            <textarea data-name="recommendation.day_number" rows="5" class="form-control"></textarea>
                                                        </td>
                                                        <td><input type="text" data-name="recommendation.activity_date"
                                                                class="form-control form-control-sm"
                                                                placeholder="yyyy-mm-dd" onfocus="this.blur()"></td>
                                                        <td>
                                                            <textarea data-name="recommendation.completed_tasks" rows="5" class="form-control"></textarea>
                                                        </td>
                                                        <td>
                                                            <textarea data-name="recommendation.remarks" rows="5" class="form-control"></textarea>
                                                        </td>
                                                        <td><button type="button"
                                                                class="btn btn-danger btn-block js-remove-button">−</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3 d-flex align-items-start h-100">
                                            <label class="form-label required-label">Not Completed Activities &
                                                Reasons</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="not_completed_activities" class="form-control" rows="8">{{ old('not_completed_activities', $travelReport->not_completed_activities) }}</textarea>
                                            @error('not_completed_activities')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3 d-flex align-items-start h-100">
                                            <label class="form-label required-label">Conclusion & Recommendations</label>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea name="conclusion_recommendations" class="form-control" rows="8">{{ old('conclusion_recommendations', $travelReport->conclusion_recommendations) }}</textarea>
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
                                                    Send to
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedApproverId = old('approver_id') ?: $travelReport->approver_id; @endphp
                                            <select name="approver_id"
                                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                                data-width="100%">
                                                <option value="">Select an Approver</option>
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
