@extends('layouts.container')

@section('title', 'Create Timesheet')

@section('page_css')
    <!-- additional page level css (datepicker/select2) if any -->
    <style>
        /* position fv icons vertically centered within cell */
        .fv-plugins-icon {
            position: absolute !important;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
        }

        /* keep activity column fixed width in create form table */
        #entries-table {
            table-layout: fixed;
            width: 100%;
        }

        .col-activity {
            width: 20%;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-project {
            width: 15%;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-hours {
            width: 10%;
            overflow: hidden;
            word-wrap: break-word;
        }

        /* attachment column narrower */
        .col-attachment {
            width: 15%;
            overflow: hidden;
            word-wrap: break-word;
        }

        .col-action {
            width: 10%;
            overflow: hidden;
            word-wrap: break-word;
        }
    </style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            // highlight menu item if exists
            $('#navbarVerticalMenu').find('#timesheets-index').addClass('active');

            // initialise select2
            $('.select2').select2({
                placeholder: 'Select...',
                width: '100%'
            });

            const form = document.getElementById('TimeSheetForm');
            if (form) {
                // dynamic rows
                let rowIndex = 0;
                let fv; // will hold FormValidation instance

                function safeRevalidateField(fieldName) {
                    if (!fv || typeof fv.revalidateField !== 'function' || !fieldName) {
                        return;
                    }
                    // only attempt if the field actually exists
                    if (fv.getField && fv.getField(fieldName)) {
                        try {
                            fv.revalidateField(fieldName);
                        } catch (_e) {
                            console.warn(`Failed to revalidate field ${fieldName}:`, _e);
                        }
                    }
                }

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
                        // if only one row, disable remove button instead of hiding
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
                    // make sure the project dropdown starts blank (avoids Chrome autofill)
                    $row.find('.project-select').val('').trigger('change').trigger('change.select2');
                    // in case Chrome still tries to autofill on focus, clear again
                    $row.find('.project-select').on('focus', function () {
                        $(this).val('').trigger('change').trigger('change.select2');
                    });
                    const $activitySelect = $row.find('.activity-select');

                    $row.find('.select2').select2({
                        placeholder: 'Select...',
                        width: '100%'
                    });

                    // register validation fields for this row
                    if (fv) {
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
                        fv.addField(`entries[${idx}][hours_spent]`, {
                            validators: {
                                notEmpty: {
                                    message: 'Hours are required'
                                },
                                numeric: {
                                    message: 'Hours must be a number'
                                },
                                between: {
                                    min: 0.01,
                                    max: 24,
                                    message: 'Hours must be between 0.01 and 24'
                                }
                            }
                        });
                        fv.addField(`entries[${idx}][attachment]`, {
                            validators: {
                                file: {
                                    extension: 'png,jpg,pdf',
                                    type: 'image/png,image/jpeg,application/pdf',
                                    maxSize: 5097152,
                                    message: 'Attachment must be png/jpg/pdf and ≤5MB'
                                }
                            }
                        });
                    }

                    // cascade project -> activity for this row
                    $row.find('.project-select').on('change', function () {
                        $obj = $(this);
                        var projectId = $($obj).val();
                        $activitySelect.empty().append('<option value="">Select Activity</option>');
                        if (projectId) {
                            var url = "{{ route('api.projects.show', ['projectId']) }}".replace('projectId', projectId);
                            var htmlToReplaceActivity = '<option value="">Select Activity</option>';

                            var successCallback = function (response) {
                                response.assignedActivities.forEach(function (activity) {
                                    htmlToReplaceActivity += '<option value="' + activity.id +
                                        '">' + activity.title + '</option>';
                                });
                                $($obj).closest('tr').find('.activity-select').html(htmlToReplaceActivity);

                                $activitySelect.select2('destroy');
                                $activitySelect.select2({
                                    placeholder: 'Select Activity',
                                    width: '100%',
                                    dropdownParent: $(document.body)
                                });
                            }
                            var errorCallback = function (error) {
                                console.log(error);
                            }
                            ajaxNativeSubmit(url, 'GET', {}, 'json', successCallback, errorCallback);
                        }
                        const idx = $row.data('row-index');
                        safeRevalidateField(`entries[${idx}][project_id]`);
                        safeRevalidateField(`entries[${idx}][activity_id]`);
                    });
                }

                // add first row
                $('#entries-body').append(buildEntryRow(rowIndex));
                // initial row will be registered once fv created later
                const $first = $('#entries-body .entry-row').last();
                initRow($first);
                refreshActions();


                // global add button removed; adding handled via inline add buttons

                $(document).on('click', '.remove-entry', function () {
                    const $row = $(this).closest('tr');
                    if ($('#entries-body .entry-row').length > 1) {
                        // unregister validation for this row
                        if (fv) {
                            const idx = $row.data('row-index');
                            fv.removeField(`entries[${idx}][project_id]`);
                            fv.removeField(`entries[${idx}][activity_id]`);
                            fv.removeField(`entries[${idx}][hours_spent]`);
                            fv.removeField(`entries[${idx}][attachment]`);
                        }
                        $row.remove();
                        refreshActions();
                    }
                    // no need to revalidate a non-existent group field
                });

                // individual inputs are validated in their own change handlers above; nothing else required here
                $('#entries-body').on('change', 'select, input', function () {
                    // noop
                });

                // delegate add-entry button in rows
                $('#entries-body').on('click', '.add-entry', function () {
                    rowIndex++;
                    const $new = buildEntryRow(rowIndex);
                    $('#entries-body').append($new);
                    initRow($new);
                    refreshActions();
                    // new row handled individually by initRow
                });

                // existing validation fields: modify to include entries callback
                fv = FormValidation.formValidation(form, {
                    fields: {
                        timesheet_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The date is required'
                                },
                                date: {
                                    format: 'YYYY-MM-DD',
                                    message: 'The date is not a valid date'
                                }
                            }
                        },
                        attachment: {
                            validators: {
                                file: {
                                    extension: 'jpeg,jpg,png,pdf',
                                    type: 'image/jpeg,image/png,application/pdf',
                                    maxSize: '5097152',
                                    message: 'The selected file is not valid file or must not be greater than 5 MB.'
                                }
                            }
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5({
                            // treat table cells + form rows as containers
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

                // once fv exists, register any pre‑existing rows (first row)
                $('#entries-body .entry-row').each(function () {
                    initRow($(this));
                });

                // datepicker
                $('[name="timesheet_date"]').datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                    endDate: new Date(),
                    todayHighlight: true,
                    todayBtn: 'true'
                }).on('change', function () {
                    fv.revalidateField('timesheet_date');
                });

                // set today if empty
                if (!$('[name="timesheet_date"]').val().trim()) {
                    const today = new Date().toISOString().split('T')[0];
                    $('[name="timesheet_date"]').val(today);
                    $('[name="timesheet_date"]').datepicker('setDate', today);
                }
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
                        <li class="breadcrumb-item"><a href="{{ route('timesheet.index') }}"
                                                       class="text-decoration-none text-dark">Timesheet</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Create Timesheet</h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border rounded">
        <div class="card-body">
            <form action="{{ route('timesheet.store') }}" method="post" enctype="multipart/form-data" id="TimeSheetForm"
                  autocomplete="off">
                @csrf

                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class=" h-100">
                            <label class="form-label required-label m-0">Date</label>
                            <input type="text" name="timesheet_date" class="form-control" placeholder="yyyy-mm-dd"
                                   readonly onfocus="this.blur()" autocomplete="off"/>
                        </div>
                    </div>
                </div>

                <!-- entries table -->
                <div class="mb-3">
                    <label class="form-label required-label">Entries</label>
                    <table class="table table-bordered" id="entries-table">
                        <thead>
                        <tr>
                            <th class="reqired-label col-project">
                                <label class="required-label" for="">Project</label>
                            </th>
                            <th class="col-activity">
                                <label class="required-label" for="">Activity</label>
                            </th>
                            <th>
                                <label class="">Task / Description</label>
                            </th>
                            <th class="col-hours"><label class="required-label" for="">Hours</label></th>
                            <th class="col-attachment">Attachment</th>
                            <th class="col-action">Action</th>
                        </tr>
                        </thead>
                        <tbody id="entries-body">
                        <!-- initial row will be added by JS -->
                        </tbody>
                    </table>
                </div>

                <template id="entry-row-template">
                    <tr class="entry-row" data-row-index="__IDX__">
                        <td class="col-project">
                            <select name="entries[__IDX__][project_id]" autocomplete="off"
                                    class="form-control select2 project-select" required>
                                <option value="">Select Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                            data-activities='@json($project->activities->map(fn($a) => ['id' => $a->id, 'title' => $a->title]))'>
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
                        <td>
                            <textarea name="entries[__IDX__][description]" class="form-control" rows="4"
                                      required></textarea>
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0.01" max="24"
                                   name="entries[__IDX__][hours_spent]" class="form-control text-end" required/>
                        </td>
                        <td class="col-attachment">
                            <input type="file" name="entries[__IDX__][attachment]" class="form-control"/>
                        </td>
                        <td class="text-center action-col">
                            <button type="button" class="btn btn-sm btn-outline-success add-entry"><i
                                    class="bi bi-plus-lg"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-entry"><i
                                    class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </template>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <a href="{{ route('timesheet.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
