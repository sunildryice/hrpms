<div class="card-body">
    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-card-text text-dark" title="{{ __('label.title') }}" aria-label="{{ __('label.title') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>{{ $project->title ?? '-' }}</div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-journal-text text-dark" title="{{ __('label.description') }}"
            aria-label="{{ __('label.description') }}" data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div class="text-wrap">{{ $project->description ? Str::limit($project->description, 180) : '-' }}</div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-person-badge text-dark" title="{{ __('label.team-lead') }}"
            aria-label="{{ __('label.team-lead') }}" data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>{{ isset($users) ? $users[$project->team_lead_id] ?? '-' : $project->teamLead->name ?? '-' }}</div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-person text-dark" title="{{ __('label.focal-person') }}"
            aria-label="{{ __('label.focal-person') }}" data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>
                {{ isset($users) ? $users[$project->focal_person_id] ?? '-' : $project->focalPerson->name ?? '-' }}
            </div>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-start gap-2">
        <i class="bi-people text-dark" title="{{ __('label.members') }}" aria-label="{{ __('label.members') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>
                @php($memberNames = $project->members->pluck('full_name')->filter()->implode(', '))
                {{ $memberNames ?: '-' }}
            </div>
        </div>
    </div>

    <div class="mb-0 d-flex align-items-start gap-2">
        <i class="bi-calendar-range text-dark"
            title="{{ __('label.start-date') }} - {{ __('label.completion-date') }}"
            aria-label="{{ __('label.start-date') }} - {{ __('label.completion-date') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            @php($sd = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null)
            @php($ed = $project->completion_date ? \Carbon\Carbon::parse($project->completion_date) : null)
            <div>
                {{ $sd ? $sd->format('M d, Y') : '-' }}
                @if ($sd || $ed)
                    -
                @endif
                {{ $ed ? $ed->format('M d, Y') : '-' }}
            </div>
            @if ($sd && $ed)
                @php($days = $sd->diffInDays($ed) + 1)
                <span class="badge bg-primary mt-1">{{ $days }} {{ Str::plural('Day', $days) }}</span>
            @endif
        </div>
    </div>

    <div class="mt-2 d-flex align-items-start gap-2">
        <i class="bi-flag text-dark" title="{{ __('label.stages') }}" aria-label="{{ __('label.stages') }}"
            data-bs-toggle="tooltip"></i>
        <div class="w-100">
            <div>
                @php($stageTitles = $project->stages->pluck('title')->filter()->implode(', '))
                {{ $stageTitles ?: '-' }}
            </div>
        </div>
    </div>
