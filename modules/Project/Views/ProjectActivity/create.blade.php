<div class="modal fade" id="addProjectActivityModal" tabindex="-1" aria-labelledby="addProjectActivityLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0 fs-6" id="addProjectActivityLabel">Add Project Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ProjectActivityCreateForm" method="post" action="#" autocomplete="off">
                <div class="modal-body">
                    {!! csrf_field() !!}

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label required-label">Stage</label>
                            <select name="stage_id" class="select2 form-control" data-width="100%">
                                <option value="">Select Stage</option>
                                @foreach ($stages as $stage)
                                    <option value="{{ $stage->id }}">{{ $stage->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label required-label">Activity Level</label>
                            <select name="activity_level" class="select2 form-control" data-width="100%">
                                <option value="">Select Level</option>
                                @foreach ($activityLevels as $level)
                                    <option value="{{ $level->value }}">{{ ucfirst(strtolower($level->name)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label">Parent Activity (Optional)</label>
                            <select name="parent_activity_id" class="select2 form-control" data-width="100%">
                                <option value="">Select Parent Activity</option>
                                {{-- Populate dynamically if needed --}}
                            </select>
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label required-label">Activity Title</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="Enter activity title" />
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label required-label">Start Date</label>
                            <input type="text" name="start_date" class="form-control date" data-toggle="datepicker"
                                placeholder="yyyy-mm-dd" />
                        </div>

                        <div class="col-lg-6">
                            <label class="form-label required-label">End Date</label>
                            <input type="text" name="end_date" class="form-control date" data-toggle="datepicker"
                                placeholder="yyyy-mm-dd" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Activity</button>
                </div>
            </form>
        </div>
    </div>
</div>
