@extends('layouts.container')

@section('title', 'Memo')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#memo-menu').addClass('active');
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
                            <li class="breadcrumb-item"><a href="{{ route('memo.index') }}"
                                class="text-decoration-none">Memo List</a></li>
                            <li class="breadcrumb-item" aria-current="page">View Memo</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">View Memo</h4>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">View Memo</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="memto" class="form-label required-label">To
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="memo_to"
                                    value="{{ $memo->getTo() }}" autofocus="" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="memthrough" class="form-label required-label">Through
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="memo_through"
                                    value="{{ $memo->getThrough() }}" autofocus="" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="memfrom" class="form-label required-label">From
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="memo_from"
                                    value="{{ $memo->getFrom() }}" autofocus="" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="atvcde" class="form-label required-label">Date</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="input-group has-validation">
                                        {{-- <div class="input-group-append">
                                            <span class="input-group-text required-label">Start</span>
                                        </div> --}}
                                        <input type="text" class="form-control  @if ($errors->has('memo_date')) is-invalid @endif" name="memo_date"
                                            value="{{ $memo->memo_date }}" autofocus="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="prblmdsc" class="form-label required-label">Subject
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" name="subject" class="form-control"
                                        value="{{ $memo->subject }}" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="details-desc" class="form-label required-label">Brief Descriptions
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea name="description" id="details-desc" cols="30" rows="6" class="form-control"
                                        id="editor" readonly>@if ($memo->description){{ $memo->description }}@endif</textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="details-desc" class="form-label required-label">Enclosure
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <textarea name="enclosure" cols="30" rows="6" class="form-control" readonly>@if ($memo->enclosure){{ $memo->enclosure }}@endif</textarea>
                                </div>
                            </div>
                            @if($attachment)
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="fileattch" class="form-label required-label">Attach File(s)</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-7">
                                        <div class="d-flex gap-3">
                                            <div class="media">
                                                <a href="{!! asset('storage/'.$memo->attachment) !!}" target="_blank" name='attachment_exist' class="fs-5"
                                                    title="View Attachment">
                                                    <i class="bi bi-file-earmark-medical"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@stop
