<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Project Activity</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="ProjectActivityCreateForm" method="post" action=""
    autocomplete="off">

    <div class="modal-body">
        {!! csrf_field() !!}

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Stage</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="stage_id" class="select2 form-control" data-width="100%">
                    <option value="">Select Stage</option>
                    @foreach ($stages as $stage)
                        <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Activity Level</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="activity_level" class="select2 form-control" data-width="100%">
                    <option value="">Select Level</option>
                    @foreach ($activityLevels as $level)
                        <option value="{{ $level->value }}">{{ ucfirst(strtolower($level->name)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Parent Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="parent_activity_id" class="select2 form-control" data-width="100%">
                    <option value="">Select Parent Activity (Optional)</option>
                    {{-- Will be populated dynamically if needed --}}
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Start Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="start_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">End Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="end_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" />
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>
