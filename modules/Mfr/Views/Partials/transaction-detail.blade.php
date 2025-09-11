<div class="card-body">
    @isset($transaction)
        <div class="p-1">
            <ul class="mb-0 list-unstyled list-py-2 text-dark">
                <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-info-circle dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $transaction->getType() }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Type"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-person-bounding-box dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $transaction->getRequester() }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Requester"></span>
                </li>
                @isset($transaction->reviewer_id)
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-center">
                            <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {!! $transaction->getReviewer() !!} </div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Reviewer"></span>
                    </li>
                @endisset
                @isset($transaction->recommender_id)
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-center">
                            <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {!! $transaction->getRecommender() !!} </div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Recommender"></span>
                    </li>
                @endisset
                @isset($transaction->approver_id)
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-center">
                            <div class="icon-section"><i class="bi-person-badge dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {!! $transaction->getApprover() !!} </div>
                        </div>
                        <span class="stretched-link" rel="tooltip" title="Approver"></span>
                    </li>
                @endisset

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-calendar-minus dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $transaction->transaction_date->format('Y-m-d') }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Transaction Date"></a>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-box-arrow-in-down dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $transaction->release_amount }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Advance Released"></a>
                </li>

                @if ($transaction->transaction_type == 2)
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-center">
                            <div class="icon-section"><i class="bi-box-arrow-up dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {{ $transaction->expense_amount }}</div>
                        </div>
                        <a href="#" class="stretched-link" rel="tooltip" title="Mfr Expenditure"></a>
                    </li>
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-center">
                            <div class="icon-section"><i class="bi-arrow-repeat dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {{ $transaction->reimbursed_amount }}</div>
                        </div>
                        <a href="#" class="stretched-link" rel="tooltip" title="Expenditure Reimbursed"></a>
                    </li>
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-center">
                            <div class="icon-section"><i class="bi-question dropdown-item-icon"></i></div>
                            <div class="d-content-section">
                                {{ $transaction->expense_amount - $transaction->reimbursed_amount }}</div>
                        </div>
                        <a href="#" class="stretched-link" rel="tooltip" title="Questioned Cost"></a>
                    </li>
                @endif

                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Remarks</span></li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $transaction->remarks }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Remarks"></a>
                </li>
                @if ($transaction->expense_amount - $transaction->reimbursed_amount > 0)
                    <li class="position-relative">
                        <div class="gap-2 d-flex align-items-start">
                            <div class="icon-section"><i class="bi-chat-dots dropdown-item-icon"></i></div>
                            <div class="d-content-section"> {{ $transaction->question_remarks }}</div>
                        </div>
                        <a href="#" class="stretched-link" rel="tooltip" title="Question Cost comments"></a>
                    </li>
                @endif
            </ul>
        </div>
    @endisset
</div>
