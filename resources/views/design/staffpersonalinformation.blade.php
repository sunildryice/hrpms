@extends('layouts.container-report')

@section('title', 'Staff Personal Information')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: 'Mukta', sans-serif;
        }

        table {
            border: 1px solid;
        }

        .table thead th {
            font-size: 0.94375rem;

        }




        tbody,
        td,
        tfoot,
        th,
        thead,
        tr {
            border-width: 0.1px;
        }

        .table tr th {
            padding: 0.45rem 0.75rem;
            width: 18%;
        }

        .table tr td {
            padding: 0.25rem 0.75rem;
        }

        .staff-image {
            width: 180px;

        }


        .staff-image  img{
            height: 100px;
            object-fit: contain;
        }

    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8"> Staff Personal Information </div>
    </div>
    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5">
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- <tr>
        <th scope="row">Staff ID </th>
        <td colspan="2">{{ $employee->employee_code }}</td>
        <td rowspan="5" class="staff-image text-center bg-white ">
                <img src="{{ asset('img/bill.jpg') }}" alt="" class="w-100 ">
        </td>
    </tr> --}}

    <div class="print-body mb-5">
        <table class="table border">
            <thead>
                <tr>
                    <th colspan="4">Staff Personal Information </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Staff ID </th>
                    <td colspan="2"></td>
                    <td rowspan="5" class="staff-image text-center bg-white ">
                            <img src="{{ asset('img/bill.jpg') }}" alt="" class="w-100 ">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Official Email Address </th>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <th scope="row">Full Name: </th>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <th scope="row">Position: </th>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <th scope="row">Joining Date: </th>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <th scope="row">Duty Station:</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">Supervisor Name:</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">Cross-functional Supervisor Name:</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">Next Line Manager Name:</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row" rowspan="4">Current Address</th>
                    <td colspan="3">Province:</td>
                </tr>
                <tr>
                    <td colspan="3">District:</td>
                </tr>
                <tr>
                    <td colspan="3">Municipality:</td>
                </tr>
                <tr>
                    <td>Ward:</td>
                    <td colspan="2">Tole:</td>
                </tr>
                <tr>
                    <th scope="row" rowspan="4">Permanent Address</th>
                    <td colspan="3">Province:</td>
                </tr>
                <tr>
                    <td colspan="3">District:</td>
                </tr>
                <tr>
                    <td colspan="3">Municipality:</td>
                </tr>
                <tr>
                    <td>Ward:</td>
                    <td colspan="2">Tole:</td>
                </tr>
                <tr>
                    <th scope="row">Telephone ( Mobile)</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">Date of Birth (DD/MM/YYYY) AD</th>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <th scope="row">Citizenship ID Number: </th>
                    <td>PAN Number: </td>
                    <td>SSF No.</td>
                    <td>CIT No.</td>
                </tr>
                <tr>
                    <th scope="row" rowspan="4">Bank Account Details:</th>
                    <td>Account Name:</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Account No.</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Bank Name</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Branch</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row"> Marital Status </th>
                    <td>Married/ Single</td>
                    <td colspan="2"> <strong>Gender - </strong> Male/ Female/ Other </td>
                </tr>
            </tbody>
        </table>
        <table class="table border">
            <thead>
                <tr>
                    <th colspan="2">Medical/ Health Information</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">Blood Group</th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">Medical Condition</th>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">Remarks</th>
                    <td></td>
                </tr>

            </tbody>
        </table>
        <table class="table border">
            <thead>
                <tr>
                    <th colspan="2">Educational Details</th>
                </tr>
            </thead>
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
                    <th scope="row">Passed Year
                    </th>
                    <td></td>
                </tr>

            </tbody>
        </table>
        <table class="table border">
            <thead>
                <tr>
                    <th colspan="4">Experience</th>
                </tr>
            </thead>
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
        <table class="table border">
            <thead>
                <tr>
                    <th colspan="4">Training</th>
                </tr>
            </thead>
            <tbody>
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

            </tbody>
        </table>
        <table class="table border">
            <thead>
                <tr>
                    <th colspan="2">Emergency Contact Information </th>
                </tr>
            </thead>
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
                    <th scope="row" rowspan="3">Nominee:</th>
                    <td> Name:</td>
                </tr>
                <tr>
                    <td>Relationship:</td>
                </tr>
                <tr>
                    <td>Nominee contact no.:</td>
                </tr>


            </tbody>
        </table>









    </div>



@endsection
