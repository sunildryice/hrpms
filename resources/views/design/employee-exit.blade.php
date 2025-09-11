@extends('layouts.container')

@section('title', 'Employee Exit')
@section('page_css')
    <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css">
@endsection
@section('page_js')
    <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('[data-toggle="datepicker"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'YYYY-MM-DD'
        });

        $('[data-toggle="datepicker-time"]').daterangepicker({
            "singleDatePicker": true,
            "timePicker": true,
            "autoApply": true,
            startDate: moment().startOf('hour'),
            endDate: '',
            locale: {
                format: 'YYYY-MM-DD, hh:mm A'
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
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
        $('.select2').val(null).trigger("change");
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar li').removeClass('active');
            $('#dashboard').addClass('active');
        });
        $('[data-toggle="datepicker-range"]').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                    'month')]
            },
            "autoApply": true,
            "drops": "auto",
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
        // $('.filter-items').on('click', function() {
        //     // //
        //     var chk_box = $(this).find('.f-check-input');

        //     var chk_status = !(chk_box.is(':checked'));
        //     var chkdata = $(this).data("id");

        //     chk_box.attr('checked', chk_status);
        //     $(this).find('i').toggleClass('bi-check-square-fill').toggleClass('bi-square');
        //     $(this).toggleClass('active');
        // });
        $('.filter-items-radio').on('click', function() {
            // //
            var chk_box = $(this).find('.f-check-input');
            obj = document.getElementsByClassName("filter-items-radio");

            var chk_status = !(chk_box.is(':checked'));
            console.log(chk_status);
            var chkdata = $(this).data("id");
            chk_box.attr('checked', chk_status);
            if (chk_status == true) {
                $(obj).removeClass('active');
                $(obj).find('i').removeClass('bi-check-square-fill').addClass('bi-square');
                $(this).find('i').removeClass('bi-square').addClass('bi-check-square-fill');
                $(this).addClass('active');
            }
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
                            <li class="breadcrumb-item" aria-current="page">Employee Exit</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Employee Exit</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-wrench-adjustable me-1"></i> New Employee Exit</button>
                </div>
            </div>
        </div>
        <section class="registration">

            <div class="row">
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                        <ul class="m-0 list-unstyled">
                            <li class="nav-item"><a href="#" class="nav-link step-item active text-decoration-none"
                                    data-tag="handovernotes"><i class="nav-icon bi-info-circle"></i> Handover Note</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                    data-tag="assethandover"><i class="nav-icon bi-pin-map"></i> Asset Handover</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                    data-tag="exitinterview"><i class="nav-icon bi-people"></i> Exit interview</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                    data-tag="payables"><i class="nav-icon bi bi-currency-exchange"></i> Payable</a></li>
                            {{-- <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none" data-tag="handovernotes"><i
                                        class="nav-icon bi-currency-dollar"></i> Employee Finance</a></li> --}}

                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card mb-4 c-tabs-content active" id="handovernotes">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for=""> Brief description of duties</label>
                                </div>

                                <div class="col-lg-9 ">
                                    <textarea rows="3" class="form-control" name="remarks"></textarea>
                                </div>

                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for=""> Reporting procedures</label>
                                </div>

                                <div class="col-lg-9 ">
                                    <textarea rows="3" class="form-control" name="remarks"></textarea>
                                </div>

                            </div>
                            <div class="row mb-3">
                                <div class="py-2 d-flex ">
                                    <div class="d-flex justify-content-end flex-grow-1">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal"><i class="bi-text-indent-left"></i> Add New</a>

                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table table-borderedless">
                                    <thead class="bg-light">
                                        <tr>

                                            <th style="width:45px;"></th>
                                            <th class="">Name of project</th>
                                            <th>Action needed</th>
                                            <th>Partners</th>
                                            <th>Budget</th>
                                            <th>Critical issues</th>
                                            <th>Status</th>
                                            <th style="width: 130px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>01</td>
                                            <td class="">Name of project</td>
                                            <td>Action needed</td>
                                            <td>Partners</td>
                                            <td>Budget</td>
                                            <td>Critical issues</td>

                                            <td><span class="badge bg-primary">Pending</span></td>

                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-eye-fill"></i></a>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-pencil-square"></i></a>

                                                <a href="#" class="btn btn-sm btn-danger"><i class="bi-trash3"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for=""> Important files (hardcopy and electronic):</label>
                                </div>

                                <div class="col-lg-9 ">
                                    <textarea rows="3" class="form-control" name="remarks"></textarea>
                                </div>

                            </div>
                            <div class="row mb-3">
                                <div class="py-2 d-flex ">
                                    <div class="d-flex justify-content-end flex-grow-1">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal"><i class="bi-text-indent-left"></i> Add New
                                            Activity
                                        </a>

                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table table-borderedless">
                                    <thead class="bg-light">
                                        <tr>

                                            <th style="width:45px;"></th>
                                            <th class="">Name</th>
                                            <th>Organization</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Comments</th>
                                            <th style="width: 130px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>01</td>
                                            <td class="">Name</td>
                                            <td>Organization</td>
                                            <td>Phone</td>
                                            <td>Email</td>
                                            <td>Comments</td>


                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-eye-fill"></i></a>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-pencil-square"></i></a>

                                                <a href="#" class="btn btn-sm btn-danger"><i class="bi-trash3"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="contractValidation" class="form-label required-label">Contact details after
                                            departure:
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <input type="file" class="form-control" id="contractValidationimage">
                                    <small>Note: TOR/Job description, Key documents relevant for the position
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card c-tabs-content" id="assethandover">
                        <div class="card-header bg-light p-2 px-3">
                            <h3 class="m-0 fs-6 text-capitalize">assets handhover</h3>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive mb-3">
                                <table class="table table-borderedless">
                                    <thead class="bg-light">
                                        <tr>

                                            <th style="width:45px;"></th>
                                            <th style="width: 25%;">Item Name</th>
                                            <th style="width: 25%;">Asset Code</th>
                                            <th>Qty</th>
                                            <th>Condition</th>
                                            <th style="width: 15%;">Progress</th>
                                            <th style="width: 130px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>01</td>
                                            <td class="">Item Name</td>
                                            <td>Asset Code</td>
                                            <td>Qnty</td>

                                            <td><span class="badge bg-primary">Good</span></td>
                                            <td><select name="" class="form-control select2" id="">
                                                    <option value="">Select One</option>
                                                    <option value="">Select One</option>
                                                    <option value="">Select One</option>
                                                    <option value="">Select One</option>
                                                </select></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-eye-fill"></i></a>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-pencil-square"></i></a>

                                                <a href="#" class="btn btn-sm btn-danger"><i
                                                        class="bi-trash3"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card c-tabs-content" id="exitinterview">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Exit Interview</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <label for="">1. Was Consistenly fair</label>
                                <div class="mt-2">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <span class="filter-items filter-items-radio d-flex gap-2 mb-2"
                                                data-id="value">
                                                <span class="filter-check">
                                                    <input type="radio" name="q-1" class="f-check-input">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    Always
                                                </span>
                                            </span>
                                        </div>
                                        <div class="col-lg-3">
                                            <span class="filter-items filter-items-radio d-flex gap-2 mb-2 active"
                                                data-id="value">
                                                <span class="filter-check">
                                                    <input type="radio" name="q-1" class="f-check-input"
                                                        checked="checked">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-check-square-fill"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    Almost
                                                </span>
                                            </span>
                                        </div>
                                        <div class="col-lg-3">
                                            <span class="filter-items filter-items-radio d-flex gap-2 mb-2"
                                                data-id="value1">
                                                <span class="filter-check">
                                                    <input type="radio" name="q-1" class="f-check-input">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    Usually
                                                </span>
                                            </span>
                                        </div>
                                        <div class="col-lg-3">
                                            <span class="filter-items filter-items-radio d-flex gap-2 mb-2"
                                                data-id="value3">
                                                <span class="filter-check">
                                                    <input type="radio" name="q-1" class="f-check-input">
                                                    <span class="filter-checkbox">
                                                        <i class="bi-square"></i>
                                                    </span>
                                                </span>
                                                <span class="filter-body">
                                                    Sometimes
                                                </span>
                                            </span>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="row mb-3">

                                <label for=""> N. What is the main reason for your leaving?</label>
                                <div class="mt-2">
                                    <textarea rows="2" class="form-control" name="remarks"></textarea>
                                </div>

                            </div>
                            <div class="row mb-3">

                                <label for="">N. What did you like most about your job?</label>
                                <div class="mt-2">
                                    <textarea rows="2" class="form-control" name="remarks"></textarea>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="card c-tabs-content" id="payables">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Payables Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="validationphone" class="m-0">Outstanding Salary
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row fv-plugins-icon-container">
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">From</span>
                                                </div>
                                                <input type="text" class="form-control" data-toggle="datepicker" value=""
                                                    aria-describedby="basic-addon2">

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon3">To</span>
                                                </div>
                                                <input type="text" class="form-control" data-toggle="datepicker" value=""
                                                    aria-describedby="basic-addon3">

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="">Annual and Sick Leave Balance</label>
                                </div>

                                <div class="col-lg-9 ">

                                    <input type="text" class="form-control" name="" id="">


                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="">Severance pay/Gratuity if any</label>
                                </div>

                                <div class="col-lg-9 ">

                                    <input type="text" class="form-control" name="" id="" data-toggle="datepicker"
                                        aria-describedby="helpId" placeholder="">


                                </div>

                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="">Others (Give details, if any)</label>
                                </div>

                                <div class="col-lg-9 ">

                                    <input type="text" class="form-control" name="" id="" data-toggle="datepicker"
                                        aria-describedby="helpId" placeholder="">


                                </div>

                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="">Outstanding Advances</label>
                                </div>

                                <div class="col-lg-9 ">

                                    <input type="text" class="form-control" name="" id="" >


                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="">Loans</label>
                                </div>

                                <div class="col-lg-9 ">

                                    <input type="text" class="form-control" name="" id="" >


                                </div>

                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <label for="">Other payables</label>
                                </div>

                                <div class="col-lg-9 ">

                                    <input type="text" class="form-control" name="" id="" >


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <div class="modal fade" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-6" id="exampleModalLabel">Maintenance Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="activitycode" class="form-label required-label">Ref. No.

                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" name="" class="form-control" id="">

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="atvcde" class="form-label required-label">Activity code


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <select class="form-select select2" name="" id="atvcde">
                                    <option selected>Select one</option>
                                    <option value="">Option one</option>
                                    <option value=""></option>
                                    <option value=""></option>
                                </select>


                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="acccde" class="form-label required-label">Account code


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <select class="form-select select2" name="" id="acccde">
                                    <option selected>Select one</option>
                                    <option value="">Option one</option>
                                    <option value=""></option>
                                    <option value=""></option>
                                </select>


                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="donor" class="form-label required-label">Donor/Grant


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <select class="form-select select2" name="" id="donor">
                                    <option selected>Select one</option>
                                    <option value="">Option one</option>
                                    <option value=""></option>
                                    <option value=""></option>
                                </select>


                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="px-3">
                                <h3 class="fs-7 fw-bold  text-uppercase text-primary border-bottom py-2">Facility being
                                    request for. Give details in table below</h3>
                            </div>

                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="itmname" class="form-label required-label">Item name


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <select class="form-select select2" name="" id="itmname">
                                    <option selected>Select one</option>
                                    <option value="">Option one</option>
                                    <option value=""></option>
                                    <option value=""></option>
                                </select>


                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="prblmdsc" class="form-label required-label">Problem/Service request for


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea name="" id="prblmdsc" cols="30" rows="5" class="form-control"></textarea>



                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="estcst" class="form-label required-label">Estimation Cost(NPR)</label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <input type="text" class="form-control" id="estcst">


                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="rmsks" class="form-label required-label">Remarks</label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea name="" id="rmsks" cols="30" rows="5" class="form-control"></textarea>



                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    @stop
