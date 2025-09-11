<header class="border-bottom d-flex align-items-center w-100 p-3 pe-3 bg-white hidden-print">

    <div class="justify-content-start flex-grow-1">
        {{-- <input type="text" class="form-control" id="" value=""> --}}
    </div>
    <div class="d-flex justify-content-end flex-grow-1">
        <ul class="list-unstyled list-inline m-0">
            <li class="list-inline-item i-noftif has-notification">
                <div class="dropdown">
                    <span class="dropdown-toggle position-relative" type="button" id="notification"
                          data-bs-toggle="dropdown"
                          aria-expanded="false">
                        <i class="bi-bell"></i>
                        @if($notificationCount)
                            <span class="bg-danger text-white fs-6 position-absolute n-count">
                                {{ $notificationCount }}
                            </span>
                        @endif
                    </span>
                    <ul class="dropdown-menu dropdown-menu-end is-notification m-0 p-0 border-0 shadow-sm" aria-labelledby="notification">
                        @foreach($notifications as $notification)
                            <li class="p-2 is-leave is-new position-relative">
                                <div class="d-flex gap-2">
                                    <div
                                        class="not-user-icon d-flex bg-white align-items-center justify-content-center rounded-circle overflow-hidden">
                                        <i class="bi-person"></i>
                                    </div>
                                    <div class="not-info">
                                        <span>{{ $notification->data['subject'] }}</span>
                                        <span class="d-block text-black-50">
                                        <strong>{{ $notification->created_at->diffForHumans() }}</strong>
                                    </span>
                                    </div>
                                </div>
{{--                                <a href="{{ $notification->data['link'] }}" class="stretched-link"></a>--}}
                                <a href="{{ route('notifications.show',$notification->id) }}" class="stretched-link"></a>
                            </li>
                        @endforeach
                        <li class="text-center p-1"><a class="text-decoration-none fs-7 text-dark" href="{{ route('notifications.index') }}"><i class=""></i> View All</a></li>
                    </ul>
                </div>
            </li>
            {{-- <li class="list-inline-item"><a href="#"><i class="bi-plus"></i></a></li> --}}
            <li class="list-inline-item">
                <div class="dropdown">
                    <span class="dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                          aria-expanded="false">
                        <i class="bi-person"></i>{!! auth()->user()->full_name !!}
                    </span>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item fs-7" href="{{ route('profile.show') }}"><i class="bi-person"></i>
                                Profile</a></li>
                        <li><a class="dropdown-item fs-7" href="{{ route('change.password.create') }}"><i
                                    class="bi-lock"></i> Change Password</a>
                        </li>
                        <li><a class="dropdown-item fs-7" href="{{ route('auth.logout') }}"><i
                                    class="bi-power"></i> Logout</a></li>
                    </ul>
                </div>

            </li>
        </ul>
    </div>

</header>
