<script type="text/javascript">
    $(function() {
        const $statusReasonModal = $('#statusReasonModal');

        const resetFields = () => {
            $('#status_reason').val('');
            $('#status_detail_id').val('');
            $('#status_value').val('');
        };

        $statusReasonModal.on('hidden.bs.modal', resetFields);
    });
</script>
