 <div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Construction Party</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('construction.parties.update', [$constructionParty->id]) !!}" method="post"
      enctype="multipart/form-data" id="constructionPartyForm" autocomplete="off">
    <div class="modal-body">

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Name</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="party_name" value="{{ old('party_name')?: $constructionParty->party_name }}" {{$constructionParty->deletable == 0 ? 'readonly' : ''}} placeholder="Party Name">
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Construction Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="contribution_amount" value="{{ old('contribution_amount')?: $constructionParty->contribution_amount }}" placeholder="Construction Amount">
            </div>
        </div>

          {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Construction Percentage</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="contribution_percentage" value="{{ old('contribution_percentage')?: $constructionParty->contribution_percentage }}" placeholder="Construction Percentage">
            </div>
        </div> --}}


    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! method_field('PUT') !!}
    {!! csrf_field() !!}
</form>
