<div class="card-body">
    @php
        if (!isset($agreement) && isset($transaction)) {
            $agreement = $transaction->agreement;
        }
    @endphp
    @isset($agreement)
        <div class="p-1">
            <ul class="mb-0 list-unstyled list-py-2 text-dark">
                <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section">
                            <i class="bi-building-dash dropdown-item-icon"></i>
                        </div>
                        <div class="d-content-section">
                            {{ $agreement->partnerOrganization->name }}
                        </div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="Partner Organization"></span>
                </li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-geo dropdown-item-icon"></i></div>
                        <div class="d-content-section">{{ $agreement->district->district_name }}</div>
                    </div>
                    <span class="stretched-link" rel="tooltip" title="District"></span>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-hash dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $agreement->grant_number }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Grant Number"></a>
                </li>

                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-center">
                        <div class="icon-section"><i class="bi-calendar-minus dropdown-item-icon"></i></div>

                        <div class="d-content-section"> {{ $agreement->getEffectiveFromDate() }} -
                            {{ $agreement->getEffectiveToDate() }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Agreement Period"></a>
                </li>

                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Approved Budget</span></li>
                <li class="position-relative">
                    <div class="gap-2 d-flex align-items-start">
                        <div class="icon-section"><i class="bi-cash dropdown-item-icon"></i></div>
                        <div class="d-content-section"> {{ $agreement->getApprovedBudget() }}</div>
                    </div>
                    <a href="#" class="stretched-link" rel="tooltip" title="Approved Budget"></a>
                </li>
            </ul>
        </div>
    @endisset
</div>
