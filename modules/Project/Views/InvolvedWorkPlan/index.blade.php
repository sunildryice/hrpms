@extends('layouts.container')

@section('title', 'Involved Work Plan')

@section('page_css')
    <style>
        .detail-row {
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #edf1f5;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            // Activate navbar menu item
            $('#navbarVerticalMenu').find('#involved-work-plan-index').addClass('active');

            // Week selector change handler
            $('#week_selector').change(function() {
                const weekStart = $(this).val();
                const baseUrl = "{{ route('involved-work-plan.index') }}";
                window.location.href = weekStart ? `${baseUrl}?week_start=${weekStart}` : baseUrl;
            });

            // Initialize DataTable
            const table = $('#WeeklyPlanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('involved-work-plan.index') }}",
                    data: function(d) {
                        d.week_start = "{{ $currentWeekStart->format('Y-m-d') }}";
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'project.short_name',
                        name: 'project.short_name',
                        defaultContent: ''
                    },
                    {
                        data: 'activity.title',
                        name: 'activity.title',
                        defaultContent: ''
                    },
                    {
                        data: 'plan_tasks',
                        name: 'plan_tasks',
                        defaultContent: ''
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_by',
                        name: 'created_by',
                        defaultContent: ''
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        defaultContent: '',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Modal handlers
            const $detailModal = $('#workPlanDetailModal');
            const modalInstance = window.bootstrap ? bootstrap.Modal.getOrCreateInstance($detailModal[0]) : null;

            // View work plan detail click handler
            $('#WeeklyPlanTable').on('click', '.view-work-plan', function() {
                const rowData = table.row($(this).closest('tr')).data();
                if (!rowData || !$detailModal.length) return;

                populateModal(rowData);
                if (modalInstance) {
                    modalInstance.show();
                } else {
                    $detailModal.modal('show');
                }
            });

            function populateModal(data) {
                $('#detailWeek').text(formatDateRange(data.work_plan_meta?.from_date, data.work_plan_meta
                ?.to_date));
                $('#detailProject').text(data.project?.short_name || 'N/A');
                $('#detailActivity').text(data.activity?.title || 'N/A');
                $('#detailTasks').text(data.plan_tasks || '-');
                $('#detailStatus').html(data.status || '-');
                $('#detailCreatedBy').text(data.created_by || 'N/A');
                $('#detailRemarks').text(data.reason || '-');
                $('#detailMembers').html(createMemberBadges(normalizeMembers(data.members_data)));
            }

            function formatDateRange(fromDate, toDate) {
                if (!fromDate || !toDate) return '-';
                return `${formatDate(fromDate)} - ${formatDate(toDate)}`;
            }

            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return Number.isNaN(date.getTime()) ? dateStr :
                    date.toLocaleDateString('en-US', {
                        month: 'short',
                        day: 'numeric',
                        year: 'numeric'
                    });
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
            }

            function normalizeMembers(rawValue) {
                if (!rawValue) return [];
                if (Array.isArray(rawValue)) return rawValue;
                try {
                    const parsed = JSON.parse(rawValue);
                    return Array.isArray(parsed) ? parsed : [];
                } catch {
                    return [];
                }
            }

            function createMemberBadges(members) {
                if (!members.length) {
                    return '<span class="text-muted">No additional members</span>';
                }
                return members.map(member =>
                    `<span class="badge bg-light text-dark border me-1 mb-1">${escapeHtml(member.name)}${member.is_self ? ' (You)' : ''}</span>`
                ).join(' ');
            }
        });
    </script>
@endsection

@section('page-content')
    {{-- Breadcrumb and Header --}}
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    <div style="min-width: 260px;">
                        <select class="form-select select2" id="week_selector" name="week_selector">
                            <option value="">Select Week</option>
                            @foreach ($weeks as $date => $label)
                                <option value="{{ $date }}"
                                    {{ $currentWeekStart->format('Y-m-d') === $date ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable --}}
    <div class="card shadow-sm border rounded c-tabs-content active" id="weekly-plan-table">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="WeeklyPlanTable">
                    <thead class="bg-light">
                        <tr>
                            <th>SN</th>
                            <th>Project</th>
                            <th>Activity</th>
                            <th>Planned Tasks</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Remarks</th>
                            <th>{{ __('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="workPlanDetailModal" tabindex="-1" aria-labelledby="workPlanDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title mb-0 fs-6" id="workPlanDetailModalLabel">Work Plan Detail</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-row row">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Week</span></div>
                        <div class="col-md-8 col-lg-9">
                            <p class="detail-value mb-0" id="detailWeek">-</p>
                        </div>
                    </div>
                    <div class="detail-row row">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Project</span></div>
                        <div class="col-md-8 col-lg-9">
                            <p class="detail-value mb-0" id="detailProject">-</p>
                        </div>
                    </div>
                    <div class="detail-row row">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Activity</span></div>
                        <div class="col-md-8 col-lg-9">
                            <p class="detail-value mb-0" id="detailActivity">-</p>
                        </div>
                    </div>
                    <div class="detail-row row">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Planned Tasks</span></div>
                        <div class="col-md-8 col-lg-9">
                            <p class="detail-value mb-0" id="detailTasks">-</p>
                        </div>
                    </div>
                    <div class="detail-row row">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Status</span></div>
                        <div class="col-md-8 col-lg-9">
                            <div class="detail-value" id="detailStatus">-</div>
                        </div>
                    </div>
                    <div class="detail-row row">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Other Members</span></div>
                        <div class="col-md-8 col-lg-9 detail-members">
                            <div class="detail-value" id="detailMembers">-</div>
                        </div>
                    </div>
                    <div class="detail-row row">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Created By</span></div>
                        <div class="col-md-8 col-lg-9">
                            <p class="detail-value mb-0" id="detailCreatedBy">-</p>
                        </div>
                    </div>
                    <div class="detail-row row mb-0 pb-0 border-0">
                        <div class="col-md-4 col-lg-3"><span class="detail-label">Remarks</span></div>
                        <div class="col-md-8 col-lg-9">
                            <p class="detail-value mb-0" id="detailRemarks">-</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
