@extends('layouts.container')

@section('title', 'View Research Uptake & Communication')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#research-communication-index').addClass('active');
        });
    </script>
@endsection

@section('page-content')
<div class="m-content">
    <div class="container-fluid">

        <x-breadcrumb :items="[
            ['route' => route('research-communication.index'), 'title' => 'Research Uptake & Communication'],
        ]" />

        <section>
            @if($researchCommunication->type_of_publication === 'Journal')
            <div class="card mb-3">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>Publication Details</h6>
                        @can('manage-research-communication')
                        <a href="{{ route('research-communication.edit', $researchCommunication->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Type of Publication</label>
                            <p>{{$researchCommunication->getPublicationTypeLabel()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Publication Title</label>
                            <p>{{$researchCommunication->publication_title ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Date of Publication</label>
                            <p>{{$researchCommunication->getDateOfPublication() ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">HERDI Members Involved</label>
                            <p>{{$researchCommunication->getMemberNames()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Journal/Paper Name</label>
                            <p>{{$researchCommunication->journal_paper_name ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Publication URL</label>
                            <p>
                                @if($researchCommunication->publication_url)
                                    <a href="{{$researchCommunication->publication_url}}" target="_blank">{{$researchCommunication->publication_url}}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            @endif

            @if($researchCommunication->type_of_publication === 'Other')
            <div class="card">
                <div class="card-header fw-bold">
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <h6>Social Media Post Details</h6>
                        @can('manage-research-communication')
                        <a href="{{ route('research-communication.edit', $researchCommunication->id) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Type of Publication</label>
                            <p>{{$researchCommunication->getPublicationTypeLabel()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Type of Post</label>
                            <p>{{$researchCommunication->type_of_post ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Date Posted</label>
                            <p>{{$researchCommunication->getDatePosted() ?: 'N/A'}}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Post Title</label>
                            <p>{{$researchCommunication->post_title ?: 'N/A'}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Project</label>
                            <p>{{$researchCommunication->getProjectTitle()}}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-bold small">Total Engagement</label>
                            <p>{{$researchCommunication->getTotalEngagement()}}</p>
                        </div>
                    </div>

                    @if($researchCommunication->platforms->isNotEmpty())
                    <hr>
                    <h6 class="fw-bold mb-2">Platform Performance</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Platform</th>
                                    <th>Views</th>
                                    <th>Link Clicks</th>
                                    <th>Reactions</th>
                                    <th>Shares</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($researchCommunication->platforms as $p)
                                <tr>
                                    <td>{{$p->platform}}</td>
                                    <td>{{$p->views}}</td>
                                    <td>{{$p->link_clicks}}</td>
                                    <td>{{$p->reactions}}</td>
                                    <td>{{$p->shares}}</td>
                                    <td>{{$p->comments}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>
            </div>
            @endif
            <a href="{{URL::previous()}}" type="button" class="btn btn-sm btn-secondary mt-3">Back</a>
        </section>
    </div>
</div>

@stop
