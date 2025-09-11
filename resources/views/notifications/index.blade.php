@extends('layouts.container')

@section('title', 'Notifications')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#notifications-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')

    <div class="pb-3 mb-3 border-bottom">
        <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-dark">Home</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
                <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">@yield('title')</h4>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table" id="notificationsTable">
                <thead>
                    <tr>
                        <th>{{ __('label.sn') }}</th>
                        <th scope="col">{{ __('label.description') }}</th>
                        {{-- <th scope="col">{{ __('label.status') }}</th> --}}
                        <th scope="col">{{ __('label.datetime') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $index=>$notification)
                        <tr class="gradeX" id="row_{{ $notification->id }}">
                            <td>
                                {{ $index + 1 }}
                            </td>
                            <td class="subject">
                                <a class="text-decoration-none text-dark" href="{!! route('notifications.show', $notification->id) !!}"
                                    target="_blank">
                                    {{ $notification->data['subject'] }}
                                </a>
                            </td>
                            {{-- <td>
                                @isset($notification->read_at)
                                        <span class="badge badge-pill bg-success">Read</span>
                                @else
                                        <span class="badge badge-pill bg-danger">Unread</span>
                                @endisset
                            </td> --}}
                            <td>
                                {!! $notification->created_at->format('M d, Y h:i A') !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Record Not Found.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
@stop
