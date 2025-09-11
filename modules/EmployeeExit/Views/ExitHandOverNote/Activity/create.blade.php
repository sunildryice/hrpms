<div class="text-white modal-header bg-primary">
    <h5 class="mb-0 modal-title fs-6" id="openModal1Label">Add New Activity</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('exit.handover.activity.note.store', [$exitHandOverNote->id]) }}" method="post"
    enctype="multipart/form-data" id="activityForm" autocomplete="off">
    <div class="modal-body">

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Activity</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="activity" class="form-control"></textarea>
            </div>
        </div>

        {{--<div class="mb-2 row">--}}
        {{--    <div class="col-lg-3">--}}
        {{--        <div class="d-flex align-items-start h-100">--}}
        {{--            <label for="" class="form-label ">Activity</label>--}}
        {{--        </div>--}}
        {{--    </div>--}}
        {{--    <div class="col-lg-9">--}}
        {{--        <select class="form-control select2" data-width="100%" name="activity_code_id">--}}
        {{--            <option value="">Select Activity Code</option>--}}
        {{--            @foreach ($activityCodes as $activity)--}}
        {{--                <option value="{!! $activity->id !!}">{{ $activity->getActivityCodeWithDescription() }}--}}
        {{--                </option>--}}
        {{--            @endforeach--}}
        {{--        </select>--}}
        {{--    </div>--}}
        {{--</div>--}}

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Organization</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="organization" value=""
                    placeholder="Organization">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Phone</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="phone" value="" placeholder="Phone">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Email</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="email" class="form-control" name="email" value="" placeholder="Email">
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Comments</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="comments" class="form-control"></textarea>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
