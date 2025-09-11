<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Project</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('project.exit.handover.note.update', [$exitHandOverNoteProject->handover_note_id, $exitHandOverNoteProject->id]) !!}" method="post"
      enctype="multipart/form-data" id="exitProjectForm" autocomplete="off">
    <div class="modal-body">

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Name of Project</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="project" class="form-control">{{$exitHandOverNoteProject->getProject()}}</textarea>
            </div>
        </div>

        {{--<div class="row mb-2">--}}
        {{--    <div class="col-lg-3">--}}
        {{--        <div class="d-flex align-items-start h-100">--}}
        {{--            <label for="" class="form-label ">Project Code</label>--}}
        {{--        </div>--}}
        {{--    </div>--}}
        {{--    <div class="col-lg-9">--}}
        {{--        <select class="form-control select2" data-width="100%" name="project_code_id">--}}
        {{--            <option value="">Select Project Code</option>--}}
        {{--            @foreach($projectCodes as $projectCode)--}}
        {{--                <option value="{!! $projectCode->id !!}"--}}
        {{--                @if($projectCode->id == $exitHandOverNoteProject->project_code_id) selected @endif>{{ $projectCode->getProjectCodeWithDescription() }}</option>--}}
        {{--            @endforeach--}}
        {{--        </select>--}}
        {{--    </div>--}}
        {{--</div>--}}
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Project Status</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="project_status" value="{{ $exitHandOverNoteProject->project_status }}" placeholder="Project Status">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Action Needed</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="action_needed" class="form-control">{{ $exitHandOverNoteProject->action_needed }}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Partners</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="partners" value="{{ $exitHandOverNoteProject->partners }}" placeholder="Partners">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Budget</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="budget" value="{{ $exitHandOverNoteProject->budget }}" placeholder="Budget">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Critical Issues</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="critical_issues" value="{{ $exitHandOverNoteProject->critical_issues }}" placeholder="Critical Issues">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
