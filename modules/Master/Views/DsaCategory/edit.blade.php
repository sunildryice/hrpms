<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit DSA Category</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.dsa.categories.update',$dsaCategory->id) !!}" method="post"
      enctype="multipart/form-data" id="dsaCategoryForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.dsa-category') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{!! $dsaCategory->title !!}" placeholder="{{ __('label.dsa-category') }}" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.dsa-rate') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="rate" value="{!! $dsaCategory->rate !!}" placeholder="{{ __('label.dsa-rate') }}" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
