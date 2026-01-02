 <div class="card-header fw-bold">Project Information</div>
 <form action="{{ route('project.store') }}" id="ProjectAddForm" method="post" enctype="multipart/form-data"
     autocomplete="off">
     <div class="card-body">
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label class="form-label required-label">{{ __('label.title') }}</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="text" class="form-control @if ($errors->has('title')) is-invalid @endif"
                     name="title" value="{!! old('title') !!}" autofocus />
                 @if ($errors->has('title'))
                     <div class="fv-plugins-message-container invalid-feedback">
                         <div data-field="title">{!! $errors->first('title') !!}</div>
                     </div>
                 @endif
             </div>
         </div>

         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label class="form-label required-label">{{ __('label.description') }}</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <textarea class="form-control @if ($errors->has('description')) is-invalid @endif" name="description" rows="4">{!! old('description') !!}</textarea>
                 @if ($errors->has('description'))
                     <div class="fv-plugins-message-container invalid-feedback">
                         <div data-field="description">{!! $errors->first('description') !!}</div>
                     </div>
                 @endif
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label class="form-label required-label">{{ __('label.start-date') }}</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="text" data-toggle="datepicker"
                     class="form-control @if ($errors->has('start_date')) is-invalid @endif" name="start_date"
                     value="{!! old('start_date') !!}" placeholder="yyyy-mm-dd" onfocus="this.blur()" />
                 @if ($errors->has('start_date'))
                     <div class="fv-plugins-message-container invalid-feedback">
                         <div data-field="start_date">{!! $errors->first('start_date') !!}</div>
                     </div>
                 @endif
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label class="form-label required-label">{{ __('label.completion-date') }}</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="text" data-toggle="datepicker"
                     class="form-control @if ($errors->has('completion_date')) is-invalid @endif" name="completion_date"
                     value="{!! old('completion_date') !!}" placeholder="yyyy-mm-dd" onfocus="this.blur()" />
                 @if ($errors->has('completion_date'))
                     <div class="fv-plugins-message-container invalid-feedback">
                         <div data-field="completion_date">{!! $errors->first('completion_date') !!}</div>
                     </div>
                 @endif
             </div>
         </div>


         {!! csrf_field() !!}

     </div>
     <div class="card-footer border-0 justify-content-end d-flex gap-2">
         <button type="submit" class="btn btn-primary btn-sm">Save</button>
         {{-- <button class="btn btn-success btn-sm">Update</button> --}}
         <a href="{!! route('employees.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
     </div>
 </form>
