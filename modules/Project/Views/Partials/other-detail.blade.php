@php
    $canEdit = auth()->user()->can('manage-project-activity-other-detail', $projectActivity->project);
@endphp
<div class="card mt-3">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <span class="fw-bold">Other Details</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" id="otherDetailsTable">
                <thead class="table-light">
                    @if ($canEdit)
                        <tr>

                            <th>{{ __('label.sn') }}</th>
                            <th>Key</th>
                            <th>Value</th>
                            @if ($canEdit)
                                <th class="text-nowrap">Action</th>
                            @endif
                        </tr>
                    @endif
                </thead>
                <tbody>
                    <!-- Rows will be managed by JS -->
                </tbody>
                @if ($canEdit)
                    <tfoot>
                        <tr>
                            <td colspan="2">
                                <input type="text" class="form-control" id="newOtherDetailKey" placeholder="Key" />
                            </td>
                            <td><input type="text" class="form-control" id="newOtherDetailValue"
                                    placeholder="Value" />
                            </td>
                            <td class="text-nowrap">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addOtherDetailBtn"
                                    rel="tooltip" title="Add Other Detail">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

@php
    // Always convert to array of {key, value} for JS
    $otherDetailsArr = [];
    if (isset($otherDetails)) {
        foreach ($otherDetails as $d) {
            $otherDetailsArr[] = ['id' => $d->id ?? null, 'key' => $d->key, 'value' => $d->value];
        }
    } elseif (isset($projectActivity) && method_exists($projectActivity, 'otherDetails')) {
        foreach ($projectActivity->otherDetails as $d) {
            $otherDetailsArr[] = ['id' => $d->id ?? null, 'key' => $d->key, 'value' => $d->value];
        }
    }
    $otherDetailsJson = json_encode($otherDetailsArr);
@endphp

@push('scripts')
    <script>
        // Initialize from backend if available
        let otherDetails = {!! $otherDetailsJson !!};
        if (!Array.isArray(otherDetails)) otherDetails = [];

        function renderOtherDetailsTable() {
            const tbody = $('#otherDetailsTable tbody');
            tbody.empty();
            if (otherDetails.length === 0) {
                tbody.append('<tr><td colspan="4" class="text-center text-muted">No other details added.</td></tr>');
                return;
            }
            otherDetails.forEach(function(detail, idx) {
                if (detail.editing) {
                    tbody.append(`
                    <tr data-idx="${idx}" data-id="${detail.id ?? ''}">
                        <td>${idx + 1}</td>
                        <td><input type="text" class="form-control  edit-key" value="${detail.key}" /></td>
                        <td><input type="text" class="form-control edit-value" value="${detail.value}" /></td>
                        <td>
                            <button class="btn btn-primary btn-sm save-edit"><i class="bi bi-check"></i></button>
                            <button class="btn btn-secondary btn-sm cancel-edit"><i class="bi bi-x"></i></button>
                        </td>
                    </tr>
                `);
                } else {
                    tbody.append(`
                    <tr data-idx="${idx}" data-id="${detail.id ?? ''}">
                        @if ($canEdit)
                        <td width="50">${idx + 1}</td>
                        @endif
                        <td class="detail-key ">${detail.key}</td>
                        <td class="detail-value">${detail.value}</td>
                        @if ($canEdit)
                        <td>
                            <button class="btn btn-outline-primary btn-sm edit-detail"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-outline-danger btn-sm delete-detail"><i class="bi bi-trash"></i></button>
                        </td>
                        @endif
                    </tr>
                `);
                }
            });
        }

        $(document).on('click', '#addOtherDetailBtn', function() {
            const key = $('#newOtherDetailKey').val().trim();
            const value = $('#newOtherDetailValue').val().trim();
            if (!key || !value) {
                toastr.error('Both key and value are required.');
                return;
            }
            // Prevent duplicate keys
            if (otherDetails.some(d => d.key === key)) {
                toastr.error('Key already exists.');
                return;
            }
            // AJAX POST to add single detail
            $.ajax({
                url: '/project-activity/' + {{ $projectActivity->id }} + '/other-details',
                method: 'POST',
                data: {
                    key,
                    value
                },
                success: function(res) {
                    otherDetails.push({
                        id: res.detail.id,
                        key: res.detail.key,
                        value: res.detail.value
                    });
                    $('#newOtherDetailKey').val('');
                    $('#newOtherDetailValue').val('');
                    renderOtherDetailsTable();
                    toastr.success(res.message || 'Added');
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error saving');
                }
            });
        });

        $(document).on('click', '.edit-detail', function() {
            const idx = $(this).closest('tr').data('idx');
            otherDetails = otherDetails.map((d, i) => ({
                ...d,
                editing: i === idx
            }));
            renderOtherDetailsTable();
        });

        $(document).on('click', '.save-edit', function() {
            const tr = $(this).closest('tr');
            const idx = tr.data('idx');
            const id = tr.data('id');
            const key = tr.find('.edit-key').val().trim();
            const value = tr.find('.edit-value').val().trim();
            if (!key || !value) {
                toastr.error('Both key and value are required.');
                return;
            }
            // Prevent duplicate keys except for the current row
            if (otherDetails.some((d, i) => d.key === key && i !== idx)) {
                toastr.error('Key already exists.');
                return;
            }
            // AJAX PUT to update single detail
            $.ajax({
                url: '/project-activity/other-details/' + id,
                method: 'PUT',
                data: {
                    key,
                    value
                },
                success: function(res) {
                    otherDetails[idx] = {
                        id: res.detail.id,
                        key: res.detail.key,
                        value: res.detail.value
                    };
                    // Remove editing mode
                    otherDetails = otherDetails.map(d => ({
                        ...d,
                        editing: false
                    }));
                    renderOtherDetailsTable();
                    toastr.success(res.message || 'Updated');
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error updating');
                }
            });
        });

        $(document).on('click', '.cancel-edit', function() {
            otherDetails = otherDetails.map(d => ({
                ...d,
                editing: false
            }));
            renderOtherDetailsTable();
        });

        $(document).on('click', '.delete-detail', function() {
            const idx = $(this).closest('tr').data('idx');
            const id = $(this).closest('tr').data('id');
            ajaxDeleteSweetAlert(
                '/project-activity/other-details/' + id,
                function(res) {
                    otherDetails.splice(idx, 1);
                    renderOtherDetailsTable();
                    toastr.success(res.message || 'Deleted');
                },
                function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error deleting');
                }
            );
        });

        $(function() {
            renderOtherDetailsTable();
        });
    </script>
@endpush
