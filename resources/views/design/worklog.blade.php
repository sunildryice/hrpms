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
                            <li class="breadcrumb-item" aria-current="page">Worklog</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Worklog</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm"><i class="bi-form"></i> New Worklog</button>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderedless">
                        <thead class="bg-light">
                            <tr>
                                <th style="width:45px;"></th>
                                <th class="" style="width:15%;">Month</th>
                                <th style="width:45%;">Major Activity</th>
                                <th>Priority</th>
                                <th>status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01</td>
                                <td>JAN</td>
                                <td>Major Activity</td>
                                <td>Priority</td>
                                <td>Open</td>
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
                    <form class="g-3 needs-validation" novalidate>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationfullname" class="form-label required-label">Date</label>
                                </div>

                            </div>
                            <div class="col-lg-9">

                                <input data-toggle="datepicker" type="text" name="" id="" class="form-control">

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Major activities / planned tasks </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea rows="5" class="form-control" name="remarks"></textarea>

                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="Activityarea" class="form-label required-label">Activity area
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select id="Activityarea" class="form-control select2" placeholder="Select an Activity Area"
                                    autocomplete="off">
                                    <option value="">Select an Activity Area</option>
                                    <option value="4">User one</option>
                                    <option value="1">User one</option>

                                </select>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="Priority" class="form-label required-label">Priority
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select id="Priority" class="form-control select2" placeholder="Select a Priority"
                                    autocomplete="off">
                                    <option value="">Select a Priority</option>
                                    <option value="4">User one</option>
                                    <option value="1">User one</option>

                                </select>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validateusers" class="form-label required-label">Status
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select id="validateusers" class="form-control select2" placeholder="Select a Priority"
                                    autocomplete="off">
                                    <option value="">Select a Status</option>
                                    <option value="4">User one</option>
                                    <option value="1">User one</option>

                                </select>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Other activities </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" name="" id="" class="form-control">

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Remarks </label>
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
            <div class="card">

                <div class="card-body">
                    <form class="g-3 needs-validation" novalidate>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="validationfullname" class="form-label required-label">Month</label>
                                </div>

                            </div>
                            <div class="col-lg-9">

                                <input data-toggle="datepicker" type="text" name="" id="" class="form-control">

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="" class="form-label required-label">Summary of major tasks </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea rows="5" class="form-control" name="remarks"></textarea>

                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="Activityarea" class="form-label required-label">Planned
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
                                    <label for="Priority" class="form-label required-label">Priority
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
                                    <label for="validateusers" class="form-label required-label">Completed
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                 <input type="text" name="" id="" class="form-control">

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
    @stop
