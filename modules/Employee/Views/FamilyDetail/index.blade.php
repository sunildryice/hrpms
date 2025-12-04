<div class="card-header fw-bold">Family Members</div>
<div class="card-body">
    <div class="table-responsive">
        <table class="table">
            <thead class="bg-light">
                <tr>
                    <th style="width: 25%">{{ __('label.name') }}</th>
                    <th>{{ __('label.relation') }}</th>
                    <th>{{ __('label.date-of-birth') }}</th>
                    {{-- <th>{{ __('label.type') }}</th> --}}
                    <th>{{ __('label.contact-no') }}</th>
                    <th>{{ __('label.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->familyDetails as $family)
                    <tr>
                        <td>{{ $family->getFullName() }}</td>
                        <td>{{ $family->getRelationName() }}</td>
                        <td>{{ $family->getDateOfBirth() }}</td>
                        {{-- <td>
                            @if ($family->emergency_contact_at)
                                <span class="badge bg-primary">Emergency Contact</span>
                            @endif
                            @if ($family->nominee_at)
                                <span class="badge bg-success">Nominee</span>
                            @endif
                        </td> --}}
                        <td>{{ $family->contact_number }}</td>
                        <td>
                            <a href="javascript:;" class="btn btn-outline-primary btn-sm"
                                onclick="displayFamilyEditForm(this, '{!! route('employees.family.details.edit', [$family->employee_id, $family->id]) !!}')">
                                <i class="bi-pencil-square"></i></a>
                        </td>
                    </tr>
                @endforeach
                {!! $employee->nominee->nominee_at !!}
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
@endpush
