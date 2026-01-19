    <div class="row p-3">
        <div class="col-sm-12">
            <section class="panel">
                <header class="panel-heading">
                    Project Activity
                    <button type="button" id="btn-open-project-activity" class="btn btn-primary btn-sm float-end"
                        data-url="{{ route('project-activity.create', ['project' => $project->id]) }}">
                        Add Project Activity
                    </button>
                </header>
                <div class="panel-body">
                    <div class="adv-table editable-table">
                        <div class="table-responsive">
                            {{-- <table class="table table-hover table-bordered table-striped" id="project-activity-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>SN</th>
                                        <th>Stage</th>
                                        <th>Activity Level</th>
                                        <th>Parent Activity</th>
                                        <th>Activity Title</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                    </tr>
                                </thead>
                                <tbody id="tablebody">
                                </tbody>
                            </table> --}}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div id="project-activity-modal-container"></div>
