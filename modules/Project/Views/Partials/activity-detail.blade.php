<style>
    .bg-orange {
        background-color: #fd7e14;
        color: #fff;
    }
</style>

<div class="card-body">
    @php
        $latestLog = $projectActivity->latestStatusLog;
        $isNoRequiredStatus = $latestLog && $latestLog->new_status && $latestLog->new_status->value === 'no_required';
    @endphp
    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-card-text text-dark" title="{{ __('label.title') }}" aria -label="{{ __('label.title') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <span class="mb-1">{{ $projectActivity->title }}</span>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-flag text-dark" title="{{ __('label.stage') }}" aria-label="{{ __('label.stage') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>{{ $projectActivity->stage->title ?? '-' }}</div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-diagram-3 text-dark" title="{{ __('label.activity-level') }}"
            aria-label="{{ __('label.activity-level') }}" data-bs-toggle="tooltip"></i>
        <div>{{ ucfirst(str_replace('_', ' ', $projectActivity->activity_level ?? '-')) }}</div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-subtract text-dark" title="{{ __('label.parent-activity') }}"
            aria-label="{{ __('label.parent-activity') }}" data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>{{ $projectActivity->parent->title ?? '-' }}</div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-people text-dark" title="{{ __('label.members') }}" aria-label="{{ __('label.members') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>
                @php($memberNames = $projectActivity->members->pluck('full_name')->filter()->implode(', '))
                {{ $memberNames ?: '-' }}
            </div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-calendar-range text-dark" title="{{ __('label.start-date') }} - {{ __('label.end-date') }}"
            aria-label="{{ __('label.start-date') }} - {{ __('label.end-date') }}" data-bs-toggle="tooltip"></i>
        <div class="w-100">
            @php($sd = $projectActivity->start_date ? \Carbon\Carbon::parse($projectActivity->start_date) : null)
            <div>
                {{ $sd ? $sd->format('M d, Y') : '-' }} @if ($sd)
                    -
                @endif
                {{ $projectActivity->display_completion_date }}
            </div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        {{-- Status --}}
        <i class="bi-info-circle text-dark" title="{{ __('label.status') }}" aria-label="{{ __('label.status') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>

                <span class="badge {{ $projectActivity->statusBgColor() ?? 'badge bg-secondary' }}">
                    {{ $projectActivity->statusLabel() ?? '-' }}
                </span>
            </div>
        </div>
    </div>

    @if ($latestLog && $latestLog?->new_status->value === 'completed')
        {{-- Completed On --}}
        @php($labelText = $isNoRequiredStatus ? 'Completed On' : 'Completed On')
        <div class="mb-2 d-flex align-items-start gap-2">
            <i class="bi-check-circle text-dark" title="{{ $labelText }}" aria-label="{{ $labelText }}"
                data-bs-toggle="tooltip"></i>
            <div class="w-100">
                <div>{{ $latestLog->created_at ? $latestLog->created_at->format('M d, Y h:i A') : '-' }}</div>
            </div>
        </div>
    @endif

    @if ($latestLog && $latestLog->remarks)
        @php($labelText = $isNoRequiredStatus ? 'Reason' : 'Remarks')
        <div class="mb-2 d-flex align-items-start gap-2">
            <i class="bi-chat-left-text text-dark" title="{{ $labelText }}" aria-label="{{ $labelText }}"
                data-bs-toggle="tooltip"></i>
            <div class="w-100">
                <div class="fw-semibold small text-muted mb-1">{{ $labelText }}</div>
                <div>{{ $latestLog->remarks }}</div>
            </div>
        </div>
    @endif
</div>
