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
        @if ($canAccessPDP && $keyGoalReview)
            let devPlanRowIndex = {{ $devPlans->count() ?: 0 }};

            $(function() {
                $('#navbarVerticalMenu').find('#performance-devplan-index').addClass('active');

                $('#btn-add-devplan').on('click', function() {
                    openDevPlanModal();
                });

                $('#btn-save-devplan').on('click', function() {
                    saveDevPlanModal();
                });
            });

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

            function openDevPlanModal(rowIndex = '', plan = '', id = '') {
                $('#devplan-row-index').val(rowIndex);
                $('#devplan-plan-id').val(id);
                $('#devplan-plan').val(plan);
                $('#devplanModalLabel').text(rowIndex === '' ? 'Add Professional Development Plan' : 'Edit Professional Development Plan');
                $('#devplanModal').modal('show');
            }

            function saveDevPlanModal() {
                const plan = $('#devplan-plan').val().trim();
                if (!plan) {
                    toastr.error('Please enter a development plan.');
                    return;
                }

                const rowIndex = $('#devplan-row-index').val();
                const id = $('#devplan-plan-id').val();
                let $row;
                let oldPlan = '';

                if (rowIndex !== '') {
                    $row = $(`#devplan-body .devplan-row[data-row-index="${rowIndex}"]`);
                    oldPlan = $row.find('.devplan-input-plan').val();
                    $row.find('.devplan-text').text(plan);
                    $row.find('.devplan-input-plan').val(plan);
                } else {
                    $('#devplan-body .placeholder-row').remove();
                    $row = $(buildDevPlanRow(devPlanRowIndex, plan, id));
                    $('#devplan-body').append($row);
                    devPlanRowIndex++;
                }

                renumberRows();

                ajaxSubmit("{{ route('performance.devplan.standalone.update') }}", 'POST', buildPayload(), function(res) {
                    toastr.success(res.message || 'Development plan saved.');
                    $('#devplanModal').modal('hide');
                }, function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'An error occurred while saving.');
                    if (rowIndex === '') {
                        $row.remove();
                        if ($('#devplan-body .devplan-row').length === 0) {
                            $('#devplan-body').html('<tr class="placeholder-row"><td colspan="3" class="text-center text-muted py-4">No development plan entries yet. Click Add Plan to get started.</td></tr>');
                        }
                    } else {
                        $row.find('.devplan-text').text(oldPlan);
                        $row.find('.devplan-input-plan').val(oldPlan);
                    }
                });
            }

            function validateRows() {
                let valid = true;
                const $rows = $('#devplan-body .devplan-row');

                if ($rows.length === 0) {
                    toastr.error('Please add at least one development plan item.', 'Validation Error');
                    return false;
                }

                $rows.each(function() {
                    const plan = $(this).find('.devplan-input-plan').val()?.trim();
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

            function buildDevPlanRow(idx, plan = '', id = '') {
                return `
                <tr class="devplan-row" data-row-index="${idx}" ${id ? `data-id="${id}"` : ''}>
                    <td class="col-sn">${idx + 1}</td>
                    <td class="col-plan">
                        <div class="devplan-text">${escapeHtml(plan)}</div>
                        <input type="hidden" name="devplans[${idx}][id]" value="${id}">
                        <input type="hidden" class="devplan-input-plan" name="devplans[${idx}][plan]" value="${escapeHtml(plan)}">
                    </td>
                    <td class="col-action">
                        <button type="button" class="btn btn-outline-primary btn-sm edit-devplan-row" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm delete-devplan-row" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            }

            function escapeHtml(value) {
                return $('<div>').text(value).html();
            }

            function renumberRows() {
                $('#devplan-body .devplan-row').each(function(i) {
                    $(this).find('.col-sn').text(i + 1);
                    $(this).attr('data-row-index', i);
                    $(this).find('input[name^="devplans"][name$="[id]"]').attr('name', `devplans[${i}][id]`);
                    $(this).find('input.devplan-input-plan').attr('name', `devplans[${i}][plan]`);
                });
            }

            $(document).on('click', '.edit-devplan-row', function() {
                const $row = $(this).closest('.devplan-row');
                const rowIndex = $row.data('row-index');
                const plan = $row.find('.devplan-input-plan').val();
                const id = $row.data('id') || '';
                openDevPlanModal(rowIndex, plan, id);
            });

            $(document).on('click', '.delete-devplan-row', function() {
                const $row = $(this).closest('.devplan-row');
                const planId = $row.data('id');
                const removeRow = function() {
                    $row.remove();
                    renumberRows();
                    if ($('#devplan-body .devplan-row').length === 0) {
                        $('#devplan-body').html('<tr class="placeholder-row"><td colspan="3" class="text-center text-muted py-4">No development plan entries yet. Click Add Plan to get started.</td></tr>');
                    }
                };

                if (planId) {
                    ajaxSweetAlert("{{ route('performance.devplan.destroy') }}", 'POST', {
                        _token: "{{ csrf_token() }}",
                        devPlanId: planId
                    }, 'Yes, delete it!', function(res) {
                        if (res.type === 'success') {
                            removeRow();
                            toastr.success(res.message || 'Development plan entry removed.');
                        } else {
                            toastr.error('Failed to delete. Please try again.');
                        }
                    });
                } else {
                    removeRow();
                }
            });
        @endif
    </script>
@endsection

@section('page-content')

    @if (!$canAccessPDP)
        {{-- PDP not available after annual/mid-term review --}}
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                <p class="mb-0">Professional Development Plan is not available.</p>
                <p>Professional Development Plan cannot be modified after an Annual Review has been created for the current fiscal year.</p>
            </div>
        </div>
    @elseif (!$keyGoalReview)
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-journal-text fs-1 d-block mb-3"></i>
                <p class="mb-0">Professional Development Plan will become available after your Key Goals Review is approved.</p>
                <p>Once your current fiscal year Key Goals Review is approved, you can manage development plan items here.</p>
            </div>
        </div>
    @else
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">Professional Development Plan</span>
                <button type="button" id="btn-add-devplan" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus me-1"></i> Add New
                </button>
            </div>
            <div class="card-body">
                <form id="devplan-form">
                    @csrf
                    <table class="table table-bordered" id="devplan-table">
                        <thead>
                            <tr>
                                <th class="col-sn">SN</th>
                                <th class="col-plan">Development Plan</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody id="devplan-body">
                            @forelse ($devPlans as $index => $plan)
                                <tr class="devplan-row" data-row-index="{{ $index }}" data-id="{{ $plan->id }}">
                                    <td class="col-sn">{{ $loop->iteration }}</td>
                                    <td class="col-plan">
                                        <div class="devplan-text">{{ $plan->objective }}</div>
                                        <input type="hidden" name="devplans[{{ $index }}][id]" value="{{ $plan->id }}">
                                        <input type="hidden" class="devplan-input-plan" name="devplans[{{ $index }}][plan]" value="{{ $plan->objective }}">
                                    </td>
                                    <td class="col-action">
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-devplan-row" title="Edit">
                                            <i class="bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete-devplan-row" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="placeholder-row">
                                    <td colspan="3" class="text-center text-muted py-4">
                                        No development plan entries yet. Click Add Plan to get started.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="text-end mt-3">
            <a href="{{ route('performance.employee.index') }}" class="btn btn-sm btn-danger px-4">Cancel</a>
        </div>

        <div class="modal fade" id="devplanModal" tabindex="-1" aria-labelledby="devplanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="devplanModalLabel">Add Professional Development Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="devplan-row-index" value="">
                        <input type="hidden" id="devplan-plan-id" value="">
                        <div class="mb-2">
                            <label for="devplan-plan" class="form-label">Development Plan</label>
                            <textarea id="devplan-plan" class="form-control" rows="3" placeholder="Describe the development activity or training attended"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn-save-devplan" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
