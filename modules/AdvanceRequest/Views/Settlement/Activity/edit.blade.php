<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModal1Label">Edit Activity for Advance Settlement</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('advance.settlement.activities.update', [$settlementActivity->advance_settlement_id, $settlementActivity->id]) !!}" method="post"
      enctype="multipart/form-data" id="AddSettlementActivityForm" autocomplete="off">
    <div class="modal-body">

          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="description">{{ $settlementActivity->description }}</textarea>
            </div>
        </div>



    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
