<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="ProjectActivityDetailForm" method="post"
    action="{{ route('project-activity.details.update', $detail->id) }}" autocomplete="off">
    <div class="modal-body">
        {!! csrf_field() !!}
        @method('PUT')

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Key Accomplishments</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="key_accomplishment" class="form-control" rows="8">{{ $detail->key_accomplishment }}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Challenges</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="challenge" class="form-control" rows="8">{{ $detail->challenge }}</textarea>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0">Lessons Learned</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="lesson_learned" class="form-control" rows="8">{{ $detail->lesson_learned }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveDetailBtn">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('ProjectActivityDetailForm');
        if (!form) return;

        const fv = FormValidation.formValidation(form, {
            fields: {
                key_accomplishment: {
                    validators: {
                        notEmpty: { message: 'Key Accomplishments is required' },
                    },
                },

            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5(),
                icon: new FormValidation.plugins.Icon({
                    valid: 'bi bi-check2-square',
                    invalid: 'bi bi-x-lg',
                    validating: 'bi bi-arrow-repeat',
                }),
            },
        });

        $('#saveDetailBtn').on('click', function() {
            fv.validate().then(function(status) {
                if (status !== 'Valid') return;

                const formData = new FormData(form);

                const successCallback = function(response) {
                    $('#openModal').modal('hide');
                    toastr.success(response.message || 'Updated successfully');

                    if (window.activityDetails && response.detail) {
                        const existingIdx = window.activityDetails.findIndex(d => d.id === response.detail.id);
                        const entry = {
                            id: response.detail.id,
                            key_accomplishment: response.detail.key_accomplishment,
                            challenge: response.detail.challenge,
                            lesson_learned: response.detail.lesson_learned,
                        };
                        if (existingIdx !== -1) {
                            window.activityDetails[existingIdx] = entry;
                        } else {
                            window.activityDetails.push(entry);
                        }
                        window.renderDetailTable();
                    } else {
                        window.location.reload();
                    }
                };

                ajaxSubmitFormData(form.action, 'POST', formData, successCallback);
            });
        });
    })();
</script>
