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

                                                <div class="col-lg-2">
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{asset('storage/'.$attachment->attachment)}}"
                                                        target="_blank"
                                                        rel="tooltip"
                                                        title="View attachment">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
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
