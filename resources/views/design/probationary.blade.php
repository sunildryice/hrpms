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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validateusers" class="form-label required-label">Employee name
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" name="" id="" class="form-control">

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validateusers" class="form-label required-label">Review type
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" name="" id="" class="form-control">

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validateusers" class="form-label required-label">Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text" name="" id="" class="form-control">

                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validateusers" class="form-label required-label">Remarks
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <textarea rows="5" class="form-control" name="remarks"></textarea>
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
                            <li class="breadcrumb-item" aria-current="page">Probationary Review</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Probationary Review</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-form"></i> New Probationary Review</button>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="card">
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>For</th>
                                <th>Review type</th>
                                <th>Date</th>
                                <th style="width:40%; ">Remarks</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Rama Singh</td>
                                <td scope="row">First review (3 month)</td>
                                <td>4/4/2022</td>
                                <td>remarks</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i
                                            class="bi-chat-left-text-fill"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i
                                            class="bi-list-columns-reverse"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi-save2"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi-trash3-fill"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>Rama Singh</td>
                                <td scope="row">First review (3 month)</td>
                                <td>4/4/2022</td>
                                <td>remarks</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i
                                            class="bi-chat-left-text-fill"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i
                                            class="bi-list-columns-reverse"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-secondary"><i class="bi-save2"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-danger"><i class="bi-trash3-fill"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="s-title fw-bold fs-6 text-custom border-bottom p-1 mb-2">
                        Indicators
                    </h4>

                    <div class="row mb-3">
                        <label for="">1. Quality and accuracy of work</label>
                        <div class="mt-2">
                            <div class="row">
                                <div class="col-lg-3">
                                    <span class="filter-items filter-items-radio d-flex gap-2 mb-2" data-id="value">
                                        <span class="filter-check">
                                            <input type="radio" name="q-1" class="f-check-input">
                                            <span class="filter-checkbox">
                                                <i class="bi-square"></i>
                                            </span>
                                        </span>
                                        <span class="filter-body">
                                            Improvement required
                                        </span>
                                    </span>
                                </div>
                                <div class="col-lg-3">
                                    <span class="filter-items filter-items-radio d-flex gap-2 mb-2 active" data-id="value">
                                        <span class="filter-check">
                                            <input type="radio" name="q-1" class="f-check-input" checked="checked">
                                            <span class="filter-checkbox">
                                                <i class="bi-check-square-fill"></i>
                                            </span>
                                        </span>
                                        <span class="filter-body">
                                            Satisfactory
                                        </span>
                                    </span>
                                </div>
                                <div class="col-lg-3">
                                    <span class="filter-items filter-items-radio d-flex gap-2 mb-2" data-id="value1">
                                        <span class="filter-check">
                                            <input type="radio" name="q-1" class="f-check-input">
                                            <span class="filter-checkbox">
                                                <i class="bi-square"></i>
                                            </span>
                                        </span>
                                        <span class="filter-body">
                                            Good
                                        </span>
                                    </span>
                                </div>
                                <div class="col-lg-3">
                                    <span class="filter-items filter-items-radio d-flex gap-2 mb-2" data-id="value3">
                                        <span class="filter-check">
                                            <input type="radio" name="q-1" class="f-check-input">
                                            <span class="filter-checkbox">
                                                <i class="bi-square"></i>
                                            </span>
                                        </span>
                                        <span class="filter-body">
                                            Excellent
                                        </span>
                                    </span>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="row mb-3">

                        <label for=""> If any areas of performance, conduct or attendance require
                            improvement please provide details below.</label>
                        <div class="mt-2">
                            <textarea rows="5" class="form-control" name="remarks"></textarea>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-9">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="q3" name="active"
                                    checked="">
                                <label class="form-check-label" for="q3">Have the objectives identified for this period of
                                    the
                                    probation been met?</label>

                            </div>

                        </div>
                    </div>



                </div>
            </div>
    </div>
    </section>

@stop
