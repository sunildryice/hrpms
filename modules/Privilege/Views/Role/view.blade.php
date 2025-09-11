@extends('layouts.container')
@section('title', 'View Role')
@section('footer_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#sidebar li').removeClass('active');
            $('#sidebar a').removeClass('active');
            $('#sidebar').find('#privilege').addClass('active');
            $('#sidebar').find('#role').addClass('active');
        });
    </script>
@endsection
@section('page-content')
    <div class="m-content p-3">
        <div class="pb-3 mb-3 border-bottom">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}"
                                   class="text-decoration-none text-dark">{{ __('label.home') }}</a>
                            </li>
                            <li class="breadcrumb-item" aria-current="page">@yield('title')</li>
                        </ol>
                    </nav>
                    <h4 class="m-0 lh1 mt-1 fs-6 text-uppercase fw-bold text-primary">{!! $role->getRoleName() !!}</h4>
                </div>
            </div>
        </div>
        <div class="container-fluid-s">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="permissionTable">
                            <thead class="thead-light">
                            <tr>
                            <tr>
                                <th>
                                    S N
                                </th>
                                <th>
                                    Permissions
                                </th>
                                <th>
                                    Users
                                </th>
                            </tr>
                            </tr>
                            </thead>
                            <tbody>
                                <tr class="gradeX">
                                    <td>
                                       1
                                    </td>
                                    <td>
                                        {!! implode('<br />', $role->permissions->pluck('permission_name')->toArray()) !!}
                                    </td>
                                    <td>
                                        @foreach ($role->users as $user)
                                            {!! $user->full_name !!} - {!! $user->getOfficeName() !!}
                                            @unless($loop->last)
                                                <br/>
                                            @endunless
                                        @endforeach
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
