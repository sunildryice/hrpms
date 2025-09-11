@extends('layouts.container')

@section('title', 'Construction Progress Attachment')

@section('page_js')
    <script type="text/javascript">
        $(function() {
            $('#navbarVerticalMenu').find('#construction-index').addClass('active');
        });
    </script>

@endsection

@section('page-content')
    <div class="m-content p-3">
        <div class="container-fluid">
            <div class="page-header pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('construction.index') }}" class="text-decoration-none">Construction</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>
            <section>
                <form action="{{route('construction.progress.attachment.store', $progressId)}}" method="post" enctype="multipart/form-data" id="createForm">
                    @csrf
                    <div class="card">
                        <div class="card-header fw-bold">
                            Construction Progress Attachments
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label class="form-label" for="title">File Title</label>
                                    <input class="form-control" type="text" name="title" id="title" value="{{old('title')}}">
                                    @if ($errors->has('title'))
                                        <span class="text-danger">{{$errors->first('title')}}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-lg-6">
                                    <label class="form-label" for="attachment">Upload File</label>
                                    <input class="form-control" type="file" name="attachment" id="attachment">
                                    @if ($errors->has('attachment'))
                                        <span class="text-danger">{{$errors->first('attachment')}}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-primary" type="submit">Upload</button>
                            <a href="{{route('construction.edit.progress', $constructionId)}}" type="button" class="btn btn-sm btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </section>

            <section>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header fw-bold">
                            Attachments
                        </div>
                        <div class="card-body">
                            <div class="col-lg-12">
                                @foreach ($attachments as $key=>$attachment)
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-1">
                                                    <span>{{++$key}}</span>
                                                </div>
                                                <div class="col-lg-9">
                                                    <span>{{$attachment->title}}</span>
                                                </div>

                                                <div class="col-lg-1">
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{asset('storage/'.$attachment->attachment)}}"
                                                        target="_blank"
                                                        rel="tooltip"
                                                        title="View attachment">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                </div>

                                                <div class="col-lg-1">
                                                    <form action="{{route('construction.progress.attachment.destroy', $attachment->id)}}" method="POST" id="deleteForm">
                                                        @csrf
                                                        @method('delete')
                                                        <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi-trash"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>


@stop
