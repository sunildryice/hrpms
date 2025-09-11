<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">View Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="display table table-bordered table-condensed" id="purchase-order-detail-table">
        <tr>
            <td class="gray-bg" width="15%">Full Name</td>
            <td class="full_name">{!! $log->getUserFullName() !!}</td>
            <td class="gray-bg" width="15%">Email Address</td>
            <td class="email_address">{!! $log->getUserEmail() !!}</td>
        </tr>
        <tr>
            <td class="gray-bg" width="15%">IP Address</td>
            <td class="ip_address">{!! $log->ip_address !!}</td>
            <td class="gray-bg" width="15%">Created At</td>
            <td class="created_at">{!! $log->getCreatedAt() !!}</td>
        </tr>
        <tr>
            <td class="gray-bg" width="15%">Before Details</td>
            <td class="before_details" colspan="3">{!! $log->before_details !!}</td>
        </tr>
        <tr>
            <td class="gray-bg" width="15%">After Details</td>
            <td class="after_details" colspan="3">{!! $log->after_details !!}</td>
        </tr>
        <tr>
            <td class="gray-bg" width="15%">Description</td>
            <td class="description" colspan="3">{!! $log->description !!}</td>
        </tr>
    </table>
</div>
