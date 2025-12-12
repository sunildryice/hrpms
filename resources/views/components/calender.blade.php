@section('page_css')
    <style>
        :root {
            --c-leave: #dc3545;
            --c-bday: #0d6efd;
            --c-project: #6610f2;
            --c-work: #198754;
            --bg-weekend: #e9ecef;
            /* Distinct Gray for Holidays */
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        #calendar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            min-height: 850px;
        }

        /* =========================================
                                                                                                                                                                                                           WEEKEND / HOLIDAY STYLING (SAT & SUN)
                                                                                                                                                                                                           ========================================= */
        .fc-day-sat,
        .fc-day-sun {
            background-color: var(--bg-weekend) !important;
        }

        /* Make the date number on weekends look "muted" */
        .fc-day-sat .fc-daygrid-day-number,
        .fc-day-sun .fc-daygrid-day-number {
            color: #adb5bd;
            font-weight: bold;
        }

        /* Optional: Add a subtle diagonal stripe pattern for extra "Holiday" feel */
        .fc-day-sat .fc-daygrid-day-frame,
        .fc-day-sun .fc-daygrid-day-frame {
            background-image: repeating-linear-gradient(45deg,
                    transparent,
                    transparent 10px,
                    rgba(0, 0, 0, 0.02) 10px,
                    rgba(0, 0, 0, 0.02) 20px);
        }

        /* =========================================
                                                                                                                                                                                                           EVENT & LIST STYLING
                                                                                                                                                                                                           ========================================= */
        .fc-event {
            border: none;
            border-radius: 3px;
            font-size: 0.8rem;
            cursor: pointer;
            padding: 2px 4px;
            margin-bottom: 2px;
        }

        .fc-daygrid-more-link {
            font-weight: 700;
            color: #495057 !important;
            background: #dee2e6;
            padding: 2px 8px;
            border-radius: 12px;
            text-decoration: none;
        }

        /* Modal List Item */
        .event-list-item {
            cursor: pointer;
            border-left: 5px solid #ccc;
            transition: background 0.2s;
        }

        .event-list-item:hover {
            background-color: #f1f3f5;
        }

        .modal-body-scrollable {
            max-height: 60vh;
            overflow-y: auto;
        }
    </style>
@endsection

<div>


    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-11">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Monthly Schedule</h5>
                    </div>
                    <div class="d-none d-md-flex gap-3 text-small">
                        <span class="d-flex align-items-center gap-1"><i class="bi bi-circle-fill"
                                style="color:var(--c-leave)"></i> Leave</span>
                        <span class="d-flex align-items-center gap-1"><i class="bi bi-circle-fill"
                                style="color:var(--c-project)"></i> Project</span>
                        <span class="d-flex align-items-center gap-1"><i class="bi bi-circle-fill"
                                style="color:var(--c-work)"></i> Work</span>
                    </div>
                </div>

                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- DAY OVERVIEW MODAL (List View) -->
    <div class="modal fade" id="dayOverviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold">Events on <span id="dayOverviewDate"
                                class="text-primary"></span></h5>
                        <small class="text-muted">Detailed list view</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="p-3 border-bottom bg-white">
                    <input type="text" id="daySearch" class="form-control" placeholder="Search...">
                </div>
                <div class="modal-body modal-body-scrollable p-0">
                    <div class="list-group list-group-flush" id="dayEventsList"></div>
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
                    <span id="singleEventType" class="badge bg-secondary mb-3">Type</span>
                    <h6 class="fw-bold">Time / Duration</h6>
                    <p id="singleEventDateRange" class="text-muted small"></p>
                    <h6 class="fw-bold">Description</h6>
                    <p id="singleEventDesc" class="text-secondary">No description.</p>
                </div>
            </div>
        </div>
    </div>


</div>


