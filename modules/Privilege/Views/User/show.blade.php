@extends('layouts.container')

@section('title', __('label.users'))

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#users-menu').addClass('active');
        });
    </script>
@endsection
@section('page-content')

    <div class="row">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    Full Name : {!! $user->getFullName() !!} <br />
                    Employee Code : {!! $user->employee ? $user->employee->getEmployeeCode() . '/' . $user->employee_id : '' !!}
                </header>
                <div class="panel-body">
                    <div class="adv-table editable-table ">
                        <div class="btn-group">

                        </div>
                        <div class="table-responsive">
                            <table class="table-hover table table-bordered table-striped scm-datatable" id="permission-table">
                                <thead>
                                    <tr>
                                        <th>
                                            S N
                                        </th>
                                        <th>
                                            Roles
                                        </th>
                                        <th>
                                            Users
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tablebody">
                                    @foreach ($user->roles as $index => $role)
                                        <tr class="gradeX" id="row_{{ $role->id }}">
                                            <td>
                                                {{ $index + 1 }}
                                            </td>
                                            <td>
                                                {{ $role->role }}
                                            </td>
                                            <td>
                                                @foreach ($role->permissions as $permission)
                                                    {!! $permission->permission_name !!}
                                                    @unless($loop->last)
                                                        <br />
                                                    @endunless
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </div>
@stop
