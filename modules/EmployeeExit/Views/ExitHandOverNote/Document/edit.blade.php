<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModal1Label">Edit
    Document  Request</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{route('document.exit.handover.note.update',
    [$exitHandOverNoteDocument->handover_note_id,$exitHandOverNoteDocument->id])}}" method="post"
      enctype="multipart/form-data" id="documentForm" autocomplete="off">
    <div class="modal-body">
         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Attachment Name</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="attachment_name" value="{{$exitHandOverNoteDocument->attachment_name}}" placeholder="Attachment Name">
            </div>
        </div>


        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                 <input type="file" class="form-control" name="attachment" value="{{$exitHandOverNoteDocument->attachment}}">
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
</form>

