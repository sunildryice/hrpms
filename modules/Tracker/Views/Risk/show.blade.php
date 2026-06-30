@extends('layouts.container')

@section('title', 'View Risk')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#risk-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('risk.index'), 'title' => 'Risk Tracker'],
        ]" />

        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>Risk Details</h6>
                        @can('manage-risk')
                        <a href="{{ route('risk.edit', $risk->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Project</label>
                            <p>{{$risk->getProjectTitle()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Date Added</label>
                            <p>{{$risk->getDateAdded()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Name</label>
                            <p>{{$risk->risk_name}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Status</label>
                            <p>{{$risk->getRiskStatusTitle() ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Type</label>
                            <p>{{$risk->getRiskTypeTitle() ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Probability</label>
                            <p>{{$risk->getRiskProbabilityTitle() ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Residual Impact</label>
                            <p>{{$risk->getRiskImpactTitle() ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Rating</label>
                            <p>{{$risk->getRiskRatingTitle() ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Response Type</label>
                            <p>{{$risk->getRiskResponseTypeTitle() ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Owner</label>
                            <p>{{$risk->risk_owner ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted fw-bold small">Description of Risk</label>
                            <p>{{$risk->description_of_risk ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted fw-bold small">Mitigating Action</label>
                            <p>{{$risk->mitigating_action ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted fw-bold small">What's Changed This Quarter</label>
                            <p>{{$risk->whats_changed_this_quarter ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted fw-bold small">Remarks</label>
                            <p>{{$risk->remarks ?: 'N/A'}}</p>
                        </div>
                    </div>

                </div>
            </div>
            <a href="{{URL::previous()}}" type="button" class="btn btn-sm btn-secondary">Back</a>
        </section>
    </div>
</div>

@stop
