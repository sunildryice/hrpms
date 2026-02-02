<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Project Activity Extension</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="ProjectActivityExtensionForm" method="post"
    action="{{ route('project-activity.extension.store', [$projectActivity->id]) }}" autocomplete="off">

    {!! csrf_field() !!}

    <div class="modal-body">

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label m-0 fw-bold">Current Completion Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="form-control-plaintext pt-2">
                    {{ $projectActivity->completion_date ? $projectActivity->completion_date->format('Y-m-d') : 'Not set' }}
                </div>
            </div>
        </div>

        @if ($projectActivity->extensions && $projectActivity->extensions->isNotEmpty())
            <div class="row mb-3">
                <div class="col-lg-3">
                    <div class="d-flex align-items-start h-100">
                        <label class="form-label m-0 fw-bold">Previous Extensions</label>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="border rounded p-2 bg-light small">
                        @foreach ($projectActivity->extensions()->latest()->get() as $extension)
                            <div class="mb-2 pb-2 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>Extended to:</strong>
                                        {{ $extension->extended_completion_date->format('Y-m-d') }}
                                    </div>
                                    <div class="text-muted">
                                        {{ $extension->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <em>Reason:</em> {{ $extension->reason }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Extension Date</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" name="extended_completion_date" class="form-control" placeholder="yyyy-mm-dd"
                    onfocus="this.blur()" autocomplete="off" />
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label class="form-label required-label m-0">Reason</label>
                </div>
            </div>
            <div class="col-lg-9">
                <textarea name="reason" class="form-control" rows="3"></textarea>
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>
