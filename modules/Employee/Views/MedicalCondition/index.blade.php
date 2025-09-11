<div class="card-header fw-bold">Medical Information</div>
@php
        $action = $employee->medicalCondition->created_at ? route('employees.medical.update', [$employee->id, $employee->medicalCondition->id]) :
            route('employees.medical.store', $employee->id);
@endphp

<form class="needs-validation" action="{{ $action }}" method="post" id="medicalConditionForm"
      enctype="multipart/form-data" autocomplete="off">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationBloodGroup" class="form-label required-label">Blood Group</label>
                </div>
            </div>
            <div class="col-lg-9">
                <select name="blood_group_id" data-width="100%" class="select2">
                    <option value="">Select a Blood Group</option>
                    @foreach($bloodGroups as $bloodGroup)
                        <option value="{!! $bloodGroup->id !!}" @if($bloodGroup->id == $employee->medicalCondition->blood_group_id) selected @endif>{!! $bloodGroup->title !!}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationMedicalCondition" class="form-label">Medical Condition</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control check-length"  name="medical_condition"
                       value="{{ old('medical_condition') ?: $employee->medicalCondition->medical_condition }}"
                       placeholder="Medical Condition" maxlength="100">
                       <span class="d-flex align-items-center justify-content-end"><small>Remaining character: <strong><span class="text-count">100</span></strong></small> </span>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="validationRemarks" class="form-label">Remarks </label>
                </div>

            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="remarks"
                       value="{{ old('remarks') ?: $employee->medicalCondition->remarks }}" placeholder="Remarks">
            </div>
        </div>


    </div>
    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        @if($employee->medicalCondition->created_at)
            <button type="submit" class="btn btn-primary btn-sm">Update</button>
            {!! method_field('PUT') !!}
        @else
            <button class="btn btn-primary btn-sm">Save</button>
        @endif
    </div>
    {!! csrf_field() !!}
</form>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            const fv = FormValidation.formValidation(document.getElementById('medicalConditionForm'), {
                fields: {
                    blood_group_id: {
                        validators: {
                            notEmpty: {
                                message: 'Blood group is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),

                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),
                },
            });

            $('#medicalConditionForm').on('change', function (){
               fv.revalidateField('blood_group_id');
            });
        });

        $('.check-length').on('keyup',function(){
            const str = $(this).val().length;
            const max = $(this).attr("maxlength");
            const result = max - str;
            $('.text-count').text(result);
        });
    </script>
@endpush
