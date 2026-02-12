@section('page_css')
    <style>
        :root {
            --c-sick-leave: #dc3545;
            --c-annual-leave: #dc3545;
            --c-lieu-leave: #dc3545;
            --c-work-from-home: #0dcaf0;
            --c-travel: #6610f2;
            --c-work: #fd7e14;
            --bg-weekend: #e9ecef;
            --c-leave: #dc3545;
            --bg-holiday: #e9ecef;
            --bg-holiday-bar: #dc3545;
        }

        #calendar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .fc-day-sat {
            background-color: var(--bg-weekend) !important;
        }

        .fc-day-sat .fc-daygrid-day-frame {
            background-image: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 10px,
                    rgba(0, 0, 0, 0.02) 10px,
                    rgba(0, 0, 0, 0.02) 20px);
        }

        .fc-day-sat .fc-daygrid-day-number {
            color: #adb5bd;
            font-weight: bold;
        }

        @if ($office->weekend_type == config('constant.Saturday+Sunday'))
            .fc-day-sun {
                background-color: var(--bg-weekend) !important;
            }

            .fc-day-sun .fc-daygrid-day-frame {
                background-image: repeating-linear-gradient(45deg,
                        transparent,
                        transparent 10px,
                        rgba(0, 0, 0, 0.02) 10px,
                        rgba(0, 0, 0, 0.02) 20px);
            }

            .fc-day-sun .fc-daygrid-day-number {
                color: #adb5bd;
                font-weight: bold;
            }
        @endif

        .fc-event {
            border: none;
            border-radius: 3px;
            font-size: 0.6rem;
            font-weight: 600;
            cursor: pointer;
            padding: 1px 3px;
        }

        .fc-daygrid-more-link {
            font-weight: 700;
            color: #495057 !important;
            background: #dee2e6;
            padding: 3px;
            border-radius: 8px;
            text-decoration: none;
        }

        .event-list-item {
            cursor: pointer;
            border-left: 5px solid #ccc;
            transition: background 0.2s;
        }

        .event-list-item:hover {
            background-color: #f1f3f5;
        }

        .fc .fc-daygrid-day-frame {
            padding-top: 2px;
            padding-bottom: 2px;
        }
    </style>
@endsection

<div>
    <div class="">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="px-4 py-2" id="calendar"></div>
            </div>
        </div>
    </div>

    {{-- DAY OVERVIEW MODAL --}}
    <div class="modal fade" id="dayOverviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold">
                            <span id="dayOverviewDate" class="text-primary"></span>
                        </h5>
                        <small id="dayOverviewSubtitle" class="text-muted"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-scrollable p-0">
                    <div class="container-fluid py-3">
                        <div class="row gx-2 gy-3" id="dayEventsList">
                            {{-- JS appends cols here --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SINGLE EVENT MODAL -->
    <div class="modal fade" id="singleEventModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" id="singleEventHeader">
                    <h5 class="modal-title" id="singleEventTitle">Title</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <h6 class="fw-bold">Time / Duration</h6>
                        <p id="singleEventDateRange" class="text-muted small mb-0"></p>
                    </div>

                    <div class="mb-3 d-none" id="singleEventLeaveModeContainer">
                        <h6 class="fw-bold">Leave Mode</h6>
                        <p id="singleEventLeaveMode" class="text-muted small mb-0"></p>
                    </div>

                    <div>
                        <h6 class="fw-bold">Description</h6>
                        <p id="singleEventDescription" class="text-muted small mb-0"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.8/index.global.min.js"></script>


@section('page_css')
    <style>
        :root {
            --c-sick-leave: #dc3545;
            --c-annual-leave: #dc3545;
            --c-lieu-leave: #dc3545;
            --c-work-from-home: #0dcaf0;
            --c-travel: #6610f2;
            --c-work: #fd7e14;
            --bg-weekend: #e9ecef;
            --c-leave: #dc3545;
            --bg-holiday: #e9ecef;
            --bg-holiday-bar: #dc3545;
        }

        #calendar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .fc-day-sat {
            background-color: var(--bg-weekend) !important;
        }

        .fc-day-sat .fc-daygrid-day-frame {
            background-image: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 10px,
                    rgba(0, 0, 0, 0.02) 10px,
                    rgba(0, 0, 0, 0.02) 20px);
        }

        .fc-day-sat .fc-daygrid-day-number {
            color: #adb5bd;
            font-weight: bold;
        }

        @if ($office->weekend_type == config('constant.Saturday+Sunday'))
            .fc-day-sun {
                background-color: var(--bg-weekend) !important;
            }

            .fc-day-sun .fc-daygrid-day-frame {
                background-image: repeating-linear-gradient(45deg,
                        transparent,
                        transparent 10px,
                        rgba(0, 0, 0, 0.02) 10px,
                        rgba(0, 0, 0, 0.02) 20px);
            }

            .fc-day-sun .fc-daygrid-day-number {
                color: #adb5bd;
                font-weight: bold;
            }
        @endif

        .fc-event {
            border: none;
            border-radius: 3px;
            font-size: 0.6rem;
            font-weight: 600;
            cursor: pointer;
            padding: 1px 3px;
        }

        .fc-daygrid-more-link {
            font-weight: 700;
            color: #495057 !important;
            background: #dee2e6;
            padding: 3px;
            border-radius: 8px;
            text-decoration: none;
        }

        .event-list-item {
            cursor: pointer;
            border-left: 5px solid #ccc;
            transition: background 0.2s;
        }

        .event-list-item:hover {
            background-color: #f1f3f5;
        }

        .fc .fc-daygrid-day-frame {
            padding-top: 2px;
            padding-bottom: 2px;
        }
    </style>
