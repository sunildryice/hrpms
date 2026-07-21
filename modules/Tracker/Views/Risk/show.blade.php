@extends('layouts.container')

@section('title', 'View Risk')

@section('page_css')
<style>
    .risk-history-table td, .risk-history-table th {
        padding: 10px;
        vertical-align: middle;
        white-space: nowrap;
    }
    .risk-history-table td:nth-child(2),
    .risk-history-table td:nth-child(4),
    .risk-history-table td:nth-child(5),
    .risk-history-table td:nth-child(6),
    .risk-history-table th:nth-child(2),
    .risk-history-table th:nth-child(4),
    .risk-history-table th:nth-child(5),
    .risk-history-table th:nth-child(6) {
        min-width: 200px;
        white-space: normal;
    }
    .risk-history-table td:first-child { min-width: 140px; }
    .risk-history-table td:nth-child(3) { min-width: 160px; }
</style>
@endsection

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#risk-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')
<div class="m-content">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('risk.index'), 'title' => 'Risk Tracker'],
        ]" />

        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>Risk Details</h6>
                        @if($risk->created_by == auth()->id())
                        <a href="{{ route('risk.edit', $risk->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Risk Name</label>
                            <p>{{$risk->risk_name}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Date Added</label>
                            <p>{{$risk->getDateAdded()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Project</label>
                            <p>{{$risk->getProjectTitle()}}</p>
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
                            <p>{{$risk->getRiskOwnerNames()}}</p>
                        </div>
                    </div>

                </div>
            </div>

            @if($risk->riskHistories->isNotEmpty())
            <div class="card mt-3">
                <div class="card-header fw-bold">
                    <h6>Risk Change History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered risk-history-table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Updated Date</th>
                                    <th>Description of Risk</th>
                                    <th>Risk Status</th>
                                    <th>Mitigating Action</th>
                                    <th>What's Changed This Period</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($risk->riskHistories as $history)
                                <tr>
                                    <td>{{$history->getUpdatedDate() ?: 'N/A'}}</td>
                                    <td>{{$history->description_of_risk ?: 'N/A'}}</td>
                                    <td>{{$history->getRiskStatusTitle() ?: 'N/A'}}</td>
                                    <td>{{$history->mitigating_action ?: 'N/A'}}</td>
                                    <td>{{$history->whats_changed_this_period ?: 'N/A'}}</td>
                                    <td>{{$history->remarks ?: 'N/A'}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <a href="{{URL::previous()}}" type="button" class="btn btn-sm btn-secondary mt-3">Back</a>
        </section>
    </div>
</div>

@stop
