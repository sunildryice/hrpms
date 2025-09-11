<div class="col-lg-12">
    <div class="card">
        <div class="card-header fw-bold">
            Approval Process
        </div>
        <div class="card-body c-b">
            @foreach ($workPlan->logs as $log)
                <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                    <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                        <i class="bi-person-circle fs-5"></i>
                    </div>
                    <div class="w-100">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2">
                                <label class="form-label mb-0">{{ $log->getCreatedBy() }}</label>
                                <span
                                    class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                            </div>
                            <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        <p class="text-justify comment-text mb-0 mt-1">
                            {{ $log->log_remarks }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
