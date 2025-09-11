<div class="card-body details-card">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div class="d-content-section">{{ $eventCompletion->getRequesterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Requester"></span>
            </li>
            @if ($eventCompletion->reviewer->id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $eventCompletion->getReviewerName() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
                </li>
            @endif
            @if ($eventCompletion->approver->id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {!! $eventCompletion->getApproverName() !!} </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Approver"></span>
                </li>
            @endif
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-map dropdown-item-icon"></i></div>
                    <div class="d-content-section">{!! $eventCompletion->getDistrictName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="District Name"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-geo-fill dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $eventCompletion->venue !!}</div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Venue"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $eventCompletion->getStartDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Program Start Date"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i class="bi-calendar-date dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {!! $eventCompletion->getEndDate() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Program End Date"></span>
            </li>
            @php
                $background = $eventCompletion->background;
                $objectives = $eventCompletion->objectives;
                $process = $eventCompletion->process;
                $closing = $eventCompletion->closing;
            @endphp
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary" >Background</span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section" >
                        <div id="initial-background" style="white-space: pre-line;">{!! strlen($background) > 100 ? substr($background, 0, 100) . '...' : substr($background, 0, 100) !!}
                        </div>
                        <div id="full-background" style="display: none; white-space: pre-line;">{!! $background !!}
                        </div>
                    </div>
                </div>
                <a href="#" class="stretched-link" id="background-section" rel="tooltip" title="Background"></a>

            </li>
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Objectives</span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section">

                        <div id="initial-objectives" style="white-space: pre-line;">{!! strlen($objectives) > 100 ? substr($objectives, 0, 100) . '...' : substr($objectives, 0, 100) !!}
                        </div>
                        <div id="full-objectives" style="display: none; white-space: pre-line;">{!! $objectives !!}
                        </div>
                    </div>

                </div>
                <a href="#" class="stretched-link" id="objectives-section" rel="tooltip" title="Objectives"></a>

            </li>
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Process</span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section">
                        <div id="initial-process" style="white-space: pre-line;">{!! strlen($process) > 100 ? substr($process, 0, 100) . '...' : substr($process, 0, 100) !!}
                        </div>
                        <div id="full-process" style="display: none; white-space: pre-line;">{!! $process !!}
                        </div>
                    </div>

                </div>
                <a href="#" class="stretched-link" id="process-section" rel="tooltip" title="Process"></a>

            </li>
            <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Closing</span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-start">
                    <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div class="d-content-section">
                        <div id="initial-closing" style="white-space: pre-line;">{!! strlen($closing) > 100 ? substr($closing, 0, 100) . '...' : substr($closing, 0, 100) !!}

                        </div>
                        <div id="full-closing" style="display: none; white-space: pre-line;">{!! $closing !!}
                        </div>
                    </div>
                </div>
                <a href="#" class="stretched-link" id="closing-section" rel="tooltip" title="Closing"></a>

            </li>
            @if (auth()->user()->can('print', $eventCompletion))
                <li class="position-relative mt-3">
                    <a href="{{ route('event.completion.print', $eventCompletion->id) }}" target="_blank"
                        title="Print ECR" class="btn btn-primary btn-sm"> <i class="bi-printer me-1"></i>
                        Print ECR</a>
                </li>
            @endif
        </ul>
    </div>
</div>
