 <div class="card-header fw-bold">Members</div>
 <form action="{{ route('project.members.update', $project->id) }}" id="ProjectMemberEditForm" method="post"
     enctype="multipart/form-data" autocomplete="off">
     {!! csrf_field() !!}
     <div class="card-body">
         <div class="row mb-3">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label class="form-label required-label">{{ __('label.members') }}</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <select name="members[]"
                     class="select2 form-control @if ($errors->has('members')) is-invalid @endif" multiple
                     data-placeholder="Select Members" style="width: 100%">
                     @foreach ($users as $id => $name)
                         <option value="{{ $id }}" @if (in_array($id, old('members', $project->members->pluck('id')->toArray()))) selected @endif>
                             {{ $name }}</option>
                     @endforeach
                 </select>
                 @if ($errors->has('members'))
                     <div class="fv-plugins-message-container invalid-feedback">
                         <div data-field="members">{!! $errors->first('members') !!}</div>
                     </div>
                 @endif
             </div>
         </div>

         <div class="row mb-3">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label class="form-label required-label">{{ __('label.team-lead') }}</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <select name="team_lead_id"
                     class="select2 form-control @if ($errors->has('team_lead_id')) is-invalid @endif" data-width="100%">
                     <option value="">Select Team Lead</option>
                     @foreach ($users as $id => $name)
                         <option value="{{ $id }}" @if (old('team_lead_id', $project->team_lead_id) == $id) selected @endif>
                             {{ $name }}</option>
                     @endforeach
                 </select>
                 @if ($errors->has('team_lead_id'))
                     <div class="fv-plugins-message-container invalid-feedback">
                         <div data-field="team_lead_id">{!! $errors->first('team_lead_id') !!}</div>
                     </div>
                 @endif
                 <span class="form-text">Team Lead are selected from the selected member list.</span>
             </div>
         </div>

         <div class="row mb-3">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label class="form-label required-label">{{ __('label.focal-person') }}</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <select name="focal_person_id"
                     class="select2 form-control @if ($errors->has('focal_person_id')) is-invalid @endif" data-width="100%">
                     <option value="">Select Focal Person</option>
                     @foreach ($users as $id => $name)
                         <option value="{{ $id }}" @if (old('focal_person_id', $project->focal_person_id) == $id) selected @endif>
                             {{ $name }}</option>
                     @endforeach
                 </select>
                 <span class="form-text">Focal Person are selected from the selected member list.</span>
                 @if ($errors->has('focal_person_id'))
                     <div class="fv-plugins-message-container invalid-feedback">
                         <div data-field="focal_person_id">{!! $errors->first('focal_person_id') !!}</div>
                     </div>
                 @endif
             </div>
         </div>
     </div>
     <div class="card-footer border-0 justify-content-end d-flex gap-2">
         <button type="submit" class="btn btn-primary btn-sm">Update</button>
         <a href="{!! route('project.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
     </div>
 </form>
