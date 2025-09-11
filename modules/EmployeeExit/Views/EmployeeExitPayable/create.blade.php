 <div class="modal-header bg-primary text-white">
     <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Add New Payable Detail</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <form action="{!! route('employee.payable.store') !!}" method="post" enctype="multipart/form-data" id="employeePayable"
     autocomplete="off">
     <div class="modal-body">
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Employee</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <select class="form-control select2" data-width="100%" name="employee_id">
                     <option value="">Select Employee</option>
                     @foreach ($employees as $employee)
                         <option value="{!! $employee->id !!}">{{ $employee->getFullName() }}</option>
                     @endforeach
                 </select>
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Salary Date From</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="text" class="form-control" name="salary_date_from" value=""
                     placeholder="Salary Date from">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Salary Date To</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="text" class="form-control" name="salary_date_to" value=""
                     placeholder="Salary Date To">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Leave Balance</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="leave_balance" value=""
                     placeholder="Leave Balance">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Salary Amount</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="salary_amount" value=""
                     placeholder="Salary Amount">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Festival Bonus</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="festival_bonus" value=""
                     placeholder="Festival Bonus">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Gratuity Amount</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="gratuity_amount" value=""
                     placeholder="Gratuity Amount">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Other Amount</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="other_amount" value=""
                     placeholder="Other Amount">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Advance Amount</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="advance_amount" value=""
                     placeholder="Advance Amount">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Loan Amount</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="loan_amount" value=""
                     placeholder="Loan Amount">
             </div>
         </div>
         <div class="row mb-2">
             <div class="col-lg-3">
                 <div class="d-flex align-items-start h-100">
                     <label for="" class="form-label required-label">Other Payable Amount</label>
                 </div>
             </div>
             <div class="col-lg-9">
                 <input type="number" class="form-control" name="other_payable_amount" value=""
                     placeholder="Other Payable Amount">
             </div>
         </div>
     </div>
     <div class="modal-footer">
         <button type="submit" class="btn btn-primary">Save</button>
     </div>
     {!! csrf_field() !!}
 </form>
