@extends('layouts.container')

@section('title', 'Employee Registration')
@section('page_css')
    <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css">
@endsection
@section('page_js')
    <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('[data-toggle="datepicker"]').datepicker({
            language: 'en-GB',
            autoHide: true
        });


        $(document).ready(function() {
            $('.step-item').click(function() {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar li').removeClass('active');
            $('#dashboard').addClass('active');
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
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li>
                            <li class="breadcrumb-item" aria-current="page">Employee Registration</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-5">Employee Registration</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm"><i class="bi-person-plus"></i> Add info</button>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="row">
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation pt-3 pb-3">
                        <ul class="m-0 list-unstyled v-mneu">
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item active text-decoration-none"
                                    data-tag="generalinformantion">
                                    <i class="nav-icon bi-info-circle"></i> General Information
                                </a>
                            </li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                    data-tag="address"><i class="nav-icon bi-pin-map"></i> Address</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                    data-tag="family-details"><i class="nav-icon bi-people"></i> Family Details</a></li>
                            <li class="nav-item"><a href="#" data-tag="tenure-details" class="nav-link step-item text-decoration-none"><i
                                        class="nav-icon bi bi-person-workspace"></i> Tenure</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                        class="nav-icon bi-calendar-heart"></i> Medical informatin</a></li>
                            <li class="nav-item"><a href="#" data-tag="education-details" class="nav-link step-item text-decoration-none"><i
                                        class="nav-icon bi bi-journal-text"></i> Education</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                        class="nav-icon bi bi-explicit"></i> Experience</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                        class="nav-icon bi bi-calendar4-range"></i> Training</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"><i
                                        class="nav-icon bi bi-lock"></i> Login Credentials</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card shadow-sm border rounded c-tabs-content active" id="generalinformantion">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">General Information</h3>
                        </div>
                        <div class="card-body">
                            <form class="g-3 needs-validation" novalidate>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Staff Id</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationCustom02" value="OHW-100-31"
                                            readonly>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationfullname" class="m-0">Full Name</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationfullname" value=""
                                            placeholder="Full name">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Official Email </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="email" class="form-control" id="validationemail"
                                            placeholder="example@example.com">

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationphone" class="m-0">Phone Number
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Phone Number"
                                                        aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">Home</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Phone Number"
                                                        aria-label="Recipient's username" aria-describedby="basic-addon2">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">Mobile</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdob" class="m-0">Date of Birth
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">

                                        <input type="text" data-toggle="datepicker" class="form-control"
                                            id="validationdob" placeholder="Date of Birth">
                                        <div class="valid-feedback ">
                                            Looks good!
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationcitizenship" class="m-0">Citizenship No
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" id="validationcitizenship"
                                                    placeholder="Citizenship No">

                                            </div>
                                            <div class="col-lg-6">
                                                <input type="file" class="form-control" id="validationcitizenshipimage">

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationpan" class="m-0">Pan No:
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" id="validationpan"
                                                    placeholder="Pan No">

                                            </div>
                                            <div class="col-lg-6">

                                                <input type="file" class="form-control" id="validationpanimage">

                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationGender" class="m-0">Gender
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationGender" class="select2"
                                            placeholder="Select a Gender..." autocomplete="off">
                                            <option value="">Select a person...</option>
                                            <option value="4">Male</option>
                                            <option value="1">Female</option>

                                        </select>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationMaritalstaatus" class="m-0">Marital Status
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationMaritalstaatus" class="select2"
                                            placeholder="Select a Marital Status..." autocomplete="off">
                                            <option value="">Select a Marital Status...</option>
                                            <option value="1">Sngle</option>
                                            <option value="2">Maried</option>
                                            <option value="3">Divorced</option>

                                        </select>

                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button class="btn btn-primary btn-sm next">Save</button>
                            <button class="btn btn-success btn-sm">Update</button>
                            <button class="btn btn-danger btn-sm">Reset</button>
                        </div>
                    </div>
                    <div class="card shadow-sm border rounded c-tabs-content" id="address">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Address</h3>
                        </div>
                        <div class="card-body">
                            <form class="g-3 needs-validation" novalidate>
                                <div class="row mb-3 ">
                                    <div class="col-lg-12">
                                        <div class="d-flex align-items-center h-100 border-bottom p-1 mb-2">
                                            <label for="validationprovience" class="m-0">Current Address
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationprovience" class="m-0">Province
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationprovience" class="select2"
                                            placeholder="Select a Provience..." autocomplete="off">
                                            <option value="">Select a Provience...</option>
                                            <option value="1">Provience 1</option>
                                            <option value="2">Provience 2</option>

                                        </select>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationprovience" class="m-0">District
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationprovience" class="select2"
                                            placeholder="Select a District..." autocomplete="off">
                                            <option value="">Select a District...</option>
                                            <option value="1">Kathmandu</option>
                                            <option value="2">Bhaktapur</option>
                                            <option value="3">Chitwan</option>
                                            <option value="4">Lalitpur</option>

                                        </select>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationmunicipality" class="m-0">Municipality
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationmunicipality" class="select2"
                                            placeholder="Select a Municipality..." autocomplete="off">
                                            <option value="">Select a Municipality...</option>
                                            <option value="1">Municipality 1</option>
                                            <option value="2">Municipality 2</option>
                                            <option value="3">Municipality 3</option>
                                            <option value="4">Municipality 4</option>

                                        </select>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationfullname" class="m-0">Ward Number</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationfullname" value=""
                                            placeholder="Full name">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationtole" class="m-0">Tole</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationtole" value=""
                                            placeholder="Tole">
                                    </div>
                                </div>

                                {{-- permanent address --}}
                                <div class="row mb-3 ">
                                    <div class="col-lg-12">
                                        <div class="d-flex align-items-center h-100 border-bottom p-1 mb-2">
                                            <label for="validationcurrent" class="m-0"><input type="checkbox"
                                                    name="" id="validationcurrent"> Same as Current Address
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationprovience2" class="m-0">Province
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationprovience2" class="select2"
                                            placeholder="Select a Provience..." autocomplete="off">
                                            <option value="">Select a Provience...</option>
                                            <option value="1">Provience 1</option>
                                            <option value="2">Provience 2</option>

                                        </select>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdistrict2" class="m-0">District
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationdistrict2" class="select2"
                                            placeholder="Select a District..." autocomplete="off">
                                            <option value="">Select a District...</option>
                                            <option value="1">Kathmandu</option>
                                            <option value="2">Bhaktapur</option>
                                            <option value="3">Chitwan</option>
                                            <option value="4">Lalitpur</option>

                                        </select>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationmunicipality2" class="m-0">Municipality
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationmunicipality2" class="select2"
                                            placeholder="Select a Municipality..." autocomplete="off">
                                            <option value="">Select a Municipality...</option>
                                            <option value="1">Municipality 1</option>
                                            <option value="2">Municipality 2</option>
                                            <option value="3">Municipality 3</option>
                                            <option value="4">Municipality 4</option>

                                        </select>

                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationwardno2" class="m-0">Ward Number</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationwardno2" value=""
                                            placeholder="Full name">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationtole2" class="m-0">Tole</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationtole2" value=""
                                            placeholder="Tole">
                                    </div>
                                </div>

                            </form>

                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button class="btn btn-primary btn-sm">Save</button>
                            <button class="btn btn-success btn-sm">Update</button>
                            <button class="btn btn-danger btn-sm">Reset</button>
                        </div>
                    </div>
                    <div class="c-tabs-content" id="family-details">
                        <div class="card">
                            <div class="card-header fw-bold">
                                <h3 class="m-0 fs-6">Family Details</h3>
                            </div>
                            <div class="card-body">
                                <form class="g-3 needs-validation" novalidate>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="Fdname" class="m-0">Name</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" id="Fdname" placeholder="Full name">
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationrelation" class="m-0">Relation
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <select id="validationrelation" class="select2"
                                                placeholder="Select a Relation..." autocomplete="off">
                                                <option value="">Select a Relation...</option>
                                                <option value="1">Mother</option>
                                                <option value="2">Father</option>
                                                <option value="3">Spouse</option>

                                            </select>

                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationdob" class="m-0">Date of Birth
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">

                                            <input type="text" data-toggle="datepicker" class="form-control"
                                                id="validationdob" placeholder="Date of Birth">
                                            <div class="valid-feedback ">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="Fdname" class="m-0">Remarks</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <textarea id="" class="form-control" placeholder="Remarks"></textarea>
                                            <div class="valid-feedback">
                                                Looks good!
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="validationcitizenship" class="m-0">Citizenship/
                                                    Birth
                                                    certificate
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <input type="text" class="form-control" id="validationcitizenship"
                                                        placeholder="Citizenship/ Birth Certificate No.">
                                                    <div class="valid-feedback">
                                                        Looks good!
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="file" class="form-control"
                                                        id="validationcitizenshipimage">
                                                    <div class="valid-feedback">
                                                        Looks good!
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="Fdname" class="m-0">Emergency Contact</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="flexSwitchCheckChecked" checked>
                                                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <div class="d-flex align-items-start h-100">
                                                <label for="Fdname" class="m-0">Is Nominee</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-9">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="flexSwitchCheckChecked" checked>
                                                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button class="btn btn-primary btn-sm">Save</button>
                                <button class="btn btn-success btn-sm">Update</button>
                                <button class="btn btn-danger btn-sm">Reset</button>
                            </div>
                        </div>
                        <div class="card shadow-sm border rounded mt-3">
                            <div class="card-header fw-bold">
                                <h3 class="m-0 fs-6">Family List</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderedless">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 25%">Name</th>
                                            <th>Relation</th>
                                            <th>Date of Birth</th>
                                            <th>Type</th>
                                            <th>Mobile No.</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Name</td>
                                            <td>Relation</td>
                                            <td>Date of Birth</td>
                                            <td>
                                                <span class="badge bg-primary">Emergency Contact</span>

                                            </td>
                                            <td>Mobile No.</td>
                                            <td>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i
                                                        class="bi-pencil-square"></i> Edit</a>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i
                                                        class="bi-eye-fill"></i> View</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Sita Shrestha</td>
                                            <td>Spouse</td>
                                            <td>Date of Birth</td>
                                            <td>
                                                <span class="badge bg-success">Nominee</span>

                                            </td>
                                            <td>Mobile No.</td>
                                            <td>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i
                                                        class="bi-pencil-square"></i> Edit</a>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i
                                                        class="bi-eye-fill"></i> View</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="c-tabs-content" id="education-details">
                        <div class="card shadow-sm border rounded mb-3">
                            <div class="card-header fw-bold">
                                <h3 class="m-0 fs-6">General Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdegree" class="m-0">Degree</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationdegree" value=""
                                            placeholder="Degree">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationinstitution" class="m-0">Institution</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationinstitution" value=""
                                            placeholder="Institution">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationpassedyear" class="m-0">Passed Year</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationpassedyear" value=""
                                            placeholder="Passed Year">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdocument" class="m-0">Document</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-control" id="validationdocument" value=""
                                            placeholder="Passed Year">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button class="btn btn-primary btn-sm">Save</button>
                                <button class="btn btn-success btn-sm">Update</button>
                                <button class="btn btn-danger btn-sm">Reset</button>
                            </div>
                        </div>
                        <div class="card shadow-sm border rounded mb-3">
                            <div class="card-header fw-bold">
                                <h3 class="m-0 fs-6">General Information</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderedless">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Degree</th>
                                            <th style="width:55%">Institution</th>
                                            <th>Passed Year</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Degree</td>
                                            <td style="width: 25%">Institution</td>
                                            <td>Passed Year</td>

                                            <td>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi-pencil-square"></i> Edit</a>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi-eye-fill"></i> View</a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="c-tabs-content" id="tenure-details">
                        <div class="card shadow-sm border rounded mb-3">
                            <div class="card-header fw-bold">
                                <h3 class="m-0 fs-6">Add New Tenure</h3>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdegree" class="m-0">Degree</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationdegree" value=""
                                            placeholder="Degree">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationinstitution" class="m-0">Institution</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationinstitution" value=""
                                            placeholder="Institution">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationpassedyear" class="m-0">Passed Year</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="validationpassedyear" value=""
                                            placeholder="Passed Year">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationdocument" class="m-0">Document</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-control" id="validationdocument" value=""
                                            placeholder="Passed Year">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                                <button class="btn btn-primary btn-sm">Save</button>
                                <button class="btn btn-success btn-sm">Update</button>
                                <button class="btn btn-danger btn-sm">Reset</button>
                            </div>
                        </div>
                        <div class="card shadow-sm border rounded mb-3">
                            <div class="card-header fw-bold">
                                <h3 class="m-0 fs-6">General Information</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderedless">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Degree</th>
                                            <th style="width:55%">Institution</th>
                                            <th>Passed Year</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Degree</td>
                                            <td style="width: 25%">Institution</td>
                                            <td>Passed Year</td>

                                            <td>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi-pencil-square"></i> Edit</a>
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi-eye-fill"></i> View</a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @stop
