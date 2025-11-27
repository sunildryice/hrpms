<div class="card-header fw-bold">
    <h3 class="m-0 fs-6">Documents</h3>
</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table table-borderedless">
            <thead class="bg-light">
            <tr>
                <th style="width: 50%">{{ __('label.signature') }}</th>
                <th style="width: 50%">{{ __('label.profile-picture') }}</th>
                <th style="width: 50%">{{ __('label.cv') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="width: 50%">
                    @if (file_exists('storage/' . $employee->signature) && $employee->signature != '')
                        <a href="{!! asset('storage/' . $employee->signature) !!}" target="_blank"
                           class="btn btn-outline-primary btn-sm"
                           title="View Signature">
                            <div class="media">
                                <img src="{{ url('storage/' . $employee->signature) }}" style="width: 80px;">
                            </div>
                        </a>
                    @endif
                </td>
                <td style="width: 50%">
                    @if (file_exists('storage/' . $employee->profile_picture) && $employee->profile_picture != '')
                        <a href="{!! asset('storage/' . $employee->profile_picture) !!}" target="_blank"
                           class="btn btn-outline-primary btn-sm"
                           title="View Profile Picture">
                            <div class="media">
                                <img src="{{ url('storage/' . $employee->profile_picture) }}" style="width: 80px;">
                            </div>
                        </a>
                    @endif
                </td>
                <td style="width: 50%">
                        @if (file_exists('storage/' . $employee->cv_attachment) && $employee->cv_attachment != '')
                            <a href="{!! asset('storage/' . $employee->cv_attachment) !!}" target="_blank" class="btn btn-success btn-sm"
                                title="View CV">
                                <i class="bi bi-file-earmark-pdf-fill fs-4"></i><br>
                                <span class="small">View CV (PDF)</span>
                            </a>
                            </a>
                        @endif
                    </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
