@extends('layouts.container')

@section('title', 'Monthly Timesheet Detail')

@php
    $today = \Carbon\Carbon::today()->format('Y-m-d');
    $canAddToday =
        isset($allDates[$today]) &&
        !$allDates[$today]['carbon']->isFuture() &&
        $timeSheet->status_id == config('constant.CREATED_STATUS');
@endphp

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#monthly-timesheets-index').addClass('active');
            $('.select2').select2({
                placeholder: "Select Status",
                width: '100%'
            });
        });

        $(function() {

            function initCascade($row) {
                $row.find('.project-select').on('change', function() {
                    const $act = $row.find('.activity-select');
                    $act.empty().append('<option value="">Select Activity</option>');
                    const acts = $(this).find(':selected').data('activities') || [];
                    acts.forEach(a => {
                        $act.append(`<option value="${a.id}">${a.title}</option>`);
                    });
                });
            }

            // automatically open entry for today
            const todayDate = '{{ $today ?? '' }}';
            const canAddToday = {{ $canAddToday ? 'true' : 'false' }};
            if (todayDate && canAddToday) {
                const $todayBtn = $(`.add-entry-btn[data-date="${todayDate}"]`);
                if ($todayBtn.length && !$todayBtn.is(':disabled')) {
                    $todayBtn.click();
                }
            }

            // Add new row 
            $(document).on('click', '.add-entry-btn', function() {
                const dateYmd = $(this).data('date');

                $(`.add-entry-btn[data-date="${dateYmd}"]`).prop('disabled', true).addClass('disabled');

                if ($(`tr.new-entry-row[data-date="${dateYmd}"]`).length > 0) {
                    return;
                }

                const $new = $($('#new-entry-template').html());
                $new.attr('data-date', dateYmd);
                $new.find('.date-display').text(moment(dateYmd, 'YYYY-MM-DD').format('DD, MMM YYYY'));

                initCascade($new);

                let $targetRow = $(`tr:has(td:contains("No timesheet entries"))`)
                    .filter(function() {
                        return $(this).find('td:first').text().trim().includes(
                            moment(dateYmd, 'YYYY-MM-DD').format('DD, MMM YYYY')
                        );
                    });

                if (!$targetRow.length) {
                    $targetRow = $(`tr[data-date-group="${dateYmd}"]:last`);
                }

                if (!$targetRow.length) {
                    $targetRow = $(`tr.add-row-indicator[data-date="${dateYmd}"]`);
                }

                if ($targetRow.length) {
                    if ($targetRow.find('td:contains("No timesheet entries")').length) {
                        $new.data('replacedRowHtml', $targetRow.prop('outerHTML'));
                        $targetRow.replaceWith($new);
                    } else {
                        $targetRow.after($new);
                    }
                } else {
                    $('#MonthlyTimeSheetTable tbody').append($new);
                }

                $new[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            });

            // Cancel new row 
            $(document).on('click', '.cancel-new', function() {
                const $tr = $(this).closest('tr');
                const dateYmd = $tr.data('date');
                const replaced = $tr.data('replacedRowHtml');
                if (replaced) {
                    $tr.replaceWith(replaced);
                } else {
                    $tr.remove();
                }
                $(`.add-entry-btn[data-date="${dateYmd}"]`).prop('disabled', false).removeClass('disabled');
            });

            // Save 
            $(document).on('click', '.save-new', function() {
                const $row = $(this).closest('tr');
                const $proj = $row.find('.project-select');
                const $act = $row.find('.activity-select');
                const $hrs = $row.find('input[type="number"]');
                const projectId = $proj.val();
                const activityId = $act.val();
                const hours = $hrs.val();

                // clear previous error styles
                $proj.removeClass('is-invalid');
                $act.removeClass('is-invalid');
                $hrs.removeClass('is-invalid');

                if (!projectId) {
                    toastr.warning('Please select a project');
                    $proj.addClass('is-invalid').focus();
                    return;
                }
                if (!activityId) {
                    toastr.warning('Please select an activity');
                    $act.addClass('is-invalid').focus();
                    return;
                }
                if (!hours || Number(hours) <= 0) {
                    toastr.warning('Hours must be greater than 0');
                    $hrs.addClass('is-invalid').focus();
                    return;
                }

                const payload = {
                    _token: '{{ csrf_token() }}',
                    timesheet_date: $row.data('date'),
                    project_id: projectId,
                    activity_id: activityId,
                    description: $row.find('input[type="text"]').val().trim(),
                    hours_spent: hours
                };

                $.post('{{ route('monthly-timesheet.inline.store', $timeSheet->id) }}', payload)
                    .done(() => {
                        toastr.success("Entry added");
                        location.reload();
                    })
                    .fail(xhr => {
                        toastr.error(xhr.responseJSON?.error || "Failed to save");
                    });
            });


            // OPEN MODAL 
            $(document).on('click', '.open-edit-modal', function() {
                const id = $(this).data('id');
                const projectId = $(this).data('project');
                const activityId = $(this).data('activity');
                const description = $(this).data('description') || '';
                const hours = $(this).data('hours') || 0;
                const date = $(this).data('date') || '';

                $('#edit-date').val(date);

                $('#edit-project, #edit-activity').select2({
                    placeholder: "Select...",
                    width: '100%',
                    dropdownParent: $(
                        '#editEntryModal')
                });

                $('#edit-entry-id').val(id);
                $('#edit-description').val(description);
                $('#edit-hours').val(hours);

                $('#edit-project').val(projectId).trigger('change');

                // Populate activities based on selected project
                const acts = $('#edit-project option:selected').data('activities') || [];
                let activityHtml = '<option value="">Select Activity</option>';
                acts.forEach(a => {
                    const selected = (a.id == activityId) ? 'selected' : '';
                    activityHtml += `<option value="${a.id}" ${selected}>${a.title}</option>`;
                });
                $('#edit-activity').html(activityHtml);

                $('#editEntryModal').modal('show');
            });

            // Cascade project → activity inside modal
            $('#edit-project').on('change', function() {
                const acts = $(this).find(':selected').data('activities') || [];
                let html = '<option value="">Select Activity</option>';
                acts.forEach(a => {
                    html += `<option value="${a.id}">${a.title}</option>`;
                });
                $('#edit-activity').html(html);
            });

            // UPDATE 
            $('#update-entry-btn').on('click', function() {
                const id = $('#edit-entry-id').val();
                const payload = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    project_id: $('#edit-project').val(),
                    activity_id: $('#edit-activity').val(),
                    description: $('#edit-description').val().trim(),
                    hours_spent: $('#edit-hours').val()
                };

                if (!payload.project_id || !payload.activity_id) {
                    toastr.warning('Project and Activity are required');
                    return;
                }

                if (!payload.hours_spent || Number(payload.hours_spent) <= 0) {
                    toastr.warning('Hours must be greater than 0');
                    return;
                }

                $.ajax({
                    url: '{{ route('monthly-timesheet.inline.update', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: payload,
                    success: function() {
                        toastr.success("Entry updated successfully");
                        location.reload(); 
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.error || "Update failed");
                    }
                });
            });

            // Cancel edit → reload
            $(document).on('click', '.cancel-entry', function() {
                location.reload();
            });

            // Save edited entry – include project/activity if changed
            $(document).on('click', '.save-entry', function() {
                const $tr = $(this).closest('tr');
                const id = $(this).data('id');

                const $proj = $tr.find('.project-select-edit');
                const $act = $tr.find('.activity-select-edit');
                const $hrs = $tr.find('.hours-cell input');

                $proj.removeClass('is-invalid');
                $act.removeClass('is-invalid');
                $hrs.removeClass('is-invalid');

                const payload = {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    description: $tr.find('.description-cell input').val()?.trim() || '',
                    hours_spent: $hrs.val() || '0'
                };

                if ($proj.length) payload.project_id = $proj.val();
                if ($act.length) payload.activity_id = $act.val();

                if (!payload.hours_spent || Number(payload.hours_spent) <= 0) {
                    toastr.warning("Hours must be greater than 0");
                    $hrs.addClass('is-invalid').focus();
                    return;
                }

                if (($proj.length || $act.length) && (!payload.project_id || !payload.activity_id)) {
                    toastr.warning("Project and Activity are required");
                    if (!payload.project_id) $proj.addClass('is-invalid').focus();
                    if (!payload.activity_id) $act.addClass('is-invalid');
                    return;
                }

                $.ajax({
                    url: '{{ route('monthly-timesheet.inline.update', ':id') }}'.replace(':id', id),
                    method: 'POST',
                    data: payload,
                    success: function() {
                        toastr.success("Entry updated");
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.error || "Update failed");
                    }
                });
            });

            // Delete 
            $(document).on('click', '.delete-entry', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: '{{ route('monthly-timesheet.inline.destroy', ':id') }}'
                            .replace(':id',
                                id),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            toastr.success("Entry deleted");
                            location.reload();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON?.error || "Delete failed");
                        }
                    });
                });
            });
        });
    </script>
