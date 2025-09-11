 <div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Payable Detail</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('exit.payable.update',$employeeExitPayable->id) !!}" method="post"
      enctype="multipart/form-data" id="employeePayable" autocomplete="off">
    <div class="modal-body">

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Employee</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input class="form-control" name="employee_id" value="{{$employeeExitPayable->employee_id}}" hidden>
                <input class="form-control" name="" value="{{$employeeExitPayable->getEmployeeName()}}" readonly>
            </div>
        </div>



         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Salary Date From</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="salary_date_from" value="{{$employeeExitPayable->salary_date_from?->format('Y-m-d')}}" placeholder="Salary Date from" readonly>
            </div>
        </div>

           <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Salary Date To</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="salary_date_to" value="{{$employeeExitPayable->salary_date_to?->format('Y-m-d')}}" placeholder="Salary Date To" readonly>
            </div>
        </div>

          <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Leave Balance</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="leave_balance" value="{{$leaveBalance}}" placeholder="Leave Balance" readonly>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Salary Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="salary_amount" value="{{$employeeExitPayable->salary_amount?$employeeExitPayable->salary_amount:0 }}" placeholder="Salary Amount">
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Festival Bonus</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="festival_bonus" value="{{$employeeExitPayable->festival_bonus?$employeeExitPayable->festival_bonus:0}}" placeholder="Festival Bonus">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Festival Bonus Date From</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="festival_bonus_date_from" value="{{$employeeExitPayable->festival_bonus_date_from?->format('Y-m-d')}}" placeholder="Bonus Date from" readonly>
            </div>
        </div>

           <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label ">Festival Bonus Date To</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="festival_bonus_date_to" value="{{$employeeExitPayable->festival_bonus_date_to?->format('Y-m-d')}}" placeholder="Bonus Date To" readonly>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Gratuity Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="gratuity_amount" value="{{$employeeExitPayable->gratuity_amount?$employeeExitPayable->gratuity_amount:0}}" placeholder="Gratuity Amount">
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Other Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="other_amount" value="{{$employeeExitPayable->other_amount?$employeeExitPayable->other_amount:0}}" placeholder="Other Amount">
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Advance Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="advance_amount" value="{{$employeeExitPayable->advance_amount?$employeeExitPayable->advance_amount:0}}" placeholder="Advance Amount">
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Loan Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="loan_amount" value="{{$employeeExitPayable->loan_amount?$employeeExitPayable->loan_amount:0}}" placeholder="Loan Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Other Payable Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="other_payable_amount"
                value="{{$employeeExitPayable->other_payable_amount?$employeeExitPayable->other_payable_amount:0}}" placeholder="Other Payable Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Deduction Amount</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="number" class="form-control" name="deduction_amount"
                value="{{$employeeExitPayable->deduction_amount?$employeeExitPayable->deduction_amount:''}}" placeholder="Deduction Amount">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Remarks</label>
                </div>
            </div>
            <div class="col-lg-9">
               <input type="text" class="form-control" name="remarks"
                value="{{$employeeExitPayable->remarks}}" placeholder="Remarks">
            </div>
        </div>

         <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="m-0">Send To</label>
                </div>
            </div>
            <div class="col-lg-9">
               @php $selectedApproverId = old('approver_id') ?: $employeeExitPayable->approver_id; @endphp
                <select name="approver_id" class="select2 form-control
                    @if($errors->has('approver_id')) is-invalid @endif" data-width="100%">
                    <option value="">Select an Approver</option>
                    @foreach($supervisors as $approver)
                        <option
                            value="{{ $approver->id }}" {{$approver->id == $selectedApproverId ? "selected":""}}>{{ $approver->full_name }}</option>
                    @endforeach
                </select>
                @if($errors->has('approver_id'))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div data-field="approver_id">
                            {!! $errors->first('approver_id') !!}
                        </div>
                    </div>
              @endif
            </div>
        </div>



    </div>
    <div class="modal-footer">
        <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">Update</button>
        <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm">Submit</button>
        <a data-bs-dismiss="modal" aria-label="Close"
                                       class="btn btn-danger btn-sm">Cancel</a>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
