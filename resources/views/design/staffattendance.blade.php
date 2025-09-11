@extends('layouts.container')

@section('title', 'Staff Attendance Record')
@section('page_css')
    <style>
        .table tr th,
        .table tr td {
            font-size: 10px;
            padding: 0.55rem 0.55rem;
        }

        .holiday {
            color: red;
        }

        .table thead th {
            text-transform: capitalize;
        }

        input,
        input:focus-visible {
            outline: none;
            padding: 0.3rem 0.5rem;
        }

        .input-custom-width {
            width: 68px;
            border: 1px solid #e4e4e4;
        }



        .sticky-col {
            position: -webkit-sticky;
            position: sticky;
            width: 150px;
            min-width: 150px;
            max-width: 100px;
            left: 0px;
            z-index: 99 !important;
            background: white !important;
        }
    </style>

@endsection
@section('page_js')
    <script>
        $('[name="pickup_time"]').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: false,
            timePickerIncrement: 1,
            locale: {
                format: ' hh:mm A'
            }
        }).on('show.daterangepicker', function(ev, picker) {
            picker.container.find(".calendar-table").hide();
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
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page"><a href="#"
                                    class="text-decoration-none"></a></li>
                            <li class="breadcrumb-item" aria-current="page">Staff Attandance</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Staff Attandance</h4>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="print-code fs-6 fw-bold">
                            Staff Attendance Record
                        </div>
                        <div class="print-header-info my-3">
                            <ul class="list-unstyled m-0 p-0 fs-7">
                                <li><span class="fw-bold me-2">Staff Name:</span><span>Shreejana Sunuwar</span></li>
                                <li><span class="fw-bold me-2">Title:</span><span>Sr.Admin & HR Officer</span></li>
                                <li><span class="fw-bold me-2">Duty station:</span><span>Kathmandu</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <ul class="list-unstyled mt-4 fs-7 ">
                            <li class="d-flex mb-2 justify-content-end align-items-center">
                                <span class="fw-bold me-2">Month:</span>
                                <span>
                                    <select class="form-select form-select-sm fs-7" aria-label="Default select example">
                                        <option selected> select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </span>
                            </li>
                            <li class="d-flex justify-content-end align-items-center">
                                <span class="fw-bold me-2">Year:</span>
                                <span>
                                    <select class="form-select form-select-sm  fs-7" aria-label="Default select example">
                                        <option selected>select</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="table-responsive  position-relative">
                    <table class="table table-borderless table-bordered mb-0 ">
                        <thead>
                            <tr>
                                <th class="sticky-col first-col text-center" scope="row">Days</th>
                                <th scope="column">Fri</th>
                                <th scope="column" class="holiday">Sat</th>
                            </tr>
                            <tr>
                                <th class="sticky-col first-col text-center" scope="row">Date</th>
                                <th scope="column">01</th>
                                <th class="holiday">02</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row" class="sticky-col first-col">Attendance</th>
                                <th>P</th>
                                <th class="holiday">X</th>

                            </tr>
                            <tr>
                                <th scope="row" class="sticky-col first-col">Time In (hh:mm) </th>
                                <td>
                                    <input data-toggle="datepicker-time" type="text" name="pickup_time" value="09:00"
                                        class="input-custom-width">
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th scope="row" class="sticky-col first-col">Time Out (hh:mm)</th>
                                <td>
                                    <input data-toggle="datepicker-time" type="text" name="pickup_time" value="09:00"
                                        class="input-custom-width">
                                </td>
                                <td> </td>

                            </tr>
                            <tr>
                                <th scope="row" class="sticky-col first-col"> Hours Worked (hh.hh) </th>
                                <td>01</td>
                                <td> </td>

                            </tr>
                            <tr>
                                <th scope="row" class="sticky-col first-col"> <strong>Hours Worked (hh.mm)</strong> </th>
                                <td>01</td>
                                <td> </td>

                            </tr>
                            <tr>
                                <th scope="row" class="sticky-col first-col"><strong>Time Charge (hh.mm)</strong> </th>
                                <td>01</td>
                                <td> </td>

                            </tr>
                            <tr>
                                <th scope="row" class="sticky-col first-col">Schooner</th>
                                <td> <input type="text" class="input-custom-width"> </td>
                                <td> </td>

                            </tr>
                            <tr>
                                <th scope="row" class="sticky-col first-col">Give2Asia</th>
                                <td> <input type="text" class="input-custom-width"></td>
                                <td> </td>

                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                            <tr class="total">
                                <th scope="row" class="sticky-col first-col"> <strong>Hours Charged (hh.mm)</strong>
                                </th>
                                <td>01</td>
                                <td> </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="#" class="btn btn-primary btn-sm"> Save </a>
                </div>
            </div>
        </div>
    </div>





@endsection
