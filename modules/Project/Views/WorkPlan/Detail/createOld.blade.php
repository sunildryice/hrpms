@extends('layouts.container')

@section('title', 'Create Work Plan')

@section('page_css')
    <style>
        .fv-plugins-icon {
            position: absolute !important;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Select2 fixes for table */
        #entries-table {
            table-layout: fixed;
            width: 100%;
        }

        /* column width helpers like timesheet */
        .col-date {
            width: 10%;
            min-width: 80px;
            max-width: 120px;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-project {
            width: 15%;
            min-width: 120px;
            max-width: 180px;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-activity {
            width: 18%;
            min-width: 120px;
            max-width: 220px;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-task {
            width: 30%;
            min-width: 150px;
            max-width: 260px;
            /* increased from flexible to give more room */
            overflow: hidden;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }

        .col-members {
            width: 20%;
            min-width: 200px;
            max-width: 320px;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-action {
            width: 8%;
            overflow: hidden;
            word-wrap: break-word;
        }

        #entries-table td {
            /* allow members to wrap but prevent project/activity overflow */
            overflow: visible;
            vertical-align: top;
            padding-top: 0.4rem;
            padding-bottom: 0.4rem;
            max-height: none;
            box-sizing: border-box;
        }

        /* ensure single-select elements do not spill out */
        #entries-table td:nth-child(1) .select2-container,
        #entries-table td:nth-child(2) .select2-container,
        #entries-table td.col-activity .select2-container {
            min-width: 120px;
            max-width: 100% !important;
        }

        .table .select2-container {
            width: 100% !important;
            white-space: normal;
            z-index: 1050;
            /* bring dropdown above other elements */
        }

        .table .select2-container--default .select2-selection--single,
        .table .select2-container--default .select2-selection--multiple {
            /* allow height to auto-expand */
            height: auto !important;
            min-height: calc(2.25rem + 2px);
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 38px;
        }

        .table td .select2-container {
            margin-bottom: 0;
            min-width: 200px;
            box-sizing: border-box;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            color: #495057;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            margin: 0.1rem 0.3rem 0.1rem 0;
            padding: 0.2rem 0.3rem 0.1rem 1rem;
            /* increased left padding */
            line-height: 0.8;
        }

        /* allow tags to wrap within the select2 container */
        .select2-container--default .select2-selection--multiple {
            flex-wrap: wrap;
            overflow-wrap: break-word;
        }

        .select2-container--default .select2-selection__choice__remove {
            margin-right: 0.2rem;
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#work-plan-index').addClass('active');

            // Global Select2 initialization
            $('.select2').select2({
                placeholder: 'Select...',
                width: '100%'
            });

            // restrict work plan date to the week range (used for row inputs)
            var wpMin = '{{ $week['start_date']->format('Y-m-d') }}';
            var wpMax = '{{ $week['end_date']->format('Y-m-d') }}';

            const form = document.getElementById('WorkPlanForm');
            if (form) {
                let rowIndex = 0;
                let fv;

                function safeRevalidate() {
                    if (fv && typeof fv.revalidateField === 'function') {
                        try {
                            fv.revalidateField('entries');
                        } catch (e) {}
                    }
                }

                // helper to coerce various data-* forms into an array
                function parseJsonPayload(payload) {
                    if (!payload) return [];
                    if (Array.isArray(payload)) return payload;
                    if (typeof payload === 'object') return payload;
                    if (typeof payload === 'string') {
                        if (!payload.trim()) return [];
                        try {
                            return JSON.parse(payload);
                        } catch (_e) {
                            return [];
                        }
                    }
                    return [];
                }

                function refreshActions() {
                    const $rows = $('#entries-body .entry-row');
                    $rows.find('.add-entry').remove();
                    $rows.find('.remove-entry').show().prop('disabled', false).removeClass('disabled');
                    if ($rows.length) {
                        $rows.last().find('.action-col').append(
                            '<button type="button" class="btn btn-sm btn-outline-primary add-entry"><i class="bi bi-plus-lg"></i></button>'
                        );
                        if ($rows.length === 1) {
                            $rows.first().find('.remove-entry').prop('disabled', true).addClass('disabled');
                        }
                    }
                }

                function buildEntryRow(idx) {
                    let tpl = $('#entry-row-template').html();
                    tpl = tpl.replace(/__IDX__/g, idx);
                    return $(tpl);
                }

                function initRow($row) {
                    const idx = $row.data('row-index');

                    // Destroy any existing Select2 instances first
                    $row.find('.select2').each(function() {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                    });

                    // Initialize ALL Select2 in row with proper container
                    $row.find('.select2').select2({
                        placeholder: 'Select...',
                        width: '100%',
                        dropdownParent: $(document.body),
                        allowClear: true
                    });
                    // ensure rendered area respects cell width
                    $row.find('.select2-selection--multiple').css('max-width', '100%');

                    const $projectSelect = $row.find('.project-select');
                    const $activitySelect = $row.find('.activity-select');
                    const $membersSelect = $row.find('.members-select');

                    // make sure project select starts empty to thwart Chrome autofill
                    $projectSelect.val(null).trigger('change').trigger('change.select2');
                    // if Chrome still tries to inject a value on focus, clear it again
                    $projectSelect.on('focus', function() {
                        $(this).val(null).trigger('change').trigger('change.select2');
                    });

                    function populateMembers() {
                        let membersData = $projectSelect.find(':selected').attr('data-members');
                        let members = [];

                        try {
                            if (membersData) {
                                members = JSON.parse(membersData.replace(/&quot;/g, '"'));
                            }
                        } catch (e) {
                            console.warn('Invalid members data:', membersData);
                            members = [];
                        }

                        // Destroy and clear before repopulating
                        if ($membersSelect.hasClass('select2-hidden-accessible')) {
                            $membersSelect.select2('destroy');
                        }

                        $membersSelect.empty().prop('disabled', true);

                        if (Array.isArray(members) && members.length > 0) {
                            members.forEach(function(member) {
                                $membersSelect.append($('<option>', {
                                    value: member.id,
                                    text: member.name || member.full_name || 'Unknown'
                                }));
                            });
                            $membersSelect.prop('disabled', false);
                        }

                        // Reinitialize members Select2
                        $membersSelect.select2({
                            placeholder: 'Select members...',
                            width: '100%',
                            dropdownParent: $(document.body),
                            multiple: true,
                            allowClear: true
                        });
                    }

                    function populateActivities() {
                        const raw = $projectSelect.find(':selected').data('activities');
                        const acts = parseJsonPayload(raw);
                        $activitySelect.empty().append('<option value="">Select Activity</option>');
                        debugger;

                        acts.forEach(a => {
                            $activitySelect.append(`<option value="${a.id}">${a.title}</option>`);
                        });

                        // Reinit activity select2
                        if ($activitySelect.hasClass('select2-hidden-accessible')) {
                            $activitySelect.select2('destroy');
                        }
                        $activitySelect.select2({
                            placeholder: 'Select Activity',
                            width: '100%',
                            dropdownParent: $(document.body)
                        });
                    }

                    // initialize datepicker on the row's date input
                    $row.find('.wp-date').datepicker({
                        startDate: wpMin,
                        endDate: wpMax,
                        autoclose: true,
                        autoHide: true,
                        format: 'yyyy-mm-dd',
                        zIndex: 1200
                    }).on('show', function() {
                        $(this).attr('readonly', true);
                    });

                    // Project change handler
                    $projectSelect.off('change.projectHandler').on('change.projectHandler', function() {
                        populateActivities();
                        populateMembers();
                        safeRevalidate();
                    });

                    // Initial population
                    if ($projectSelect.val()) {
                        setTimeout(() => {
                            populateActivities();
                            populateMembers();
                        }, 100);
                    }

                    // FormValidation fields (delayed to avoid conflicts)
                    setTimeout(() => {
                        if (fv) {
                            fv.addField(`entries[${idx}][work_plan_date]`, {
                                validators: {
                                    notEmpty: {
                                        message: 'Date is required'
                                    },
                                    date: {
                                        format: 'YYYY-MM-DD',
                                        message: 'Invalid date'
                                    }
                                }
                            });
                            fv.addField(`entries[${idx}][project_id]`, {
                                validators: {
                                    notEmpty: {
                                        message: 'Project is required'
                                    }
                                }
                            });
                            fv.addField(`entries[${idx}][activity_id]`, {
                                validators: {
                                    notEmpty: {
                                        message: 'Activity is required'
                                    }
                                }
                            });
                            fv.addField(`entries[${idx}][planned_task]`, {
                                validators: {
                                    notEmpty: {
                                        message: 'Task is required'
                                    }
                                }
                            });
                            fv.addField(`entries[${idx}][members][]`, {
                                validators: {
                                    notEmpty: {
                                        message: 'At least one member is required'
                                    }
                                }
                            });
                        }
                    }, 300);

                    // Revalidate on any change (includes inputs such as date picker)
                    $row.off('changeinput.rowHandler').on('changeinput.rowHandler', 'select, textarea, input',
                        function() {
                            if (fv) {
                                const name = $(this).attr('name');
                                if (name) {
                                    fv.revalidateField(name);
                                }
                            }
                            safeRevalidate();
                        });
                }

                // Create first row
                $('#entries-body').append(buildEntryRow(rowIndex));
                const $firstRow = $('#entries-body .entry-row').last();
                initRow($firstRow);
                refreshActions();

                // Remove entry handler
                $(document).off('click.removeEntry').on('click.removeEntry', '.remove-entry', function() {
                    const $row = $(this).closest('tr');
                    if ($('#entries-body .entry-row').length > 1) {
                        const idx = $row.data('row-index');
                        if (fv) {
                            fv.removeField(`entries[${idx}][project_id]`);
                            fv.removeField(`entries[${idx}][activity_id]`);
                            fv.removeField(`entries[${idx}][planned_task]`);
                            fv.removeField(`entries[${idx}][members][]`);
                        }
                        $row.remove();
                        refreshActions();
                        safeRevalidate();
                    }
                });

                // Add entry handler
                $('#entries-body').off('click.addEntry').on('click.addEntry', '.add-entry', function() {
                    rowIndex++;
                    const $newRow = buildEntryRow(rowIndex);
                    $('#entries-body').append($newRow);
                    initRow($newRow);
                    refreshActions();
                    safeRevalidate();
                });

                // Initialize FormValidation
                fv = FormValidation.formValidation(form, {
                    fields: {
                        // row-level date fields are added dynamically
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            rowSelector: 'td, .row.mb-2',
                            eleInvalidClass: 'is-invalid',
                            eleValidClass: 'is-valid',
                            messageClass: 'invalid-feedback'
                        }),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat'
                        })
                    }
                });
            }
        });
    </script>
