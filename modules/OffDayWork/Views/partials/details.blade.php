<div class="card">
    <div class="card-header fw-bold">
        Off Day Work Request Details
    </div>
    <div class="card-body">
        <div class="p-1">
            <ul class="mb-0 list-unstyled list-py-2 text-dark">
                <li class="pb-2">
                    <span class="card-subtitle text-uppercase text-primary">About</span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-hash dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">{{ $offDayWork->getRequestId() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Request Number"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-person-bounding-box dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $offDayWork->requester->full_name ?? '-' }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Requester"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-diagram-3 dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            @php
                                $names = $offDayWork->getProjectNames();
                            @endphp
                            {{ implode(', ', $names) }}
                        </div>

                    </div>
                    <span class="stretched-link" rel="tooltip" title="Project"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-person-check dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $offDayWork->approver->full_name ?? '-' }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section">
                            <i class="bi-calendar3-range dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $offDayWork->getOffDayWorkDate() }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Duration"></span>
                </li>

                <li class="pt-4 pb-2">
                    <span class="card-subtitle text-uppercase text-primary">Deliverables</span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section">
                            <i class="bi-list-task dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{-- @foreach ($offDayWork->projects as $project)
                                <strong>{{ $project->short_name }}:</strong>
                                @php $deliverables = is_array($project->pivot->deliverables) ? $project->pivot->deliverables : json_decode($project->pivot->deliverables, true); @endphp
                                @if (!empty($deliverables))
                                    <ul class="list-unstyled mb-2">
                                        @foreach ($deliverables as $d)
                                            <li><i class="bi-dot"></i> {{ $d }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <em>No deliverables listed.</em>
                                @endif
                            @endforeach --}}

                            @if ($deliverables)
                                @foreach ($deliverables as $item)
                                    <strong>{{ $item['project_name'] ?? 'N/A' }}:</strong>
                                    @if (!empty($item['tasks']))
                                        <ul class="list-unstyled mb-2">
                                            @foreach ($item['tasks'] as $task)
                                                <li><i class="bi-dot"></i> {{ $task }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <em>No deliverables listed.</em>
                                    @endif
                                @endforeach
                            @else
                                <em>No deliverables listed.</em>
                            @endif
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Deliverables"></span>
                </li>

                <li class="pt-4 pb-2">
                    <span class="card-subtitle text-uppercase text-primary">
                        Reason for Off Day Work
                    </span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section">
                            <i class="bi-chat-dots dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {!! nl2br(e($offDayWork->reason)) !!}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Reason"></span>
                </li>

                <li class="pt-4 pb-2">
                    <span class="card-subtitle text-uppercase text-primary">Status</span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-info-circle dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            <span class="{!! $offDayWork->getStatusClass() !!}">
                                {!! $offDayWork->getStatus() !!}
                            </span>
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Status"></span>
                </li>
            </ul>
        </div>
    </div>
</div>
