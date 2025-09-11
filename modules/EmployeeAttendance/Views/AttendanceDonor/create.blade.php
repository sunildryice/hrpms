<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Update Worklog: {{ $donor->description }} |
        {{ $attendanceDate }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="g-3 needs-validation" action="{{ route('attendance.detail.store') }}" id="donorForm" method="post"
    enctype="multipart/form-data" autocomplete="off" novalidate>
    <div class="card-body">
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationfullname" class="form-label required-label">{{ __('label.date') }}</label>
                </div>
            </div>
            <div class="col-lg-3">
                <input {{-- data-toggle="datepicker" --}} type="text" name="attendanceDate" value="{{ $attendanceDate }}"
                    id="" class="form-control" readonly>
                <input type="hidden" name="donorId" value="{{ $donor->id }}" id="" class="form-control"
                    readonly>
                <input type="hidden" name="attendanceId" value="{{ $attendance->id }}" id=""
                    class="form-control" readonly>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-start h-100">
                            <label for="validationfullname"
                                class="form-label required-label">{{ __('label.worked-hours') }}</label>
                        </div>
                    </div>
                    @php
                        $chHr =
                            $attendanceDetailDonor?->getWorkedHours() ?:
                            ($isUnrestricted
                                ? $attendanceDetail?->unrestricted_hours ?? 0
                                : 0);
                        $chHrArray = explode('.', $chHr);
                        $hr = $chHrArray[0];
                        if (count($chHrArray) > 1) {
                            $min = $chHrArray[1];
                        } else {
                            $min = 0;
                        }
                        $min = str_pad($min, 2, '0', STR_PAD_RIGHT);
                    @endphp
                    <div class="col-lg-6">
                        <input type="number" name="chargedHours" value="{{ $chHr }}"
                            @if ($isUnrestricted) readonly @endif class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                    </div>
                    <div class="col-lg-6">
                        <small>
                            <span id="hours">{{ $hr }}</span> Hr
                            <span id="minutes">{{ $min }}</span> Minutes
                        </small>
                    </div>
                </div>

            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.activity-desc') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="activities">{{ $attendanceDetailDonor?->activities }}</textarea>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="Activityarea" class="m-0 required-label">{{ __('label.project') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select id="Activityarea" class="form-control select2 required-label" placeholder="Select a Project"
                    name="project_id" autocomplete="off">
                    <option value="">Select a Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ $project->id == $attendanceDetailDonor?->project_id ? 'selected' : '' }}>
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    {!! csrf_field() !!}
    <div class="gap-2 border-0 card-footer justify-content-end d-flex">
        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm next">Save</button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    </div>
</form>