@endsection

@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ isset($workPlan) ? route('work-plan.details', $workPlan) : route('work-plan.index') }}"
                                class="text-decoration-none text-dark">Work Plan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Create Work Plan :
                    {{ $week['start_date']->format('M j') }} - {{ $week['end_date']->format('M j, Y') }}</h4>

            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded">
        <div class="card-body">
            <form action="{{ route('work-plan.store') }}" method="post" id="WorkPlanForm" autocomplete="off">
                @csrf

                <div class="mb-3">
                    <label class="form-label required-label">Entries</label>
                    <table class="table table-bordered" id="entries-table">
                        <thead>
                            <tr>
                                <th class="col-date"><label class="required-label">Date</label></th>
                                <th class="col-project"><label class="required-label">Project</label></th>
                                <th class="col-activity"><label class="required-label">Activity</label></th>
                                <th class="col-task"><label class="required-label">Task</label></th>
                                <th class="col-members"><label class="required-label">Involved Members</label></th>
                                <th class="col-action" style="text-align:center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="entries-body">
                        </tbody>
                    </table>
                </div>

                <template id="entry-row-template">
                    <tr class="entry-row" data-row-index="__IDX__">
                        <td class="col-date">
                            <input type="text" name="entries[__IDX__][work_plan_date]" class="form-control wp-date"
                                placeholder="yyyy-mm-dd" readonly required />
                        </td>
                        <td class="col-project">
                            <select name="entries[__IDX__][project_id]" class="form-control select2 project-select"
                                required>
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}" data-activities='@json($project->activities->map(fn($a) => ['id' => $a->id, 'title' => $a->title]))'
                                        data-members='@json($project->allMembers()->map(fn($m) => ['id' => $m->id, 'name' => $m->full_name]))'>
                                        {{ $project->short_name ?: $project->title }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="col-activity">
                            <select name="entries[__IDX__][activity_id]" class="form-control select2 activity-select"
                                required>
                                <option value="">Select Activity</option>
                            </select>
                        </td>
                        <td class="col-task">
                            <textarea name="entries[__IDX__][planned_task]" class="form-control" rows="4" maxlength="500" required></textarea>
                        </td>
                        <td class="col-members">
                            <select name="entries[__IDX__][members][]" class="form-control select2 members-select" multiple
                                required>
                                <!-- Options populated dynamically -->
                            </select>
                        </td>
                        <td class="col-action text-center action-col">
                            <button type="button" class="btn btn-sm btn-outline-success add-entry"><i
                                    class="bi bi-plus-lg"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-entry"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </template>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <a href="{{ route('work-plan.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
