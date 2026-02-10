@extends('layouts.container')

@section('title', 'Profile')

@section('page_css')
    <style>
        .staff-image {
            width: 100px;
            height: 100px;
        }

        .staff-image img {
            height: 100px;
            object-fit: contain;
        }
    </style>
@endsection

@section('page_js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#dashboard').addClass('active');

            $('.step-item').click(function() {
                $('.step-item').removeClass('active');
                $(this).addClass('active');
                var tagid = $(this).data('tag');
                $('.c-tabs-content').removeClass('active').addClass('hide');
                $('#' + tagid).addClass('active').removeClass('hide');
            });


            $('#leaveRequestsTable').DataTable();
            $('#leaveEncashTable').DataTable();

            var oTable = $('#assetTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('profile.assets.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'asset_number',
                        name: 'asset_number'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'office',
                        name: 'office'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'assigned_on',
                        name: 'assigned_on'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'sticky-col'
                    },
                ]
            });

            // Check In Today with Confirmation
            $(document).on('click', '.checkin-today-btn', function() {
                let date = $(this).data('date');
                let btn = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    // icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#01aef0',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Check In',
                    cancelButtonText: 'Cancel',
                    // reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="bi bi-hourglass-split"></i> Checking in...');

                        $.ajax({
                            url: "{{ route('attendance.checkin.today') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                date: date
                            },
                            success: function(response) {
                                toastr.success('Checked in at ' + response.time);
                                $('#today-attendance-action').html(`
                        <button class="btn btn-warning btn-sm checkout-today-btn" data-date="${date}">
                            <i class="bi bi-box-arrow-in-left"></i> Check Out
                        </button>
                    `);
                                oTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(
                                    '<i class="bi bi-box-arrow-in-right"></i> Check In Now'
                                );
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to check in');
                            }
                        });
                    }
                });
            });

            // Check Out Today with Confirmation
            $(document).on('click', '.checkout-today-btn', function() {
                let date = $(this).data('date');
                let btn = $(this);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    // icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#01aef0',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Check Out',
                    cancelButtonText: 'Cancel',
                    reverseButtons: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="bi bi-hourglass-split"></i> Checking out...');

                        $.ajax({
                            url: "{{ route('attendance.checkout.today') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                date: date
                            },
                            success: function(response) {
                                toastr.success('Checked out at ' + response.time +
                                    ' on ' + response.worked_hours + ' hours worked'
                                );
                                $('#today-attendance-action').html(`
                                    <button class="btn btn-success btn-sm" disabled>
                                        <i class="bi bi-fingerprint"></i> Completed Today
                                    </button>
                                `);
                                oTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                btn.prop('disabled', false).html(
                                    '<i class="bi bi-box-arrow-in-left"></i> Check Out'
                                );
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to check out');
                            }
                        });
                    }
                });
            });
        });
    </script>
    {{-- for navigation menu --}}
    <script>
        const container = document.querySelector(".tabs-s");
        const primary = container.querySelector(".-primary");
        const primaryItems = container.querySelectorAll(".-primary > li:not(.-more)");
        container.classList.add("--jsfied");

        // insert "more" button and duplicate the list

        primary.insertAdjacentHTML(
            "beforeend",
            `
  <li class="-more parent-tab-s is-more">
    <button type="button" aria-haspopup="true" aria-expanded="false">
      More <span>&darr;</span>
    </button>
    <ul class="p-0 m-0 -secondary list-unstyled">
      ${primary.innerHTML}
    </ul>
  </li>
`
        );
        const secondary = container.querySelector(".-secondary");
        const secondaryItems = secondary.querySelectorAll(".parent-tab-s");
        const allItems = container.querySelectorAll("li");
        const moreLi = primary.querySelector(".-more");
        const moreBtn = moreLi.querySelector("button");
        moreBtn.addEventListener("click", (e) => {
            e.preventDefault();
            container.classList.toggle("--show-secondary");
            moreBtn.setAttribute(
                "aria-expanded",
                container.classList.contains("--show-secondary")
            );
        });

        // adapt tabs-s

        const doAdapt = () => {
            // reveal all items for the calculation
            allItems.forEach((item) => {
                item.classList.remove("--hidden");
            });

            // hide items that won't fit in the Primary
            let stopWidth = moreBtn.offsetWidth;
            let hiddenItems = [];
            const primaryWidth = primary.offsetWidth;
            primaryItems.forEach((item, i) => {
                if (primaryWidth >= stopWidth + item.offsetWidth) {
                    stopWidth += item.offsetWidth;
                } else {
                    item.classList.add("--hidden");
                    hiddenItems.push(i);
                }
            });

            // toggle the visibility of More button and items in Secondary
            if (!hiddenItems.length) {
                moreLi.classList.add("--hidden");
                container.classList.remove("--show-secondary");
                moreBtn.setAttribute("aria-expanded", false);
            } else {
                secondaryItems.forEach((item, i) => {
                    if (!hiddenItems.includes(i)) {
                        item.classList.add("--hidden");
                    }
                });
            }
        };

        doAdapt(); // adapt immediately on load
        window.addEventListener("resize", doAdapt); // adapt on window resize

        // hide Secondary on the outside click

        document.addEventListener("click", (e) => {
            let el = e.target;
            while (el) {
                if (el === secondary || el === moreBtn) {
                    return;
                }
                el = el.parentNode;
            }
            container.classList.remove("--show-secondary");
            moreBtn.setAttribute("aria-expanded", false);
        });
    </script>
