@extends('layouts.container')

@section('title', 'Edit Travel Request')

@section('page_js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#navbarVerticalMenu').find('#travel-request-menu').addClass('active');
        });

        document.addEventListener('DOMContentLoaded', function(e) {

            // NEW: Day-wise Itinerary Dynamic Rows
            const dayItineraryContainer = document.getElementById('day-itinerary-container');
            const departureDateStr =
                '{{ $travelRequest->departure_date ? $travelRequest->departure_date->format('Y-m-d') : '' }}';
            const returnDateStr =
                '{{ $travelRequest->return_date ? $travelRequest->return_date->format('Y-m-d') : '' }}';
            let itineraryData = [];

            function generateDateRange(start, end) {
                const dates = [];
                let current = new Date(start);
                const endDate = new Date(end);
                while (current <= endDate) {
                    dates.push(new Date(current).toISOString().split('T')[0]);
                    current.setDate(current.getDate() + 1);
                }
                return dates;
            }

            function initializeItineraryData() {
                if (!departureDateStr || !returnDateStr) {
                    itineraryData = [];
                    console.log('No date range → itineraryData empty');
                    return;
                }
                const dates = generateDateRange(departureDateStr, returnDateStr);
                const savedData = {!! json_encode(
                    $travelRequest->travelRequestDayItineraries->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'date' => $item->date ? $item->date->format('Y-m-d') : null,
                            'activities' => $item->planned_activities ?? '',
                            'accommodation' => !!$item->accommodation,
                            'air_ticket' => !!$item->air_ticket,
                            'vehicle' => !!$item->vehicle,
                            'from' => $item->departure_place ?? '',
                            'to' => $item->arrival_place ?? '',
                            'departure_time' => $item->departure_time ?? '',
                            'completed_tasks' => $item->completed_tasks ?? '',
                            'remarks' => $item->remarks ?? '',
                        ];
                    }),
                ) !!} ?? [];
                const savedMap = {};
                savedData.forEach(item => {
                    if (item.date) savedMap[item.date] = item;
                });
                itineraryData = dates.map(date => {
                    if (savedMap[date]) {
                        return savedMap[date];
                    } else {
                        return {
                            date: date,
                            activities: '',
                            accommodation: false,
                            air_ticket: false,
                            vehicle: false,
                            from: '',
                            to: '',
                            departure_time: '',
                            completed_tasks: '',
                            remarks: ''
                        };
                    }
                });
            }
            // Hide/show the three air ticket columns (header + all cells)
            function updateAirTicketColumnsVisibility() {
                const hasAirTicket = itineraryData.some(row => row.air_ticket);
                document.querySelectorAll('.air-ticket-col').forEach(el => {
                    el.style.display = hasAirTicket ? '' : 'none';
                });
            }

            function renderDayItineraryRows() {
                dayItineraryContainer.innerHTML = '';
                itineraryData.forEach((row, index) => {
                    const tr = document.createElement('tr');
                    tr.className = 'day-itinerary-row';
                    tr.dataset.index = index;

                    let actions = `
                    <button type="button" class="btn btn-outline-primary btn-sm edit-row-btn me-1" data-index="${index}" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                `;
                    // Only show delete if row has id (already saved)
                    if (row.id) {
                        actions += `
                    <button type="button" class="btn btn-outline-danger btn-sm delete-day-itinerary"
                            data-href="{{ route('travel.requests.day-itinerary.destroy', [$travelRequest->id, ':id']) }}"
                            data-id="${row.id}"
                            title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                `;
                    }

                    tr.innerHTML = `
                    <td>${row.date}</td>
                    <td>${row.activities || '<em class="text-muted">No activities</em>'}</td>
                    <td class="text-center">
                        ${row.accommodation ? '<span class="text fw-bold">Yes</span>' : '<span class="text-muted fw-bold">No</span>'}
                    </td>
                    <td class="text-center">
                        ${row.air_ticket ? '<span class="text fw-bold">Yes</span>' : '<span class="text-muted fw-bold">No</span>'}
                    </td>
                    <td class="text-center">
                        ${row.vehicle ? '<span class="text fw-bold">Yes</span>' : '<span class="text-muted fw-bold">No</span>'}
                    </td>
                    <td class="air-ticket-col text-center">${row.air_ticket ? (row.from || '-') : ''}</td>
                    <td class="air-ticket-col text-center">${row.air_ticket ? (row.to || '-') : ''}</td>
                    <td class="air-ticket-col text-center">${row.air_ticket ? (row.departure_time || '-') : ''}</td>
                    <td>${row.completed_tasks || '<em class="text-muted">No tasks</em>'}</td>
                    <td>${row.remarks || '<em class="text-muted">No remarks</em>'}</td>
                    <td class="text-center">
                        ${actions}
                    </td>
                `;
                    dayItineraryContainer.appendChild(tr);
                });
                updateAirTicketColumnsVisibility();
                attachEditEvents();
                attachDeleteEvents();
            }

            function attachEditEvents() {
                document.querySelectorAll('.edit-row-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const index = parseInt(this.dataset.index);
                        openEditModal(index);
                    });
                });
            }

            function attachDeleteEvents() {
                document.querySelectorAll('.delete-day-itinerary').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const baseUrl = this.getAttribute('data-href');
                        const id = this.getAttribute('data-id');
                        const url = baseUrl.replace(':id', id);

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            // icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085D6',
                            cancelButtonColor: '#dc3545',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: url,
                                    type: 'DELETE',
                                    data: {
                                        _token: $('meta[name="csrf-token"]').attr(
                                            'content')
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            toastr.success(response.message ||
                                                'Deleted successfully!');
                                            if (response.itineraryCount) {
                                                $('.submit-record').show();
                                            } else {
                                                $('.submit-record').hide();
                                            }
                                            reloadItineraryDataFromServer();
                                            renderDayItineraryRows();
                                            $('#savedDayItineraryTable')
                                                .DataTable().ajax.reload();
                                        } else {
                                            toastr.error(response.message ||
                                                'Failed to delete.');
                                        }
                                    },
                                    error: function(xhr) {
                                        toastr.error('Error deleting record.');
                                        console.error(xhr);
                                    }
                                });
                            }
                        });
                    });
                });
            }

            function openEditModal(index) {
                if (!itineraryData[index]) {
                    console.warn('Trying to open modal with invalid index:', index);
                    toastr.warning('Data not loaded yet. Please try again.');
                    return;
                }
                const data = itineraryData[index];

                document.getElementById('editRowIndex').value = index;
                document.getElementById('editDate').value = data.date || '';
                document.getElementById('editActivities').value = data.activities || '';
                document.getElementById('editAccommodation').checked = !!data.accommodation;
                document.getElementById('editAirTicket').checked = !!data.air_ticket;
                document.getElementById('editVehicle').checked = !!data.vehicle;
                document.getElementById('editFrom').value = data.from || '';
                document.getElementById('editTo').value = data.to || '';
                document.getElementById('editDepartureTime').value = data.departure_time || '';
                document.getElementById('editCompletedTasks').value = data.completed_tasks || '';
                document.getElementById('editRemarks').value = data.remarks || '';

                toggleAirTicketFieldsInModal(!!data.air_ticket);
                $('#editItineraryModal').modal('show');
            }

            function toggleAirTicketFieldsInModal(show) {
                document.querySelectorAll('.air-ticket-fields').forEach(row => {
                    row.style.display = show ? 'flex' : 'none';
                });
            }

            document.getElementById('editAirTicket').addEventListener('change', function() {
                toggleAirTicketFieldsInModal(this.checked);
            });

            function showFieldError(fieldId, message) {
                const errorEl = document.getElementById(`error-${fieldId}`);
                const inputEl = document.getElementById(
                    `edit${fieldId.charAt(0).toUpperCase() + fieldId.slice(1)}`); // e.g. editActivities

                if (errorEl && inputEl) {
                    errorEl.textContent = message;
                    inputEl.classList.add('is-invalid');
                }
            }

            function clearAllErrors() {
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.textContent = '';
                });
                document.querySelectorAll('.form-control, .form-check-input').forEach(el => {
                    el.classList.remove('is-invalid');
                });
            }

            document.getElementById('saveEditBtn').addEventListener('click', async function() {
                const index = parseInt(document.getElementById('editRowIndex').value);
                const isAirTicket = document.getElementById('editAirTicket').checked;
                // Clear previous errors
                clearAllErrors();

                const updatedRow = {
                    date: itineraryData[index].date,
                    planned_activities: document.getElementById('editActivities').value.trim(),
                    accommodation: document.getElementById('editAccommodation').checked ? 1 : 0,
                    air_ticket: isAirTicket ? 1 : 0,
                    vehicle: document.getElementById('editVehicle').checked ? 1 : 0,
                    departure_place: isAirTicket ? document.getElementById('editFrom').value
                        .trim() : '',
                    arrival_place: isAirTicket ? document.getElementById('editTo').value.trim() :
                        '',
                    departure_time: isAirTicket ? document.getElementById('editDepartureTime').value
                        .trim() : '',
                    completed_tasks: document.getElementById('editCompletedTasks').value.trim(),
                    remarks: document.getElementById('editRemarks').value.trim()
                };
                // Client-side validation
                let hasError = false;
                if (!updatedRow.planned_activities) {
                    showFieldError('activities', 'Planned activities are required!');
                    hasError = true;
                }
                if (!updatedRow.completed_tasks) {
                    showFieldError('completedTasks', 'Carried activities / completed tasks are required!');
                    hasError = true;
                }

                if (isAirTicket) {
                    if (!updatedRow.departure_place) {
                        showFieldError('from',
                            'Departure place is required when air ticket is selected!');
                        hasError = true;
                    }
                    if (!updatedRow.arrival_place) {
                        showFieldError('to', 'Arrival place is required when air ticket is selected!');
                        hasError = true;
                    }
                    if (!updatedRow.departure_time) {
                        showFieldError('departureTime',
                            'Departure time is required when air ticket is selected!');
                        hasError = true;
                    }
                }
                if (hasError) {
                    return;
                }
                const btn = this;
                btn.disabled = true;
                // btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

                try {
                    const currentRow = itineraryData[index];
                    const dayId = currentRow.id;
                    let response;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content');
                    const payload = {
                        date: updatedRow.date,
                        planned_activities: updatedRow.planned_activities,
                        accommodation: updatedRow.accommodation,
                        air_ticket: updatedRow.air_ticket,
                        vehicle: updatedRow.vehicle,
                        departure_place: updatedRow.departure_place || null,
                        arrival_place: updatedRow.arrival_place || null,
                        departure_time: updatedRow.departure_time || null,
                        completed_tasks: updatedRow.completed_tasks,
                        remarks: updatedRow.remarks,
                    };

                    if (dayId) {
                        const updateUrlTemplate =
                            '{{ route('travel.requests.day-itinerary.update', [$travelRequest->id, ':dayItinerary']) }}';
                        const updateUrl = updateUrlTemplate.replace(':dayItinerary', dayId);

                        response = await fetch(updateUrl, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });
                    } else {
                        const createUrl =
                            '{{ route('travel.requests.day-itinerary.store', $travelRequest->id) }}';

                        response = await fetch(createUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });
                    }

                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({}));
                        // Show server-side validation errors inline
                        if (errorData.errors) {
                            Object.keys(errorData.errors).forEach(field => {
                                const messages = errorData.errors[field];
                                const friendlyField = field.replace('planned_activities',
                                        'activities')
                                    .replace('departure_place', 'from')
                                    .replace('arrival_place', 'to')
                                    .replace('departure_time', 'departureTime');

                                showFieldError(friendlyField, messages[
                                    0]); 
                            });
                        }

                        throw new Error(errorData.message || (dayId ? 'Failed to update' :
                            'Failed to create') + ' day itinerary');
                    }
                    const result = await response.json();

                    if (!dayId && result.id) {
                        itineraryData[index].id = result.id;
                    }
                    await reloadItineraryDataFromServer();
                    renderDayItineraryRows();
                    $('#savedDayItineraryTable').DataTable().ajax.reload();

                    $('#editItineraryModal').modal('hide');
                    toastr.success('Saved successfully!');
                    if (result.itineraryCount !== undefined) {
                        if (result.itineraryCount > 0) {
                            $('.submit-record').show();
                        } else {
                            $('.submit-record').hide();
                        }
                    }
                } catch (err) {
                    toastr.error(err.message || 'Error saving changes');
                    console.error('Save error:', err);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'Save';
                }
            });

            async function reloadItineraryDataFromServer() {
                try {
                    const response = await fetch(
                        '{{ route('travel.requests.day-itinerary.index', $travelRequest->id) }}?all=true', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );
                    if (!response.ok) {
                        throw new Error(`Failed to reload: ${response.status} ${response.statusText}`);
                    }
                    const json = await response.json();
                    const freshSaved = json.data || json;

                    const savedMap = {};
                    freshSaved.forEach(item => {
                        if (item.date) {
                            savedMap[item.date] = {
                                id: item.id,
                                date: item.date,
                                activities: item.planned_activities || '',
                                accommodation: !!item.accommodation,
                                air_ticket: !!item.air_ticket,
                                vehicle: !!item.vehicle,
                                from: item.departure_place || '',
                                to: item.arrival_place || '',
                                departure_time: item.departure_time || '',
                                completed_tasks: item.completed_tasks || '',
                                remarks: item.remarks || ''
                            };
                        }
                    });
                    const dates = generateDateRange(departureDateStr, returnDateStr);
                    itineraryData = dates.map(date => savedMap[date] || {
                        date: date,
                        activities: '',
                        accommodation: false,
                        air_ticket: false,
                        vehicle: false,
                        from: '',
                        to: '',
                        departure_time: '',
                        completed_tasks: '',
                        remarks: ''
                    });
                    dayItineraryContainer.innerHTML = '';
                    await new Promise(resolve => setTimeout(resolve, 10));
                    renderDayItineraryRows();

                    return true;
                } catch (err) {
                    console.error('Reload failed:', err);
                    toastr.error('Failed to refresh itinerary. Please refresh page.');
                    return false;
                }
            }

            // Initial load
            initializeItineraryData();
            renderDayItineraryRows();

            var savedDayTable = $('#savedDayItineraryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('travel.requests.day-itinerary.index', $travelRequest->id) }}",
                bFilter: false,
                bPaginate: false,
                bInfo: false,
                columns: [{
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'planned_activities',
                        name: 'planned_activities'
                    },
                    {
                        data: 'accommodation',
                        name: 'accommodation',
                        orderable: false,
                        searchable: false,
                    },

                    {
                        data: 'air_ticket',
                        name: 'air_ticket',
                        orderable: false,
                        searchable: false,
                    },
                    {  
                        data: 'vehicle',
                        name: 'vehicle',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'completed_tasks',
                        name: 'completed_tasks'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                ]
            });

            //Hide "Add Estimation" button if an estimate already exists
            @if ($travelRequest->travelRequestEstimate)
                $('.estimateAddBlock').hide();
            @else
                $('.estimateAddBlock').show();
            @endif

            const form = document.getElementById('travelRequestEditForm');
            const fv = FormValidation.formValidation(form, {
                fields: {
                    travel_type_id: {
                        validators: {
                            notEmpty: {
                                message: 'Travel Type is required',
                            },
                        },
                    },
                    purpose_of_travel: {
                        validators: {
                            notEmpty: {
                                message: 'The Purpose of Travel is required',
                            },
                        },
                    },
                    project_code_id: {
                        validators: {
                            notEmpty: {
                                message: 'The project code is required',
                            },
                        },
                    },
                    departure_date: {
                        validators: {
                            notEmpty: {
                                message: 'The departure date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    return_date: {
                        validators: {
                            notEmpty: {
                                message: 'The return date is required',
                            },
                            date: {
                                format: 'YYYY-MM-DD',
                                message: 'The value is not a valid date',
                            },
                        },
                    },
                    final_destination: {
                        validators: {
                            notEmpty: {
                                message: 'Destination is required',
                            },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5(),
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
                            field: 'departure_date',
                            message: 'Departure date must be a valid date and earlier than return date.',
                        },
                        endDate: {
                            field: 'return_date',
                            message: 'Return date must be a valid date and later than departure date.',
                        },
                    }),
                },
            });

            document.querySelector('.submit-record').addEventListener('click', function(e) {
                e.preventDefault();
                fv.validate().then(function(status) {
                    if (status === 'Valid') {
                        let btnInput = document.querySelector('input[name="btn"]');
                        if (!btnInput) {
                            btnInput = document.createElement('input');
                            btnInput.type = 'hidden';
                            btnInput.name = 'btn';
                            form.appendChild(btnInput);
                        }
                        btnInput.value = 'submit';
                        form.submit();
                    }
                });
            });

            // Passport Section Toggle
            const travelTypeSelect = document.querySelector('select[name="travel_type_id"]');
            const passportSection = document.getElementById('passportSection');

            function togglePassportSection() {
                if (travelTypeSelect.value == '2') {
                    passportSection.style.display = 'block';
                } else {
                    passportSection.style.display = 'none';
                }
            }
            togglePassportSection();
            travelTypeSelect.addEventListener('change', togglePassportSection);
            $(travelTypeSelect).on('change', togglePassportSection);


            // External Travelers Dynamic Rows 
            const countInput = document.getElementById('external_traveler_count');
            const container = document.getElementById('external-travelers-container');

            function renderExternalTravelers() {
                const count = parseInt(countInput.value) || 0;
                if (count < 0) {
                    countInput.value = 0;
                    return;
                }

                const existingRows = Array.from(container.querySelectorAll('.external-traveler-row'));
                container.innerHTML = '';

                for (let i = 0; i < count; i++) {
                    const row = document.createElement('div');
                    row.className = 'row mb-2 align-items-end external-traveler-row';

                    const oldName = existingRows[i]?.querySelector('input[name$="[name]"]')?.value || '';
                    const oldEmail = existingRows[i]?.querySelector('input[name$="[email]"]')?.value || '';

                    let rowHTML = `
                    <div class="col-lg-3"></div>
                    <div class="col-md-4">
                        <input type="text" 
                               name="external_travelers[${i}][name]" 
                               class="form-control" 
                               placeholder="Full Name *" 
                               value="${oldName}" 
                               required>
                    </div>
                    <div class="col-md-4">
                        <input type="email" 
                               name="external_travelers[${i}][email]" 
                               class="form-control" 
                               placeholder="Email (optional)" 
                               value="${oldEmail}">
                    </div>
                    <div class="col-md-1">
                    <div class="btn-group" role="group">
                    <button type="button" class="btn btn-danger btn-sm remove-traveler-row" title="Remove">
                        <i class="bi bi-trash"></i>
                    </button>`;

                    if (i === count - 1 && count > 0) {
                        rowHTML += `
                    <button type="button" class="btn btn-success btn-sm add-traveler-row ms-1" title="Add another traveler">
                        <i class="bi bi-plus-lg"></i>
                    </button>`;
                    }

                    rowHTML += `
                    </div>
                    </div>`;

                    row.innerHTML = rowHTML;
                    container.appendChild(row);
                }

                attachRowEvents();
            }

            function attachRowEvents() {
                // Remove row
                document.querySelectorAll('.remove-traveler-row').forEach(btn => {
                    btn.onclick = function() {
                        this.closest('.external-traveler-row').remove();
                        const newCount = container.children.length;
                        countInput.value = newCount;
                        if (newCount > 0) renderExternalTravelers();
                    };
                });

                // Add row
                document.querySelectorAll('.add-traveler-row').forEach(btn => {
                    btn.onclick = function() {
                        countInput.value = parseInt(countInput.value || 0) + 1;
                        renderExternalTravelers();
                    };
                });
            }

            if (countInput && container) {
                const savedCount = {{ $travelRequest->external_traveler_count ?? 0 }};
                if (countInput.value == 0 && savedCount > 0) {
                    countInput.value = savedCount;
                }

                renderExternalTravelers();

                countInput.addEventListener('input', function() {
                    if (this.value < 0) this.value = 0;
                    renderExternalTravelers();
                });

                countInput.addEventListener('change', renderExternalTravelers);
            }

            $(form).find('[name="departure_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! $travelRequest->parentTravelRequest
                    ? $travelRequest->parentTravelRequest->departure_date->subDays(14)->format('Y-m-d')
                    : date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('departure_date');
                fv.revalidateField('return_date');
            });

            $(form).find('[name="return_date"]').datepicker({
                language: 'en-GB',
                autoHide: true,
                format: 'yyyy-mm-dd',
                startDate: '{!! $travelRequest->parentTravelRequest
                    ? $travelRequest->parentTravelRequest->departure_date->subDays(14)->format('Y-m-d')
                    : date('Y-m-d') !!}',
                zIndex: 2048,
            }).on('change', function(e) {
                fv.revalidateField('departure_date');
                fv.revalidateField('return_date');
            });

            $(form).on('change', '[name="project_code_id"]', function(e) {
                fv.revalidateField('project_code_id');
            });

            @if (!$authUser->can('submit', $travelRequest))
                $('.estimateDiv').hide();
                $('.submit-record').hide();
            @endif
        });

        var estimateTable = $('#estimationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('travel.requests.estimate.index', $travelRequest->id) }}",
            bFilter: false,
            bPaginate: false,
            bInfo: false,
            columns: [{
                    data: 'estimated_dsa',
                    name: 'estimated_dsa'
                },
                {
                    data: 'estimated_air_fare',
                    name: 'estimated_air_fare'
                },
                {
                    data: 'estimated_vehicle_fare',
                    name: 'estimated_vehicle_fare'
                },
                {
                    data: 'estimated_hotel_accommodation',
                    name: 'estimated_hotel_accommodation'
                },
                {
                    data: 'estimated_airport_taxi',
                    name: 'estimated_airport_taxi'
                },
                {
                    data: 'miscellaneous_amount',
                    name: 'miscellaneous_amount'
                },
                {
                    data: 'estimated_event_activities_cost',
                    name: 'estimated_event_activities_cost'
                },
                {
                    data: 'miscellaneous_remarks',
                    name: 'miscellaneous_remarks'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'sticky-col'
                },
            ]
        });

        $('#estimationTable').on('click', '.delete-record', function(e) {
            e.preventDefault();
            $object = $(this);
            var $url = $object.attr('data-href');
            var tRow = '<tr><td colspan="7" class="text-center">Record not found.</td></tr>';
            var successCallback = function(response) {
                toastr.success(response.message, 'Success', {
                    timeOut: 5000
                });
                if (response.estimateCount) {
                    $('.estimateAddBlock').hide();
                } else {
                    $('.estimateAddBlock').show();
                }
                estimateTable.ajax.reload();
            }
            ajaxDeleteSweetAlert($url, successCallback);
        });

        $(document).on('click', '.open-estimation-modal-form', function(e) {
            e.preventDefault();
            $('#estimateAddModal').find('.modal-content').html('');
            $('#estimateAddModal').modal('show').find('.modal-content').load($(this).attr('href'), function() {
                const form = document.getElementById('estimateForm');
                const fv = FormValidation.formValidation(form, {
                    fields: {
                        estimated_dsa: {
                            validators: {
                                notEmpty: {
                                    message: 'The Estimated DSA is required',
                                },
                                numeric: {
                                    message: 'The Estimated DSA should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        estimated_air_fare: {
                            validators: {
                                numeric: {
                                    message: 'The Estimated Air Fare should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        estimated_vehicle_fare: {
                            validators: {
                                numeric: {
                                    message: 'The Estimated Vehicle Fare should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        estimated_hotel_accommodation: {
                            validators: {
                                numeric: {
                                    message: 'The Estimated Hotel Accommodation  should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        estimated_airport_taxi: {
                            validators: {
                                numeric: {
                                    message: 'The Estimated Airport Taxi should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        miscellaneous_amount: {
                            validators: {
                                numeric: {
                                    message: 'The Miscellaneous Amount should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        estimated_event_activities_cost: {
                            validators: {
                                numeric: {
                                    message: 'The Estimated Event/Activities Amount should be number.',
                                },
                                between: {
                                    inclusive: true,
                                    min: 0,
                                    max: 99999999,
                                    message: 'The value must be between 0 to 99999999',
                                },
                            },
                        },
                        total_amount: {
                            validators: {
                                numeric: {
                                    message: 'Total amount is invalid'
                                }
                            }
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap5: new FormValidation.plugins.Bootstrap5(),
                        submitButton: new FormValidation.plugins.SubmitButton(),
                        icon: new FormValidation.plugins.Icon({
                            valid: 'bi bi-check2-square',
                            invalid: 'bi bi-x-lg',
                            validating: 'bi bi-arrow-repeat',
                        }),
                    },
                }).on('core.form.valid', function(event) {
                    $url = fv.form.action;
                    $form = fv.form;
                    data = $($form).serialize();
                    var successCallback = function(response) {
                        $('#estimateAddModal').modal('hide');
                        toastr.success(response.message, 'Success', {
                            timeOut: 5000
                        });
                        if (response.estimateCount) {
                            $('.estimateAddBlock').hide();
                        } else {
                            $('.estimateAddBlock').show();
                        }
                        estimateTable.ajax.reload();
                    }
                    ajaxSubmit($url, 'POST', data, successCallback);
                });
                const fields = [
                    'estimated_dsa',
                    'estimated_air_fare',
                    'estimated_vehicle_fare',
                    'estimated_hotel_accommodation',
                    'estimated_airport_taxi',
                    'miscellaneous_amount',
                    'estimated_event_activities_cost'
                ];

                const totalInput = document.getElementById('total_amount');

                function calculateTotal() {
                    let sum = 0;
                    fields.forEach(id => {
                        const input = document.getElementById(id);
                        if (input) {
                            const val = parseFloat(input.value) || 0;
                            sum += val;
                        }
                    });
                    totalInput.value = sum.toFixed(2);
                }

                calculateTotal();

                fields.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', calculateTotal);
                        el.addEventListener('change', calculateTotal);
                    }
                });
            });
        });
    </script>
    <link href="{{ asset('plugins/slim-select/dist/slimselect.css') }}" rel="stylesheet">
    <script src="{{ asset('plugins/slim-select/dist/slimselect.min.js') }}"></script>
    <script>
        new SlimSelect({
            select: '#accompanying_staff',
            placeholder: 'Select accompanying staffs'
        });
        new SlimSelect({
            select: '#substitutes',
            placeholder: 'Select substitutes'
        });
    </script>
@endsection
@section('page-content')


    <div class="page-header pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center">
            <div class="brd-crms flex-grow-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{!! route('dashboard.index') !!}"
                                class="text-decoration-none text-dark">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('travel.requests.index') }}"
                                class="text-decoration-none text-dark">Travel Request</a></li>
                        <li class="breadcrumb-item" aria-current="page">Edit Travel Request</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="registration">
        <form action="{{ route('travel.requests.update', $travelRequest->id) }}" id="travelRequestEditForm" method="post"
            enctype="multipart/form-data" autocomplete="off">

            <div class="card">
                <div class="card-body">
                    @php $selectedAccompanyingStaffs = $travelRequest->accompanyingStaffs->pluck('id')->toArray(); @endphp
                    @php $travelSubstitutes = $travelRequest->substitutes->pluck('id')->toArray(); @endphp
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationtraveltype" class="form-label required-label">Travel
                                    Type
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="travel_type_id" class="select2 form-control" data-width="100%">
                                <option value="">Select a Travel Type</option>
                                @foreach ($travelTypes as $travelType)
                                    <option value="{{ $travelType->id }}"
                                        {{ $travelType->id == $travelRequest->travel_type_id ? 'selected' : '' }}>
                                        {{ $travelType->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('travel_type_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="travel_type_id">
                                        {!! $errors->first('travel_type_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div id="passportSection" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label class="form-label">Passport Number</label>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-control bg-light border-0">
                                    @if ($employeePassportNumber)
                                        <strong class="text-dark">{{ $employeePassportNumber }}</strong>
                                    @else
                                        <span class="text-danger">
                                            <i class="bi bi-x-circle"></i> Not provided
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <label class="form-label">Passport Attachment</label>
                            </div>
                            <div class="col-lg-2">
                                <div
                                    class="form-control bg-light border-0 d-flex align-items-center justify-content-between">
                                    @if ($employeePassportAttachment && \Storage::disk('public')->exists($employeePassportAttachment))
                                        <a href="{{ asset('storage/' . $employeePassportAttachment) }}" target="_blank"
                                            class="btn btn-success btn-sm">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <small class="text-success ms-2">
                                            <i class="bi bi-check-circle-fill"></i> Uploaded
                                        </small>
                                    @else
                                        <span class="text-warning">
                                            <i class="bi bi-exclamation-triangle"></i> Not uploaded
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3">
                                @if (!$employeePassportNumber || !$employeePassportAttachment)
                                    <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary"
                                        style="text-decoration: none" target="_blank" rel="noopener noreferrer">
                                        Edit Profile <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if (!$employeePassportNumber && !$employeePassportAttachment)
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="alert alert-warning border small py-2 mb-3" role="alert">
                                        <i class="bi bi-info-circle"></i>
                                        <strong>Action Required:</strong> Please update your profile with passport details
                                        for international travel.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationAccompanyingStaffs" class="m-0">Accompanying
                                    Staff</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="accompanying_staff[]" id="accompanying_staff"
                                class="@if ($errors->has('accompanying_staff')) is-invalid @endif" data-width="100%" multiple>
                                <option value="">Select Accompanying Staff</option>
                                @foreach ($substitutes as $staff)
                                    <option value="{{ $staff->id }}" @if (in_array($staff->id, $selectedAccompanyingStaffs)) selected @endif>
                                        {{ $staff->getFullNameWithCode() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('accompanying_staff'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="accompanying_staff">
                                        {!! $errors->first('accompanying_staff') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProject" class="form-label required-label">Project
                                </label>
                            </div>
                        </div>
                        @php $selectedProjectCodeId =  old('project_code_id') ?: $travelRequest->project_code_id  @endphp
                        <div class="col-lg-9">
                            <select name="project_code_id"
                                class="select2 form-control
                                                    @if ($errors->has('project_code_id')) is-invalid @endif"
                                data-width="100%">
                                <option value="">Select a Project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}"
                                        {{ $project->id == $selectedProjectCodeId ? 'selected' : '' }}>
                                        {{ $project->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('project_code_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="project_code_id">
                                        {!! $errors->first('project_code_id') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationPurposeofTravel" class="form-label required-label">Purpose
                                    of Travel </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control @if ($errors->has('purpose_of_travel')) is-invalid @endif"
                                name="purpose_of_travel" value="{{ $travelRequest->purpose_of_travel }}"
                                placeholder="Purpose of travel">
                            @if ($errors->has('purpose_of_travel'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="purpose_of_travel">{!! $errors->first('purpose_of_travel') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationdd" class="form-label required-label">Departure Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control @if ($errors->has('departure_date')) is-invalid @endif"
                                name="departure_date"
                                value="{{ old('departure_date') ?: ($travelRequest->departure_date ? $travelRequest->departure_date->format('Y-m-d') : '') }}"
                                data-toggle="datepicker" onfocus="this.blur()" placeholder="yyyy-mm-dd" />
                            @if ($errors->has('departure_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="departure_date">{!! $errors->first('departure_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationreturndate" class="form-label required-label">Return
                                    Date
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                class="form-control @if ($errors->has('return_date')) is-invalid @endif"
                                name="return_date"
                                value="{{ old('return_date') ?: ($travelRequest->return_date ? $travelRequest->return_date->format('Y-m-d') : '') }}"
                                data-toggle="datepicker" onfocus="this.blur()" placeholder="yyyy-mm-dd" />
                            @if ($errors->has('return_date'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="return_date">{!! $errors->first('return_date') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationProject" class="m-0">Substitutes
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <select name="substitutes[]" id="substitutes" data-width="100%" multiple
                                class="@if ($errors->has('substitutes')) is-invalid @endif">
                                @foreach ($substitutes as $substitute)
                                    <option value="{{ $substitute->id }}"
                                        {{ in_array($substitute->id, $travelSubstitutes) ? 'selected' : '' }}>
                                        {{ $substitute->getFullNameWithCode() }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('substitutes'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="substitutes">
                                        {!! $errors->first('substitutes') !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks" class="form-label required-label">Destination
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="text"
                                value="{{ old('final_destination') ?: $travelRequest->final_destination }}"
                                class="form-control @if ($errors->has('final_destination')) is-invalid @endif"
                                name="final_destination" />
                            @if ($errors->has('final_destination'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="final_destination">{!! $errors->first('final_destination') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- External Travelers (Outside Organization) --}}
                    <div class="row mb-3">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label class="form-label">Number of travelers (if any outside the organization)</label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            <input type="number" min="0" id="external_traveler_count"
                                name="external_traveler_count" class="form-control"
                                value="{{ old('external_traveler_count', $travelRequest->external_traveler_count ?? 0) }}"
                                placeholder="e.g. 0, 1, 2, 3...">
                            <small class="text-muted">Enter 0 if no external travelers</small>
                        </div>
                    </div>

                    <div id="external-travelers-container" class="mb-4">
                        @foreach ($travelRequest->external_travelers as $index => $traveler)
                            <div class="row mb-2 align-items-end external-traveler-row">
                                <div class="col-lg-3">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="external_travelers[{{ $index }}][name]"
                                        class="form-control" placeholder="Full Name *"
                                        value="{{ old("external_travelers.$index.name", $traveler['name'] ?? '') }}"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <input type="email" name="external_travelers[{{ $index }}][email]"
                                        class="form-control" placeholder="Email (optional)"
                                        value="{{ old("external_travelers.$index.email", $traveler['email'] ?? '') }}">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-traveler-row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row mb-2">
                        <div class="col-lg-3">
                            <div class="d-flex align-items-start h-100">
                                <label for="validationRemarks"
                                    class="form-label required-label">{{ __('label.approval') }} </label>
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @php $selectedApproverId = old('approver_id') ?: $travelRequest->approver_id; @endphp
                            <select name="approver_id"
                                class="select2 form-control
                                                @if ($errors->has('approver_id')) is-invalid @endif"
                                data-width="100%">
                                @if ($supervisors->count() !== 1)
                                    <option value="">Select an Approver</option>
                                @endif
                                @foreach ($supervisors as $approver)
                                    <option value="{{ $approver->id }}"
                                        {{ $approver->id == $selectedApproverId ? 'selected' : '' }}>
                                        {{ $approver->getFullName() }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('approver_id'))
                                <div class="fv-plugins-message-container invalid-feedback">
                                    <div data-field="approver_id">{!! $errors->first('approver_id') !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer border-0 justify-content-end d-flex gap-2">
                    <button type="submit" name="btn" value="save" class="btn btn-primary btn-sm">
                        Update
                    </button>

                </div>
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}
            </div>

            <div class="card mt-4">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center justify-content-between">
                        <span>Travel Itinerary</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 120px;">Date</th>
                                    <th>Planned Activities</th>
                                    <th class="text-center">Accommodation</th>
                                    <th class="text-center">Air Ticket</th>
                                    <th class="text-center">Vehicle</th>
                                    <th class="air-ticket-col">From</th>
                                    <th class="air-ticket-col">To</th>
                                    <th class="air-ticket-col">Departure Time</th>
                                    <th>Carried Activities / Completed Tasks</th>
                                    <th>Remarks</th>
                                    <th style="width: 100px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="day-itinerary-container">
                                <!-- Dynamic rows injected by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Travel Itinerary</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="savedDayItineraryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 120px;">Date</th>
                                    <th>Planned Activities</th>
                                    <th class="text-center">Accommodation</th>
                                    <th class="text-center">Air Ticket</th>
                                    <th class="text-center">Vehicle</th>
                                    <th class="text-center">Carried Activities / Completed Tasks</th>
                                    <th class="text-center">Remarks</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="editItineraryModal" tabindex="-1" aria-labelledby="editItineraryModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="editItineraryModalLabel">Edit Travel Itinerary</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editItineraryForm">
                                <input type="hidden" id="editRowIndex">

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Date</label>
                                    <div class="col-md-9">
                                        <input type="date" id="editDate" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label required-label">Planned Activities</label>
                                    <div class="col-md-9">
                                        <textarea id="editActivities" class="form-control" rows="3" placeholder="Describe planned activities..."></textarea>
                                        <div class="invalid-feedback" id="error-activities"></div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-check-label" for="editAccommodation">Accommodation</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editAccommodation">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-check-label" for="editAirTicket">Air Ticket</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editAirTicket">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-check-label" for="editVehicle">Vehicle</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="editVehicle">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3 air-ticket-fields" style="display: none;">
                                    <label class="col-md-3 col-form-label required-label">From</label>
                                    <div class="col-md-9">
                                        <input type="text" id="editFrom" class="form-control"
                                            placeholder="Departure city/airport">
                                        <div class="invalid-feedback" id="error-from"></div>
                                    </div>
                                </div>

                                <div class="row mb-3 air-ticket-fields" style="display: none;">
                                    <label class="col-md-3 col-form-label required-label">To</label>
                                    <div class="col-md-9">
                                        <input type="text" id="editTo" class="form-control"
                                            placeholder="Arrival city/airport">
                                        <div class="invalid-feedback" id="error-to"></div>
                                    </div>
                                </div>

                                <div class="row mb-3 air-ticket-fields" style="display: none;">
                                    <label class="col-md-3 col-form-label required-label">Departure Time</label>
                                    <div class="col-md-9">
                                        <input type="text" id="editDepartureTime" class="form-control"
                                            placeholder="e.g. 14:30">
                                        <div class="invalid-feedback" id="error-departureTime"></div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label required-label">Carried Activities / Completed Tasks</label>
                                    <div class="col-md-9">
                                        <textarea id="editCompletedTasks" class="form-control" rows="3" placeholder="Carried Activities / Completed Tasks"></textarea>
                                        <div class="invalid-feedback" id="error-completedTasks"></div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-md-3 col-form-label">Remarks</label>
                                    <div class="col-md-9">
                                        <textarea id="editRemarks" class="form-control" rows="2" 
                                                  placeholder="Remarks"></textarea>
                                        <div class="invalid-feedback" id="error-remarks"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="saveEditBtn" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card ">
                <div class="card-header fw-bold">
                    <div class="d-flex align-items-center add-info justify-content-between">
                        <span> Travel Advance Request</span>
                        <div class="estimateAddBlock">
                            <button data-toggle="modal" class="btn btn-primary btn-sm open-estimation-modal-form"
                                href="{!! route('travel.requests.estimate.create', $travelRequest->id) !!}">
                                <i class="bi-plus"></i> Add Estimation
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body estimate">
                    <div class="table-responsive">
                        <table class="table" id="estimationTable">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('label.estimated-dsa') }}</th>
                                    <th scope="col">{{ __('label.estimated-air-fare') }}</th>
                                    <th scope="col">{{ __('label.estimated-vehicle-fare') }}</th>
                                    <th scope="col">{{ __('label.estimated-hotel-accommodation') }}</th>
                                    <th scope="col">{{ __('label.estimated-airport-taxi') }}</th>
                                    <th scope="col">{{ __('label.miscellaneous-amount') }}</th>
                                    <th scope="col">{{ __('label.estimated-event-activities-cost') }}</th>
                                    <th scope="col">{{ __('label.miscellaneous-remarks') }}</th>
                                    <th scope="col">{{ __('label.total-advance-amount') }}</th>
                                    <th style="width: 150px">{{ __('label.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="justify-content-end d-flex gap-2" id="submitRequest">
                <button type="submit" name="btn" value="submit" class="btn btn-success btn-sm submit-record"
                    @if (!$authUser->can('submit', $travelRequest)) style="display:none;" @endif>
                    Submit
                </button>
                <a href="{!! route('travel.requests.index') !!}" class="btn btn-danger btn-sm">Cancel</a>
            </div>
        </form>
    </section>

    <div class="modal fade" id="estimateAddModal" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-labelledby="estimateAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>

@stop
