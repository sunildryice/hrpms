@extends('layouts.container')

@section('title', 'View Business Development')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#business-development-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('business-development.index'), 'title' => 'Business Development'],
        ]" />

        <section>
            <div class="card">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>Business Development Details</h6>
                        @can('manage-business-development')
                        <a href="{{ route('business-development.edit', $businessDevelopment->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Thematic Area</label>
                            <p>{{$businessDevelopment->getThematicAreaTitle() ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Date</label>
                            <p>{{$businessDevelopment->getDate()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Call Name</label>
                            <p>{{$businessDevelopment->call_name ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Donor Name</label>
                            <p>{{$businessDevelopment->donor_name ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Project Type</label>
                            <p>{{$businessDevelopment->project_type ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Status</label>
                            <p>{{$businessDevelopment->status ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Result</label>
                            <p>{{$businessDevelopment->result ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Attachment</label>
                            <p>
                                @if ($businessDevelopment->attachment)
                                    <a href="{{asset('storage/'.$businessDevelopment->attachment)}}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-text"></i> View Attachment
                                    </a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            <a href="{{URL::previous()}}" type="button" class="btn btn-sm btn-secondary">Back</a>
        </section>
    </div>
</div>

@stop
