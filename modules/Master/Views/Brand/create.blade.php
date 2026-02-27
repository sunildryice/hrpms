<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6">Add Brand</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<form action="{{ route('master.brands.store') }}" method="post" id="brandForm">
    <div class="modal-body">
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label required-label">Brand Name</label>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" placeholder="Brand name" required>
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-lg-3 col-form-label">Description</label>
            <div class="col-lg-9">
                <textarea class="form-control" name="description" rows="3" placeholder="description"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    @csrf
</form>
