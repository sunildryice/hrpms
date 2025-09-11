@extends('layouts.container')

@section('title', 'Vehicle')
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
        $('.filter-items').on('click', function() {
            // //
            var chk_box = $(this).find('.f-check-input');

            var chk_status = !(chk_box.is(':checked'));
            var chkdata = $(this).data("id");

            chk_box.attr('checked', chk_status);
            $(this).find('i').toggleClass('bi-check-square-fill').toggleClass('bi-square');
            $(this).toggleClass('active');
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
                            <li class="breadcrumb-item" aria-current="page">Vehicle</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Vehicle</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm"><i class="bi-truck"></i> Add Vehicle</button>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="row">
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation pt-3 pb-3">
                        <ul class="m-0 list-unstyled v-mneu">
                            <li class="nav-item">
                                <a href="#" class="nav-link step-item  text-decoration-none" data-tag="office-vech">
                                    <i class="nav-icon bi-truck"></i> Office Vehicle
                                </a>
                            </li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none active"
                                    data-tag="hire-vehicle"><i class="nav-icon bi-truck-flatbed"></i> Hire Vehicle</a></li>

                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card shadow-sm border rounded c-tabs-content " id="office-vech">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Office vehicle Request Form</h3>
                        </div>
                        <div class="card-body">
                            <form class="g-3 needs-validation" novalidate>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationfullname" class="m-0">Vehicle Type</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">

                                        <select class="form-control select2" name="" id="">
                                            <option>Type One</option>
                                            <option>Type two</option>
                                            <option>Type three</option>
                                            <option>Type Four</option>

                                        </select>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Date </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input data-toggle="datepicker-time" type="text" name="" id=""
                                            class="form-control">

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Purpose </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" name="" id="" class="form-control">

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validateusers" class="m-0">User
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validateusers" class="form-control select2"
                                            placeholder="Select a person..." autocomplete="off">
                                            <option value="">Select a person...</option>
                                            <option value="4">User one</option>
                                            <option value="1">User one</option>

                                        </select>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea rows="5" class="form-control" name="remarks"></textarea>

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
                    <div class="card shadow-sm border rounded c-tabs-content active" id="hire-vehicle">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Hire vehicle Request Form</h3>
                        </div>
                        <div class="card-body">
                            <form class="g-3 needs-validation" novalidate>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationfullname" class="m-0">Date</label>
                                        </div>

                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" data-toggle="datepicker-range" class="form-control">


                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Purpose </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <input type="text" name="" id="" class="form-control">

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="validationGender" class="m-0">Users
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <select id="validationGender" class="select2"
                                            placeholder="Select a person..." autocomplete="off">
                                            <option value="">Select a person...</option>
                                            <option value="4">User one</option>
                                            <option value="1">User one</option>

                                        </select>

                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Type</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">

                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-square"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            4WD
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-2">
                                                    <span
                                                        class="filter-items d-flex gap-2 mb-2 align-items-center h-100 active"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input"
                                                                checked="checked">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-check-square-fill"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            Pickup Truck
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-square"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            Minitruck
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-square"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            SML Truck
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-square"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            Bolero
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
                                            <label for="" class="m-0">For</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">

                                        <div class="mt-3">
                                            <div class="row">
                                                <div class="col-lg-2">
                                                    <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-square"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            Full Day
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-2">
                                                    <span
                                                        class="filter-items d-flex gap-2 mb-2 align-items-center h-100 active"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input"
                                                                checked="checked">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-check-square-fill"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            Half Day
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-square"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="">Hrs</span>
                                                                <input type="text" name="" id="" class="form-control">
                                                            </div>
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="filter-items d-flex gap-2 mb-2 align-items-center h-100"
                                                        data-id="value">
                                                        <span class="filter-check">
                                                            <input type="checkbox" class="f-check-input">
                                                            <span class="filter-checkbox">
                                                                <i class="bi-square"></i>
                                                            </span>
                                                        </span>
                                                        <span class="filter-body">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="">Others</span>
                                                                <input type="text" name="" id="" class="form-control">
                                                            </div>
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
                                            <label for="" class="m-0">Pick Up </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">Time</span>
                                                    </div>
                                                    <input type="text" class="form-control" name="Time"
                                                        placeholder="Pick Up time" value="" aria-label="Pick Up time"
                                                        aria-describedby="basic-addon2">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">Location</span>
                                                    </div>
                                                    <input type="text" class="form-control fv-plugins-icon-input"
                                                        name="mobile_number" placeholder="Pick Up Location" value=""
                                                        aria-label="Pick Up Location" aria-describedby="basic-addon2">


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Travel </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="input-group">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">From</span>
                                                    </div>
                                                    <input type="text" class="form-control" name="travelfrom"
                                                        placeholder="Travel From" value="" aria-label="Travel From"
                                                        aria-describedby="basic-addon2">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="input-group has-validation">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">Destination</span>
                                                    </div>
                                                    <input type="text" class="form-control fv-plugins-icon-input"
                                                        name="travelto" placeholder="Desitination Point" value=""
                                                        aria-label="Desitination Point" aria-describedby="basic-addon2">


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-lg-3">
                                        <div class="d-flex align-items-start h-100">
                                            <label for="" class="m-0">Remarks </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">
                                        <textarea rows="5" class="form-control" name="remarks"></textarea>

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
                </div>
            </div>
        </section>
    @stop
