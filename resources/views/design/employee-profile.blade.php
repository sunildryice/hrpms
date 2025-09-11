@extends('layouts.container')

@section('title', 'User Registration')

@section('page_js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar li').removeClass('active');
            $('#dashboard').addClass('active');
        });
    </script>
@endsection
@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li>
                        <li class="breadcrumb-item" aria-current="page">User Registration</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-5">User Registration</h4>
            </div>
            <div class="ad-info justify-content-end">
                <button class="btn btn-primary btn-sm"><i class="bi-person-plus"></i> Add info</button>
            </div>
        </div>

    </div>
    <div class="container-fluid">
        <div class="emp-header">

        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="p-2 bg-white mb-2 rounded">
                    <div class="d-flex">

                        <div class="user-pro d-flex align-items-center justify-content-center  bg-danger text-white">
                            <img src="" alt="">
                            <i class="bi-person"></i>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Profile

                    </div>
                    <div class="card-body">
                        <div class="p2">
                            <ul class="list-unstyled list-py-2 text-dark mb-0">
                                <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>
                                <li class="position-relative"><i class="bi-person dropdown-item-icon"></i> Ram Bahadur
                                    Shrestha <a href="#" class="stretched-link" rel="tooltip" title="Profile"></a>
                                </li>
                                <li><i class="bi-people dropdown-item-icon"></i> Maried</li>
                                <li><i class="bi-building dropdown-item-icon"></i> Kathmandu</li>

                                <li class="pt-4 pb-2"><span
                                        class="card-subtitle text-uppercase text-primary">Contacts</span></li>
                                <li class="position-relative"><i class="bi-at dropdown-item-icon"></i> rambdr@owh.com <a
                                        href="#" class="stretched-link" rel="tooltip" title="Contact email"></a></li>
                                <li class="position-relative"><i class="bi-phone dropdown-item-icon"></i> +(977)
                                    987-654-3210 <a href="#" class="stretched-link" rel="tooltip"
                                        title="Contact Number"></a></li>

                                <li class="pt-4 pb-2"><span class="card-subtitle text-uppercase text-primary">Others</span>
                                </li>
                                <li><i class="bi-droplet dropdown-item-icon"></i> A+</li>
                                <li><i class="bi-stickies dropdown-item-icon"></i> Working on 8 projects</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">

                <div class="tabs-s mb-2">
                    <ul class="m-0 list-unstyled list-inline">
                        <li class="list-inline-item">
                            <a href="#" class="nav-link step-item active text-decoration-none">
                                <i class="nav-icon bi-info-circle"></i>Profile Inforamtion
                            </a>
                        </li>

                        <li class="list-inline-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                    class="nav-icon bi bi-person-workspace"></i>
                                Tenure</a></li>
                        <li class="list-inline-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                    class="nav-icon bi bi-person-workspace"></i>
                                Leave</a></li>

                        <li class="list-inline-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                    class="nav-icon bi bi-vi"></i>
                                Travel</a></li>
                        <li class="list-inline-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                    class="nav-icon bi bi-journal-text"></i>
                                Education</a></li>

                    </ul>
                </div>

                <div class="c-tabs-contnet">
                    <div class="c-tabs-item active" id="">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Profile

                            </div>
                            <div class="card-body">
                                <div class="p2">
                                    <table class="table">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">First</th>
                                                <th scope="col">Last</th>
                                                <th scope="col">Handle</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>Mark</td>
                                                <td>Otto</td>
                                                <td>@mdo</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                                <td>Jacob</td>
                                                <td>Thornton</td>
                                                <td>@fat</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                                <td>Larry</td>
                                                <td>the Bird</td>
                                                <td>@twitter</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- End Table -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
