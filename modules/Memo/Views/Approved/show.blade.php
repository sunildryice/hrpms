@extends('layouts.container')

@section('title', 'Memo')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#approved-memo-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')
    <div class="container-fluid">
        <div class="page-header pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('approved.memo.index') }}"
                                class="text-decoration-none">Memo</a></li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Memo</h4>
                </div>
            </div>
        </div>
        <section>

            @include('Memo::Partials.detail')

            <div class="col">
                <div class="row" style="display: flex; flex-direction: row; border: 2px solid; border-color: lightgray; box-shadow: 2px 3px lightgray; border-radius: 5px; margin: 0px 5px; margin-bottom: 15px;">
                    <div style="height:30px; background-color: lightgray; margin-bottom: 10px; padding: 5px; margin-bottom: 0px;">
                        <p>Memo Description</p>
                    </div>
                    <div style="overflow-x: auto;">
                        <p style="margin-top: 0px; padding-top: 0px; padding-bottom: 0px; white-space: nowrap;">{!! $memo->description !!}</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="row" style="display: flex; flex-direction: row; border: 2px solid; border-color: lightgray; box-shadow: 2px 3px lightgray; border-radius: 5px; margin: 0px 5px; margin-bottom: 15px;">
                    <div style="height:30px; background-color: lightgray; margin-bottom: 10px; padding: 5px; margin-bottom: 0px;">
                        <p>Memo Enclosure</p>
                    </div>
                    <p style="margin-top: 0px; padding-top: 5px; padding-bottom: 0px;">{!! $memo->enclosure !!}</p>
                </div>
            </div>
        </section>
        <div class="card">
            <div class="card-header fw-bold">
                Memo Process
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        @foreach ($memo->logs as $log)
                            <div class="d-flex py-2 flex-row gap-2 mb-2 border-bottom ">
                                <div width="40" height="40" class="rounded-circle mr-3 user-icon">
                                    <i class="bi-person"></i>
                                </div>
                                <div class="w-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-md-2 mb-2 mb-md-0">
                                            <span class="me-2">{{ $log->getCreatedBy() }}</span>
                                            <span
                                                class="badge bg-primary c-badge">{!! $log->createdBy->employee->latestTenure->getDesignationName() !!}</span>
                                        </div>
                                        <small title="{{$log->created_at}}">{{ $log->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <p class="text-justify comment-text mb-0 mt-1">
                                        {{ $log->log_remarks }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
