<div class="card-header fw-bold">Social Media</div>
<form action="{{ route('employees.social-media.update', ['employee' => $employee->id]) }}" class="needs-validation"
    method="post" id="socialAccountAddForm" enctype="multipart/form-data" autocomplete="off">


    <div class="row mb-2 py-2 px-3">
        <div class="col-lg-3 mt-2">
            <div class="d-flex align-items-start h-100">
                <label for="bio" class="form-label">Bio</label>
            </div>
        </div>
        <div class="col-lg-9 mt-2">
            <textarea class="form-control @if ($errors->has('bio')) is-invalid @endif" name="bio" rows="4"
                placeholder="Bio">{{ old('bio', $employee->bio ?? '') }}</textarea>
            @if ($errors->has('bio'))
                <div class="fv-plugins-message-container invalid-feedback">
                    <div>{!! $errors->first('bio') !!}</div>
                </div>
            @endif
        </div>

        @foreach ($socialMediaAccounts as $account)
            <div class="col-lg-3 mt-2">
                <div class="d-flex align-items-start h-100">
                    <label for="{{ strtolower($account->title) }}" class="form-label">{{ $account->title }}</label>
                </div>
            </div>
            <div class="col-lg-9 mt-2">
                <input type="text" class="form-control @if ($errors->has(strtolower($account->title))) is-invalid @endif"
                    name="{{ strtolower($account->title) }}" placeholder="{{ $account->title }} Url"
                    value="{{ old(strtolower($account->title), $employeeSocialMediaLinks[$account->title] ?? '') }}">
                @if ($errors->has(strtolower($account->title)))
                    <div class="fv-plugins-message-container invalid-feedback">
                        <div>{!! $errors->first(strtolower($account->title)) !!}</div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="card-footer border-0 justify-content-end d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
    </div>
    {!! csrf_field() !!}
</form>
@push('scripts')
    <script type="text/javascript">
        var end_date = "{!! date('Y-m-d') !!}";
        document.addEventListener('DOMContentLoaded', function(e) {
            const form = document.getElementById('socialAccountAddForm');
            const fv = FormValidation.formValidation(form, {

                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
                    excluded: new FormValidation.plugins.Excluded(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    defaultSubmit: new FormValidation.plugins.DefaultSubmit(),

                    icon: new FormValidation.plugins.Icon({
                        valid: 'bi bi-check2-square',
                        invalid: 'bi bi-x-lg',
                        validating: 'bi bi-arrow-repeat',
                    }),

                    startEndDate: new FormValidation.plugins.StartEndDate({
                        format: 'YYYY-MM-DD',
                        startDate: {
                            field: 'period_from',
                            message: 'From date must be a valid date and earlier than to date.',
                        },
                        endDate: {
                            field: 'period_to',
                            message: 'To date must be a valid date and later than from date.',
                        },
                    }),
                },
            });

        $('[name="period_from"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
            startDate: '2022-04-02',
            endDate: end_date,
        }).on('change', function(e) {
            var start_date = $(this).val();
            $('[name="period_to"]').datepicker("option", "startDate", start_date);
            fv.revalidateField('period_from');
            fv.revalidateField('period_to');
        });

        $('[name="period_to"]').datepicker({
            language: 'en-GB',
            autoHide: true,
            format: 'yyyy-mm-dd',
            endDate: end_date,
        }).on('change', function(e) {
            fv.revalidateField('period_from');
            fv.revalidateField('period_to');
        });
        });
    </script>
@endpush
