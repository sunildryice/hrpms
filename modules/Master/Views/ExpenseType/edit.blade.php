<div class="modal-header bg-primary text-white">
    <h5 class="modal-title mb-0 fs-6" id="openModalLabel">Edit Expense Type</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{!! route('master.expense.types.update',$expenseType->id) !!}" method="post"
      enctype="multipart/form-data" id="expenseTypeForm" autocomplete="off">
    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-lg-3">
                <div class="d-flex align-items-start h-100">
                    <label for="" class="form-label required-label">Expense Type</label>
                </div>
            </div>
            <div class="col-lg-9">
                <input type="text" class="form-control" name="title" value="{!! $expenseType->title !!}" placeholder="Expense Type" />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
    {!! csrf_field() !!}
    {!! method_field('PUT') !!}
</form>
