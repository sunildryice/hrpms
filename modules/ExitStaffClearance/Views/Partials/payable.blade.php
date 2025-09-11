<div class="card">
    <div class="card-header fw-bold">
        <span class="card-title d-flex justify-content-between">
            <div>
                <span class="fw-bold">B.</span>
                <span>
                    Payable
                </span>
            </div>
            <div>
                {{-- @if ($authUser->can('update', $staffClearance->employeeExitPayable)) --}}
                {{--     <a data-toggle="modal" class="btn btn-outline-primary btn-sm open-payable-modal-form" --}}
                {{--         href="{{ route('exit.payable.edit', $staffClearance->employeeExitPayable->id) }}" --}}
                {{--         rel="tooltip" title="Edit Employee Payable Request"><i class="bi-pencil-square"></i> --}}
                {{--         Update</a> --}}
                {{-- @endif --}}
            </div>

        </span>
    </div>
    <div class="card-body" id="payable-container">
        @include('ExitStaffClearance::Partials.Table.payable')
    </div>
</div>
