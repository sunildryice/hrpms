@extends('layouts.container')

@section('title', 'Empolyee Requisition')
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
        }, function (start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format(
                'YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });

        $(document).ready(function () {
            $('.step-item').click(function () {
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
        $(document).ready(function () {
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
        }, function (start, end, label) {
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
        $('.filter-items-radio').on('click', function () {
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
                            <li class="breadcrumb-item" aria-current="page">Empolyee Requisition</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Empolyee Requisition</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-wrench-adjustable me-1"></i> New Empolyee Requisition
                    </button>
                </div>
            </div>
        </div>
        <section class="registration">

            <div class="row">
                <div class="col-lg-3">
                    <div class="rounded border shadow-sm vertical-navigation sm-menu-vr pt-3 pb-3 bg-white">
                        <ul class="m-0 list-unstyled">
                            <li class="nav-item"><a href="#" class="nav-link step-item active text-decoration-none"
                                                    data-tag="genralinformation"><i class="nav-icon bi-info-circle"></i>
                                    General</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                                    data-tag="qualification"><i class="nav-icon bi-book-fill"></i>
                                    Qualifation</a></li>
                            <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                                    data-tag="othersinformation"><i class="nav-icon bi-people"></i>Others</a>
                            </li>
                            {{-- <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none"
                                    data-tag="payables"><i class="nav-icon bi bi-currency-exchange"></i> Payable</a></li> --}}
                            {{-- <li class="nav-item"><a href="#" class="nav-link step-item text-decoration-none" data-tag="handovernotes"><i
                                        class="nav-icon bi-currency-dollar"></i> Employee Finance</a></li> --}}

                        </ul>
                    </div>
                </div>
                <div class="col-lg-9">

                    <div class="card mb-4 c-tabs-content active" id="genralinformation">
                        <div class="card-header bg-light p-2 px-3">
                            <h3 class="m-0 fs-6 text-capitalize">General Inforamtion</h3>
                        </div>

                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> Position</label></div>
                                <div class="col-lg-9">
                                    <input type="text" name="" id="" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> For FY</label></div>
                                <div class="col-lg-9">
                                    <input type="text" name="" id="" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> Requested Level</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" name="" id="" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> Replacement for</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" name="" id="" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> Workstation</label>
                                </div>
                                <div class="col-lg-9">
                                    <select name="" id="" class="form-control select2">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> Date required
                                        from</label></div>
                                <div class="col-lg-9">
                                    <input type="text" data-toggle="datepicker" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> Requested
                                        Date</label></div>
                                <div class="col-lg-9">
                                    <input type="text" data-toggle="datepicker" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label"> Is this position
                                        budgeted</label></div>
                                <div class="col-lg-9">
                                    <select name="" id="" class="form-control select2">
                                        <option value="1">Yes</option>
                                        <option value="2">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label">Types of
                                        Employeement</label></div>
                                <div class="col-lg-9">
                                    <select name="" id="" class="form-control select2">
                                        <option value="1">type of employement</option>

                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label">Activitiy Code</label>
                                </div>
                                <div class="col-lg-9">
                                    <select name="" id="" class="form-control select2">
                                        <option value="1">type of employement</option>

                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label">Account Code</label>
                                </div>
                                <div class="col-lg-9">
                                    <select name="" id="" class="form-control select2">
                                        <option value="1">type of employement</option>

                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label">Work Load</label>
                                </div>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="Hours per week">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="" class="form-label required-label">Duration</label>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-8 position-relative">
                                            <input type="number" min="1" class="form-control">
                                        </div>
                                        <div class="col-lg-4 position-relative">
                                            <select class="form-control select2" data-width="100%">
                                                <option value="">Month/Year *</option>
                                                <option value="1">Yearly</option>
                                                <option value="2">Monthly</option>
                                                <option value="3">Weekly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label">Other Specify</label>
                                </div>
                                <div class="col-lg-9">
                                    <textarea name="" id="" cols="30" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3"><label for="" class="required-label">Reason for
                                        request</label>
                                </div>
                                <div class="col-lg-9">
                                    <textarea name="" id="" cols="30" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>

                            <a href="#" class="btn btn-danger btn-sm">Cancel</a>
                        </div>
                    </div>
                    <div class="card c-tabs-content " id="qualification">
                        <div class="card-header bg-light p-2 px-3">
                            <h3 class="m-0 fs-6 text-capitalize">Qualification</h3>
                        </div>

                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <label for="" class="required-label">Education</label>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text required-label"
                                                          id="basic-addon2">Required</span>
                                                </div>
                                                <select aria-describedby="basic-addon2" class="form-control">
                                                    <option value="0">Select One</option>
                                                    <option value="SLC">SLC</option>
                                                    <option value="+2">+2</option>
                                                    <option value="bachelor">bachelor</option>
                                                    <option value="Masters">Masters</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text required-label"
                                                          id="basic-addon2">Preferred</span>
                                                </div>
                                                <select aria-describedby="basic-addon2" class="form-control">
                                                    <option value="0">Select One</option>
                                                    <option value="SLC">SLC</option>
                                                    <option value="+2">+2</option>
                                                    <option value="bachelor">bachelor</option>
                                                    <option value="Masters">Masters</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <label for="" class="required-label">Work Experience</label>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text required-label"
                                                          id="basic-addon2">Required</span>
                                                </div>
                                                <select aria-describedby="basic-addon2" class="form-control">
                                                    <option value="0">Select One</option>
                                                    <option value="SLC">SLC</option>
                                                    <option value="+2">+2</option>
                                                    <option value="bachelor">bachelor</option>
                                                    <option value="Masters">Masters</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text required-label"
                                                          id="basic-addon2">Preferred</span>
                                                </div>
                                                <select aria-describedby="basic-addon2" class="form-control">
                                                    <option value="0">Select One</option>
                                                    <option value="SLC">SLC</option>
                                                    <option value="+2">+2</option>
                                                    <option value="bachelor">bachelor</option>
                                                    <option value="Masters">Masters</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <label for="" class="required-label">Skill</label>
                                </div>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text required-label"
                                                          id="basic-addon2">Required</span>
                                                </div>
                                                <select aria-describedby="basic-addon2" class="form-control">
                                                    <option value="0">Select One</option>
                                                    <option value="SLC">SLC</option>
                                                    <option value="+2">+2</option>
                                                    <option value="bachelor">bachelor</option>
                                                    <option value="Masters">Masters</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text required-label"
                                                          id="basic-addon2">Preferred</span>
                                                </div>
                                                <select aria-describedby="basic-addon2" class="form-control">
                                                    <option value="0">Select One</option>
                                                    <option value="SLC">SLC</option>
                                                    <option value="+2">+2</option>
                                                    <option value="bachelor">bachelor</option>
                                                    <option value="Masters">Masters</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card c-tabs-content" id="othersinformation">
                        <div class="card-header fw-bold">
                            <h3 class="m-0 fs-6">Other Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-3">
                                    <div class="d-flex align-items-start h-100">
                                        <label for="acccde" class="form-label required-label">TOR/ JD Submitted

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
                                        <label for="acccde" class="form-label required-label">Tentative date of submission (if
                                            No in jd)

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
                                        <label for="acccde" class="form-label required-label">Logistics Requirements
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-9">

                                    <textarea name="" id="" cols="30" rows="5" class="form-control"></textarea>


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
    </div>

@stop
