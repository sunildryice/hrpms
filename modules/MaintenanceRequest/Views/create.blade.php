<div class="modal-header bg-primary text-white">
    <div class="modal-title text-uppercase fw-bold" id="openModalLabel">Add Maintenance Request</div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('maintenance.requests.store') !!}" method="post" enctype="multipart/form-data" id="maintenanceAddForm"
    autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <label class="form-label required-label">{{ __('label.request-date') }}
                </label>
            </div>
            <div class="col-lg-9">
                <input name="request_date" type="text" value="{{ date('Y-m-d') }}" class="form-control" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm" name="btn" value="save">Save</button>
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Close</button>
    </div>
    {!! csrf_field() !!}
</form>
<script>
