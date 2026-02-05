<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Activity Update Period</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form action="{{ route('activity-update-periods.update', $accessPeriod->id) }}" method="POST"
        id="activityUpdatePeriodEditForm" autocomplete="off">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label required-label">Start Date</label>
                <input type="text" name="start_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" value="{{ $accessPeriod->start_date }}">
            </div>
            <div class="col-md-6">
                <label class="form-label required-label">End Date</label>
                <input type="text" name="end_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" value="{{ $accessPeriod->end_date }}">
            </div>
            <div class="col-12">
                <div class="form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1"
                        id="is_active" {{ $accessPeriod->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button type="submit" form="activityUpdatePeriodEditForm" class="btn btn-primary">Update</button>
</div>
