@extends('layouts.container')

@section('title', 'Leave Setup')

@section('page_js')

    <script type="text/javascript">
        $('.filter-items').on('click', function () {
            // //
            var chk_box = $(this).find('.f-check-input');

            var chk_status = !(chk_box.is(':checked'));
            var chkdata = $(this).data("id");

            console.log(chkdata);

            chk_box.attr('checked', chk_status);
            $(this).find('i').toggleClass('bi-check-square-fill').toggleClass('bi-square');
            $(this).toggleClass('active');
        });
        $(document).ready(function () {
            $('#sidebar li').removeClass('active');
            $('#dashboard').addClass('active');
        });
    </script>
@endsection
@section('page-content')

    <div class="container-fluid">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
                <div class="brd-crms flex-grow-1">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li>
                            <li class="breadcrumb-item" aria-current="page">Leave Setup</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Leave Setup</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                        class="btn btn-primary btn-sm">
                        <i class="bi-door-open-fill"></i> New Leave
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border rounded c-tabs-content active" id="employee-table">
            {{-- <div class="card-header fw-bold">
                <h3 class="m-0 fs-6">Employees</h3>
            </div> --}}
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('label.sn') }}</th>
                            <th>Leave name</th>
                            <th>Type</th>
                            <th>Yearly Balance</th>
                            <th>Weekend</th>
                            <th>Applicable to All</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row">1</td>
                            <td>Leave name</td>
                            <td>Type</td>
                            <td>Yearly Balance</td>
                            <td>Weekend</td>
                            <td>Yes</td>
                            <td>Status</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="#" class="act-btns bt-primary"><i class="bi-pencil-square"></i></a>
                                    <a href="#" class="act-btns bt-success"><i class="bi-pencil-square"></i></a>
                                    <a href="#" class="act-btns bt-danger"><i class="bi-pencil-square"></i></a>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td scope="row">2</td>
                            <td>Leave name</td>
                            <td>Type</td>
                            <td>Yearly Balance</td>
                            <td>Weekend</td>
                            <td>Yes</td>
                            <td>Status</td>
                            <td>

                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title mb-0 fs-6" id="staticBackdropLabel">LEAVE SETUP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="" class="m-0">Name of leave</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="validationCustom02" value=""
                                placeholder="Name of Leave">

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="" class="m-0">Type</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="validationCustom02" value="" placeholder="Type">
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <span class="filter-items d-flex gap-2 mb-2" data-id="value">
                                            <span class="filter-check">
                                                <input type="checkbox" class="f-check-input">
                                                <span class="filter-checkbox">
                                                    <i class="bi-square"></i>
                                                </span>
                                            </span>
                                            <span class="filter-body">
                                                Default
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="filter-items d-flex gap-2 mb-2 active" data-id="value">
                                            <span class="filter-check">
                                                <input type="checkbox" class="f-check-input" checked="checked">
                                                <span class="filter-checkbox">
                                                    <i class="bi-check-square-fill"></i>
                                                </span>
                                            </span>
                                            <span class="filter-body">
                                                Fixed
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-lg-4">
                                        <span class="filter-items d-flex gap-2 mb-2" data-id="value">
                                            <span class="filter-check">
                                                <input type="checkbox" class="f-check-input">
                                                <span class="filter-checkbox">
                                                    <i class="bi-square"></i>
                                                </span>
                                            </span>
                                            <span class="filter-body">
                                                Includes Weekends
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-lg-4">
                                        <span class="filter-items d-flex gap-2 mb-2" data-id="value">
                                            <span class="filter-check">
                                                <input type="checkbox" class="f-check-input">
                                                <span class="filter-checkbox">
                                                    <i class="bi-square"></i>
                                                </span>
                                            </span>
                                            <span class="filter-body">
                                                Female
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-lg-4">
                                        <span class="filter-items d-flex gap-2 mb-2" data-id="value">
                                            <span class="filter-check">
                                                <input type="checkbox" class="f-check-input">
                                                <span class="filter-checkbox">
                                                    <i class="bi-square"></i>
                                                </span>
                                            </span>
                                            <span class="filter-body">
                                                Applicable to all staff
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="" class="m-0">Yearly balance</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col-lg-8"> <input type="text" class="form-control" id="validationCustom02"
                                        value="" placeholder="Type"></div>
                                <div class="col-lg-4">

                                    <select class="form-control select2" data-width="100%" name="" id="">
                                        <option>DAY/HOUR</option>
                                        <option>Day</option>
                                        <option>Hour</option>
                                    </select>

                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="" class="m-0">Maximum carry over</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" class="form-control" id="validationCustom02" value=""
                                placeholder="Maximum carry over">

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="" class="m-0">Yearly balance</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select class="form-control select2" data-width="100%" name="" id="">
                                <option>FY/Yearly</option>
                                <option>2078/79</option>
                                <option>2077/78</option>
                            </select>


                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="Fdname" class="m-0">Status</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <div class=" form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked"
                                    name="active" checked="">
                                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button> --}}
                    <button type="button" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection