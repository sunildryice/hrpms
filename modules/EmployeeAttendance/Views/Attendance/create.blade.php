<form action="{{route('attendance.store')}}" method="POST">
    @csrf
    <div class="modal fade" id="createAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="emp_id" value="{{$employeeId}}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="year">Year</label>
                                <select class="form-control" name="year" id="year">
                                    @if (now()->month <= 6)
                                        <option value="{{date("Y",strtotime("-1 year"))}}">{{date("Y",strtotime("-1 year"))}}</option>
                                    @endif
                                    <option value="{{now()->year}}" selected>{{now()->year}}</option>
                                </select>
                            </div>
                        </div>
                        @php
                            $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        @endphp
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="month">Month</label>
                                <select class="form-control" name="month" id="month">
                                    @foreach ($months as $key => $month)
                                        <option value="{{$key+1}}" {{date('m') == $key+1 ? 'selected' : ''}}>{{$month}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>
