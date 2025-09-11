<div class="card">
    <div class="card-header fw-bold">
        <span class="card-title">
            <span>
                Employee Details
            </span>
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Name of Leaving Staff</span>
                    </div>
                    <div class="col-lg-6">
                        <span>{{ $staffClearance->getEmployeeName() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Designation</span>
                    </div>
                    <div class="col-lg-6">
                        <span>{{ $staffClearance->employee->getDesignationName() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Duty Station</span>
                    </div>
                    <div class="col-lg-6">
                        <span>{{ $staffClearance->employee->getDutyStation() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Resigned Date</span>
                    </div>
                    <div class="col-lg-6">
                        <span>{{ $staffClearance->getResignationDate() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Date Joined to OHW</span>
                    </div>
                    <div class="col-lg-6">
                        <span>{{ $staffClearance->employee->getFirstJoinedDate() }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Last Working Date</span>
                    </div>
                    <div class="col-lg-6">
                        <span>{{ $staffClearance->getLastDutyDate() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Handover Note Status:</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="{{ $staffClearance->handoverNote->getStatusClass() }}">
                            {{ $staffClearance->handoverNote->getStatus() }}</span>
                        <a href="{{ route('approved.exit.handover.note.show', $staffClearance->handoverNote?->id) }}"
                            @class(['text-decoration-none', 'mx-1'])>
                            <i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Exit Interview Status</span>
                    </div>
                    <div class="col-lg-6">
                        <span class="{{ $staffClearance->exitInterview->getStatusClass() }}">
                            {{ $staffClearance->exitInterview->getStatus() }}</span>
                        @can('hr-staff-clearance')
                            <a href="{{ route('approved.exit.interview.show', $staffClearance->exitInterview?->id) }}"
                                @class(['text-decoration-none', 'mx-1'])>
                                <i class="bi bi-box-arrow-up-right"></i></a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-4">
                        <span class="fw-bold">Asset Handover Status</span>
                    </div>
                    <div class="col-lg-6">
                        {{-- @if ($statusString = $staffClearance->employee->getAssetHandoverStatus()) --}}
                        {{--     {!! $statusString !!} --}}
                        {{-- @else --}}
                            <span class="{{ $staffClearance->exitAssetHandover->getStatusClass() }}">
                                {{ $staffClearance->exitAssetHandover->getStatus() }}</span>
                        {{-- @endif --}}
                        <a href="{{ route('approved.exit.handover.asset.show', $staffClearance->exitAssetHandover?->id) }}"
                            @class(['text-decoration-none', 'mx-1'])>
                            <i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</div>
