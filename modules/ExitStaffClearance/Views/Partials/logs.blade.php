@foreach ($staffClearance->logs()->orderBy('created_at', 'desc')->get() as $log)
    <div class="flex-row gap-2 py-2 mb-2 d-flex border-bottom">
        <div width="40" height="40" class="mr-3 rounded-circle user-icon">
            <i class="bi-person"></i>
        </div>
        <div class="w-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div
                    class="mb-2 d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-md-0">
                    <span class="me-2">{{ $log->createdBy->getFullName() }}</span>
                    <span class="badge bg-primary c-badge">
                        {!! $log->createdBy->employee->latestTenure->getDesignationName() !!}
                    </span>
                </div>
                <small title="{{ $log->created_at }}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
            </div>
            <p class="mt-1 mb-0 text-justify comment-text">
                {{ $log->log_remarks }}
            </p>
        </div>
    </div>
@endforeach