@endsection

<div>
    <div class="">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="px-4 py-2" id="calendar"></div>
            </div>
        </div>
    </div>

    {{-- DAY OVERVIEW MODAL --}}
    <div class="modal fade" id="dayOverviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold">
                            <span id="dayOverviewDate" class="text-primary"></span>
                        </h5>
                        <small id="dayOverviewSubtitle" class="text-muted"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-scrollable p-0">
                    <div class="container-fluid py-3">
                        <div class="row gx-2 gy-3" id="dayEventsList">
                            {{-- JS appends cols here --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SINGLE EVENT MODAL -->
    <div class="modal fade" id="singleEventModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header text-white" id="singleEventHeader">
                    <h5 class="modal-title" id="singleEventTitle">Title</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <h6 class="fw-bold">Time / Duration</h6>
                        <p id="singleEventDateRange" class="text-muted small mb-0"></p>
                    </div>

                    <div class="mb-3 d-none" id="singleEventLeaveModeContainer">
                        <h6 class="fw-bold">Leave Mode</h6>
                        <p id="singleEventLeaveMode" class="text-muted small mb-0"></p>
                    </div>

                    <div>
                        <h6 class="fw-bold">Description</h6>
                        <p id="singleEventDescription" class="text-muted small mb-0"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.8/index.global.min.js"></script>

<script>
    const CALENDAR_URL_TEMPLATE =
        "{{ route('api.calender.index', ['officeId' => '__OFFICE_ID__', 'month' => '__MONTH__', 'year' => '__YEAR__']) }}";

    const TYPE_PRIORITY = {
        sick_leave: 1,
        annual_leave: 2,
        lieu_leave: 3,
        work_from_home: 4,
        travel: 5,
        work: 6,
        leave: 7,
        holiday: 8
    };

    function getPriority(type) {
        return TYPE_PRIORITY[type] ?? 99;
    }

    function buildCalendarUrl(officeId, month, year) {
        return CALENDAR_URL_TEMPLATE
            .replace('__OFFICE_ID__', officeId)
            .replace('__MONTH__', month)
            .replace('__YEAR__', year);
    }

    function fmt(d) {
        return d.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    }

    function normalizeDate(dateStr) {
        if (!dateStr) return null;
        if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
            return dateStr;
        }
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return null;
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${m}-${day}`;
    }

    function addOneDay(dateStr) {
        if (!dateStr) return null;
        const d = new Date(dateStr);
        if (isNaN(d.getTime())) return null;
        d.setDate(d.getDate() + 1);
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${d.getFullYear()}-${m}-${day}`;
    }

    function subtractDay(dateObj) {
        if (!dateObj) return null;
        const d = new Date(dateObj.getTime());
        d.setDate(d.getDate() - 1);
        return d;
    }

    function setHolidayBackground(dateStr) {
        const dateEl = document.querySelector(`.fc-day[data-date='${dateStr}']`);
        if (dateEl) {
            dateEl.style.backgroundColor = 'var(--bg-holiday)';
            const frame = dateEl.querySelector('.fc-daygrid-day-frame');
            if (frame) {
                frame.style.backgroundImage = `
                    repeating-linear-gradient(45deg,
                        transparent,
                        transparent 10px,
                        rgba(0, 0, 0, 0.02) 10px,
                        rgba(0, 0, 0, 0.02) 20px)`;
            }
            dateEl.style.fontWeight = 'bold';
        }
    }

    function getLeaveModeLabel(leaveRequest) {
        if (!leaveRequest.leave_days || leaveRequest.leave_days.length === 0) {
            return '';
        }

        const modes = new Set();
        leaveRequest.leave_days.forEach(day => {
            if (day.leave_mode && day.leave_mode.title) {
                modes.add(day.leave_mode.title);
            }
        });

        if (modes.size === 0) return '';
        if (modes.size === 1) return Array.from(modes)[0];

        return 'Mixed Mode';
    }

    function mapApiDataToEvents(data) {
        const events = [];

        const sickLeaves = data.sickLeaves || [];
        const annualLeaves = data.annualLeaves || [];
        const lieuLeaveRequests = data.lieuLeaveRequests || [];
        const workFromHomeRequests = data.workFromHomeRequests || [];
        const travelRequests = data.travelRequests || [];
        const offDayWorks = data.offDayWorks || [];
        const otherLeaves = data.otherLeaves || [];
        const holidays = data.holidays || [];

        // sick leave
        sickLeaves.forEach(sl => {
            const requester = sl.requester || {};
            const desc = sl.remarks || 'No description.';
            const mode = getLeaveModeLabel(sl);
            events.push({
                title: `${requester.full_name ?? requester.name ?? 'Employee'} is on Sick Leave`,
                start: normalizeDate(sl.start_date),
                end: addOneDay(normalizeDate(sl.end_date)),
                type: 'sick_leave',
                allDay: true,
                backgroundColor: 'var(--c-leave)',
                /* description: desc, */
                priority: getPriority('sick_leave'),
                extendedProps: {
                    description: desc,
                    leaveMode: mode
                }
            });
        });

        // annual leave
        annualLeaves.forEach(al => {
            const requester = al.requester || {};
            const desc = al.remarks || 'No description.';
            const mode = getLeaveModeLabel(al);
            events.push({
                title: `${requester.full_name ?? requester.name ?? 'Employee'} is on Annual Leave`,
                start: normalizeDate(al.start_date),
                end: addOneDay(normalizeDate(al.end_date)),
                type: 'annual_leave',
                allDay: true,
                backgroundColor: 'var(--c-annual-leave)',
                /* description: desc, */
                priority: getPriority('annual_leave'),
                extendedProps: {
                    description: desc,
                    leaveMode: mode
                }
            });
        });

        // lieu leave
        lieuLeaveRequests.forEach(llr => {
            const requester = llr.requester || {};
            const desc = llr.remarks || 'No description.';
            events.push({
                title: `${requester.full_name ?? requester.name ?? 'Employee'} is on Lieu Leave`,
                start: normalizeDate(llr.start_date),
                end: addOneDay(normalizeDate(llr.end_date)),
                type: 'lieu_leave',
                allDay: true,
                backgroundColor: 'var(--c-lieu-leave)',
                /* description: desc, */
                priority: getPriority('lieu_leave'),
                extendedProps: {
                    /* description: desc */
                }
            });
        });

        // work from home
        workFromHomeRequests.forEach(wfh => {
            const requester = wfh.requester || {};
            const desc = wfh.remarks || 'No description.';
            events.push({
                title: `${requester.full_name ?? requester.name ?? 'Employee'} is Working from Home`,
                start: normalizeDate(wfh.start_date),
                end: addOneDay(normalizeDate(wfh.end_date)),
                type: 'work_from_home',
                allDay: true,
                backgroundColor: 'var(--c-work-from-home)',
                /* description: desc, */
                priority: getPriority('work_from_home'),
                extendedProps: {
                    /* description: desc */
                }
            });
        });

        // travel
        travelRequests.forEach(tr => {
            const requester = tr.requester || {};
            const desc = tr.remarks || 'No description.';
            events.push({
                title: `${requester.full_name ?? requester.name ?? 'Employee'} is on Travel`,
                start: normalizeDate(tr.departure_date),
                end: addOneDay(normalizeDate(tr.return_date)),
                type: 'travel',
                allDay: true,
                backgroundColor: 'var(--c-travel)',
                /* description: desc, */
                priority: getPriority('travel'),
                extendedProps: {
                    /* description: desc */
                }
            });
        });

        // off-day work
        offDayWorks.forEach(odw => {
            const requester = odw.requester || {};
            const date = normalizeDate(odw.date);
            const desc = odw.description || 'No description.';
            events.push({
                title: `${requester.full_name ?? requester.name ?? 'Employee'} is working on an Off-day`,
                start: date,
                end: date,
                type: 'work',
                allDay: true,
                backgroundColor: 'var(--c-work)',
                /* description: desc, */
                priority: getPriority('work'),
                extendedProps: {
                    /* description: desc */
                }
            });
        });

        // other leaves
        otherLeaves.forEach(ol => {
            const requester = ol.requester || {};
            const desc = ol.remarks || 'No description.';
            const mode = getLeaveModeLabel(ol);
            events.push({
                title: `${requester.full_name ?? requester.name ?? 'Employee'} is on Leave`,
                start: normalizeDate(ol.start_date),
                end: addOneDay(normalizeDate(ol.end_date)),
                type: 'leave',
                allDay: true,
                backgroundColor: 'var(--c-leave)',
                description: desc,
                priority: getPriority('leave'),
                extendedProps: {
                    description: desc,
                    leaveMode: mode
                }
            });
        });

        // holidays with female-only note
        holidays.forEach(h => {
            const holidayDate = normalizeDate(h.holiday_date);
            setHolidayBackground(holidayDate);

            const onlyFemale = h.only_female === 1 || h.only_female === '1';
            const baseTitle = h.title;
            const baseDesc = h.description || '';
            const desc = onlyFemale ?
                `${baseDesc} (only for female)` :
                baseDesc;



            events.push({
                title: `${baseTitle}`,
                start: holidayDate,
                end: addOneDay(holidayDate),
                type: 'holiday',
                allDay: true,
                backgroundColor: 'var(--bg-holiday-bar)',
                description: desc,
                priority: getPriority('holiday'),
                extendedProps: {
                    only_female: h.only_female,
                    type: 'holiday',
                    description: desc
                }
            });
        });

        return events;
    } // extendedProps.type and only_female are used to build the holiday subtitle. 

    async function fetchCalendarDataForRange(start, end) {
        const month = start.getMonth() + 1;
        const year = start.getFullYear();
        const officeId = "{{ $office->id }}";
        const url = buildCalendarUrl(officeId, month, year);

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                Accept: 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }

        return await response.json();
    }

    function openDayOverviewModal(date, segs) {
        $('#dayOverviewDate').text(date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }));

        // holiday subtitle (if any holiday on this day)
        const holidaySegs = segs.filter(seg => {
            const e = seg.event;
            return e.extendedProps.type === 'holiday' || e.type === 'holiday';
        });

        let subtitle = '';
        if (holidaySegs.length > 0) {
            const firstHoliday = holidaySegs[0].event;
            const baseTitle = firstHoliday.title;
            const onlyFemale = firstHoliday.extendedProps.only_female;
            if (onlyFemale === 1 || onlyFemale === '1') {
                subtitle = `${baseTitle} (only for female)`;
            } else {
                subtitle = baseTitle;
            }
        }
        $('#dayOverviewSubtitle').text(subtitle);

        $('#dayEventsList').empty();

        segs.forEach(seg => {
            const event = seg.event;

            let dateHtml = ``;
            if (event.end && (event.end.getTime() !== event.start.getTime() + 86400000)) {
                const lastDay = subtractDay(event.end);
                dateHtml =
                    `<span class="badge bg-light text-dark border">${fmt(event.start)} ➔ ${fmt(lastDay)}</span>`;
            }

            const $el = $(`
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="list-group-item event-list-item h-100 p-3"
                         style="border-left-color:${event.backgroundColor}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">${event.title}</h6>
                                ${dateHtml}
                                ${event.extendedProps.leaveMode ? `<small class="text-muted d-block mt-1">${event.extendedProps.leaveMode}</small>` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `);

            $('#dayEventsList').append($el);
        });

        new bootstrap.Modal(document.getElementById('dayOverviewModal')).show();
    }

    function openSingleEventModal(event) {
        $('#singleEventTitle').text(event.title);
        $('#singleEventHeader').css('background-color', event.backgroundColor);

        let range = fmt(event.start);
        if (event.end) {
            const lastDay = subtractDay(event.end);
            range += ` - ${fmt(lastDay)}`;
        }
        $('#singleEventDateRange').text(range);

        const desc = event.extendedProps.description || event.description || 'No description.';
        $('#singleEventDescription').text(desc);

        const leaveMode = event.extendedProps.leaveMode;
        if (leaveMode && leaveMode.trim() !== '') {
            $('#singleEventLeaveMode').text(leaveMode);
            $('#singleEventLeaveModeContainer').removeClass('d-none');
        } else {
            $('#singleEventLeaveMode').text('');
            $('#singleEventLeaveModeContainer').addClass('d-none');
        }

        new bootstrap.Modal(document.getElementById('singleEventModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            initialView: 'dayGridMonth',
            aspectRatio: 2.5,
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: ''
            },
            contentHeight: 700,
            dayMaxEvents: 3,
            eventOrder: 'priority',
            eventOrderStrict: true,

            datesSet: async function(info) {
                try {
                    const currentStart = info.view.currentStart;
                    const month = currentStart.getMonth() + 1;
                    const year = currentStart.getFullYear();

                    const monthStart = new Date(year, month - 1, 1);
                    const monthEnd = new Date(year, month, 0);

                    const data = await fetchCalendarDataForRange(monthStart, monthEnd);
                    const events = mapApiDataToEvents(data);

                    calendar.removeAllEvents();
                    calendar.addEventSource(events);
                } catch (e) {
                    console.error('Failed to load calendar data', e);
                }
            },

            moreLinkClick: function(info) {
                openDayOverviewModal(info.date, info.allSegs);
                return 'function';
            },

            eventClick: function(info) {
                openSingleEventModal(info.event);
            }
        });

        calendar.render();
    });
</script>
