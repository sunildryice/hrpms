@extends('layouts.container')

@section('title', 'Employee Leaves')

@section('page_js')

    <script type="text/javascript">
        function calcBalanceLeave($ele) {
            var opening = parseFloat($($ele).closest('form').find('[name="opening_balance"]').val());
            var taken = parseFloat($($ele).closest('form').find('[name="taken"]').val());
            var earned = parseFloat($($ele).closest('form').find('[name="earned"]').val());
            var lapsed = parseFloat($($ele).closest('form').find('[name="lapsed"]').val());
            var balance = opening + earned - taken - lapsed;
            $($ele).closest('form').find('.balance').val(balance);
        }

        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#employees-menu').addClass('active');

            $(document).on('shown.bs.modal', '#openModal', function(e) {
                const form = document.getElementById('leaveEditForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        opening_balance: {
                            validators: {
                                notEmpty: {
                                    message: 'Opening balance is required',
                                },
                            },
                        },
                        earned: {
                            validators: {
                                notEmpty: {
                                    message: 'Earned is required',
                                },
                            },
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        console.log(response);
                        $('#openModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        $('#employeeLeaveTable').find('#row_' + response.leave.id).find(
                            ".opening_balance").text(response.leave.opening_balance);
                        $('#employeeLeaveTable').find('#row_' + response.leave.id).find(
                            ".earned").text(response.leave.earned);
                        $('#employeeLeaveTable').find('#row_' + response.leave.id).find(
                            ".taken").text(response.leave.taken);
                        $('#employeeLeaveTable').find('#row_' + response.leave.id).find(
                            ".lapsed").text(response.leave.lapsed);
                        $('#employeeLeaveTable').find('#row_' + response.leave.id).find(
                            ".balance").text(response.leave.balance);
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });

                form.querySelector('[name="opening_balance"]').addEventListener('change', function() {
                    calcBalanceLeave(this);
                });
            });


        });
    </script>
@endsection
@section('page-content')
    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}"
                                class="text-decoration-none">Employee</a></li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                {{-- <h4 class="m-0 lh1 mt-1 fs-5">User Registration</h4> --}}
            </div>
            <div class="add-info justify-content-end">
                {{-- <button class="btn btn-primary btn-sm"><i class="bi-person-plus"></i> Add info</button> --}}
            </div>
        </div>

    </div>
    <div class="container-fluid">
        <div class="emp-header">

        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="p-2 bg-white mb-2 rounded">
                    <div class="d-flex">

                        <div class="user-pro d-flex align-items-center justify-content-center  bg-danger text-white">
                            <img src="" alt="">
                            <i class="bi-person"></i>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header fw-bold">
                        Profile
                    </div>
                    <div class="card-body">
                        <div class="p2">
                            <ul class="list-unstyled list-py-2 text-dark mb-0">
                                <li class="pb-2"><span class="card-subtitle text-uppercase text-primary">About</span></li>
                                <li class="position-relative"><i
                                        class="bi-person dropdown-item-icon"></i>{{ $employee->getFullName() }}
                                    <a href="#" class="stretched-link" rel="tooltip" title="Profile"></a>
                                </li>
                                <li><i class="bi-people dropdown-item-icon"></i> {{ $employee->getMaritalStatus() }}
                                </li>
                                <li><i class="bi-building dropdown-item-icon"></i>
                                    {{ $employee->address->temporary_district ? $employee->address->temporary_district->district_name : '' }}
                                </li>

                                <li class="pt-4 pb-2"><span
                                        class="card-subtitle text-uppercase text-primary">Contacts</span></li>
                                <li class="position-relative"><i class="bi-at dropdown-item-icon"></i>
                                    {{ $employee->official_email_address }}
                                    <a href="#" class="stretched-link" rel="tooltip" title="Contact email"></a>
                                </li>
                                <li class="position-relative"><i class="bi-phone dropdown-item-icon"></i>
                                    {{ $employee->mobile_number }}
                                    <a href="#" class="stretched-link" rel="tooltip" title="Contact Number"></a>
                                </li>

                                <li class="pt-4 pb-2">
                                    {{-- <span class="card-subtitle text-uppercase text-primary">Others</span></li> --}}
                                    {{-- <li><i class="bi-droplet dropdown-item-icon"></i> A+</li> --}}
                                    {{-- <li><i class="bi-stickies dropdown-item-icon"></i> Working on 8 projects</li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">

                <div class="tabs-s mb-2">
                    <ul class="m-0 list-unstyled list-inline">
                        <li class="list-inline-item">
                            <a href="{{ route('employees.profile', $employee->id) }}"
                                class="nav-link step-item text-decoration-none">
                                <i class="nav-icon bi-info-circle"></i>Profile Information
                            </a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="nav-link active step-item text-decoration-none">
                                <i class="nav-icon bi bi-person-workspace"></i>Leave Details
                            </a>
                        </li>
                        {{-- <li class="list-inline-item"> --}}
                        {{-- <a href="#" class="nav-link step-item text-decoration-none"> --}}
                        {{-- <i class="nav-icon bi bi-vi"></i>Travel Requests --}}
                        {{-- </a> --}}
                        {{-- </li> --}}
                        {{-- <li class="list-inline-item"> --}}
                        {{-- <a href="#" class="nav-link step-item text-decoration-none"> --}}
                        {{-- <i class="nav-icon bi bi-journal-text"></i>Leave Requests --}}
                        {{-- </a> --}}
                        {{-- </li> --}}
                    </ul>
                </div>

                <div class="c-tabs-contnet">
                    <div class="c-tabs-item" id="">
                        <div class="card">
                            <div class="card-header fw-bold">
                                Leave Details
                            </div>
                            <div class="card-body">
                                <div class="p2">
                                    <div class="table-responsive">
                                        <table class="table" id="employeeLeaveTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th scope="col">SN</th>
                                                    <th scope="col">Leave</th>
                                                    <th scope="col">Opening balance</th>
                                                    <th scope="col">Earned</th>
                                                    <th scope="col">Taken</th>
                                                    <th scope="col">Lapsed</th>
                                                    <th scope="col">Balance</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($leaves as $leave)
                                                    <tr id="row_{!! $leave->id !!}">
                                                        <th scope="row">{!! $loop->iteration !!}</th>
                                                        <td class="leave_type">
                                                            {{ $leave->getLeaveType() }}
                                                            / {{ $leave->leaveType->getLeaveBasis() }}
                                                        </td>
                                                        <td class="opening_balance">{{ $leave->opening_balance }}</td>
                                                        <td class="earned">{{ $leave->earned }}</td>
                                                        <td class="taken">{{ $leave->taken }}</td>
                                                        <td class="lapsed">{{ $leave->lapsed }}</td>
                                                        <td class="balance">{{ $leave->balance }}</td>
                                                        <td>
                                                            <a data-toggle="modal"
                                                                class="btn btn-outline-primary btn-sm open-modal-form"
                                                                href="{!! route('employees.leaves.edit', [$leave->employee_id, $leave->id]) !!}">
                                                                <i class="bi-pencil-square"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- End Table -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
