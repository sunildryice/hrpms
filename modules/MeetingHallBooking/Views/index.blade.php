@extends('layouts.container')

@section('title', 'Meeting Hall Booking')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#meeting-hall-requests-menu').addClass('active');
        });

        var oTable = $('#meetingHallTable').DataTable({
                scrollX: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('meeting.hall.bookings.index') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'meeting_hall',
                    name: 'maintenance_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'meeting_date',
                    name: 'meeting_date'
                },
                {
                    data: 'start_time',
                    name: 'start_time'
                },
                {
                    data: 'end_time',
                    name: 'end_time'
                },
                {
                    data: 'number_of_attendees',
                    name: 'number_of_attendees'
                },
                {
                    data: 'purpose',
                    name: 'purpose'
                },
                {
                    data: 'remarks',
                    name: 'remarks'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'booked_by',
                    name: 'booked_by'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className:'sticky-col'
                },
            ]
        });

        $('#meeting-hall-table').on('click', '.cancel-meeting-booking', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            let confirmText = $object.attr('data-confirm') ?? "Do you wnat to cancel this meeting hall booking ?"
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                oTable.ajax.reload();
            }
            //var confirmText = 'Do you want to cancel this meeting hall booking ?';
            ajaxSweetAlert($url, 'POST', {}, confirmText, successCallback);
        });

        $('#meeting-hall-table').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                $($object).closest('tr').remove();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });
    </script>
@endsection
@section('page-content')

        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">Meeting Hall Booking</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">Meeting Hall Booking</h4>
                </div>
                <div class="add-info justify-content-end">
                    <a href="{!! route('meeting.hall.bookings.create') !!}" class="btn btn-primary btn-sm">
                        <i class="bi-person-plus"></i> New Meeting Hall Booking
                    </a>
                </div>
            </div>

        </div>
        <div class="card" id="meeting-hall-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderedless" id="meetingHallTable">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('label.sn') }}</th>
                                <th>{{ __('label.meeting-hall') }}</th>
                                <th>{{ __('label.meeting-date') }}</th>
                                <th>{{ __('label.start-time') }}</th>
                                <th>{{ __('label.end-time') }}</th>
                                <th>{{ __('label.no-of-attendees') }}</th>
                                <th>{{ __('label.purpose') }}</th>
                                <th>{{ __('label.remarks') }}</th>
                                <th>{{ __('label.status') }}</th>
                                <th>{{ __('label.booked-by') }}</th>
                                <th>{{ __('label.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


@stop
