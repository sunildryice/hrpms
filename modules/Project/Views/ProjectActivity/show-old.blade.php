<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Show Project Activity</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    {!! csrf_field() !!}

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label required-label m-0">Title</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" name="title" readonly value="{{ old('title', $projectActivity->title) }}"
                class="form-control" />
        </div>
    </div>


    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label required-label m-0">Stage</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" name="activity_stage_id" readonly
                value="{{ old('activity_stage_id', $projectActivity->stage->title) }}" class="form-control" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label required-label m-0">Activity Level</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" name="activity_level" readonly
                value="{{ old('activity_level', ucfirst(strtolower($projectActivity->activity_level))) }}"
                class="form-control" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label m-0">Parent Activity</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" name="parent_activity_id" readonly
                value="{{ old('parent_id', $projectActivity->parent?->title ?? '-') }}" class="form-control" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label required-label m-0">Start Date</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" name="start_date"
                value="{{ old('start_date', $projectActivity->start_date->format('Y-m-d')) }}" readonly
                class="form-control" placeholder="yyyy-mm-dd" onfocus="this.blur()" autocomplete="off" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label required-label m-0">Completion Date</label>
            </div>
        </div>
        <div class="col-lg-9">
            <input type="text" name="completion_date"
                value="{{ old('completion_date', $projectActivity->completion_date->format('Y-m-d')) }}" readonly
                class="form-control" placeholder="yyyy-mm-dd" onfocus="this.blur()" autocomplete="off" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-lg-3">
            <div class="d-flex align-items-start h-100">
                <label class="form-label required-label m-0">Members</label>
            </div>
        </div>
        <div class="col-lg-9">
            @foreach ($projectActivity->members as $member)
                <span class="badge bg-secondary mb-1">{{ $member->full_name }}</span>
            @endforeach
        </div>
    </div>


</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
</div>