@endsection
@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="gap-2 d-flex flex-column flex-lg-row align-items-start align-items-lg-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="m-0 breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 mt-1 lh1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
            <div class="add-info justify-content-end d-flex align-items-center gap-1">
                {{-- <div class="py-3 rounded text-end" id="today-attendance-action">
                    @php
                        $today = now()->format('Y-m-d');
                        $employeeId = auth()->user()->employee->id;

                        $attendanceMaster = \Modules\EmployeeAttendance\Models\Attendance::where(
                            'employee_id',
                            $employeeId,
                        )
                            ->where('year', now()->year)
                            ->where('month', now()->month)
                            ->first();

                        $hasCheckIn = false;
                        $hasCheckOut = false;

                        if ($attendanceMaster) {
                            $todayDetail = $attendanceMaster
                                ->attendanceDetails()
                                ->where('attendance_date', $today)
                                ->first();

                            $hasCheckIn = $todayDetail && $todayDetail->checkin;
                            $hasCheckOut = $todayDetail && $todayDetail->checkout;
                        }
                    @endphp

                    @if ($hasCheckOut)
                        <button class="btn btn-success btn-sm" disabled>
                            <i class="bi bi-fingerprint"></i> Completed Today
                        </button>
                    @elseif ($hasCheckIn)
                        <button class="btn btn-warning btn-sm checkout-today-btn" data-date="{{ $today }}">
                            <i class="bi bi-box-arrow-in-left"></i> Check Out
                        </button>
                    @else
                        <button class="btn btn-primary btn-sm checkin-today-btn" data-date="{{ $today }}">
                            <i class="bi bi-box-arrow-in-right"></i> Check In
                        </button>
                    @endif
                </div> --}}
                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary" style=" text-decoration: none">Edit
                    Profile <i class="bi bi-pencil-square"></i></a>
            </div>
        </div>

    </div>
    <div class="container-fluid">
        <div class="emp-header">

        </div>
        <div class="row">
            <div class="col-lg-3">
                {{-- <div class="p-2 mb-2 bg-white rounded">
                    <div class="d-flex">
                        <div class="text-white user-pro d-flex align-items-center justify-content-center bg-danger">
                            <img src="" alt="">
                            <i class="bi-person"></i>
                        </div>
                    </div>
                </div> --}}
                <div class="card">
                    <div class="card-header fw-bold">
                        Profile
                    </div>
                    @php
                        $ppflag =
                            file_exists('storage/' . $employee->profile_picture) && $employee->profile_picture != '';
                    @endphp
                    <div class="card-body">
                        <div class="p2">
                            <div
                                class="user-pro d-flex align-items-center justify-content-center text-white mb-4 @if ($ppflag) staff-image @else bg-danger @endif">
                                @if ($ppflag)
                                    <img src="{{ asset('storage/' . $employee->profile_picture) }}" alt=""
                                        class="w-100">
                                @else
                                    <i class="bi-person"></i>
                                @endif
                            </div>
                            <ul class="mb-0 list-unstyled list-py-2 text-dark">
                                <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>
                                <li class="position-relative"><i
                                        class="bi-person dropdown-item-icon"></i>{{ $employee->getFullName() }}
                                    <a href="#" class="stretched-link" rel="tooltip" title="Profile"></a>
                                </li>
                                <li><span rel="tooltip" title="Address"><i class="bi-people dropdown-item-icon"></i>
                                        {{ $employee->getMaritalStatus() }}</span>
                                </li>
                                <li><span rel="tooltip" title="Address"><i class="bi-building dropdown-item-icon"></i>
                                        {{ $employee->address->temporary_district ? $employee->address->temporary_district->district_name : '' }}</span>
                                </li>

                                <li class="pt-4 pb-2"><span
                                        class="card-subtitle text-uppercase text-primary">Contacts</span></li>
                                <li class="position-relative"><i class="bi-at dropdown-item-icon"></i>
                                    {{ $employee->official_email_address }}
                                    <a href="#" class="stretched-link" rel="tooltip" title="Contact email"></a>
                                </li>
                                <li class="position-relative"><i class="bi-phone dropdown-item-icon"></i>
                                    {{ $employee->mobile_number }}
                                    <a href="#" class="stretched-link text-decoration-none" rel="tooltip"
                                        title="Contact Number"></a>
                                </li>
                                @php
                                    $locationUrl = $employee->address?->current_location;
                                @endphp
                                @isset($locationUrl)
                                    <li class="position-relative"><i class="bi-geo-alt dropdown-item-icon me-2"></i>
                                        <a href="{{ $locationUrl }}" target="_blank"
                                            class="stretched-link text-decoration-none" rel="tooltip"
                                            title="Google Map location">
                                            Location <i class="bi-box-arrow-up-right dropdown-item-icon me-2"></i> </a>
                                    </li>
                                @endisset
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="mb-2 bg-menus- bg-s-custom">
                    <nav class="tabs-s">
                        <ul class="-primary">
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="general_information"
                                    class="nav-link step-item text-decoration-none active">
                                    <i class="nav-icon bi bi-info-circle"></i>General Information
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="leave"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-person-workspace"></i>Leave Details
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="{{ route('attendance.show', $employee->id) }}"
                                    class="nav-link text-decoration-none">
                                    <i class="nav-icon bi bi-fingerprint"></i>Attendance
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="asset"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-cart"></i>Assets
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="tenure"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-person-workspace"></i>Tenure
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="address"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-pin-map"></i>Address
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="family_details"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-people"></i>Family Details
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="medical_information"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-calendar-heart"></i>Medical Information
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="education"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-journal-text"></i>Education
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="experience"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-explicit"></i>Experience
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="training"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-calendar4-range"></i>Training
                                </a>
                            </li>
                            <li class="nav-item parent-tab-s">
                                <a href="javascript:void(0);" data-tag="social-media"
                                    class="nav-link step-item text-decoration-none">
                                    <i class="nav-icon bi bi-globe"></i>Social Media
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="c-tabs-contents">
                    <div class="c-tabs-content active" id="general_information">
                        <div class="card">
                            <div class="card-header fw-bold">
                                General Information
                            </div>
                            <div class="card-body">
                                <div class="p2">
                                    <div class="table-responsive">
                                        <table class="table" id="generalInformationTable">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Staff ID </th>
                                                    <td>{{ $employee->request_id }}</td>
                                                    <td>{{ $employee->employeeType?->title }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Full Name: </th>
                                                    <td colspan="3">{{ $employee->getFullName() }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Official Email Address </th>
                                                    <td colspan="3">{{ $employee->official_email_address }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Mobile Number</th>
                                                    <td colspan="3">{{ $employee->mobile_number }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Joined Date (DD/MM/YYYY) AD</th>
                                                    <td colspan="3">{{ $employee->getJoinedDate() }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Date of Birth (DD/MM/YYYY) AD</th>
                                                    <td colspan="3">{{ $employee->getDateOfBirth() }}</td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">Citizenship Number</th>
                                                    <td>
                                                        {{ $employee->citizenship_number }}
                                                    </td>
                                                    <td>
                                                        @if ($employee->citizenship_attachment && file_exists('storage/' . $employee->citizenship_attachment))
                                                            <a href="{!! asset('storage/' . $employee->citizenship_attachment) !!}" target="_blank"
                                                                class="fs-5" title="View Attachment">
                                                                <i class="bi bi-file-earmark-medical"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted"> (No attachment)</span>
                                                        @endif
                                                    </td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">NID Number</th>
                                                    <td colspan="3">{{ $employee->nid_number }}</td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">PAN Number</th>
                                                    <td>
                                                        {{ $employee->pan_number }}
                                                    </td>
                                                    <td>
                                                        @if ($employee->pan_attachment && file_exists('storage/' . $employee->pan_attachment))
                                                            <a href="{!! asset('storage/' . $employee->pan_attachment) !!}" target="_blank"
                                                                class="fs-5" title="View Attachment">
                                                                <i class="bi bi-file-earmark-medical"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted"> (No attachment)</span>
                                                        @endif
                                                    </td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">Passport Number</th>
                                                    <td>
                                                        {{ $employee->passport_number }}
                                                    </td>
                                                    <td>
                                                        @if ($employee->passport_attachment && file_exists('storage/' . $employee->passport_attachment))
                                                            <a href="{!! asset('storage/' . $employee->passport_attachment) !!}" target="_blank"
                                                                class="fs-5" title="View Attachment">
                                                                <i class="bi bi-file-earmark-medical"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted"> (No attachment)</span>
                                                        @endif
                                                    </td>
                                                    <td></td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">Driving License Number</th>
                                                    <td colspan="3">{{ $employee->vehicle_license_number }}</td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">CIT Number</th>
                                                    <td colspan="3">{{ $employee->finance->cit_number }}</td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">Bank Account Number</th>
                                                    <td colspan="3">{{ $employee->finance->account_number }}</td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">Bank Account Holder Name</th>
                                                    <td colspan="3">{{ $employee->finance->account_holder_name }}</td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">Bank Name</th>
                                                    <td colspan="3">{{ $employee->finance->bank_name }}</td>
                                                </tr>

                                                <tr>
                                                    <th scope="row">Gender</th>
                                                    <td colspan="3">{{ $employee->genderName->title }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Probation Complete Date</th>
                                                    <td colspan="3">{{ $employee->probation_complete_date }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Roles</th>
                                                    <td colspan="3">{{ $employee->user->getRolesName() }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Office</th>
                                                    <td colspan="3">{{ $employee->latestTenure->getOfficeName() }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- End Table -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-tabs-content" id="leave">
                        <div class="card">
                            <div class="card-body">
                                <div class="p2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="employeeLeaveTable">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Month</th>
                                                    @foreach ($leaveTypes as $leaveType)
                                                        @if ($leaveType->leave_frequency == 2)
                                                            <th colspan="5" class="text-center">
                                                                {{ $leaveType->title }}
                                                                ({{ $leaveType->getLeaveBasis() }})
                                                            </th>
                                                        @else
                                                            <th rowspan="1" class="text-center">
                                                                {{ $leaveType->title }}
                                                                ({{ $leaveType->getLeaveBasis() }})</th>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    @foreach ($leaveTypes as $leaveType)
                                                        @if ($leaveType->leave_frequency == 2)
                                                            <th>Opening Balance</th>
                                                            <th>Earned</th>
                                                            <th>Taken</th>
                                                            <th>Paid</th>
                                                            <th>Balance</th>
                                                        @else
                                                            <th class="text-center">Taken</th>
                                                        @endif
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($leaves->groupBy('reported_date') as $leaveGroups)
                                                    <tr>
                                                        <td>{{ $leaveGroups->first()->getReportedDateMonth() }}</td>
                                                        @foreach ($leaveTypes as $leaveType)
                                                            @php
                                                                $selectedLeave = $leaveGroups
                                                                    ->filter(function ($leaveGroup) use ($leaveType) {
                                                                        return $leaveType->id ==
                                                                            $leaveGroup->leave_type_id;
                                                                    })
                                                                    ->first();
                                                            @endphp
                                                            @if ($leaveType->leave_frequency == 2)
                                                                <td class="text-center">
                                                                    {{ $selectedLeave?->opening_balance }}</td>
                                                                <td>{{ $selectedLeave?->earned }}</td>
                                                                <td>{{ $selectedLeave?->taken }}</td>
                                                                <td>{{ $selectedLeave?->paid }}</td>
                                                                <td>{{ $selectedLeave?->balance }}</td>
                                                            @else
                                                                <td class="text-center">{{ $selectedLeave?->taken }}</td>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <th>Total</th>
                                                @foreach ($leaveTypes as $leaveType)
                                                    @if ($leaveType->leave_frequency == 2)
                                                        @php
                                                            $earnedTotal = $leaves
                                                                ->where('leave_type_id', $leaveType->id)
                                                                ->sum('earned');
                                                            $takenTotal = $leaves
                                                                ->where('leave_type_id', $leaveType->id)
                                                                ->sum('taken');
                                                            $paidTotal = $leaves
                                                                ->where('leave_type_id', $leaveType->id)
                                                                ->sum('paid');
                                                        @endphp
                                                        <td></td>
                                                        <td>{{ $earnedTotal }}</td>
                                                        <td>{{ $takenTotal }}</td>
                                                        <td>{{ $paidTotal }}</td>
                                                        <td></td>
                                                    @else
                                                        @php
                                                            $takenTotal = $leaves
                                                                ->where('leave_type_id', $leaveType->id)
                                                                ->sum('taken');
                                                        @endphp
                                                        <td class="text-center">{{ $takenTotal }}</td>
                                                    @endif
                                                @endforeach
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- End Table -->
                                </div>
                            </div>
                        </div>

                        @foreach ($previousLeaves->groupBy(function ($item) {
                return $item->reported_date->format('Y');
            })->sortKeysDesc() as $index => $prevLeaves)
                            <div class="mb-3 card collapsible-card">
                                <div role="button" class="card-header d-flex align-items-center justify-content-between"
                                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $index }}"
                                    aria-expanded="false" aria-controls="collapseCard">
                                    Leave Details: {{ $index }}
                                    <i class="bi bi-caret-down-fill indicator"></i>
                                </div>
                                <div id="collapse-{{ $index }}" class="collapse">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="employeeLeaveTable">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th rowspan="2">Month</th>
                                                        @foreach ($leaveTypes as $leaveType)
                                                            @if ($leaveType->leave_frequency == 2)
                                                                <th colspan="5" class="text-center">
                                                                    {{ $leaveType->title }}
                                                                    ({{ $leaveType->getLeaveBasis() }})
                                                                </th>
                                                            @else
                                                                <th rowspan="1" class="text-center">
                                                                    {{ $leaveType->title }}
                                                                    ({{ $leaveType->getLeaveBasis() }})</th>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                    <tr>
                                                        @foreach ($leaveTypes as $leaveType)
                                                            @if ($leaveType->leave_frequency == 2)
                                                                <th>Opening Balance</th>
                                                                <th>Earned</th>
                                                                <th>Taken</th>
                                                                <th>Paid</th>
                                                                <th>Balance</th>
                                                            @else
                                                                <th class="text-center">Taken</th>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($prevLeaves->groupBy('reported_date') as $leaveGroups)
                                                        @if (
                                                            $employee->exitHandoverNote &&
                                                                is_null($employee->activated_at) &&
                                                                $leaveGroups->first()?->reported_date->gt($employee->exitHandoverNote?->last_duty_date))
                                                            @break;
                                                        @endif
                                                        <tr>
                                                            <td>{{ $leaveGroups->first()->getReportedDateMonth() }}</td>
                                                            @foreach ($leaveTypes as $leaveType)
                                                                @php
                                                                    $selectedLeave = $leaveGroups
                                                                        ->filter(function ($leaveGroup) use (
                                                                            $leaveType,
                                                                        ) {
                                                                            return $leaveType->id ==
                                                                                $leaveGroup->leave_type_id;
                                                                        })
                                                                        ->first();
                                                                @endphp
                                                                @if ($leaveType->leave_frequency == 2)
                                                                    <td class="text-center">
                                                                        {{ $selectedLeave?->opening_balance }}</td>
                                                                    <td>{{ $selectedLeave?->earned }}</td>
                                                                    <td>{{ $selectedLeave?->taken }}</td>
                                                                    <td>{{ $selectedLeave?->paid }}</td>
                                                                    <td>{{ $selectedLeave?->balance }}</td>
                                                                @else
                                                                    <td class="text-center">{{ $selectedLeave?->taken }}
                                                                    </td>
                                                                @endif
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <th>Total</th>
                                                    @foreach ($leaveTypes as $leaveType)
                                                        @if ($leaveType->leave_frequency == 2)
                                                            @php
                                                                $earnedTotal = $prevLeaves
                                                                    ->where('leave_type_id', $leaveType->id)
                                                                    ->sum('earned');
                                                                $takenTotal = $prevLeaves
                                                                    ->where('leave_type_id', $leaveType->id)
                                                                    ->sum('taken');
                                                                $paidTotal = $prevLeaves
                                                                    ->where('leave_type_id', $leaveType->id)
                                                                    ->sum('paid');
                                                            @endphp
                                                            <td></td>
                                                            <td>{{ $earnedTotal }}</td>
                                                            <td>{{ $takenTotal }}</td>
                                                            <td>{{ $paidTotal }}</td>
                                                            <td></td>
                                                        @else
                                                            @php
                                                                $takenTotal = $prevLeaves
                                                                    ->where('leave_type_id', $leaveType->id)
                                                                    ->sum('taken');
                                                            @endphp
                                                            <td class="text-center">{{ $takenTotal }}</td>
                                                        @endif
                                                    @endforeach
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="card">
                            <div class="card-header fw-bold">
                                Approved Leave Requests
                            </div>
                            <div class="card-body" style="overflow: auto;">
                                <table class="table table-responsive table-sm" id="leaveRequestsTable">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Type</th>
                                            <th>Request Days</th>
                                            <th>Request Date</th>
                                            <th>Leave Request No.</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaveRequests as $key => $leaveRequest)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $leaveRequest->getLeaveType() }}</td>
                                                <td>{{ $leaveRequest->getLeaveDuration() . ' ' . $leaveRequest->leaveType->getLeaveBasis() }}
                                                </td>
                                                <td>{{ $leaveRequest->getRequestDate() }}</td>
                                                <td>{{ $leaveRequest->getLeaveNumber() }}</td>
                                                <td>{{ $leaveRequest->getStartDate() }}</td>
                                                <td>{{ $leaveRequest->getEndDate() }}</td>
                                                <td><span
                                                        class="{{ $leaveRequest->getStatusClass() }}">{{ $leaveRequest->getStatus() }}</span>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{ route('leave.requests.detail', $leaveRequest->id) }}"
                                                        title="View Leave Request" target="_blank"><i
                                                            class="bi bi-eye"></i></a>
                                                    &emsp;
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{ route('leave.requests.print', $leaveRequest->id) }}"
                                                        title="Print Leave Request" target="_blank"><i
                                                            class="bi-printer"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header fw-bold">
                                Approved Leave Encashment Requests
                            </div>
                            <div class="card-body" style="overflow: auto;">
                                <table class="table table-responsive table-sm" id="leaveEncashTable">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Type</th>
                                            <th>Encashed Balance</th>
                                            <th>Request Date</th>
                                            <th>Leave Encash No.</th>
                                            <th>Requester</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaveEncashments as $key => $leaveEncash)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $leaveEncash->getLeaveType() }}</td>
                                                <td>{{ $leaveEncash->encash_balance . ' ' . $leaveEncash->leaveType->getLeaveBasis() }}
                                                </td>
                                                <td>{{ $leaveEncash->getRequestDate() }}</td>
                                                <td>{{ $leaveEncash->getEncashNumber() }}</td>
                                                <td>{{ $leaveEncash->getRequesterName() }}</td>
                                                <td><span
                                                        class="{{ $leaveEncash->getStatusClass() }}">{{ $leaveEncash->getStatus() }}</span>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{ route('approved.leave.encash.show', $leaveEncash->id) }}"
                                                        title="View Leave Request" target="_blank"><i
                                                            class="bi bi-eye"></i></a>
                                                    &emsp;
                                                    <a class="btn btn-sm btn-outline-primary"
                                                        href="{{ route('leave.encash.print', $leaveEncash->id) }}"
                                                        title="Print Leave Request" target="_blank"><i
                                                            class="bi-printer"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

                    <div class="c-tabs-content" id="asset">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="assetTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('label.sn') }}</th>
                                                <th>{{ __('label.asset-number') }}</th>
                                                <th>{{ __('label.item-name') }}</th>
                                                <th>{{ __('label.office') }}</th>
                                                <th>{{ __('label.department') }}</th>
                                                <th>{{ __('label.assigned-on') }}</th>
                                                <th>Handover</th>
                                                <th>{{ __('label.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-tabs-content" id="tenure">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Tenure
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    @foreach ($employee->tenures as $tenure)
                                        <table class="table table-bordered">
                                            <tbody>
                                                @if ($loop->first)
                                                    <label class="mb-2 form-label fw-bold">Latest Tenure</label>
                                                @endif
                                                <tr>
                                                    <th scope="row" width="10%">Position: </th>
                                                    <td colspan="3">{{ $tenure->getDesignationName() }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">{{ __('label.from-date') }}: </th>
                                                    <td colspan="3">{{ $tenure->getJoinedDate() }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">To Date: </th>
                                                    <td colspan="3">{{ $tenure->getToDate() }}</td>
                                                </tr>
                                                @if ($loop->first && $employee->exitHandoverNote && is_null($employee->activated_at))
                                                    <tr class="text-danger">
                                                        <th scope="row">Resignation Date: </th>
                                                        <td colspan="3">
                                                            {{ $employee->exitHandoverNote?->getResignationDate() }}
                                                        </td>
                                                    </tr>
                                                    <tr class="text-danger">
                                                        <th scope="row">Last Duty Date: </th>
                                                        <td colspan="3">
                                                            {{ $employee->exitHandoverNote?->getLastDutyDate() }}
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th scope="row">Duty Station:</th>
                                                    <td colspan="3">{{ $tenure->duty_station }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">District:</th>
                                                    <td colspan="3">{{ $tenure->getDutyStation() }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Office:</th>
                                                    <td colspan="3">{{ $tenure->getOfficeName() }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Line Manager Name:</th>
                                                    <td colspan="3">{{ $tenure->getSupervisorName() }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Reviewer Name:</th>
                                                    <td colspan="3">
                                                        {{ $tenure->getNextLineManagerName() }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-tabs-content" id="asset">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="assetTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('label.sn') }}</th>
                                                <th>{{ __('label.asset-number') }}</th>
                                                <th>{{ __('label.item-name') }}</th>
                                                <th>{{ __('label.office') }}</th>
                                                <th>{{ __('label.department') }}</th>
                                                <th>{{ __('label.assigned-on') }}</th>
                                                <th>Handover</th>
                                                <th>{{ __('label.action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-tabs-content" id="tenure">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Tenure
                            </div>
                            <div class="card-body">
                                <div class="p2">
                                    <div class="table-responsive">
                                        @foreach ($employee->tenures as $tenure)
                                            <div class="card">
                                                <div class="card-body">
                                                    <table>
                                                        <tbody>
                                                            @if ($loop->first)
                                                                <u>
                                                                    <span><strong>Latest Tenure</strong></span>
                                                                </u>
                                                            @endif
                                                            <tr>
                                                                <th scope="row">Position: </th>
                                                                <td colspan="3">
                                                                    {{ $tenure->getDesignationName() }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Joining Date: </th>
                                                                <td colspan="3">{{ $tenure->getJoinedDate() }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Duty Station:</th>
                                                                <td colspan="3">{{ $tenure->getDutyStation() }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Office:</th>
                                                                <td colspan="3">{{ $tenure->getOfficeName() }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Supervisor Name:</th>
                                                                <td colspan="3">{{ $tenure->getSupervisorName() }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Cross-functional Supervisor Name:
                                                                </th>
                                                                <td colspan="3">
                                                                    {{ $tenure->getCrossSupervisorName() }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Next Line Manager Name:</th>
                                                                <td colspan="3">
                                                                    {{ $tenure->getNextLineManagerName() }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-tabs-content" id="address">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Address
                            </div>
                            <div class="card-body">
                                <div class="p2">
                                    <div class="table-responsive">
                                        <table class="table" id="addressTable">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" rowspan="4">Current Address</th>
                                                    <td colspan="3">Province:
                                                        {{ $employee->address->temporary_province->province_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">District:
                                                        {{ $employee->address->temporary_district->district_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Municipality:
                                                        {{ $employee->address->temporary_local_level->local_level_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Ward: {{ $employee->address->temporary_ward }}</td>
                                                    <td colspan="2">Tole: {{ $employee->address->temporary_tole }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row" rowspan="4">Permanent Address</th>
                                                    <td colspan="3">Province:
                                                        {{ $employee->address->permanent_province->province_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">District:
                                                        {{ $employee->address->permanent_district->district_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Municipality:
                                                        {{ $employee->address->permanent_local_level->local_level_name }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Ward: {{ $employee->address->permanent_ward }}</td>
                                                    <td colspan="2">Tole: {{ $employee->address->permanent_tole }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-tabs-content" id="family_details">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Family Details
                            </div>
                            @if ($employee->familyDetails->isNotEmpty())
                                @foreach ($employee->familyDetails as $familyDetail)
                                    <div class="card-body">
                                        <div class="p2">
                                            <div class="table-responsive">
                                                <table class="table" id="familyDetailsTable">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Full Name</th>
                                                            <td>{{ $familyDetail->full_name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Relationship</th>
                                                            <td>{{ $familyDetail->getRelationName() }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Contact Number</th>
                                                            <td>{{ $familyDetail->contact_number }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Nominee ?</th>
                                                            <td>{{ isset($familyDetail->nominee_at) ? 'Yes' : 'No' }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="card-body">
                                    <div class="p2">
                                        <div class="table-responsive">
                                            <table class="table" id="familyDetailsTable">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row">Full Name</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Relationship</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Contact Number</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Nominee ?</th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="c-tabs-content" id="medical_information">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Medical Information
                            </div>
                            <div class="card-body">
                                <div class="p2">
                                    <div class="table-responsive">
                                        <table class="table" id="medicalInformationTable">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Blood Group</th>
                                                    <td>{{ $employee->medicalCondition->bloodGroup->title }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Medical Condition</th>
                                                    <td>{{ $employee->medicalCondition->medical_condition }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Remarks</th>
                                                    <td>{{ $employee->medicalCondition->remarks }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="c-tabs-content" id="education">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Education
                            </div>
                            @if ($employee->education->isNotEmpty())
                                @foreach ($employee->education as $education)
                                    <div class="card-body">
                                        <div class="p2">
                                            <div class="table-responsive">
                                                <table class="table" id="educationTable">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Education Level</th>
                                                            <td>{{ $education->getEducationLevel() }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Name of Degree</th>
                                                            <td>{{ $education->getDegree() }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Institution</th>
                                                            <td>{{ $education->getInstitution() }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Passed Year</th>
                                                            <td>{{ $education->getPassedYear() }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="card-body">
                                    <div class="p2">
                                        <div class="table-responsive">
                                            <table class="table" id="educationTable">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row">Education Level</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Name of Degree</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Institution</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Passed Year</th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="c-tabs-content" id="experience">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Experience
                            </div>
                            @if ($employee->experiences->isNotEmpty())
                                @foreach ($employee->experiences as $experience)
                                    <div class="card-body">
                                        <div class="p2">
                                            <div class="table-responsive">
                                                <table class="table" id="experienceTable">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Institution</th>
                                                            <td colspan="3">{{ $experience->institution }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Position</th>
                                                            <td colspan="3">{{ $experience->position }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Period From</th>
                                                            <td>{{ $experience->getPeriodFrom() }}</td>

                                                            <th scope="row">Period To</th>
                                                            <td>{{ $experience->getPeriodTo() }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Remarks</th>
                                                            <td colspan="3">{{ $experience->remarks }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="card-body">
                                    <div class="p2">
                                        <div class="table-responsive">
                                            <table class="table" id="experienceTable">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row">Institution</th>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Position</th>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Period From</th>
                                                        <td></td>

                                                        <th scope="row">Period To</th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">Remarks</th>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="c-tabs-content" id="training">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Training
                            </div>
                            @if ($employee->trainings->isNotEmpty())
                                @foreach ($employee->trainings as $training)
                                    <div class="card-body">
                                        <div class="p2">
                                            <div class="table-responsive">
                                                <table class="table" id="trainingTable">
                                                    <tbody>
                                                        <div>
                                                            <tr>
                                                                <th scope="row">Institution</th>
                                                                <td colspan="3">{{ $training->institution }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Training Topic</th>
                                                                <td colspan="3">{{ $training->training_topic }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Period From</th>
                                                                <td>{{ $training->getPeriodFrom() }}</td>

                                                                <th scope="row">Period To</th>
                                                                <td>{{ $training->getPeriodTo() }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Remarks</th>
                                                                <td colspan="3">{{ $training->remarks }}</td>
                                                            </tr>
                                                        </div>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="card-body">
                                    <div class="p2">
                                        <div class="table-responsive">
                                            <table class="table" id="trainingTable">
                                                <tbody>
                                                    <div>
                                                        <tr>
                                                            <th scope="row">Institution</th>
                                                            <td colspan="3"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Training Topic</th>
                                                            <td colspan="3"></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Period From</th>
                                                            <td></td>

                                                            <th scope="row">Period To</th>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Remarks</th>
                                                            <td colspan="3"></td>
                                                        </tr>
                                                    </div>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="c-tabs-content" id="social-media">
                        <div class="card shadow-sm border rounded mb-5">
                            @include('Profile::SocialMedia.show')
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
