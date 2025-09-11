<div class="card-body">
    <div class="p-1">
        <ul class="list-unstyled list-py-2 text-dark mb-0">
            <li class="pb-2"><span
                    class="card-subtitle text-uppercase text-primary">About</span></li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-bounding-box dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section">{{ $travelReport->getReporterName() }}</div>
                </div>
                <span class="stretched-link" rel="tooltip"
                      title="Requester"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-person-badge dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $travelReport->getApproverName() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Approver"></span>
            </li>
            <li class="position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <div class="icon-section"><i
                            class="bi-stack dropdown-item-icon"></i></div>
                    <div
                        class="d-content-section"> {!! $travelReport->getStatus() !!} </div>
                </div>
                <span class="stretched-link" rel="tooltip" title="Report Status"></span>
            </li>
        </ul>
    </div>
</div>

