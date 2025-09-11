@extends('layouts.container')

@section('title', 'Travel Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#travel-report-menu').addClass('active');

        });
        document.addEventListener('DOMContentLoaded', function (e) {
            const objectivesValidators = {
                validators: {
                    notEmpty: {
                        message: 'The objectives is required',
                    },
                },
            };
            const observationValidators = {
                validators: {
                    notEmpty: {
                        message: 'The observation is required',
                    },
                },
            };
            const activitiesValidators = {
                validators: {
                    notEmpty: {
                        message: 'The activities is required',
                    },
                },
            };
            const subjectValidators = {
                validators: {
                    // notEmpty: {
                    //     message: 'The recommendation subject is required',
                    // },
                },
            };
            const recommendationDateValidators = {
                validators: {
                    // notEmpty: {
                    //     message: 'The recommendation date is required',
                    // },
                    // date: {
                    //     format: 'YYYY-MM-DD',
                    //     message: 'The recommendation date is not valid',
                    // },
                },
            };
            const responsibleValidators = {
                validators: {
                    // notEmpty: {
                    //     message: 'The responsible person is required',
                    // },
                },
            };
            const remarksValidators = {
                validators: {
                    // notEmpty: {
                    //     message: 'The recommendation remarks is required',
                    // },
                },
            };

            let rowIndex = {{ $travelReportRecommendations->count() - 1 }};
            const form = document.getElementById('travelReportAddForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    'objectives': objectivesValidators,
                    'observation': observationValidators,
                    'activities': activitiesValidators,
                    'recommendation[recommendation_subject][0]': subjectValidators,
                    'recommendation[recommendation_date][0]': recommendationDateValidators,
                    'recommendation[recommendation_responsible][0]': responsibleValidators,
                    'recommendation[recommendation_remarks][0]': remarksValidators,
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
            }).on('core.field.added', function (e) {
                if (e.field === 'recommendation[recommendation_date][' + rowIndex + ']') {
                    // The added field is recommendation date
                    {{--$(form.querySelector('[name= "' + e.field + '"]')).datepicker({--}}
                    {{--    language: 'en-GB',--}}
                    {{--    autoHide: true,--}}
                    {{--    format: 'yyyy-mm-dd',--}}
                    {{--    startDate: '{!! date('Y-m-d') !!}',--}}
                    {{--}).on('change', function(e) {--}}
                    {{--    fv.revalidateField("'" + e.field + "'");--}}
                    {{--});--}}
                }
            });

            // Attach datepicker to the first existing due date
            // datePicker('recommendation[0].recommendation_date');
            {{--$(form.querySelector('[name= "recommendation[recommendation_date][0]"')).datepicker({--}}
            {{--    language: 'en-GB',--}}
            {{--    autoHide: true,--}}
            {{--    format: 'yyyy-mm-dd',--}}
            {{--    startDate: '{!! date('Y-m-d') !!}',--}}
            {{--}).on('change', function(e) {--}}
            {{--    fv.revalidateField('recommendation[recommendation_date][0]');--}}
            {{--});--}}

            const removeRow = function (rowIndex) {
                const row = form.querySelector('[data-row-index="' + rowIndex + '"]');

                // Remove field
                fv.removeField('recommendation[recommendation_subject][' + rowIndex + ']')
                    .removeField('recommendation[recommendation_date][' + rowIndex + ']')
                    .removeField('recommendation[recommendation_responsible][' + rowIndex + ']')
                    .removeField('recommendation[recommendation_remarks][' + rowIndex + ']');

                // Remove row
                row.parentNode.removeChild(row);
            };

            const template = document.getElementById('template');
            $(document).on('click', '.removeButton', function (e) {
                const index = $(this).closest('tr').attr('data-row-index');
                // //
                removeRow(index);
            });
            document.getElementById('addButton').addEventListener('click', function () {
                rowIndex++;

                const clone = template.cloneNode(true);
                clone.removeAttribute('id');

                // Show the row
                clone.style.display = 'block';
                clone.setAttribute('data-row-index', rowIndex);
                clone.removeAttribute('style');

                // Insert before the template
                template.before(clone);

                clone
                    .querySelector('[data-name="recommendation.recommendation_subject"]')
                    .setAttribute('name', 'recommendation[recommendation_subject][' + rowIndex + ']');
                clone
                    .querySelector('[data-name="recommendation.recommendation_date"]')
                    .setAttribute('name', 'recommendation[recommendation_date][' + rowIndex + ']');
                clone
                    .querySelector('[data-name="recommendation.recommendation_responsible"]')
                    .setAttribute('name', 'recommendation[recommendation_responsible][' + rowIndex + ']');
                clone
                    .querySelector('[data-name="recommendation.recommendation_remarks"]')
                    .setAttribute('name', 'recommendation[recommendation_remarks][' + rowIndex + ']');

                // Add new fields
                // Note that we also pass the validator rules for new field as the third parameter
                fv.addField('recommendation[recommendation_subject][' + rowIndex + ']', subjectValidators)
                    .addField('recommendation[recommendation_date][' + rowIndex + ']',
                        recommendationDateValidators)
                    .addField('recommendation[recommendation_responsible][' + rowIndex + ']',
                        responsibleValidators)
                    .addField('recommendation[recommendation_remarks][' + rowIndex + ']',
                        remarksValidators);

                // Handle the click event of removeButton
                const removeBtn = clone.querySelector('.js-remove-button');
                removeBtn.setAttribute('data-row-index', rowIndex);
                removeBtn.addEventListener('click', function (e) {
                    // Get the row index
                    const index = e.target.getAttribute('data-row-index');
                    removeRow(index);
                });
            });
            @foreach ($travelReport->travelReportRecommendations as $index => $recommendation)
            fv.addField('recommendation[recommendation_subject][' + {{ $index }} + ']',
                subjectValidators)
                .addField('recommendation[recommendation_date][' + {{ $index }} + ']',
                    recommendationDateValidators)
                .addField('recommendation[recommendation_responsible][' + {{ $index }} + ']',
                    responsibleValidators)
                .addField('recommendation[recommendation_remarks][' + {{ $index }} + ']',
                    remarksValidators);
            @endforeach
        });
    </script>
