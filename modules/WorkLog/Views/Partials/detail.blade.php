<div class="row">
    <div class="row mb-2">
        <label class="form-label col-xl-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Employee"> Employee :</label>
        <span class="col-xl-11"> {{ $workPlan->getEmployeeName() }}</span>
    </div>
    <div class="row mb-2">
        <label class="form-label col-xl-1 pe-0" data-bs-toggle="tooltip" data-bs-placement="left" title="Year Month"> Year Month
            :</label>
        <span class="col-xl-11"> {{ $workPlan->getYearMonth() }}</span>
    </div>
    <div class="row mb-2">
        <label class="form-label col-xl-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Summary">
            Summary :</label>
        <span class="col-xl-11"> {{ $workPlan->summary }}</span>
    </div>
    <div class="row mb-2">
        <label class="form-label col-xl-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Planned">
            Planned :</label>
        <span class="col-xl-11"> {{ $workPlan->planned }}</span>
    </div>
    <div class="row mb-2">
        <label class="form-label col-xl-1" data-bs-toggle="tooltip" data-bs-placement="left" title="Completed"> Completed
            :</label>
        <span class="col-xl-11"> {{ $workPlan->completed }}</span>
    </div>
</div>
