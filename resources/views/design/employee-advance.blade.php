@extends('layouts.container')

@section('title', 'Work Log')
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
                            <li class="breadcrumb-item" aria-current="page">Advance Request</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Advance Request</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-outline-success btn-sm"><i class="bi-printer-fill"></i></button>
                    <button class="btn btn-primary btn-sm"><i class="bi-form"></i> New Advance Request</button>
                </div>
            </div>
        </div>
        <section class="registration">

            <div class="card">

                <div class="card-body">
                    <form class="g-3 needs-validation" novalidate>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Ref # </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="" id="">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationfullname" class="form-label required-label">Program completion date
                                    </label>
                                </div>

                            </div>
                            <div class="col-lg-6">
                                <input type="text" name="" id="" class="form-control">
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control" placeholder="Total Days For Settelment" readonly>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Advance Ammount </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="" id="" readonly>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Expenditure Paid </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="" id="">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Cash surplus deficit </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="" id="">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Project </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select name="" id="" class="form-control select2">
                                    <option value="">select one </option>
                                    <option value="">select one</option>
                                    <option value="">select one</option>
                                    <option value="">select one</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="py-1 d-flex ">
                                <div class="d-flex justify-content-end flex-grow-1">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"><i class="bi-text-indent-left"></i> New Activity
                                        Details</a>

                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="px-3">
                                <h3 class="fs-7 fw-bold  text-uppercase text-primary border-bottom py-2">Activity Details
                                </h3>
                            </div>

                        </div>
                        <div class="row mb-4">
                            <div class="px-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;">SN</th>
                                            <th>Activity</th>

                                            <th style="width: 12%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td>Account code Activity</td>

                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-eye-fill"></i></a>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-pencil-square"></i></a>
                                                <a href="#" class="btn btn-sm btn-outline-secondary"><i
                                                        class="bi-printer"></i></a>
                                                <a href="#" class="btn btn-sm btn-danger"><i class="bi-trash3"></i></a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>


                        </div>
                        <div class="row mb-1">
                            <div class="py-1 d-flex ">
                                <div class="d-flex justify-content-end flex-grow-1">
                                    <a href="javascript:void(0)" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal"><i class="bi-text-indent-left"></i> New Expense
                                        Details</a>

                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="px-3">
                                <h3 class="fs-7 fw-bold  text-uppercase text-primary border-bottom py-2">Expense Details
                                </h3>
                            </div>

                        </div>


                        <div class="row mb-3">
                            <div class="px-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Bill Invoice No.</th>
                                            <th>Activity Name</th>
                                            <th>Date</th>
                                            <th>Expense Type</th>
                                            <th>Expense Category</th>
                                            <th>Gross</th>
                                            <th>Less: Tax</th>
                                            <th>Net Amount Paid</th>
                                            <th style="width: 12%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-eye-fill"></i></a>
                                                <a href="#" class="btn btn-sm btn-outline-primary"><i
                                                        class="bi-pencil-square"></i></a>
                                                <a href="#" class="btn btn-sm btn-outline-secondary"><i
                                                        class="bi-printer"></i></a>
                                                <a href="#" class="btn btn-sm btn-danger"><i class="bi-trash3"></i></a>
                                            </td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>


                        </div>
                        <div class="row mb-2">
                            <div class="px-3">
                                <h3 class="fs-7 fw-bold  text-uppercase text-primary border-bottom py-2">Expense Summary
                                </h3>
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="px-3">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Expense Category</th>
                                            <th>Gross</th>
                                            <th>Less Tax</th>
                                            <th>Net Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Expense Category</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>

                                    </tbody>
                                </table>
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
        </section>
        <div class="modal fade" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-6" id="exampleModalLabel">advance detail</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="activitycode" class="form-label required-label">Activity code

                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <select class="form-select select2" name="" id="activitycode">
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
                                    <label for="donor" class="form-label required-label">Donor


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
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="donor" class="form-label required-label">Desc


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <textarea name="" id="" cols="5" rows="5" class="form-control"></textarea>


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