@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="container-fluid">

            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('travel.reports.index') }}" class="text-decoration-none">Travel
                                        Reports
                                    </a>
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

                        @if ($travelReport->returnLog()->exists())
                            <div class="card">
                                <div class="card-header fw-bold text-danger">
                                    Return Remarks
                                </div>
                                <div class="card-body">
                                    <ul class="mb-0 list-unstyled list-py-2 text-dark">

                                        <li class="position-relative">
                                            <div class="gap-2 d-flex align-items-start">
                                                <div class="icon-section"><i
                                                        class="bi-chat-dots dropdown-item-icon"></i>
                                                </div>
                                                <div
                                                    class="d-content-section"> {{ $travelReport->returnLog->log_remarks }}
                                                </div>
                                            </div>
                                            <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-9">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Update Travel Report
                            </div>
                            <form action="{{ route('travel.reports.update', $travelReport->id) }}"
                                  id="travelReportAddForm"
                                  method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="" class="form-label required-label">
                                                            Overview of specific objectives and expected outputs
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea name="objectives" class="form-control"
                                                              rows="8">{{ $travelReport->objectives }}</textarea>
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
                                                            Observation
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea name="observation" class="form-control"
                                                              rows="8"
                                                              placeholder="">{{ $travelReport->observation }}</textarea>
                                                    @if ($errors->has('observation'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="observation">
                                                                {!! $errors->first('observation') !!}
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-lg-3">
                                                    <div class="d-flex align-items-start h-100">
                                                        <label for="" class="form-label required-label">
                                                            Activities
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea name="activities" class="form-control"
                                                              rows="8"
                                                              placeholder="">{{ $travelReport->activities }}</textarea>
                                                    @if ($errors->has('activities'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="activities">
                                                                {!! $errors->first('activities') !!}
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
                                                            <th colspan="5">Recommendation For</th>
                                                        </tr>
                                                        <tr>
                                                            <th class="">What</th>
                                                            <th class="">When</th>
                                                            <th class="">Who</th>
                                                            <th class="">Remarks</th>
                                                            <th></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        @forelse($travelReport->travelReportRecommendations as $index=>$recommendation)
                                                            <tr data-row-index="{{ $index }}">
                                                                <td>
                                                                        <textarea
                                                                            name="recommendation[recommendation_subject][{{ $index }}]"
                                                                            rows="10"
                                                                            class="form-control"
                                                                            placeholder="">{!! $recommendation->recommendation_subject !!}</textarea>
                                                                </td>
                                                                <td>
                                                                        <textarea
                                                                            name="recommendation[recommendation_date][{{ $index }}]"
                                                                            rows="10"
                                                                            class="form-control"
                                                                            placeholder="">{!! $recommendation->recommendation_date !!}</textarea>
                                                                </td>
                                                                <td>
                                                                        <textarea
                                                                            name="recommendation[recommendation_responsible][{{ $index }}]"
                                                                            rows="10"
                                                                            class="form-control"
                                                                            placeholder="">{!! $recommendation->recommendation_responsible !!}</textarea>
                                                                </td>
                                                                <td style="width: 40%">
                                                                        <textarea
                                                                            name="recommendation[recommendation_remarks][{{ $index }}]"
                                                                            rows="10"
                                                                            class="form-control"
                                                                            placeholder="">{!! $recommendation->recommendation_remarks !!}</textarea>
                                                                </td>
                                                                <td>
                                                                    @if ($loop->first)
                                                                        <button type="button"
                                                                                class="btn btn-primary btn-block"
                                                                                id="addButton"> +
                                                                        </button>
                                                                    @else
                                                                        <button
                                                                            class="btn btn-danger btn-block removeButton"
                                                                            type="button"> -
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td>
                                                                        <textarea
                                                                            name="recommendation[recommendation_subject][0]"
                                                                            rows="10"
                                                                            class="form-control" placeholder="">
                                                                            @if (old('recommendation[recommendation_subject][0]'))
                                                                                {{ old('recommendation[recommendation_subject][0]') }}
                                                                            @endif
                                                                            </textarea>
                                                                    @if ($errors->has('recommendation[recommendation_subject][0]'))
                                                                        <div
                                                                            class="fv-plugins-message-container invalid-feedback">
                                                                            <div
                                                                                data-field="recommendation[recommendation_subject][0]">
                                                                                {!! $errors->first('recommendation[recommendation_subject][0]') !!}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                        <textarea
                                                                            name="recommendation[recommendation_date][0]"
                                                                            rows="10"
                                                                            class="form-control" placeholder="">
                                                                            @if (old('recommendation[recommendation_date][0]'))
                                                                                {{ old('recommendation[recommendation_date][0]') }}
                                                                            @endif
                                                                            </textarea>
                                                                    @if ($errors->has('recommendation[recommendation_date][0]'))
                                                                        <div
                                                                            class="fv-plugins-message-container invalid-feedback">
                                                                            <div
                                                                                data-field="recommendation[recommendation_date][0]">
                                                                                {!! $errors->first('recommendation[recommendation_date][0]') !!}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                        <textarea
                                                                            name="recommendation[recommendation_responsible][0]"
                                                                            rows="10"
                                                                            class="form-control" placeholder="">
                                                                            @if (old('recommendation[recommendation_responsible][0]'))
                                                                                {{ old('recommendation[recommendation_responsible][0]') }}
                                                                            @endif
                                                                            </textarea>
                                                                    @if ($errors->has('recommendation[recommendation_responsible][0]'))
                                                                        <div
                                                                            class="fv-plugins-message-container invalid-feedback">
                                                                            <div
                                                                                data-field="recommendation[recommendation_responsible][0]">
                                                                                {!! $errors->first('recommendation[recommendation_responsible][0]') !!}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td style="width: 40%">
                                                                        <textarea
                                                                            name="recommendation[recommendation_remarks][0]"
                                                                            rows="10"
                                                                            class="form-control" placeholder="">
                                                                            @if (old('recommendation[recommendation_remarks][0]'))
                                                                                {{ old('recommendation[recommendation_remarks][0]') }}
                                                                            @endif
                                                                            </textarea>
                                                                    @if ($errors->has('recommendation[recommendation_remarks][0]'))
                                                                        <div
                                                                            class="fv-plugins-message-container invalid-feedback">
                                                                            <div
                                                                                data-field="recommendation[recommendation_remarks][0]">
                                                                                {!! $errors->first('recommendation[recommendation_remarks][0]') !!}
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <button type="button"
                                                                            class="btn btn-primary btn-block"
                                                                            id="addButton"> +
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforelse

                                                        <!-- Template -->
                                                        <tr id="template" style="display: none">
                                                            <td>
                                                                    <textarea
                                                                        data-name="recommendation.recommendation_subject"
                                                                        rows="10"
                                                                        class="form-control" placeholder=""></textarea>
                                                            </td>
                                                            <td>
                                                                    <textarea
                                                                        data-name="recommendation.recommendation_date"
                                                                        rows="10"
                                                                        class="form-control" placeholder=""></textarea>
                                                            </td>
                                                            <td>
                                                                    <textarea
                                                                        data-name="recommendation.recommendation_responsible"
                                                                        rows="10"
                                                                        class="form-control" placeholder=""></textarea>
                                                            </td>
                                                            <td>
                                                                    <textarea
                                                                        data-name="recommendation.recommendation_remarks"
                                                                        rows="10"
                                                                        class="form-control" placeholder=""></textarea>
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
                                                        <label for="" class="m-0">
                                                            Other Comments
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <textarea name="other_comments" class="form-control"
                                                              placeholder="">{{ $travelReport->other_comments }}</textarea>
                                                    @if ($errors->has('other_comments'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div data-field="other_comments">
                                                                {!! $errors->first('other_comments') !!}
                                                            </div>
                                                        </div>
                                                    @endif
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
                                                    <select name="approver_id" class="select2 form-control
                                                @if($errors->has('approver_id')) is-invalid @endif" data-width="100%">
                                                        <option value="">Select an Approver</option>
                                                        @foreach($supervisors as $approver)
                                                            <option
                                                                value="{{ $approver->id }}" {{$approver->id == $selectedApproverId ? "selected":""}}>{{ $approver->getFullName() }}</option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('approver_id'))
                                                        <div class="fv-plugins-message-container invalid-feedback">
                                                            <div
                                                                data-field="approver_id">{!! $errors->first('approver_id') !!}</div>
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
                                    <button type="submit" name="btn" value="save"
                                            class="btn btn-primary btn-sm">Update
                                    </button>
                                    @if ($authUser->can('submit', $travelReport))
                                        <button type="submit" name="btn" value="submit"
                                                class="btn btn-success btn-sm">
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

    </div>
    </div>
@stop
