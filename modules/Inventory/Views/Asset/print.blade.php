@extends('layouts.container-report')

@section('title', 'Asset Labels Print')

@section('page_css')
    <style>
        body {
            margin: 0;
            padding: 10px;
            font-family: Arial, sans-serif;
        }

        .asset-labels-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .single-label {
            width: 2in;
            height: 1in;
            border: 1.5px solid #000;
            background: #fff;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .label-top {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 4px 2px;
        }

        .label-top img {
            width: 95px;
            height: auto;
            object-fit: contain;
        }

        .label-divider {
            border-top: 1px solid #000;
            width: 100%;
        }

        .label-bottom {
            height: 34px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0.5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .asset-labels-wrapper {
                gap: 4px;
            }
        }
    </style>
@endsection

@section('page-content')
    <script type="text/javascript">
        window.print();
    </script>

    <div class="asset-labels-wrapper">
        @forelse($assets as $asset)
            <div class="single-label">
                <div class="label-top">
                    <img src="{{ asset('img/logonp.png') }}" alt="Logo">
                </div>

                <div class="label-divider"></div>

                <div class="label-bottom">
                    {{ $asset->getAssetNumber() }}
                </div>
            </div>
        @empty
            <div>No assets found to print.</div>
        @endforelse
    </div>
@endsection