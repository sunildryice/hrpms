<form action="{{route('attendance.import')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="importAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Import Attendance</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="attendance_file" id="attendance_file" required>
                    @if ($errors->has('attendance_file'))
                        <div class="invalid-feedback">
                            {{$errors->first('attendance_file')}}
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Import</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>
