@extends('layouts.container')

@section('title', 'Vehicle view')
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
                            <li class="breadcrumb-item" aria-current="page">Vehicle view</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Vehicle view</h4>
                </div>
                <div class="ad-info justify-content-end">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"><i
                            class="bi-wrench-adjustable"></i> New Vehicle view</button>
                </div>
            </div>
        </div>
        <section class="registration">
            <div class="card mb-4 shadow mb-3">
                <div class="card-header bg-light p-2 px-3">
                    <h3 class="m-0 fs-6 text-capitalize">Vechile Request view</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi-activity"></i>
                                <span> Purpose of Travel</span>
                            </div>

                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi-calendar-range"></i>
                                <span> 2022-10-01 - 2022-12-10 <span class="badge bg-primary">12 days</span></span>
                            </div>

                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi-truck"></i>
                                <span> 4WD</span>
                            </div>

                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Days">
                                <i class="bi-pin-map"></i>
                                <span> Kathamdnu - Kakardvita</span>
                            </div>

                        </div>
                        <div class="col-lg-6 mb-2">
                            <div class="d-flex align-items-start gap-2" rel="tooltip" title="Travel from to">
                                <i class="bi-pin-map"></i>
                                <span> Kathamdnu - Kakardvita</span>
                            </div>

                        </div>


                        <div class="col-lg-6 mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi-currency-dollar"></i>
                                <span> Rs.12222</span>
                            </div>

                        </div>

                        <div class="col-lg-12 mb-2">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi-people"></i>
                                <span> Rames Shrestha, Krish Khadka,Rames Shrestha, Krish Khadka Rames Shrestha,
                                    Krish Khadka,Rames Shrestha, Krish Khadka Rames Shrestha, Krish Khadka,Rames
                                    Shrestha, Krish Khadka <span class="badge bg-info">5+ More</span></span>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
             <div class="card mb-4 shadow">
                <div class="card-body">
                    add remarks view here
                </div>
            </div>
        </section>

    @stop
