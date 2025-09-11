@extends('layout.containerlist')

@section('title', 'Email Log')

@section('footer_js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#sidebar li').removeClass('active');
        $('#sidebar a').removeClass('active');
        $('#sidebar').find('#privilege').addClass('active');
//        $('#sidebar').find('#logs').addClass('active');

        $('.date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            endDate: '0d'
        });
    });
</script>
@endsection
@section('dynamicdata')

<div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Email Logs
            </header>

            <div class="panel-body">
                <div class="">
                    <form role="form" method="get" action="">
                        <div class="col-xs-3 form-group">
                            <label>Period From</label>
                            <input class="form-control date" type="text" name="from_date" placeholder="Start date"
                                   value="{{ (!empty($requestData['from_date'])) ? $requestData['from_date'] : date('Y-m-d') }}"/>
                        </div>
                        <div class="col-xs-3 form-group">
                            <label>Period To</label>
                            <input class="form-control date" type="text" placeholder="end date" name="to_date"
                                   value="{{ (!empty($requestData['to_date'])) ? $requestData['to_date'] : date('Y-m-d') }}"/>
                        </div>
                        <div class="col-xs-3 form-group">
                            <label>User</label>
                            <select name="user_id" class="form-control select-search">
                                <option value="">All</option>
                                @foreach($users as $id=>$user)
                                    <option value="{{ $id }}"
                                            @if(!empty($requestData['user_id'])) @if($id == $requestData['user_id']) selected="selected" @endif @endif>{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-3 form-group">
                            <br />
                            <button type="submit" class="btn btn-info">Search</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

            <div class="panel-body">
                <div class="adv-table editable-table ">                 
                    <div class="btn-group">
                    </div>
                    <table class="display table table-bordered table-striped" id="log-table">
                        <thead>
                            <tr>
                                <th>
                                    S N
                                </th>
                                <th>
                                    User
                                </th>
                                <th>
                                    IP Address
                                </th>
                                <th>
                                    Subject
                                </th>
                                <th>
                                    To Address
                                </th>
                                <th>
                                    Type
                                </th>
                                <th width="18%">Date Time</th>
                            </tr>
                        </thead>
                        <tbody id="tablebody">
                            @forelse($logs as $index=>$log)
                            <tr class="gradeX" id="row_{{ $log->id }}" >
                                <td>
                                    {{ $loop->iteration + $logs->perPage() * ($logs->currentPage()-1) }}
                                </td>
                                <td class="user">
                                    {{ $log->user ? $log->user->full_name .' ('. $log->user->email_address .')' : "" }}
                                </td>
                                <td class="ip_address">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="subject">
                                    {{ $log->subject }}
                                </td>
                                <td class="to_email">
                                    {{ $log->to_email }}
                                </td>
                                <td class="type">
                                    {{ $log->type == 2 ? "Email Sent" : "Email Sending" }}
                                </td>
                                <td class="created_at">
                                    {{ $log->created_at->format('M d, Y h:i A') }}
                                </td>
                            </tr>
                                @empty
                                <tr>
                                    <td colspan="7">Record Not Found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>
                                    S N
                                </th>
                                <th>
                                    User
                                </th>
                                <th>
                                    IP Address
                                </th>
                                <th>
                                    Subject
                                </th>
                                <th>
                                    To Address
                                </th>
                                <th>
                                    Type
                                </th>
                                <th>Date Time</th>
                            </tr>
                        </tfoot>
                    </table>

                    {!! $logs->appends($requestData)->links() !!}

                </div>
            </div>
        </section>
    </div>
</div>

@stop
