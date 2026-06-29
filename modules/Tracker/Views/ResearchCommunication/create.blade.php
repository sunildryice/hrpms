@extends('layouts.container')

@section('title', 'Create Research Communication')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#research-communication-index').addClass('active');

            const form = document.getElementById('researchCommunicationCreateForm');
            const fv = FormValidation.formValidation(form, {
                fields: {},
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat'
                    }),
                }
            });

            $('[name="date_of_publication"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

            $('[name="date_posted"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
            });

        });
    </script>
@endsection

@section('page-content')
<div class="m-content p-3">
    <div class="container-fluid">

        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">
                                <a href="{{route('research-communication.index')}}" class="text-decoration-none">Research Communication</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
            </div>
        </div>

        <section>
            <form action="{{route('research-communication.store')}}" method="POST" id="researchCommunicationCreateForm">
                @csrf
                <div class="card mb-3">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">Publication Details</h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="type_of_publication">Type of Publication</label>
                                <select class="form-select select2" name="type_of_publication" id="type_of_publication">
                                    <option value="">Select Type of Publication</option>
                                    <option value="Journal" {{old('type_of_publication') == 'Journal' ? 'selected' : ''}}>Journal</option>
                                    <option value="Other" {{old('type_of_publication') == 'Other' ? 'selected' : ''}}>Other</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="publication_title">Publication Title</label>
                                <input class="form-control" type="text" name="publication_title" id="publication_title" value="{{old('publication_title')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="date_of_publication">Date of Publication</label>
                                <input class="form-control" type="text" name="date_of_publication" id="date_of_publication" value="{{old('date_of_publication')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="herdi_members_involved">HERDI Members Involved</label>
                                <input class="form-control" type="text" name="herdi_members_involved" id="herdi_members_involved" value="{{old('herdi_members_involved')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="journal_paper_name">Journal/Paper Name</label>
                                <input class="form-control" type="text" name="journal_paper_name" id="journal_paper_name" value="{{old('journal_paper_name')}}">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="publication_url">Publication URL</label>
                                <input class="form-control" type="text" name="publication_url" id="publication_url" value="{{old('publication_url')}}">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-bold">
                        <h6 class="card-title">Social Media Post Details</h6>
                    </div>

                    <div class="card-body">

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="type_of_post">Type of Post</label>
                                <select class="form-select select2" name="type_of_post" id="type_of_post">
                                    <option value="">Select Type of Post</option>
                                    @foreach($postTypes as $type)
                                        <option value="{{$type->value}}" {{old('type_of_post') == $type->value ? 'selected' : ''}}>{{$type->label()}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="date_posted">Date Posted</label>
                                <input class="form-control" type="text" name="date_posted" id="date_posted" value="{{old('date_posted')}}" onfocus="this.blur()" placeholder="YYYY-MM-DD">
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="post_title">Post Title</label>
                                <input class="form-control" type="text" name="post_title" id="post_title" value="{{old('post_title')}}">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-4">
                                <label class="form-label" for="project_id">Project</label>
                                <select class="form-select select2" name="project_id" id="project_id">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{$project->id}}" {{old('project_id') == $project->id ? 'selected' : ''}}>{{$project->short_name ?? $project->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label" for="posted_in">Posted In</label>
                                <select class="form-select select2" name="posted_in" id="posted_in">
                                    <option value="">Select Posted In</option>
                                    @foreach(['Bluesky', 'Facebook', 'LinkedIn', 'Twitter', 'Website', 'X', 'Youtube'] as $platform)
                                        <option value="{{$platform}}" {{old('posted_in') == $platform ? 'selected' : ''}}>{{$platform}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-2">
                                <label class="form-label" for="views">Views</label>
                                <input class="form-control" type="number" name="views" id="views" value="{{old('views', 0)}}" min="0">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="link_clicks">Link Clicks</label>
                                <input class="form-control" type="number" name="link_clicks" id="link_clicks" value="{{old('link_clicks', 0)}}" min="0">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="reactions">Reactions</label>
                                <input class="form-control" type="number" name="reactions" id="reactions" value="{{old('reactions', 0)}}" min="0">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="shares">Shares</label>
                                <input class="form-control" type="number" name="shares" id="shares" value="{{old('shares', 0)}}" min="0">
                            </div>
                            <div class="col-lg-2">
                                <label class="form-label" for="comments">Comments</label>
                                <input class="form-control" type="number" name="comments" id="comments" value="{{old('comments', 0)}}" min="0">
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary" id="btnSubmit">Create</button>
                        <a href="{{route('research-communication.index')}}" role="button" class="btn btn-sm btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </section>

    </div>
</div>

@stop
