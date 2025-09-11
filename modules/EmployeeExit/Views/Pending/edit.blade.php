<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Update Employee Exit</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('employee.exit.pending.update', $exitHandoverNote->id) !!}" method="POST"
    id="pendingEmployeeExitUpdateForm" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="skip_exit_handover_note" class="m-0">Close Exit Handover Note</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                        id="skip_exit_handover_note" name="skip_exit_handover_note"
                        {{$exitHandoverNote->status_id == config('constant.CLOSED_STATUS') ? 'checked disabled' : ''}}>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="skip_exit_handover_note_remarks" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-6">
                <textarea class="form-control" name="skip_exit_handover_note_remarks" id="skip_exit_handover_note_remarks" rows="2" placeholder="Remarks">{{ $exitHandoverNote->remarks }}</textarea>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="skip_exit_interview" class="m-0">Close Exit Interview</label>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch"
                        id="skip_exit_interview" name="skip_exit_interview"
                        {{$exitHandoverNote->exitInterview->status_id == config('constant.CLOSED_STATUS') ? 'checked disabled' : ''}}>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="skip_exit_interview_remarks" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-6">
                <textarea class="form-control" name="skip_exit_interview_remarks" id="skip_exit_interview_remarks" rows="2" placeholder="Remarks">{{ $exitHandoverNote->exitInterview->remarks }}</textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>
