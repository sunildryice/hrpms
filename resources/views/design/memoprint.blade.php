@extends('layouts.container-report')

@section('title', 'Work Log')
@section('page_css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: 'Mukta', sans-serif;
        }
    </style>
@endsection
@section('page_js')

@endsection

@section('page-content')

    <!-- CSS only -->

    <div class="print-title fw-bold mb-3 translate-middle text-center ">
        <div class="fs-5"> HERD International</div>
        <div class="fs-8">Memo</div>
    </div>

    <div class="print-header">
        <div class="row">
            <div class="col-lg-8">

                <div class="print-header-info my-3">
                    <ul class="list-unstyled m-0 p-0 fs-7">
                        <li><span class="fw-bold me-2"> To. :</span><span>Kathmandu</span></li>
                        <li><span class="fw-bold me-2"> Subject:</span><span>Kathmandu</span></li>
                        <li><span class="fw-bold me-2">Date :</span><span>6502</span></li>


                    </ul>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column justify-content-end">
                    <div class="d-flex flex-column justify-content-end brand-logo mb-4 flex-grow-1">
                        <div class="d-flex flex-column justify-content-end float-right">
                            <img src="{{ asset('img/logonp.png') }}" alt="" class="align-self-end pe-5 logo-img">
                        </div>

                    </div>
                    <ul class="list-unstyled m-0 p-0 fs-7 align-self-end">


                    </ul>
                </div>

            </div>
        </div>



    </div>
    <div class="print-body">
        <strong>Memo Details</strong>
        <div>
            <p> Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste numquam sunt maiores dolores, eveniet
                voluptatibus sit necessitatibus ducimus libero reprehenderit ex quos itaque consequatur harum! Incidunt modi
                odit quidem esse.
            </p>
            <p> Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste numquam sunt maiores dolores, eveniet
                voluptatibus sit necessitatibus ducimus libero reprehenderit ex quos itaque consequatur harum! Incidunt modi
                odit quidem esse.
            </p>
            <ul>
                <li>asdf</li>
                <li>asdf</li>
                <li>asdf</li>
                <li>asdf</li>
                <li>asdf</li>
            </ul>
            <ol>
                <li>asdafsdf asdfasdf</li>
                <li>asdafsdf asdfasdf</li>
                <li>asdafsdf asdfasdf</li>
                <li>asdafsdf asdfasdf</li>
                <li>asdafsdf asdfasdf</li>
                <li>asdafsdf asdfasdf</li>
            </ol>
        </div>
    </div>
    <div class="print-footer">
    </div>


@endsection
