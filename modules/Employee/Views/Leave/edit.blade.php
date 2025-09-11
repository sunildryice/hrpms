<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Leave</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('employees.leaves.update', [$leave->employee_id, $leave->id]) !!}" method="post"
      enctype="multipart/form-data" id="leaveEditForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Name of leave</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" class="form-control" disabled value="{!! $leave->getLeaveType() !!}" />
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" disabled value="{!! $leave->leaveType->getLeaveBasis() !!}" />
                    </div>
                </div>

            </div>
        </div>
        @php
            $carryOver = ($leave->leaveType->leave_basis == 2 ? 8*$leave->leaveType->maximum_carry_over : $leave->leaveType->maximum_carry_over)
        @endphp
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Maximum Carryover (In {{ $leave->leaveType->getLeaveBasis() }})</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" class="form-control" name="maximum_carryover" readonly="readonly"
                       value="{{ $carryOver }}" placeholder="Maximum Carryover"/>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Opening Balance</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" min="1" class="form-control" name="opening_balance"
                       value="{{ $leave->opening_balance }}" placeholder="Opening Balance"/>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Earned</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" min="0" class="form-control" name="earned" value="{{ $leave->earned }}"
                       placeholder="Earned">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Lapsed</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" min="0" class="form-control" name="lapsed" value="{{ $leave->lapsed }}"
                       placeholder="Lapsed">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Taken</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" disabled value="{{ $leave->taken }}" />
                <input type="hidden" class="form-control" name="taken" value="{{ $leave->taken }}" />
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Balance</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="number" min="0" class="form-control balance" disabled value="{{ $leave->balance }}"
                       placeholder="Balance">
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="remarks" value="{{ $leave->remarks }}"
                       placeholder="Remarks">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
