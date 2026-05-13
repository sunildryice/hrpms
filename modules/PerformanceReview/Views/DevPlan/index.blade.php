@extends('layouts.container')

@section('title', 'My Development Plan')

@section('page_css')
    <style>
        #devplan-table th,
        #devplan-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            vertical-align: middle;
        }

        #devplan-table {
            table-layout: fixed;
            width: 100%;
        }

        .col-sn {
            width: 5%;
        }

        .col-plan {
            width: 87%;
        }

        .col-action {
            width: 8%;
            text-align: center;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            margin: 0 2px;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .review-meta span {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .review-meta strong {
            color: #495057;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        @if ($keyGoalReview)
            let devPlanRowIndex = {{ $devPlans->count() ?: 0 }};

            $(function() {
                /* Highlight sidebar link */
                $('#navbarVerticalMenu').find('#performance-devplan-index').addClass('active');

                updateDevPlanButtons();

                /* ── SAVE ── */
                $('#btn-save').on('click', function() {
                    if (!validateRows()) return;

                    $.ajax({
                        url: "{{ route('performance.devplan.standalone.update') }}",
                        type: 'POST',
                        data: buildPayload(),
                        beforeSend: function() {
                            $('#btn-save').prop('disabled', true).text('Saving…');
                        },
                        success: function(res) {
                            if (res.type === 'success') {
                                toastr.success(res.message || 'Development plan saved.');
                            } else {
                                toastr.error('Could not save. Please try again.');
                            }
                            $('#btn-save').prop('disabled', false).text('Save');
                        },
                        error: function(xhr) {
                            toastr.error(
                                xhr.responseJSON?.message ||
                                'An error occurred. Please try again.'
                            );
                            $('#btn-save').prop('disabled', false).text('Save');
                        }
                    });
                });
            });

            /* ── Helpers ── */

            function buildPayload() {
                const base = {
                    _token: "{{ csrf_token() }}",
                    performance_review_id: {{ $keyGoalReview->id }},
                };

                const formData = $('#devplan-form').serializeArray();
                const payload = {};
                formData.forEach(function(f) {
                    payload[f.name] = f.value;
                });

                return {
                    ...base,
                    ...payload
                };
            }

            function validateRows() {
                let valid = true;
                $('#devplan-body .devplan-row').each(function() {
                    const plan = $(this).find('textarea').val()?.trim();
                    if (!plan) {
                        valid = false;
                        $(this).addClass('table-danger');
                    } else {
                        $(this).removeClass('table-danger');
                    }
                });

                if (!valid) {
                    toastr.error('Please fill in all development plan fields.', 'Validation Error');
                }
                return valid;
            }

            /* ── Row builder ── */
            function buildDevPlanRow(idx, plan = '', id = null) {
                return `
            <tr class="devplan-row" data-row-index="${idx}" ${id ? `data-id="${id}"` : ''}>
                <td class="col-sn">${idx + 1}</td>
                <td class="col-plan">
                    <input type="hidden" name="devplans[${idx}][id]" value="${id ?? ''}">
                    <textarea class="form-control"
                              name="devplans[${idx}][plan]"
                              rows="2"
                              placeholder="Describe the development activity or training attended"
                              required>${plan.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</textarea>
                </td>
                <td class="col-action">
                    <button type="button" class="btn btn-outline-primary btn-sm add-devplan-row" title="Add row">
                        <i class="bi bi-plus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-devplan-row" title="Remove row">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
            }

            function updateDevPlanButtons() {
                const $rows = $('#devplan-body .devplan-row');
                $rows.find('.add-devplan-row').hide();
                $rows.find('.remove-devplan-row').show();
                if ($rows.length === 1) $rows.find('.remove-devplan-row').hide();
                $rows.last().find('.add-devplan-row').show();
            }

            /* Renumber SN column */
            function renumberRows() {
                $('#devplan-body .devplan-row').each(function(i) {
                    $(this).find('.col-sn').text(i + 1);
                    $(this).attr('data-row-index', i);
                    $(this).find('input[type=hidden]').attr('name', `devplans[${i}][id]`);
                    $(this).find('textarea').attr('name', `devplans[${i}][plan]`);
                });
            }

            $(document).on('click', '.add-devplan-row', function() {
                devPlanRowIndex++;
                const $row = $(buildDevPlanRow(devPlanRowIndex));
                $('#devplan-body').append($row);
                updateDevPlanButtons();
            });

            $(document).on('click', '.remove-devplan-row', function() {
                const $row = $(this).closest('tr');
                const planId = $row.data('id');

                const doRemove = function() {
                    $row.remove();
                    renumberRows();
                    updateDevPlanButtons();
                };

                if (planId) {
                    $.ajax({
                        url: "{{ route('performance.devplan.destroy') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            devPlanId: planId
                        },
                        success: function(res) {
                            if (res.type === 'success') {
                                doRemove();
                                toastr.success('Development plan entry removed.');
                            } else {
                                toastr.error('Failed to delete. Please try again.');
                            }
                        },
                        error: function() {
                            toastr.error('Server error. Please try again.');
                        }
                    });
                } else {
                    doRemove();
                }
            });
        @endif
    </script>
@endsection

@section('page-content')

    @if (!$keyGoalReview)
        {{-- No key-goals review found yet ──────────────────────────────────────── --}}
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                <p class="mb-0">No Key Goals Review found.</p>
                <p>A development plan can be created once a Key Goals Review has been set up for you.</p>
            </div>
        </div>
    @else
        {{-- Development plan table ─────────────────────────────────────────────── --}}
        <div class="card mb-3">
            <div class="card-header fw-bold">
                Professional Development Plan
            </div>
            <div class="card-body">
                <form id="devplan-form">
                    @csrf
                    <table class="table table-bordered" id="devplan-table">
                        <thead>
                            <tr>
                                <th class="col-sn">SN</th>
                                <th class="col-plan">Development Plan </th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody id="devplan-body">
                            @forelse ($devPlans as $index => $plan)
                                <tr class="devplan-row" data-row-index="{{ $index }}" data-id="{{ $plan->id }}">
                                    <td class="col-sn">{{ $loop->iteration }}</td>
                                    <td class="col-plan">
                                        <input type="hidden" name="devplans[{{ $index }}][id]"
                                            value="{{ $plan->id }}">
                                        <textarea class="form-control" name="devplans[{{ $index }}][plan]" rows="2"
                                            placeholder="Describe the development activity or training attended" required>{{ old('devplans.' . $index . '.plan', $plan->objective ?? '') }}</textarea>
                                    </td>
                                    <td class="col-action">
                                        <button type="button" class="btn btn-outline-primary btn-sm add-devplan-row"
                                            title="Add row">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-devplan-row"
                                            title="Remove row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="devplan-row" data-row-index="0">
                                    <td class="col-sn">1</td>
                                    <td class="col-plan">
                                        <textarea class="form-control" name="devplans[0][plan]" rows="2"
                                            placeholder="Describe the development activity or training attended" required></textarea>
                                    </td>
                                    <td class="col-action">
                                        <button type="button" class="btn btn-outline-primary btn-sm add-devplan-row"
                                            title="Add row">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-devplan-row"
                                            style="display:none" title="Remove row">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="button" id="btn-save" class="btn btn-sm btn-primary px-4">
                <i class="bi bi-floppy me-1"></i> Save
            </button>
            <a href="{{ route('performance.employee.index') }}" class="btn btn-sm btn-danger px-4">Cancel</a>
        </div>
    @endif

@endsection
