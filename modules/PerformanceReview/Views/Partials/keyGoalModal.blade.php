<div class="modal fade" id="keyGoalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6" id="keyGoalModalTitle">Add Key Goal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="keyGoalForm">
                    <div class="modal-body">

                        <input type="hidden" name="key_goal_id" id="key_goal_id">

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Objective</label>
                            </div>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="title" id="title" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label class="form-label fw-bold">Output / Deliverable</label>
                            </div>
                            <div class="col-lg-9">
                                <textarea class="form-control" name="output_deliverables" id="output_deliverables" rows="3"></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                    @csrf
                </form>
            </div>
        </div>
    </div>