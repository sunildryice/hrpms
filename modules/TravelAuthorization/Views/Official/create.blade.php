<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add Officials</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('ta.requests.official.store', $travel->id) !!}" method="post"
      enctype="multipart/form-data" id="officialForm" autocomplete="off">
    {{--    @dump($errors)--}}
    <div class="modal-body">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label required-label">{{ __('label.name') }}</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="text" class="form-control" name="name" autofocus="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label ">{{ __('label.post') }}</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="text" class="form-control" name="post" autofocus="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label ">{{ __('label.level') }}</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="text" class="form-control" name="level" autofocus="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="" class="form-label ">{{ __('label.office') }}</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="input-group has-validation">
                            <input type="text" class="form-control" name="office" autofocus="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label for="validationProject" class="form-label required-label">District
                        </label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <select name="district_id" class="select2 form-control"
                            data-width="100%">
                        <option value="">Select District</option>
                        @foreach($districts as $district)
                            <option
                                value="{{ $district->id }}" {{$district->id == old('district_id')? "selected":""}}>
                                {{ $district->district_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
