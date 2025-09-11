@push('scripts')
<script>
    // Start - Attachment Scripts Section
    var attachmentTable = $('#attachmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{!! route('attachments.list', http_build_query(['modelType' => $modelType, 'modelId' => $modelId])) !!}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'attachment',
                    name: 'attachment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'link',
                    name: 'link',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    // End - Attachment Scripts Section
</script>
@endpush


    <div class="card">
        <div class="card-header fw-bold"> Attachments</div>
        <div class="container-fluid-s">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="attachmentTable">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col">Title</th>
                                <th scope="col" style="width: 150px">Attachment</th>
                                <th scope="col" style="width: 150px">Link</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