<!-- Scripts -->

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.8/index.global.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        // --- DATA GENERATION ---
        function generateEvents() {
            let events = [];
            const today = new Date();
            const y = today.getFullYear();
            const m = String(today.getMonth() + 1).padStart(2, '0');

            let leaveRequests = @json($leaveRequests);
            let travelRequests = @json($travelRequests);
            let offDayWorks = @json($offDayWorks);



            leaveRequests.forEach(lr => {
                events.push({
                    title: `Leave: ${lr.requester.full_name}`,
                    start: lr.start_date,
                    end: lr.end_date,
                    type: 'leave',
                    allDay: true,
                    backgroundColor: 'var(--c-leave)',
                    description: lr.remarks || 'No description.'
                });
            });

            travelRequests.forEach(tr => {
                events.push({
                    title: `Travel: ${tr.requester.full_name}`,
                    start: tr.departure_date,
                    allDay: true,
                    end: tr.return_date,
                    type: 'travel',
                    backgroundColor: 'var(--c-project)',
                    description: tr.remarks || 'No description.'
                });
            });

            offDayWorks.forEach(odw => {
                events.push({
                    title: `Off-day Work: ${odw.requester.full_name}`,
                    start: odw.date,
                    end: odw.date,
                    type: 'work',
                    allDay: true,
                    backgroundColor: 'var(--c-work)',
                    description: odw.description || 'No description.'
                });
            });

            // 2. High Density Day (30 events)
            for (let i = 1; i <= 30; i++) {
                events.push({
                    title: `Audit Task ${i}`,
                    start: `${y}-${m}-15`,
                    type: 'work',
                    backgroundColor: 'var(--c-work)',
                    description: 'Routine check.'
                });
            }

            // // 3. Weekend Work (To show event on gray background)
            // events.push({
            //     title: 'Overtime Maintenance',
            //     start: `${y}-${m}-16`, // Likely a weekend depending on month
            //     type: 'work',
            //     backgroundColor: '#fd7e14', // Orange
            //     description: 'Emergency server patching.'
            // });

            return events;
        }

        // --- CALENDAR INIT ---
        var calendar = new FullCalendar.Calendar(calendarEl, {
            themeSystem: 'bootstrap5',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            dayMaxEvents: 3,

            // Events
            events: generateEvents(),

            // Custom Interaction
            moreLinkClick: function(info) {
                openDayOverviewModal(info.date, info.allSegs);
                return "function";
            },
            eventClick: function(info) {
                openSingleEventModal(info.event);
            }
        });

        calendar.render();

        // --- MODAL LOGIC (Simplified for brevity) ---
        function subtractDay(d) {
            let n = new Date(d);
            n.setDate(n.getDate() - 1);
            return n;
        }

        function fmt(d) {
            return d.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            });
        }

        function openDayOverviewModal(date, segs) {
            $('#dayOverviewDate').text(date.toLocaleDateString());
            $('#dayEventsList').empty();

            segs.forEach(seg => {
                let event = seg.event;
                // Date Range Logic
                let dateHtml =
                    `<span class="badge bg-light text-dark border">Single Day</span>`;
                if (event.end && (event.end.getTime() !== event.start.getTime() +
                        86400000)) {
                    dateHtml =
                        `<span class="badge bg-light text-dark border">${fmt(event.start)} ➔ ${fmt(subtractDay(event.end))}</span>`;
                }

                let $el = $(`
                    <div class="list-group-item event-list-item p-3" style="border-left-color:${event.backgroundColor}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold">${event.title}</h6>
                                ${dateHtml}
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </div>
                `);
                $el.on('click', () => openSingleEventModal(event));
                $('#dayEventsList').append($el);
            });
            new bootstrap.Modal(document.getElementById('dayOverviewModal')).show();
        }

        function openSingleEventModal(event) {
            $('#singleEventTitle').text(event.title);
            $('#singleEventHeader').css('background-color', event.backgroundColor);
            $('#singleEventType').text(event.extendedProps.type || 'Event');
            $('#singleEventDesc').text(event.extendedProps.description || 'No description.');

            let range = fmt(event.start);
            if (event.end) range += ` - ${fmt(subtractDay(event.end))}`;
            $('#singleEventDateRange').text(range);

            new bootstrap.Modal(document.getElementById('singleEventModal')).show();
        }

        // Search
        $('#daySearch').on('keyup', function() {
            var v = $(this).val().toLowerCase();
            $("#dayEventsList .list-group-item").toggle(function() {
                return $(this).text().toLowerCase().indexOf(v) > -1;
            });
        });
    });
</script>
