<script type="text/javascript">
    $(function() {
        const $statusReasonModal = $('#statusReasonModal');
        const $outputDocumentRows = $('#outputDocumentRows');
        const $addOutputDocumentRowBtn = $('#addOutputDocumentRow');

        const createOutputDocumentRow = () => $(
            '<div class="output-doc-row d-flex flex-column flex-md-row align-items-start align-items-md-center mb-2">' +
            '<div class="flex-grow-1 d-flex flex-column flex-md-row align-items-start align-items-md-center">' +
            '<input type="text" class="form-control document-name-input mb-2 mb-md-0 me-md-2" name="" placeholder="Document name" required>' +
            '<input type="file" class="form-control document-file-input output-doc-input me-md-2" name="" accept="application/pdf,image/jpeg,image/png" required>' +
            '</div>' +
            '<button type="button" class="btn btn-outline-danger btn-sm remove-output-doc mt-2 mt-md-0 ms-md-2">' +
            '<i class="bi bi-trash"></i>' +
            '</button>' +
            '</div>'
        );

        const reindexDocumentRows = () => {
            $outputDocumentRows.children('.output-doc-row').each(function(index) {
                $(this).find('.document-name-input').attr('name', `documents[${index}][name]`);
                $(this).find('.document-file-input').attr('name', `documents[${index}][file]`);
            });
        };

        const refreshRowState = () => {
            reindexDocumentRows();
            const hasRemovableRows = $outputDocumentRows.children('.output-doc-row').length > 1;
            $outputDocumentRows.find('.remove-output-doc').prop('disabled', !hasRemovableRows);
        };

        const resetRows = () => {
            const $baseRow = $outputDocumentRows.children('.output-doc-row').first();
            $baseRow.find('input[type="text"]').val('');
            $baseRow.find('input[type="file"]').val('');
            $outputDocumentRows.children('.output-doc-row').not($baseRow).remove();
            refreshRowState();
        };

        $addOutputDocumentRowBtn.on('click', function() {
            $outputDocumentRows.append(createOutputDocumentRow());
            refreshRowState();
        });

        $outputDocumentRows.on('click', '.remove-output-doc', function() {
            if ($(this).is(':disabled')) {
                return;
            }
            $(this).closest('.output-doc-row').remove();
            refreshRowState();
        });

        $statusReasonModal.on('shown.bs.modal', function() {
            refreshRowState();
        }).on('hidden.bs.modal', function() {
            $('#status_reason').val('');
            resetRows();
        });
    });
</script>