@endsection

@section('page-content')
    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('monthly-timesheet.index') }}"
                                    class="text-decoration-none text-dark">Monthly Timesheet</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="card border shadow-sm rounded h-100">
                    <div class="card-header">
                        Timesheet Summary of {{ $yearMonthFormatted }} (Status:
                        {{ $timeSheet->status->name ?? $timeSheet->status_id }})
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-3"><b class="text-muted d-block">Projects</b>
                                <h5 class="my-3">{{ $stats['projects'] ?? 0 }}</h5>
                            </div>
                            <div class="col-3"><b class="text-muted d-block">Activities</b>
                                <h5 class="my-3">{{ $stats['activities'] ?? 0 }}</h5>
                            </div>
                            <div class="col-3"><b class="text-muted d-block">Tasks</b>
                                <h5 class="my-3">{{ $stats['tasks'] ?? 0 }}</h5>
                            </div>
                            <div class="col-3"><b class="text-muted d-block">Total Hours</b>
                                <h5 class="my-3">{{ number_format($stats['hours'] ?? 0, 2) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border rounded" id="monthly-timesheet-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="MonthlyTimeSheetTable">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Project</th>
                                <th>Activities</th>
                                <th>Tasks</th>
                                <th>Hours</th>
                                <th style="width: 160px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $today = \Carbon\Carbon::today()->format('Y-m-d');
                                $canAddToday =
                                    isset($allDates[$today]) &&
                                    !$allDates[$today]['carbon']->isFuture() &&
                                    $timeSheet->status_id == config('constant.CREATED_STATUS');
                            @endphp

                            @foreach ($allDates as $dateKey => $dayData)
                                @php
                                    $items = $dayData['items'];
                                    $carbon = $dayData['carbon'];
                                    $dateYmd = $dateKey;

                                    $isToday = $carbon->isToday();
                                    $isFuture = $carbon->isFuture();
                                    $canModify =
                                        !$isFuture && $timeSheet->status_id == config('constant.CREATED_STATUS');

                                    $reasonText = strip_tags($dayData['reason'] ?? '');
                                    $isAbsence =
                                        str_contains($reasonText, 'Weekend') ||
                                        str_contains($reasonText, 'On Leave') ||
                                        str_contains($reasonText, 'On Lieu Leave') ||
                                        str_contains($reasonText, 'On Travel');

                                    $canAdd = $canModify && !$isAbsence;
                                @endphp

                                @if ($items->isEmpty())
                                    <tr class="date-group-start {{ $loop->first ? '' : 'border-top border-2' }}"
                                        data-date-group="{{ $dateYmd }}">
                                        <td class="align-middle">{{ $carbon->format('d, M Y') }}</td>
                                        <td colspan="4" class="text-center text-muted fw-bold py-3">
                                            {!! $dayData['reason'] ?? 'No timesheet entries' !!}
                                        </td>
                                        <td class="text-center">
                                            @if ($canAdd)
                                                <button type="button" class="btn btn-sm btn-outline-success add-entry-btn"
                                                    data-date="{{ $dateYmd }}" title="Add">
                                                    <i class="bi bi-plus-lg"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        $datePrinted = false;
                                        $projectGroups = $items->groupBy(fn($ts) => $ts->project_id ?? 0);
                                    @endphp

                                    @foreach ($projectGroups as $projId => $projItems)
                                        @php $projPrinted = false; @endphp
                                        @foreach ($projItems->groupBy(fn($ts) => $ts->activity_id ?? 0) as $actId => $actItems)
                                            @php $actPrinted = false; @endphp

                                            @foreach ($actItems as $item)
                                                <tr class="{{ !$datePrinted ? 'date-group-start border-top border-2' : '' }}"
                                                    data-date-group="{{ $dateYmd }}"
                                                    data-project-id="{{ $item->project_id ?? '' }}"
                                                    data-activity-id="{{ $item->activity_id ?? '' }}">
                                                    @if (!$datePrinted)
                                                        <td rowspan="{{ $items->count() }}"
                                                            class="align-middle text-center">
                                                            {{ $carbon->format('d, M Y') }}
                                                        </td>
                                                        @php $datePrinted = true; @endphp
                                                    @endif

                                                    @if (!$projPrinted)
                                                        <td rowspan="{{ $projItems->count() }}"
                                                            class="project-cell align-middle"
                                                            data-project-id="{{ $item->project_id ?? '' }}">
                                                            {{ optional($item->project)->title ?? (optional($item->project)->short_name ?? '—') }}
                                                        </td>
                                                        @php $projPrinted = true; @endphp
                                                    @endif

                                                    @if (!$actPrinted)
                                                        <td rowspan="{{ $actItems->count() }}"
                                                            class="activity-cell align-middle"
                                                            data-activity-id="{{ $item->activity_id ?? '' }}">
                                                            {{ optional($item->activity)->title ?? '—' }}
                                                        </td>
                                                        @php $actPrinted = true; @endphp
                                                    @endif

                                                    <td class="description-cell">{{ $item->description ?: '—' }}</td>
                                                    <td class="hours-cell text-end">
                                                        {{ number_format($item->hours_spent, 2) }}</td>

                                                    <td class="text-center">
                                                        @if ($canModify && $item->created_by == auth()->id())
                                                            <div class="btn-group btn-group-sm normal-actions gap-2">
                                                                <button class="btn btn-outline-primary open-edit-modal"
                                                                    data-id="{{ $item->id }}"
                                                                    data-date="{{ $item->timesheet_date?->format('Y-m-d') }}"
                                                                    data-project="{{ $item->project_id }}"
                                                                    data-activity="{{ $item->activity_id }}"
                                                                    data-description="{{ $item->description }}"
                                                                    data-hours="{{ $item->hours_spent }}" title="Edit">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </button>
                                                                <button class="btn btn-outline-danger delete-entry"
                                                                    data-id="{{ $item->id }}" title="Delete">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>

                                                            <div class="btn-group btn-group-sm edit-actions d-none gap-2">
                                                                <button class="btn btn-success save-entry"
                                                                    data-id="{{ $item->id }}" title="Save">
                                                                    <i class="bi bi-check-lg"></i>
                                                                </button>
                                                                <button class="btn btn-secondary cancel-entry"
                                                                    title="Cancel">
                                                                    <i class="bi bi-x-lg"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                        @if ($canAdd && $loop->parent->parent->last)
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-success add-entry-btn"
                                                                data-date="{{ $dateYmd }}" title="Add another timesheet">
                                                                <i class="bi bi-plus-lg"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editEntryModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white mb-3">
                                    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit TimeSheet</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" id="edit-entry-id">

                                    <div class="row g-3">

                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label class="form-label m-0">Date</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <input type="text" id="edit-date" class="form-control" readonly>
                                            </div>
                                        </div>


                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label class="form-label required-label m-0">Project</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select id="edit-project" class="form-control select2">
                                                    <option value="">Select Project</option>
                                                    @foreach ($projects as $p)
                                                        <option value="{{ $p->id }}"
                                                            data-activities='@json($p->activities->map(fn($a) => ['id' => $a->id, 'title' => $a->title]))'>
                                                            {{ $p->title ?: $p->short_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label class="form-label required-label m-0">Activity / Sub
                                                        Activity</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <select id="edit-activity" class="form-control select2">
                                                    <option value="">Select Activity</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label class="form-label required-label m-0">Hours Spent</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <input type="number" step="0.01" min="0.01" max="24"
                                                    id="edit-hours" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-lg-3">
                                                <div class="d-flex align-items-start h-100">
                                                    <label class="form-label m-0">Description</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-9">
                                                <textarea id="edit-description" class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-primary" id="update-entry-btn">Update</button>
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <template id="new-entry-template">
                        <tr class="new-entry-row table-light">
                            <td class="date-display align-middle fw-bold text-center"></td>
                            <td>
                                <select class="form-select project-select" required>
                                    <option value="">Select Project</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}"
                                            data-activities='@json($p->activities->map(fn($a) => ['id' => $a->id, 'title' => $a->title ?? '']))'>
                                            {{ $p->title ?: $p->short_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="form-select activity-select" required>
                                    <option value="">Select Activity</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" placeholder="Task / description"
                                    maxlength="500">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" max="24"
                                    class="form-control text-end" placeholder="0.00" required>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-success save-new"><i class="bi bi-check-lg"></i></button>
                                <button class="btn btn-sm btn-secondary cancel-new"><i class="bi bi-x-lg"></i></button>
                            </td>
                        </tr>
                    </template>
                </div>
            </div>

            @if ($authUser->can('submit', $timeSheet))
                <div class="card-footer border-top">
                    <form action="{{ route('monthly-timesheet.update', $timeSheet->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-lg-3">
                                            <label class="m-0">{{ __('label.approval') }}</label>
                                        </div>
                                        <div class="col-lg-9">
                                            @php $selectedApproverId = old('approver_id') ?: $timeSheet->approver_id; @endphp
                                            <select name="approver_id"
                                                class="select2 form-control @error('approver_id') is-invalid @enderror"
                                                data-width="100%">
                                                @if ($supervisors->count() !== 1)
                                                    <option value="">Select an Approver</option>
                                                @endif
                                                @foreach ($supervisors as $approver)
                                                    <option value="{{ $approver->id }}"
                                                        {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                                        {{ $approver->getFullName() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('approver_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 d-flex justify-content-end gap-2">
                            <button type="submit" name="action" value="submit"
                                class="btn btn-success btn-sm">Submit</button>
                            <a href="{{ route('monthly-timesheet.index') }}" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            @elseif ($timeSheet->status_id == config('constant.CREATED_STATUS'))
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        Approval form will be available after the period ends on
                        <strong>{{ $timeSheet->end_date->format('d M Y') }}</strong>
                    </small>
                </div>
            @endif

            @if (in_array($timeSheet->status_id, [config('constant.APPROVED_STATUS'), config('constant.SUBMITTED_STATUS')]))
                <div class="card mt-3">
                    <div class="card-header fw-bold">Approval & Comments History</div>
                    <div class="card-body">
                        @foreach ($timeSheet->logs as $log)
                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom">
                                <div class="rounded-circle user-icon">
                                    <i class="bi-person-circle fs-5"></i>
                                </div>
                                <div class="w-100">
                                    <div
                                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                            <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                            <span class="badge bg-primary c-badge">
                                                {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                                            </span>
                                        </div>
                                        <small
                                            title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <p class="text-justify comment-text mb-0 mt-1">{{ $log->log_remarks }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
