@php
    $canEdit = auth()->user()->can('manage-project-activity-detail', $projectActivity->project);
@endphp

<div class="card mt-3">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <span class="fw-bold">Details</span>
            @if ($canEdit)
                <button type="button" class="btn btn-outline-primary btn-sm add-detail-btn"
                    href="{{ route('project-activity.details.create', $projectActivity->id) }}">
                    <i class="bi bi-plus"></i> Add
                </button>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" id="detailsTable">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">{{ __('label.sn') }}</th>
                        <th>Key Accomplishments</th>
                        <th>Challenges</th>
                        <th>Lessons Learned</th>
                        @if ($canEdit)
                            <th class="text-nowrap" style="width:130px">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr class="empty-row">
                        <td colspan="{{ $canEdit ? 5 : 4 }}" class="text-center text-muted">No entries added yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@php
    $detailsArr = [];
    if (isset($projectActivity) && method_exists($projectActivity, 'details')) {
        foreach ($projectActivity->details as $d) {
            $detailsArr[] = [
                'id' => $d->id,
                'key_accomplishment' => $d->key_accomplishment,
                'challenge' => $d->challenge,
                'lesson_learned' => $d->lesson_learned,
            ];
        }
    }
    $detailsJson = json_encode($detailsArr);
@endphp

@push('scripts')
    <script>
        var activityDetails = {!! $detailsJson !!};

        function renderDetailTable() {
            const tbody = $('#detailsTable tbody');
            tbody.empty();

            if (activityDetails.length === 0) {
                const colspan = {{ $canEdit ? 5 : 4 }};
                tbody.append(`<tr class="empty-row"><td colspan="${colspan}" class="text-center text-muted">No entries added yet.</td></tr>`);
                return;
            }

            activityDetails.forEach(function(item, idx) {
                const editUrl = '/project-activity/details/' + item.id + '/edit';
                tbody.append(`
                    <tr data-idx="${idx}" data-id="${item.id}">
                        <td style="width:50px">${idx + 1}</td>
                        <td class="text-wrap">${item.key_accomplishment}</td>
                        <td class="text-wrap">${item.challenge}</td>
                        <td class="text-wrap">${item.lesson_learned}</td>
                        @if ($canEdit)
                        <td class="text-nowrap" style="width:130px">
                            <a class="btn btn-outline-primary btn-sm edit-detail-btn"
                               href="${editUrl}"
                               rel="tooltip" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-outline-danger btn-sm delete-detail-btn"
                                    data-id="${item.id}" data-idx="${idx}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                `);
            });
        }

        $(document).on('click', '.add-detail-btn', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                //
            });
        });

        $(document).on('click', '.edit-detail-btn', function(e) {
            e.preventDefault();
            $('#openModal').find('.modal-content').html('');
            $('#openModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                //
            });
        });

        $(document).on('click', '.delete-detail-btn', function() {
            const $btn = $(this);
            const id = $btn.data('id');
            const idx = $btn.data('idx');

            ajaxDeleteSweetAlert(
                '/project-activity/details/' + id,
                function(res) {
                    activityDetails.splice(idx, 1);
                    renderDetailTable();
                    toastr.success(res.message || 'Deleted');
                },
                function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting');
                }
            );
        });

        $(function() {
            renderDetailTable();
        });
    </script>
@endpush
