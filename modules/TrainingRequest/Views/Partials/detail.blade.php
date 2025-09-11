<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span
                    class="card-subtitle text-uppercase text-primary">About</span></li>
            @if($trainingRequest->getTrainingRequestNumber())
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-wrench-adjustable dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {{ $trainingRequest->getTrainingRequestNumber() }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip"
                       title="Training Request Number"></a>
                </li>
            @endif
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-book-half dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $trainingRequest->title }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Course Name"></a>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-calendar3-range dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $trainingRequest->getDuration() }} <span
                            class="badge bg-primary">{{ $trainingRequest->getTotalDays() }}
                                                        Days</span></div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip"
                   title="Training Period"></a>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-clock dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $trainingRequest->own_time }}Hrs
                    </div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Own Time"></a>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-clock-fill dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $trainingRequest->work_time }}Hrs
                    </div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Work Time"></a>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-file-diff dropdown-item-icon"></i></div>
                    <div class="d-content-section"> {{ $trainingRequest->duration }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip"
                   title="Course Duration"></a>
            </li>
            @if(!in_array(\Route::getCurrentRoute()->getName(), ['training.requests.recommend.create', 'approve.training.request.create']))
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-currency-dollar dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $trainingRequest->course_fee }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Course Fee"></a>
                </li>
                @isset($trainingRequest->approved_amount)
                    <li class="position-relative">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="icon-section"><i
                                    class="bi-currency-dollar dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {{ $trainingRequest->approved_amount }}</div>
                        </div>
                        <a href="#" class="stretched-link" rel="tooltip" title="Approved Amount"></a>
                    </li>
                @endisset
            @endif
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-activity dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {{ $trainingRequest->getActivityCode() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Activity Code"></a>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-123 dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {{ $trainingRequest->getAccountCode() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Account Code"></a>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-chat-dots dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {{ $trainingRequest->description }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Description"></a>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {{ $trainingRequest->getCreatedBy() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Requester"></a>
            </li>

            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {{ $trainingRequest->getRecommenderName() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Recommender"></a>
            </li>
            @isset($trainingRequest->reviewer_id)
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-person-badge dropdown-item-icon"></i></div>
                        <div
                            class="d-content-section"> {{ $trainingRequest->getReviewerName() }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Reviewer"></a>
                </li>
            @endisset
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {{ $trainingRequest->getApproverName() }}</div>
                </div>
                <a href="#" class="stretched-link" rel="tooltip" title="Approver"></a>
            </li>
            @if(file_exists('storage/'.$trainingRequest->attachment) && $trainingRequest->attachment != '')
                <li class="position-relative">
                    <div class="d-flex gap-2 align-items-center">
                        <div class="icon-section"><i
                                class="bi-eye"></i></div>
                        <div class="d-content-section">
                            <a href="{!! asset('storage/'.$trainingRequest->attachment) !!}"
                               target="_blank"
                               title="View Attachment">Attachment
                            </a>
                        </div>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</div>
