@extends('layouts.container')

@section('title', 'Access Denied')

@section('footer_js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#sidebar li').removeClass('active');
        });
    </script>
@endsection

@section('dynamicdata')

    <!-- Main content -->
    <div class="content body">
        <section id="introduction">
            <h2 class="page-header">Error! Access Denied</h2>
            <div class="row">
                <div class="col-md-12">
                    @include('layout.alert')
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-body">
                                <p><strong> An error has occurred while processing your request.</strong></p>
                                <p>This may occurred because there was an attempt to manipulate this software or
                                    you have not enough permission to process this request.</p>
                                <p>If you do not have enough permission, you can cross check or request your system administrator to
                                    get additional access ore role redefined.</p>
                                <p>Users are prohibited from taking unauthorized actions to intentionally modify the process or the system itself.</p>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>

@stop
