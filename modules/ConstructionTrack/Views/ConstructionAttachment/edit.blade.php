<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModalLabel">Edit Attachment</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('construction.attachment.update', $constructionAttachment->id) !!}" method="POST" enctype="multipart/form-data" id="constructionAttachmentEditForm"
    autocomplete="off">
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
    <div class="modal-body">

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="title" class="form-label required-label">Title</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{{ $constructionAttachment->title }}"
                    placeholder="Attachment title">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="attachment" class="m-0">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" name="attachment" id="attachment">
                @if ($constructionAttachment->attachment)
                    <a class="btn btn-sm btn-outline-primary"
                        href="{{ asset('storage/' . $constructionAttachment->attachment) }}" target="_blank"
                        rel="tooltip" title="View attachment">
                        <i class="bi bi-file-earmark-text"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="attachment" class="form-label">Link</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="link" class="form-control" id="">{{$constructionAttachment->link}}</textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
