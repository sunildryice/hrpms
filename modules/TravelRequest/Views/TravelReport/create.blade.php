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

            $('input[name="recommendation[activity_date][0]"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{{ date('Y-m-d') }}'
            }).on('change', function() {
                fv.revalidateField(this.name);
            });

            const removeRow = function(rowIndex) {
                const row = form.querySelector('[data-row-index="' + rowIndex + '"]');
                if (!row) return;

                fv.removeField('recommendation[day_number][' + rowIndex + ']')
                    .removeField('recommendation[activity_date][' + rowIndex + ']')
                    .removeField('recommendation[completed_tasks][' + rowIndex + ']')
                    .removeField('recommendation[remarks][' + rowIndex + ']');

                row.parentNode.removeChild(row);
            };

            const template = document.getElementById('template');

            document.getElementById('addButton').addEventListener('click', function() {
                rowIndex++;

                const clone = template.cloneNode(true);
                clone.removeAttribute('id');
                clone.style.display = 'table-row';
                clone.setAttribute('data-row-index', rowIndex);

                clone.querySelector('[data-name="recommendation.day_number"]').setAttribute('name',
                    'recommendation[day_number][' + rowIndex + ']');
                clone.querySelector('[data-name="recommendation.activity_date"]').setAttribute('name',
                    'recommendation[activity_date][' + rowIndex + ']');
                clone.querySelector('[data-name="recommendation.completed_tasks"]').setAttribute('name',
                    'recommendation[completed_tasks][' + rowIndex + ']');
                clone.querySelector('[data-name="recommendation.remarks"]').setAttribute('name',
                    'recommendation[remarks][' + rowIndex + ']');

                const dateInput = clone.querySelector('[data-name="recommendation.activity_date"]');
                $(dateInput).datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    startDate: '{{ date('Y-m-d') }}'
                }).on('change', function() {
                    fv.revalidateField(this.name);
                });

                // Add validation
                fv.addField('recommendation[day_number][' + rowIndex + ']', subjectValidators);
                fv.addField('recommendation[activity_date][' + rowIndex + ']', dateValidators);
                fv.addField('recommendation[completed_tasks][' + rowIndex + ']', taskValidators);
                fv.addField('recommendation[remarks][' + rowIndex + ']', remarkValidators);

                const currentRowIndex = rowIndex;

                const removeBtn = clone.querySelector('.js-remove-button');
                removeBtn.setAttribute('data-row-index', currentRowIndex);
                removeBtn.addEventListener('click', function() {
                    removeRow(currentRowIndex);
                });

                template.before(clone);
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
                                                        <th colspan="5">Daily Carried Activities / Completed Tasks</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="">Day</th>
                                                        <th class="">Date</th>
                                                        <th class="">Carried Activities / Completed Tasks</th>
                                                        <th class="">Remarks</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="recommendation[day_number][0]"
                                                                class="form-control" rows="1"
                                                                @if (old('recommendation[day_number][0]')) {{ old('recommendation[day_number][0]') }} @endif>
                                                                
                                                            @if ($errors->has('recommendation[day_number][0]'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="recommendation[day_number][0]">
                                                                        {!! $errors->first('recommendation[day_number][0]') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input type="text" name="recommendation[activity_date][0]"
                                                                data-name="recommendation.activity_date"
                                                                class="form-control form-control-sm"
                                                                placeholder="yyyy-mm-dd" onfocus="this.blur()"
                                                                @if (old('recommendation[activity_date][0]')) {{ old('recommendation[activity_date][0]') }} @endif>

                                                            @if ($errors->has('recommendation[activity_date][0]'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="recommendation[activity_date][0]">
                                                                        {!! $errors->first('recommendation[activity_date][0]') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <textarea name="recommendation[completed_tasks][0]" rows="3" class="form-control" placeholder="">
                                                                @if (old('recommendation[completed_tasks][0]'))
                                                                    {{ old('recommendation[completed_tasks][0]') }}
                                                                @endif
                                                            </textarea>
                                                            @if ($errors->has('recommendation[completed_tasks][0]'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="recommendation[completed_tasks][0]">
                                                                        {!! $errors->first('recommendation[completed_tasks][0]') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td style="width: 40%">
                                                            <textarea name="recommendation[remarks][0]" rows="3" class="form-control" placeholder="">
                                                                @if (old('recommendation[remarks][0]'))
                                                                    {{ old('recommendation[remarks][0]') }}
                                                                @endif
                                                            </textarea>
                                                            @if ($errors->has('recommendation[remarks][0]'))
                                                                <div class="fv-plugins-message-container invalid-feedback">
                                                                    <div data-field="recommendation[remarks][0]">
                                                                        {!! $errors->first('recommendation[remarks][0]') !!}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-primary btn-block"
                                                                id="addButton"> +
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Template -->
                                                    <tr id="template" style="display: none">
                                                        <td>
                                                            <input type="text" data-name="recommendation.day_number"
                                                                class="form-control" rows="1">
                                                        </td>
                                                        <td>
                                                            <input type="text" data-name="recommendation.activity_date"
                                                                class="form-control form-control-sm"
                                                                placeholder="yyyy-mm-dd" onfocus="this.blur()">
                                                        </td>
                                                        <td>
                                                            <textarea data-name="recommendation.completed_tasks" rows="3" class="form-control" placeholder=""></textarea>
                                                        </td>
                                                        <td>
                                                            <textarea data-name="recommendation.remarks" rows="3" class="form-control" placeholder=""></textarea>
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-block js-remove-button"
                                                                id="removeButton"> -
                                                            </button>
                                                        </td>
                                                    </tr>
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
