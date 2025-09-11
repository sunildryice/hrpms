<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add LTA</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('lta.update', [$lta->id]) !!}" method="post" enctype="multipart/form-data" id="ltaForm" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Organization / Individual</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="supplier_id">
                    <option value="">Select Organization / Individual</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{!! $supplier->id !!}" @if ($lta->supplier_id == $supplier->id) selected @endif>
                            {{ $supplier->getSupplierNameandVAT() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="office_id" class="form-label required-label">Office</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" name="office_id" id="office_id" data-width="100%">
                    <option value="">Select office</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}" @if ($lta->office_id == $office->id) selected @endif>
                            {{ $office->getOfficeName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">{{ __('label.contract-number') }}</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="contract_number" value="{{ $lta->contract_number }}"
                    placeholder="{{ __('label.contract-number') }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Description</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="description">{{ $lta->description }}</textarea>
            </div>
        </div>



        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Contract Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control datepicker" readonly name="contract_date"
                    value="{{ $lta->contract_date->format('Y-m-d') }}" placeholder="Contact Date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Start Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly name="start_date"
                    value="{{ $lta->start_date->format('Y-m-d') }}" placeholder="Start Date">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">End Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" readonly name="end_date"
                    value="{{ $lta->end_date->format('Y-m-d') }}" placeholder="End Date">
            </div>
        </div>


        {{-- <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Contract Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="contract_amount" value=""
                    placeholder="Contract Amount">
            </div>
        </div> --}}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Focal Person</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select class="form-control select2" data-width="100%" name="focal_person_id">
                    <option value="">Select Focal Person</option>
                    @foreach ($employees as $employee)
                        <option value="{!! $employee->id !!}" @if ($lta->focal_person_id == $employee->id) selected @endif>
                            {{ $employee->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea rows="5" class="form-control" name="remarks">{{ $lta->remarks }}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationAttachment" class="m-0">Attachment</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="file" class="form-control" name="attachment" />
                <small>Supported file types jpeg/jpg/png/pdf and file size of upto 2MB.</small>
                @if (file_exists('storage/' . $lta->attachment) && $lta->attachment != '')
                    <a href="{!! asset('storage/' . $lta->attachment) !!}" target="_blank" class="fs-5" title="View Attachment">
                        <i class="bi bi-file-earmark-medical"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
