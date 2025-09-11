<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModal1Label">Edit Activity</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{route('activity.exit.handover.note.update',[$exitHandOverNoteActivity->handover_note_id,$exitHandOverNoteActivity->id])}}" method="post"
      enctype="multipart/form-data" id="activityForm" autocomplete="off">
    <div class="modal-body">

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="activity" class="form-control">{{$exitHandOverNoteActivity->getActivity()}}</textarea>
            </div>
        </div>

        {{--<div class="row mb-2">--}}
        {{--    <div class="col-lg-3">--}}
        {{--        <div class="d-flex align-items-start h-100">--}}
        {{--            <label for="" class="form-label ">Activity</label>--}}
        {{--        </div>--}}
        {{--    </div>--}}
        {{--    <div class="col-lg-9">--}}
        {{--         <select class="form-control select2" data-width="100%" name="activity_code_id">--}}
        {{--            <option value="">Select Activity Code</option>--}}
        {{--             @foreach($activityCodes as $activityCode)--}}
        {{--                <option value="{!! $activityCode->id !!}" @if($exitHandOverNoteActivity->activity_code_id == $activityCode->id) selected @endif>{{ $activityCode->getActivityCodeWithDescription() }}</option>--}}
        {{--            @endforeach--}}
        {{--        </select>--}}
        {{--    </div>--}}
        {{--</div>--}}

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Organization</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="organization" value="{{$exitHandOverNoteActivity->organization}}" placeholder="Organization">
            </div>
        </div>

          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Phone</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="phone" value="{{$exitHandOverNoteActivity->phone}}" placeholder="Phone">
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Email</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="email" class="form-control" name="email" value="{{$exitHandOverNoteActivity->email}}" placeholder="Email">
            </div>
        </div>

          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Comments</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="comments" class="form-control">{{$exitHandOverNoteActivity->comments}}</textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>

