<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModal1Label">Add New Activity for Advance Settlement</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('advance.settlement.activities.store', $settlements->id) !!}" method="post"
      enctype="multipart/form-data" id="AddSettlementActivityForm" autocomplete="off">
    <div class="modal-body">

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                 <textarea rows="5" class="form-control" name="description"></textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>

