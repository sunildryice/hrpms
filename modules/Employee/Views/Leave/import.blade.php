@extends('layouts.container')

@section('title', 'Import Employee Leaves')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#navbarVerticalMenu').find('#employees-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')

    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}"
                                   class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{!! route('employees.index') !!}" class="text-decoration-none">{{ __('label.employee') }}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                </div>
                <div class="add-info justify-content-end">
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <form action="{!! route('employees.leaves.import.store') !!}" method="post"
                          enctype="multipart/form-data" id="employeeImportForm" autocomplete="off">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationAttachment" class="m-0">Attachment</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="file" class="form-control" name="attachment" />
                                    <small>Supported file types excel and file size of upto 2MB.</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">
                                Submit
                            </button>
                            <a href="{!! route('employees.index') !!}"
                               class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                        {!! csrf_field() !!}
                    </form>
                </div>
            </div>

        </div>
    </div>

@stop
