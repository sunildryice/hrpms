<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const statusForm = document.getElementById('activityStatusForm');
        if (!statusForm) {
            return;
        }

        const $activityStatusModal = $('#activityStatusModal');
        const $activityStatusMessage = $('#activityStatusMessage');
        const $activityStatusDate = $('#activityStatusDate');
        const $activityStatusValue = $('#activityStatusValue');
        const $activityDocumentSection = $('#activityOutputDocumentsSection');
        const $activityDocumentRows = $('#activityOutputDocumentRows');
        const $addActivityDocumentRowBtn = $('#addActivityDocumentRow');
        const DOCUMENT_REQUIRED_STATUSES = ['completed', 'no_required'];

        const DOCUMENT_ALLOWED_TYPES = 'application/pdf,image/jpeg,image/png';
        const DOCUMENT_ALLOWED_EXTENSIONS = 'pdf,jpg,jpeg,png';
        const DOCUMENT_MAX_SIZE = 5 * 1024 * 1024; // 5MB
        const documentFieldRegistry = new WeakMap();
        let fvStatus;

        const shouldRequireDocuments = (status) => DOCUMENT_REQUIRED_STATUSES.includes(status);

        const createActivityDocumentRow = () => $(
            '<div class="activity-doc-row d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-2">' +
            '<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 w-100">' +
            '<div class="fv-row flex-grow-1 w-100">' +
            '<input type="text" class="form-control document-name-input" placeholder="Document name">' +
            '</div>' +
            '<div class="fv-row flex-grow-1 w-100">' +
            '<input type="file" class="form-control document-file-input" accept="application/pdf,image/jpeg,image/png">' +
            '</div>' +
            '</div>' +
            '<button type="button" class="btn btn-outline-danger btn-sm remove-activity-output-doc mt-2 mt-md-0">' +
            '<i class="bi bi-trash"></i>' +
            '</button>' +
            '</div>'
        );

        const unregisterDocumentValidators = ($row) => {
            const registry = documentFieldRegistry.get($row[0]);
            if (registry && fvStatus) {
                fvStatus.removeField(registry.nameField);
                fvStatus.removeField(registry.fileField);
            }

            documentFieldRegistry.delete($row[0]);
            $row.find('.document-name-input').removeAttr('name').prop('required', false);
            $row.find('.document-file-input').removeAttr('name').prop('required', false);
        };

        const registerDocumentValidators = ($row, index) => {
            unregisterDocumentValidators($row);

            const nameField = `documents[${index}][name]`;
            const fileField = `documents[${index}][file]`;
            const $nameInput = $row.find('.document-name-input');
            const $fileInput = $row.find('.document-file-input');

            $nameInput.attr('name', nameField).prop('required', true);
            $fileInput.attr('name', fileField).prop('required', true);

            if (fvStatus) {
                fvStatus.addField(nameField, {
                    validators: {
                        notEmpty: {
                            message: 'Document name is required.',
                        },
                    },
                });

                fvStatus.addField(fileField, {
                    validators: {
                        notEmpty: {
                            message: 'Document file is required.',
                        },
                        file: {
                            extension: DOCUMENT_ALLOWED_EXTENSIONS,
                            type: DOCUMENT_ALLOWED_TYPES,
                            maxSize: DOCUMENT_MAX_SIZE,
                            message: 'File must be PDF/JPG/PNG and under 5MB.',
                        },
                    },
                });
            }

            documentFieldRegistry.set($row[0], {
                nameField,
                fileField
            });
        };

        const refreshActivityDocumentRows = (documentsEnabled) => {
            $activityDocumentRows.children('.activity-doc-row').each(function(index) {
                const $row = $(this);

                if (documentsEnabled) {
                    registerDocumentValidators($row, index);
                } else {
                    unregisterDocumentValidators($row);
                }
            });

            const allowRemoval = documentsEnabled && $activityDocumentRows.children('.activity-doc-row')
                .length > 1;
            $activityDocumentRows.find('.remove-activity-output-doc').prop('disabled', !allowRemoval);
        };

        const resetActivityDocumentRows = () => {
            $activityDocumentRows.children('.activity-doc-row').each(function() {
                unregisterDocumentValidators($(this));
            });
            const $rows = $activityDocumentRows.children('.activity-doc-row');
            const $baseRow = $rows.first();
            $baseRow.find('.document-name-input').val('');
            $baseRow.find('.document-file-input').val('');
            $rows.not($baseRow).remove();
        };

        const toggleActivityDocumentSection = (status) => {
            const enableDocuments = shouldRequireDocuments(status);
            if (!enableDocuments) {
                resetActivityDocumentRows();
            }
            $activityDocumentSection.toggle(enableDocuments);
            refreshActivityDocumentRows(enableDocuments);
        };
        fvStatus = FormValidation.formValidation(statusForm, {
            fields: {
                status_date: {
                    validators: {
                        notEmpty: {
                            message: 'Date is required.'
                        }
                    }
                },
                remarks: {
                    validators: {
                        notEmpty: {
                            message: 'Reason is required.',
                            enabled: false,
                        }
                    }
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: '',
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),
            },
        }).on('core.form.valid', function() {
            const url = $activityStatusModal.data('url');
            const statusValue = $activityStatusValue.val();
            const dateValue = $activityStatusDate.val().trim();

            if (!url || !statusValue) {
                return;
            }

            const formData = new FormData(statusForm);
            formData.set('status', statusValue);
            formData.set('remarks', $activityStatusMessage.val().trim());
            formData.set('status_date', dateValue);

            ajaxSubmitFormData(url, 'POST', formData, function(response) {
                $activityStatusModal.data('updated', true);
                $activityStatusModal.modal('hide');
                toastr.success(response.message, 'Success');
                $('#projectActivityTable').DataTable().ajax.reload(null, false);
            });
        });

        const updateStatusRemarksValidator = (status) => {
            const requireRemarks = status === 'completed' || status === 'no_required';
            fvStatus.enableValidator('remarks', 'notEmpty', requireRemarks);
            if (!requireRemarks) {
                fvStatus.resetField('remarks', true);
            }
        };

        $addActivityDocumentRowBtn.on('click', function() {
            const $row = createActivityDocumentRow();
            $activityDocumentRows.append($row);
            refreshActivityDocumentRows(shouldRequireDocuments($activityStatusValue.val()));
        });

        $activityDocumentRows.on('click', '.remove-activity-output-doc', function() {
            if ($(this).is(':disabled')) {
                return;
            }
            const $row = $(this).closest('.activity-doc-row');
            unregisterDocumentValidators($row);
            $row.remove();
            refreshActivityDocumentRows(shouldRequireDocuments($activityStatusValue.val()));
        });

        $('#projectActivityTable').on('focus', '.activity-status-change', function() {
            $(this).data('previous', this.value);
        });

        $('#projectActivityTable').on('change', '.activity-status-change', function() {
            const $select = $(this);
            const newStatus = $select.val();
            const oldStatus = $select.data('previous');
            const activityId = $select.data('activity-id');

            if (!activityId) {
                return;
            }

            const url = "{{ url('/project-activity') }}" + '/' + activityId + '/status';

            if (['no_required', 'completed', 'under_progress'].includes(newStatus)) {
                let title;
                let label;
                let dateLabel;

                if (newStatus === 'no_required') {
                    title = 'Mark activity as Not Required';
                    label = 'Reason';
                    dateLabel = 'Completion Date';
                } else if (newStatus === 'completed') {
                    title = 'Mark activity as Completed';
                    label = 'Remarks';
                    dateLabel = 'Completion Date';
                } else {
                    title = 'Mark activity as Under Progress';
                    label = 'Remarks';
                    dateLabel = 'Actual Start Date';
                }

                $activityStatusModal.data('url', url);
                $activityStatusModal.data('select', $select);
                $activityStatusModal.data('oldStatus', oldStatus);
                $activityStatusModal.data({
                    status: newStatus,
                    updated: false,
                });

                fvStatus.resetForm(true);
                updateStatusRemarksValidator(newStatus);
                toggleActivityDocumentSection(newStatus);
                $activityStatusValue.val(newStatus);

                $('#activityStatusModalLabel').text(title);
                $('#activityStatusMessageLabel')
                    .text(label)
                    .toggleClass('required-label', newStatus !== 'under_progress');
                $('#activityStatusDateLabel').text(dateLabel);

                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                $activityStatusDate.val(formattedDate);

                $activityStatusDate.datepicker('destroy');
                $activityStatusDate.datepicker({
                    language: 'en-GB',
                    autoHide: true,
                    format: 'yyyy-mm-dd',
                    zIndex: 2048,
                });

                if (newStatus === 'under_progress') {
                    $('#activityStatusMessageContainer').hide();
                } else {
                    $('#activityStatusMessageContainer').show();
                }

                $activityStatusModal.modal('show');
            } else {
                ajaxSubmit(url, 'POST', {
                    status: newStatus
                }, function(response) {
                    toastr.success(response.message || 'Status updated successfully');
                    $('#projectActivityTable').DataTable().ajax.reload(null, false);
                });
            }
        });

        $activityStatusDate.on('change', function() {
            fvStatus.revalidateField('status_date');
        });

        $activityStatusMessage.on('input', function() {
            fvStatus.revalidateField('remarks');
        });

        $activityStatusModal.on('hidden.bs.modal', function() {
            const updated = $activityStatusModal.data('updated');
            const $select = $activityStatusModal.data('select');
            const oldStatus = $activityStatusModal.data('oldStatus');

            fvStatus.resetForm(true);
            $activityStatusValue.val('');
            toggleActivityDocumentSection(null);

            if (!updated && $select && typeof oldStatus !== 'undefined') {
                $select.val(oldStatus);
            }
        });

        $activityStatusModal.on('hide.bs.modal', function() {
            const activeElement = document.activeElement;
            if (activeElement && this.contains(activeElement)) {
                activeElement.blur();
            }
        });
    });
</script>
