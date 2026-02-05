@extends('layouts.container')

@section('title', 'HR Training')
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
        $('.filter-items').on('click', function () {
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
                        <li class="breadcrumb-item" aria-current="page">HR Training</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">HR Training</h4>
            </div>
            <div class="ad-info justify-content-end">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                        class="bi-people-fill me-1"></i> New HR Training</button>
            </div>
        </div>
    </div>
    <section class="registration">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive mb-3">
                    <table class="table table-borderedless">
                        <thead class="bg-light">
                            <tr>

                                <th style="width:45px;"></th>
                                <th class="">Name of Course</th>
                                <th>Duration</th>
                                <th style="width: 40%;">Remarks</th>
                                <th>Status</th>
                                <th style="width: 130px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01</td>
                                <td class="">Name of Course</td>
                                <td>Duration</td>
                                <td>Remarks</td>

                                <td><span class="badge bg-primary">Pending</span></td>

                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i class="bi-eye-fill"></i></a>
                                    <a href="#" class="btn btn-sm btn-outline-primary"><i
                                            class="bi-pencil-square"></i></a>

                                    <a href="#" class="btn btn-sm btn-danger"><i class="bi-trash3"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header fw-bold">
                        Training Request Information
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center ad-info justify-content-end">
                        </div>
                        <div class="p-1">
                            <ul class="list-unstyled list-py-2 text-dark mb-0">
                                <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i
                                                class="bi-wrench-adjustable dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> Training1</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip"
                                        title="Training Request Number"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-book-half dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> ADdd</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Course Name"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-calendar3-range dropdown-item-icon"></i>
                                        </div>
                                        <div class="d-content-section"> 2022-06-15 -
                                            2022-06-16 <span class="badge bg-primary">2
                                                Days</span> </div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Training Period"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-clock dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> 00:00:10 Hrs</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Own Time"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-clock-fill dropdown-item-icon"></i>
                                        </div>
                                        <div class="d-content-section"> 00:00:12 Hrs</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Work Time"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-file-diff dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> 12</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Course Duration"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-currency-dollar dropdown-item-icon"></i>
                                        </div>
                                        <div class="d-content-section"> 1.00</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Course Fee"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-activity dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> 1.1.1.3 : HFOMC training at HF level</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Activity Code"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-123 dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> 6410 : Governance</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Account Code"></a>
                                </li>
                                <li class="position-relative">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="icon-section"> <i class="bi-chat-dots dropdown-item-icon"></i></div>
                                        <div class="d-content-section"> asdfasf</div>
                                    </div>
                                    <a href="#" class="stretched-link" rel="tooltip" title="Description"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-bold">Request Information </div>
                    <div class="card-body">
                        <div class="mb-3 border-bottom pb-2 mb-2">
                            <label for="">1. Why do you think feel you need this training.</label>
                            <p><i class="me-2"><strong>Answer:</strong></i>Why do you think feel you
                                need this training </p>
                        </div>
                        <div class="mb-3 border-bottom pb-2 mb-2">
                            <label for="">1. Why do you think feel you need this training.</label>
                            <p><i class="me-2"><strong>Answer:</strong></i>Why do you think feel you
                                need this training </p>
                        </div>
                        <div class="mb-3 border-bottom pb-2 mb-2">
                            <label for="">1. Why do you think feel you need this training.</label>
                            <p><i class="me-2"><strong>Answer:</strong></i>Why do you think feel you
                                need this training </p>
                        </div>
                        <div class="mb-3 border-bottom pb-2 mb-2">
                            <label for="">1. Why do you think feel you need this training.</label>
                            <p><i class="me-2"><strong>Answer:</strong></i>Why do you think feel you
                                need this training </p>
                        </div>
                    </div>
                </div>
                <form action="http://127.0.0.1:8000/reponses/training/1/request" method="post"
                    enctype="multipart/form-data" id="trainingDetailAddForm" autocomplete="off"
                    class="fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate">
                    <div class="card">
                        <div class="card-header fw-bold">
                            Response Fill
                        </div>
                        <div class="card-body">
                            <div class="row mb-2 fv-plugins-icon-container">
                                <div class="col-lg-12">
                                    <label for="qone" class="form-label required-label">1. Is training the appropriate
                                        solution? Hint:
                                        The
                                        problem must be caused by a skill/knowledge deficiency.
                                    </label>
                                    <textarea name="textarea[9]" id="qone" cols="30" rows="5"
                                        class="form-control question fv-plugins-icon-input"></textarea><i
                                        data-field="textarea" class="fv-plugins-icon"></i>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row mb-2 fv-plugins-icon-container">
                                <div class="col-lg-12">
                                    <label for="qone" class="form-label required-label">Is the request aligned with our
                                        current
                                        organizational objectives and priorities?
                                    </label>
                                    <textarea name="textarea[10]" id="qone" cols="30" rows="5"
                                        class="form-control question fv-plugins-icon-input"></textarea><i
                                        data-field="textarea" class="fv-plugins-icon"></i>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row mb-2 fv-plugins-icon-container">
                                <div class="col-lg-12">
                                    <label for="qone" class="form-label required-label">Are resources available to meet
                                        the request?
                                    </label>
                                    <textarea name="textarea[11]" id="qone" cols="30" rows="5"
                                        class="form-control question fv-plugins-icon-input"></textarea><i
                                        data-field="textarea" class="fv-plugins-icon"></i>
                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <label for="" class="form-label required-label">Send To</label>
                                    <div class="mt-2">
                                        <select name="reviewer_id"
                                            class="select2 form-control select2-hidden-accessible" data-width="100%"
                                            data-select2-id="select2-data-1-4wsy" tabindex="-1" aria-hidden="true">
                                            <option value="" data-select2-id="select2-data-3-3ej1">Select a Reviewer
                                            </option>
                                            <option value="15">
                                                archana awale
                                            </option>
                                            <option value="12">
                                                Sunil Adhikari
                                            </option>
                                            <option value="18">
                                                suman shrestha
                                            </option>
                                            <option value="21">
                                                Sony Maharjan
                                            </option>
                                            <option value="27">
                                                Bipin Khadka
                                            </option>
                                            <option value="24">
                                                laxmi shrestha
                                            </option>
                                            <option value="30">
                                                Rojit Rana
                                            </option>
                                            <option value="33">
                                                Mohan GC
                                            </option>
                                        </select><span class="select2 select2-container select2-container--default"
                                            dir="ltr" data-select2-id="select2-data-2-wdfn" style="width: 100%;"><span
                                                class="selection"><span
                                                    class="select2-selection select2-selection--single" role="combobox"
                                                    aria-haspopup="true" aria-expanded="false" tabindex="0"
                                                    aria-disabled="false"
                                                    aria-labelledby="select2-reviewer_id-qm-container"
                                                    aria-controls="select2-reviewer_id-qm-container"><span
                                                        class="select2-selection__rendered"
                                                        id="select2-reviewer_id-qm-container" role="textbox"
                                                        aria-readonly="true" title="Select a Reviewer">Select a
                                                        Reviewer</span><span class="select2-selection__arrow"
                                                        role="presentation"><b
                                                            role="presentation"></b></span></span></span><span
                                                class="dropdown-wrapper" aria-hidden="true"></span></span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="_token" value="14kcbje0EjKgBZa2SvLutf87qEiYA8bSwvoVyBDr">
                        </div>
                        <div class="card-footer border-0 justify-content-end d-flex gap-2">
                            <button type="submit" class="btn btn-success" name="btn" value="submit">Submit</button>
                            <a href="http://127.0.0.1:8000/reponses/training/request" class="btn btn-danger">Cancel</a>
                        </div>
                        <div></div><input type="hidden">
                    </div>
                    <input type="hidden" name="_token" value="14kcbje0EjKgBZa2SvLutf87qEiYA8bSwvoVyBDr">
            </div>
            <div class="card-footer border-0 justify-content-end d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm" name="btn" value="submit">Submit</button>
                <a href="http://127.0.0.1:8000/reponses/training/request" class="btn btn-danger btn-sm">Cancel</a>
            </div>
            <div></div><input type="hidden">
        </div>
        </form>
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
                    <div class="col-lg-12">

                        <label for="activitycode" class="form-label required-label">Name of Course(s) or Training
                            requested
                            Training Institution Name

                        </label>
                        <input type="text" name="" class="form-control" id="">

                    </div>

                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start h-100">
                            <label for="atvcde" class="form-label required-label">Date of course


                            </label>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-group has-validation">
                                    <div class="input-group-append">
                                        <span class="input-group-text required-label">Start</span>
                                    </div>
                                    <input type="text" class="form-control" name="departure_date" autofocus="">


                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-group has-validation">
                                    <div class="input-group-append">
                                        <span class="input-group-text required-label">End</span>
                                    </div>
                                    <input type="text" class="form-control" name="departure_date" autofocus="">


                                </div>
                            </div>
                        </div>



                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-3">
                        <div class="d-flex align-items-start h-100">
                            <label for="actcde" class="form-label required-label">Activity code


                            </label>
                        </div>
                    </div>
                    <div class="col-lg-9">

                        <select class="form-select select2" name="" id="actcde">
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
                            <label for="prblmdsc" class="form-label required-label">Brief Descriptions


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
                            <label for="fileattch" class="form-label required-label">Attach a copy of course(s)</label>
                        </div>
                    </div>
                    <div class="col-lg-9">

                        <input type="file" class="form-control" id="fileattch">


                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#exampleModal1">Next</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal1" aria-labelledby="exampleModal1Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6" id="exampleModal1Label">Maintenance Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="row mb-2">
                    <div class="col-lg-12">

                        <label for="qone" class="form-label required-label">Why do you think/feel you need this
                            training?
                        </label>

                        <textarea name="" id="qone" cols="30" rows="5" class="form-control"></textarea>



                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class=" form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="q3" name="active"
                                checked="">
                            <label class="form-check-label" for="q3">I meet the criteria for this application and I
                                have completed all information as requested and attached the course
                                description.</label>

                        </div>

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