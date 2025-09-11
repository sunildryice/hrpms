            {{-- @if ( --}}
            {{--     $authUser->can('logistic-staff-clearance') || --}}
            {{--         $authUser->can('hr-staff-clearance') || --}}
            {{--         $authUser->can('finance-staff-clearance')) --}}
                @foreach ($departments as $department)
                    @if (
                        ($department->id == config('constant.LOGISTIC_CLEARANCE') && $authUser->can('logistic-staff-clearance')) ||
                            ($department->id == config('constant.HR_CLEARANCE') && $authUser->can('hr-staff-clearance')) ||
                            ($department->id == config('constant.FINANCE_CLEARANCE') && $authUser->can('finance-staff-clearance')))
                        <tr>
                            <td>
                                <span style="width: 100%" class="fw-bold">{{ $department->title }}</span>
                            </td>
                            <td colspan="3"></td>
                        </tr>
                        @foreach ($department->childrens as $children)
                            <tr id="{{ "clearance-row-{$children->id}" }}">
                                <td>
                                    <span style="width: 100%">-{{ $children->title }}</span>
                                </td>
                                @php
                                    $record = $records->firstWhere('clearance_department_id', $children->id);
                                @endphp
                                <td class="text-center">
                                    <input class="form-check-input" type="checkbox" style="scale: 2;"
                                        name="{{ "clearance[{$children->id}][check]" }}"
                                        id="{{ 'clearance-check-' . $children->id }}"
                                        data-department="{{ $children->id }}"
                                        @if ($record?->cleared_at) checked @endif
                                        @if ($record && $record?->created_by != $authUser->id) disabled @endif>
                                </td>
                                <td>
                                    <textarea style="width: 100%; height: 100%" name="{{ "clearance[{$children->id}][remarks]" }}"
                                        @if ($record && $record?->created_by != $authUser->id) disabled @endif data-department="{{ $children->id }}"
                                        id="{{ 'clearance-remarks-' . $children->id }}" rows="2">{{ $record?->remarks }}</textarea>
                                </td>
                                <td class="text-center fs-4 delete-col">
                                    @if ($record && $record->created_by == $authUser->id)
                                        <a href = "javascript:;" class="delete-record text-danger"
                                            data-href="{{ route('clearance.record.destroy', $record->id) }}"
                                            rel="tooltip" title="Delete Record">
                                            <i class="bi-backspace"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            {{-- @endif --}}
