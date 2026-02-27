<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Brand</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('master.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data" id="brandForm"
    autocomplete="off">

    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label">Brand Name</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{{ old('title', $brand->title) }}"
                    placeholder="Enter brand name" required>
                @error('title')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="description" rows="4" placeholder="description">{{ old('description', $brand->description) }}</textarea>
                @error('description')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>

    @csrf
    @method('PUT')
</form>
