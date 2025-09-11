<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Create LTA Item</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('lta.items.store', [$lta->id]) !!}" method="post" enctype="multipart/form-data" id="ltaItemFrom" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Item</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="item_id">
                    <option value="">Select Item</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}">{!! $item->getItemName() !!}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="unit_id">
                    <option value="">Select Unit</option>
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Unit Price </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="unit_price" value="" placeholder="Unit Price">
            </div>
        </div>

        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="m-0">{{ __('label.vat-applicable') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                        name="vat_applicable" @if (old('vat_applicable')) checked @endif>
                    <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                </div>
            </div>
        </div> --}}


        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="specification" class="m-0">{{ __('label.specification') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea class="form-control" name="specification" id="specification" rows="2"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
