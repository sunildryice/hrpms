<div class="card h-100">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Project Activity</span>
            <button type="button" id="btn-open-project-activity" class="btn btn-primary btn-sm"
                data-url="{{ route('project-activity.create', ['project' => $project->id]) }}">
                Add Project Activity
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped" id="project-activity-table">
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
                <tbody id="tablebody"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="project-activity-modal-container"></div>

<script>
    (function() {
        $(document).on('click', '#btn-open-project-activity', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var url = $btn.data('url');
            if (!url) return;

            $btn.prop('disabled', true);
            $.get(url)
                .done(function(html) {
                    var $container = $('#project-activity-modal-container');
                    $container.html(html);
                    var modalEl = document.getElementById('addProjectActivityModal');
                    if (!modalEl) return;

                    if ($.fn.select2) {
                        $(modalEl).find('.select2').select2({
                            width: '100%',
                            dropdownParent: $(modalEl)
                        });
                    }

                    if ($.fn.datepicker) {
                        $(modalEl).find('[data-toggle="datepicker"]').each(function() {
                            $(this).datepicker({
                                language: 'en-GB',
                                autoHide: true,
                                format: 'yyyy-mm-dd'
                            });
                        });
                    }

                    if (window.bootstrap && bootstrap.Modal) {
                        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    } else if (typeof $(modalEl).modal === 'function') {
                        $(modalEl).modal('show');
                    }
                })
                .fail(function() {
                    alert('Failed to load form. Please try again.');
                })
                .always(function() {
                    $btn.prop('disabled', false);
                });
        });
    })();
</script>
