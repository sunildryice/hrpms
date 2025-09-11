@extends('layouts.container')

@section('title', 'User Registration')

@section('page_js')

    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar li').removeClass('active');
            $('#dashboard').addClass('active');
        });
    </script>
@endsection
@section('page-content')
<div class=" pb-3 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <div class="brd-crms flex-grow-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">HR</a></li>
                    <li class="breadcrumb-item" aria-current="page">User Registration</li>
                </ol>
            </nav>
            <h4 class="m-0 lh1 mt-1 fs-5">User Registration</h4>
        </div>
        <div class="ad-info justify-content-end">
            <button class="btn btn-primary btn-sm"><i class="bi-person-plus"></i> Add info</button>
        </div>
    </div>

</div>
<div class="container-fluid">
    <div class="card shadow-sm border rounded c-tabs-content active" id="generalinformantion">
        <div class="card-header fw-bold">
            <h3 class="m-0 fs-6">Employee List</h3>
        </div>
        <div class="card-body">
            <table class="table table-borderedless">
                <thead class="bg-light">
                    <tr>
                        <th style="width:45px;"></th>
                        <th class="" style="width:120px;">Staff Code</th>
                        <th>Name</th>
                        <th>Position</th>

                        <th>Supervisor</th>
                        <th>Duty station</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01</td>
                        <td>OHW-100-123</td>
                        <td>Ram Shrestha</td>
                        <td><strong>Manager</strong> <span class="d-block">Human Resource</span></td>

                        <td>Supervisor</td>
                        <td>Kaski</td>
                        <td>Active</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary"><i class="bi-pencil-square"></i> Edit</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
