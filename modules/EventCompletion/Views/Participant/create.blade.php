<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Event Completion Participants</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('event.completion.participants.store', $eventCompletion->id) !!}" method="post" enctype="multipart/form-data" id="participantsForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationName" class="form-label required-label">Name of Participant</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="name">
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validaionOffice" class="form-label required-label">Office</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="office">
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationDesignation" class="form-label ">Designation</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="designation">
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="vaildationContact" class="form-label ">Contact</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="input-group has-validation">
                        <input type="text" class="form-control" name="contact">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
