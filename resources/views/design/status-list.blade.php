@extends('layouts.container')

@section('title', 'User Registration')

@section('page_js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar li').removeClass('active');
            $('#dashboard').addClass('active');
        });
        Command: toastr["Success"]("Your Status has been approved.", "Success")
            toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
            }
    </script>
@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li>
                            <li class="breadcrumb-item" aria-current="page">Status List</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-5">Status List</h4>
                </div>
                <div class="ad-info justify-content-end">
                    {{-- <button class="btn btn-primary btn-sm"><i class="bi-person-plus"></i> Add info</button> --}}
                </div>
            </div>

        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-header fw-bold">
                    <h3 class="m-0 fs-6">Status List</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                {{-- <th scope="col" style="width: 10px;">#</th> --}}
                                <th scope="col">First</th>
                                <th scope="col">Last</th>
                                <th scope="col">Handle</th>
                                <th scope="col">Status</th>
                                <th style="width: 150px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- <td scope="row" class="bg-warning" rel="tooltip" title="Canceled"></td> --}}
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>
                                    <span class="badge bg-warning">Canceled</span>
                                </td>
                                <td>
                                    <a href="javascript:;" class="btn btn-primary btn-sm">
                                        <i class="bi-eye"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-outline-primary btn-sm">
                                        <i class="bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-danger btn-sm">
                                        <i class="bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                {{-- <td scope="row" class="bg-success"></td> --}}
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>
                                    <span class="badge bg-success">Approved</span>
                                </td>
                                <td>
                                    <a href="javascript:;" class="btn btn-primary btn-sm">
                                        <i class="bi-eye"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-outline-primary btn-sm">
                                        <i class="bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-danger btn-sm">
                                        <i class="bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                {{-- <td scope="row" class="bg-danger"></td> --}}
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>
                                    <span class="badge bg-danger">Regected</span>
                                </td>
                                <td>
                                    <a href="javascript:;" class="btn btn-primary btn-sm">
                                        <i class="bi-eye"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-outline-primary btn-sm">
                                        <i class="bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-danger btn-sm">
                                        <i class="bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                {{-- <td scope="row" class="bg-secondary"></td> --}}
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>
                                    <span class="badge bg-secondary">Pending</span>
                                </td>
                                <td>
                                    <a href="javascript:;" class="btn btn-primary btn-sm">
                                        <i class="bi-eye"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-outline-primary btn-sm">
                                        <i class="bi-pencil-square"></i>
                                    </a>
                                    <a href="javascript:;" class="btn btn-danger btn-sm">
                                        <i class="bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                {{-- <td scope="row" class="bg-dark"></td> --}}
                                <td>Mark</td>
                                <td>Otto</td>
                                <td>@mdo</td>
                                <td>
                                    <span class="badge bg-dark">Close</span>
                                </td>
                                <td>
                                    <a href="javascript:;" class="btn btn-primary btn-sm">
                                        <i class="bi-eye"></i>
                                    </a>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
