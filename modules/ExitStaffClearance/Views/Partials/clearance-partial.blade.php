<div class="card">
    <div class="card-header fw-bold">
        <span class="card-title">
            <span class="fw-bold"></span>
            <span>
                Clearance History
            </span>
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <table id="clearance-history-table" class="mb-3" style="width: 100%">
                    <thead>
                        <tr>
                            <th style="width: 20%" rowspan="2">Departments </th>
                            <th style="width: 50%" colspan="2">Cleared By:</th>
                            <th style="width: 30%" rowspan="2">Remarks</th>
                        </tr>
                        <tr>
                            <th style="width: 20%">Name </th>
                            <th style="width: 20%">Cleared Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@isset($staffClearance)
    @push('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
            });
        </script>
    @endpush
@endisset
