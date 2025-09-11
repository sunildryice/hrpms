@extends('layouts.container')

@section('title', 'Import Inventory')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#inventories-menu').addClass('active');
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
                                    <a href="{!! route('inventories.index') !!}" class="text-decoration-none">Inventories</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                            </ol>
                        </nav>
                        <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
                    </div>
                </div>
            </div>
            <section class="import">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Import Inventory
                            </div>
                            <div class="card-body">
                                <form action="{{route('inventories.import.store')}}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row mb-2 pb-2 border-bottom">
                                        <div class="form-group">
                                            <input type="file" name="inventory" id="inventory">
                                        </div>
                                        @if ($errors->has('inventory'))
                                            <span class="invalid-feedback mt-2">{{$errors->first('inventory')}}</span>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <button class="btn btn-sm btn-primary" style="float: right" type="submit">Import</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <span><i><a class="text-decoration-none" href="{{asset('sample/inventory_import_format_sample.xlsx')}}">Download</a> Sample File</i></span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
