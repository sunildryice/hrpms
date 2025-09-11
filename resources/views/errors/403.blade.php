@extends('layouts.container')

@section('title', 'Access Denied')

@section('page_js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#dashboard').addClass('active');
        });
    </script>
@endsection

@section('page-content')
    <div class="m-content p-3">
        <div class="container-fluid">
            <div class="pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="brd-crms flex-grow-1">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a></li>
                                <li class="breadcrumb-item">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-5"></h4>
                    </div>
                </div>
            </div>
            <div class="welcome-page">
                <p><strong> An error has occurred while processing your request.</strong></p>
                <p>This may occurred because there was an attempt to manipulate this software or
                    you have not enough permission to process this request.</p>
                <p>If you have not enough permission, you can request to your system administrator to
                    get additional access.</p>
                <p>Users are prohibited from taking unauthorized actions to intentionally modify the system.</p>
            </div>
        </div>

    </div>
@stop
