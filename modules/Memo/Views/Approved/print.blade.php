@extends('layouts.container-report')

@section('title', 'Memo')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
         

        .print-info {
                font-size: 0.8rem;
            }

        @media print {
            .print-info {
                font-size: 1.2rem;
            }
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <script type="text/javascript">
        window.print();
    </script>

    <section class="print-info bg-white" id="print-info">

        <div class="print-title fw-bold mb-3 translate-middle text-center">
            <div class="fs-5"> One Heart Worldwide</div>
            <div class="fs-8"> Memo</div>
        </div>

        <div class="print-header">
            <div class="row">
                <div class="col-lg-8">
                    <div class="print-code">
                         <strong>Date: </strong> {{ $memo->getMemoDate() }}
                    </div>

                    <div class="print-header-info my-5 ">
                        <ul class="list-unstyled m-0 p-0">
                            <li><span class="fw-bold me-2">Memo No. :</span><span>{{ $memo->getMemoNumber() }}</span></li>
                            <li><span class="fw-bold me-2"> To:</span><span>{{ $memo->getTo() }}</span></li>
                            <li><span class="fw-bold me-2"> From:</span><span>{{ $memo->getCreatedBy() }}</span></li>
                            <li><span class="fw-bold me-2"> CC:</span><span>{{ $memo->getThrough() }}</span></li>
                            <li><span class="fw-bold me-2"> Subject:</span><span>{{ $memo->subject }}</span></li>
                        </ul>
                    </div>
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
        <div class="print-body">
            <strong>Memo Descriptions</strong>
            <div>
               {!! $memo->description !!}
            </div>
            <strong>Memo Enclosure</strong>
            <div>
                <p>{{ $memo->enclosure }}</p>
            </div>

        </div>
        <div class="print-footer">
            <div class="print-header-info mb-3">
                <ul class="list-unstyled m-0 p-0">
                    <li><span class="fw-bold me-2"> Requester:</span><span>{{ $memo->getCreatedBy() }}</span></li>
                    <li><span class="fw-bold me-2"> Submitted at:</span><span>{{ $submittedDate }}</span></li>
                    <li><span class="fw-bold me-2"> Approver:</span><span>{{ $memo->getTo() }}</span></li>
                    <li><span class="fw-bold me-2"> Approved at:</span><span>{{ $approvedDate }}</span></li>
                </ul>
            </div>
        </div>
    </section>


@endsection
