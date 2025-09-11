<!-- If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius -->
<div class="page-header pb-3 mb-3 border-bottom">
    <div class="d-flex align-items-center">
        <div class="brd-crms flex-grow-1">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{!! route('dashboard.index') !!}" class="text-decoration-none text-dark">Home</a>
                    </li>
                    @foreach ($items as $item)
                        <li class="breadcrumb-item" aria-current="page">
                            <a href="{{$item['route']}}" class="text-decoration-none">{{$item['title']}}</a>
                        </li>
                    @endforeach
                    <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                </ol>
            </nav>
            <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
        </div>
    </div>
</div>
