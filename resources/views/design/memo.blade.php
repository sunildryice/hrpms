@extends('layouts.container')

@section('title', 'Memo')
@section('page_css')
    <link rel="stylesheet" href="https://selectize.dev/css/selectize.bootstrap5.css">
@endsection
@section('page_js')
    <script src="https://selectize.dev/js/selectize.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script>
        $('[data-toggle="datepicker"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'YYYY-MM-DD'
        });
        ClassicEditor
            .create(document.querySelector('#details-desc'))
            .catch(error => {
                console.error(error);
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
                            <li class="breadcrumb-item" aria-current="page">Memo</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Memo</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-people-fill me-1"></i> New Memo</button>
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
                                    <th style="width: 47%;">Bill No</th>
                                    <th>Date </th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th style="width: 130px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>01</td>
                                    <th style="width: 47%;">Bill No</th>
                                    <th>Date </th>
                                    <th>Subject</th>
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
                </div>
            </div>

        </section>
        <div class="modal fade" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fs-6" id="exampleModalLabel">Inter Office Memo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="memto" class="form-label required-label">To
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select class="form-select select2" name="" id="memto">
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
                                    <label for="memthrough" class="form-label required-label">Through
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select class="form-select select2" name="" id="memthrough">
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
                                    <label for="memfrom" class="form-label required-label">From
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <select class="form-select select2" name="" id="memfrom">
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
                                    <label for="atvcde" class="form-label required-label">Date</label>
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
                                    <label for="prblmdsc" class="form-label required-label">Subject


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" name="" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="details-desc" class="form-label required-label">Brief Descriptions


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea name="" id="details-desc" cols="30" rows="6" class="form-control" id="editor"></textarea>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="details-desc" class="form-label required-label">Enclosure


                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <textarea name="" cols="30" rows="6" class="form-control"></textarea>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-lg-3">
                                <div class="d-flex align-items-start h-100">
                                    <label for="fileattch" class="form-label required-label">Attach File(s)</label>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <input type="file" class="form-control" id="fileattch">


                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>

    @stop
