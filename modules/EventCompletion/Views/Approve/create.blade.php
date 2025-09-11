@php $authUser = auth()->user();@endphp
@extends('layouts.container')

@section('title', 'Approve Event Completion Report')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#approve-event-completion-menu').addClass('active');

            var oTable = $('#participantsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('event.completion.participants.index', $eventCompletion->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'designation',
                        name: 'designation'
                    },
                    {
                        data: 'contact',
                        name: 'contact'
                    },
                ]
            });
        });

        document.addEventListener('DOMContentLoaded', function(e) {
            //
            const form = document.getElementById('eventCompletionApproveForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    status_id: {
                        validators: {
                            notEmpty: {
                                message: 'Status is required',
                            },
                        },
                    },
                    recommended_to: {
                        validators: {
                            notEmpty: {
                                message: 'Recommended to is required.',
                            },
                        },
                    },
                    log_remarks: {
                        validators: {
                            notEmpty: {
                                message: 'The remarks is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    excluded: new FormValidation.plugins.Excluded({
                        excluded: function(field, ele, eles) {
                            const statusId = parseInt(form.querySelector('[name="status_id"]')
                                .value);
                            return (field === 'recommended_to' && statusId !== 4) || (field ===
                                'status_id' && statusId === 4);
                        },
                    }),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $(form).on('change', '[name="status_id"]', function(e) {
                fv.revalidateField('status_id');
                if (this.value == 4) {
                    $(form).find('#recommendBlock').show();
                } else {
                    $(form).find('#recommendBlock').hide();
                }
            }).on('change', '[name="recommended_to"]', function(e) {
                fv.revalidateField('recommended_to');
            });

            $('#background-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-background').toggle();
                $('#full-background').toggle();
            });

            $('#objectives-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-objectives').toggle();
                $('#full-objectives').toggle();
            });

            $('#process-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-process').toggle();
                $('#full-process').toggle();
            });

            $('#closing-section').on('click', function(e) {
                e.preventDefault();
                $('#initial-closing').toggle();
                $('#full-closing').toggle();
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
                            <a href="{{ route('approve.event.completion.index') }}"
                                class="text-decoration-none text-dark">Event Completion Reports</a>
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
                        Event Completion Report Details
                    </div>
                    @include('EventCompletion::Partials.detail')
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">
                        Event Participants
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="participantsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">{{ __('label.name') }}</th>
                                        <th scope="col">{{ __('label.office') }}</th>
                                        <th scope="col">{{ __('label.designation') }}</th>
                                        <th scope="col">{{ __('label.contact') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>



                @include('Attachment::list', [
                    'modelType' => 'Modules\EventCompletion\Models\EventCompletion',
                    'modelId' => $eventCompletion->id,
                ])

                <div class="card">
                    <div class="card-header fw-bold">
                        Event Completion Report Process
                    </div>
                    <form action="{{ route('approve.event.completion.store', $eventCompletion->id) }}"
                        id="eventCompletionApproveForm" method="post" enctype="multipart/form-data" autocomplete="off">
                        <div class="card-body">
                            <div class="c-b">
                                @foreach ($eventCompletion->logs as $log)
                                    <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                        <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                            <i class="bi-person-circle fs-5"></i>
                                        </div>
                                        <div class="w-100">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                                <div
                                                    class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                                    <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                                    <span class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                                </div>
                                                <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                            </div>
                                            <p class="text-justify comment-text mb-0 mt-1">
                                                {{ $log->log_remarks }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border-top pt-4">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Status
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="status_id" class="select2 form-control" data-width="100%">
                                            <option value="">Select a Status</option>
                                            <option value="2">Return to Requester</option>
                                            <option value="8">Reject</option>
                                            @if ($eventCompletion->status_id != 4)
                                                <option value="4">Recommend</option>
                                            @endif
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

                                <div class="row mb-2" id="recommendBlock" style="display: none;">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationleavetype" class="form-label required-label">Recommended
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select name="recommended_to" class="select2 form-control" data-width="100%">
                                            <option>Select Recommended To</option>
                                            @foreach ($approvers as $approver)
                                                <option value="{{ $approver->id }}">
                                                    {{ $approver->getFullName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('approver_id'))
                                            <div class="fv-plugins-message-container invalid-feedback">
                                                <div data-field="approver_id">
                                                    {!! $errors->first('approver_id') !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationRemarks" class="form-label required-label">Remarks
                                            </label>
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
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{!! route('approve.event.completion.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@stop
