<script type="text/javascript">
    $(function() {
        const documentsListUrlTemplate = "{{ route('work-plan.detail-documents', ['detail' => '__ID__']) }}";
        const buildDocumentsUrl = (detailId) => documentsListUrlTemplate.replace('__ID__', detailId);

        const $documentsModal = $('#workPlanDocumentsModal');
        const $documentsTableBody = $('#workPlanDocumentsTableBody');

        const escapeHtml = (value) => $('<div>').text(value || '').html();

        const setDocumentsLoadingState = () => {
            $documentsTableBody.html(
                '<tr><td colspan="3" class="text-center text-muted py-4">Loading documents...</td></tr>'
            );
        };

        const renderDocumentRows = (documents) => {
            if (!documents || !documents.length) {
                $documentsTableBody.html(
                    '<tr><td colspan="3" class="text-center text-muted py-4">No documents uploaded yet.</td></tr>'
                );
                return;
            }

            const rows = documents.map((document) => {
                const uploadedAt = escapeHtml(document.uploaded_at || '-');
                const title = escapeHtml(document.title || 'Untitled Document');
                const viewUrl = document.view_url;
                const downloadUrl = document.download_url;

                return '<tr>' +
                    '<td>' + title + '</td>' +
                    '<td>' + uploadedAt + '</td>' +
                    '<td class="text-center">' +
                    '<a class="btn btn-outline-primary btn-sm me-1" href="' + viewUrl +
                    '" target="_blank" rel="noopener">' +
                    '<i class="bi bi-eye"></i> </a>' +
                    '<a class="btn btn-outline-secondary btn-sm" href="' + downloadUrl +
                    '" target="_blank" rel="noopener">' +
                    '<i class="bi bi-download"></i> </a>' +
                    '</td>' +
                    '</tr>';
            }).join('');

            $documentsTableBody.html(rows);
        };

        $(document).on('click', '.view-documents', function() {
            const detailId = $(this).data('id');

            if (!detailId) {
                toastr.error('Unable to locate work plan detail.');
                return;
            }

            setDocumentsLoadingState();
            $documentsModal.modal('show');

            $.getJSON(buildDocumentsUrl(detailId))
                .done(function(response) {
                    renderDocumentRows(response.documents || []);
                })
                .fail(function() {
                    toastr.error('Could not load documents.');
                    $documentsTableBody.html(
                        '<tr><td colspan="3" class="text-center text-danger py-4">Failed to load documents.</td></tr>'
                    );
                });
        });
    });
</script>
