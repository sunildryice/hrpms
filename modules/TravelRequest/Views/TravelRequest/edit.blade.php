<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="requestEditModal">Edit Travel Request Itinerary</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('travel.requests.update', $travelRequest->id) }}" id="travelRequestEditForm" method="post"
        enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationtraveltype" class="form-label">Travel Type
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="travel_type_id" class="select2 form-control" data-width="100%">
                    <option value="">Select a Travel Type</option>
                    @foreach($travelTypes as $travelType)
                        <option value="{{ $travelType->id }}"
                            {{ $travelType->id == $travelRequest->travel_type_id? "selected":"" }}>
                            {{ $travelType->title }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('travel_type_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="travel_type_id">
                            {!! $errors->first('travel_type_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationaccompanyingStaffs" class="form-label">Accompanying Staff</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="accompanying_staff[]" class="select2 form-control
                    @if($errors->has('accompanying_staff')) is-invalid @endif"
                    placeholder="Select Accompanying Staff..." autocomplete="off"
                    data-width="100%" multiple>
                    <option value="">Select Accompanying Staff...</option>
                    @foreach($accompanyingStaffs as $accompanyingStaffs)
                        <option value="{{ $accompanyingStaffs->id }}" @if (in_array($accompanyingStaffs->id, $selectedaccompanyingStaffs)) selected @endif>
                            {{ $accompanyingStaffs->full_name }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('accompanying_staff'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="accompanying_staff">
                            {!! $errors->first('accompanying_staff') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationPurposeofTravel" class="form-label">Purpose of Travel </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text"
                        class="form-control @if($errors->has('purpose_of_travel')) is-invalid @endif"
                        name="purpose_of_travel"
                        value="{{ $travelRequest->purpose_of_travel }}"
                        placeholder="Purpose of travel">
                @if($errors->has('purpose_of_travel'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div
                            data-field="purpose_of_travel">{!! $errors->first('purpose_of_travel') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationProject" class="form-label">Project
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="project_code_id" class="select2 form-control
                    @if($errors->has('project_code_id')) is-invalid @endif"
                    placeholder="Select a Project..." autocomplete="off" data-width="100%">
                    <option value="">Select a Project...</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $project->id == $travelRequest->project_code_id? "selected":"" }}>
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('project_code_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="project_code_id">
                            {!! $errors->first('project_code_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationProject" class="form-label">Substitute
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="substitute_id" class="select2 form-control
                    @if($errors->has('substitute_id')) is-invalid @endif"
                    placeholder="Select a Substitute..." autocomplete="off" data-width="100%">
                    <option value="">Select a Substitute...</option>
                    @foreach($substitutes as $substitute)
                        <option value="{{ $substitute->id }}" {{$substitute->id == $travelRequest->substitute_id? "selected":""}}>
                            {{ $substitute->full_name }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('substitute_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="substitute_id">
                            {!! $errors->first('substitute_id') !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationdd" class="form-label">Departure Date
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control
                    @if($errors->has('departure_date')) is-invalid @endif"
                        name="departure_date" value="{{ $travelRequest->departure_date }}" data-toggle="datepicker"/>
                {{-- <input type="hidden" value="{{ date('Y-m-d') }}" name="today"
                        class="form-control"/> --}}
                @if($errors->has('departure_date'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div
                            data-field="departure_date">{!! $errors->first('departure_date') !!}</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationreturndate" class="form-label">Return Date
                    </label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control
                    @if($errors->has('return_date')) is-invalid @endif"
                        name="return_date" value="{{ $travelRequest->return_date }}" data-toggle="datepicker"/>
                {{-- <input type="hidden" value="{{ date('Y-m-d') }}" name="today"
                        class="form-control"/> --}}
                    @if($errors->has('return_date'))
                        <div class="fv-plugins-message-container invalid-feedback">
                            <div
                                data-field="return_date">{!! $errors->first('return_date') !!}</div>
                        </div>
                    @endif
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationRemarks" class="form-label">Remarks </label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea type="text"
                        class="form-control @if($errors->has('remarks')) is-invalid @endif"
                        name="remarks">@if($travelRequest->remarks){{ $travelRequest->remarks }}@endif</textarea>
                @if($errors->has('remarks'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div
                            data-field="remarks">{!! $errors->first('remarks') !!}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
<script>
    $(".select2").select2({
        dropdownParent: $('.modal'),
        width: '100%',
        dropdownAutoWidth: true
    });
</script>
