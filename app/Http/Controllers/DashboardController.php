<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Repositories\NotificationRepository;
use Carbon\Carbon;
use Modules\Announcement\Repositories\AnnouncementRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\EventCompletion\Repositories\EventCompletionRepository;
use Modules\ExitStaffClearance\Repositories\StaffClearanceRepository;
use Modules\LeaveRequest\Repositories\LeaveRequestRepository;
use Modules\LieuLeave\Repositories\LieuLeaveRequestRepository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\MeetingHallBooking\Repositories\MeetingHallBookingRepository;
use Modules\PerformanceReview\Repositories\PerformanceReviewRepository;
use Modules\Project\Repositories\WorkPlanRepository;
use Modules\PurchaseOrder\Repositories\PurchaseOrderRepository;
use Modules\PurchaseRequest\Models\PurchaseRequest;
use Modules\PurchaseRequest\Repositories\PurchaseRequestRepository;
use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\VehicleRequest\Repositories\VehicleRequestRepository;
use Modules\WorkFromHome\Repositories\WorkFromHomeRepository;
use Rats\Zkteco\Lib\ZKTeco;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Helper $helper
     * @param OfficeRepository $offices
     * @param NotificationRepository $notifications
     * @param FiscalYearRepository $fiscalYears
     * @param TravelRequestRepository $travelRequests
     * @param VehicleRequestRepository $vehicleRequests
     * @param LeaveRequestRepository $leaveRequests
     * @param PurchaseOrderRepository $purchaseOrders
     * @param MeetingHallBookingRepository $meetingHallBookings
     * @param AnnouncementRepository $announcements
     * @param PurchaseRequestRepository $purchaseRequests
     * @param LocalTravelRepository $localTravels
     */
    public function __construct(
        protected Helper $helper,
        protected OfficeRepository $offices,
        protected NotificationRepository $notifications,
        protected FiscalYearRepository $fiscalYears,
        protected TravelRequestRepository $travelRequests,
        protected VehicleRequestRepository $vehicleRequests,
        protected LeaveRequestRepository $leaveRequests,
        protected PurchaseOrderRepository $purchaseOrders,
        protected MeetingHallBookingRepository $meetingHallBookings,
        protected AnnouncementRepository $announcements,
        protected PurchaseRequestRepository $purchaseRequests,
        protected LocalTravelRepository $localTravels,
        protected EventCompletionRepository $eventCompletion,
        protected PerformanceReviewRepository $performanceReviews,
        protected StaffClearanceRepository $staffClearances,
        protected WorkFromHomeRepository $workFromHomes,
        protected LieuLeaveRequestRepository $lieuLeaveRequests,
        protected EmployeeRepository $employees,
    ) {
    }

    /**
     * show dashboard page
     *
     * @return view
     */
    public function index()
    {

        $authUser = auth()->user();

        $currentWeekStart = Carbon::now()->startOfWeek(Carbon::SUNDAY);
        $currentWeekEnd = $currentWeekStart->copy()->addDays(6);
        $currentWeekWorkPlans = collect();

        if ($authUser->employee) {
            $currentWeekWorkPlans = app(WorkPlanRepository::class)
                ->getUserWorkPlanDetailsByWeek($currentWeekStart->toDateString(), $currentWeekEnd->toDateString(), $authUser->id)
                ->get();
        }
        
        $canSeeTeamEvents = $authUser->employee && ($authUser->employee->isSupervisor() || $authUser->can('view-upcoming-events'));

        $upcomingBirthdays = collect();
        $upcomingAnniversaries = collect();
        $days = 7;

        if ($canSeeTeamEvents) {
            $upcomingBirthdays = $this->employees->getUpcomingBirthdays($days);
            $upcomingAnniversaries = $this->employees->getUpcomingAnniversaries($days);
        }

        $lieuLeaveRequests = $this->lieuLeaveRequests->getLieuLeaveRequestsForApproval($authUser);
        $leaveRequests = $this->leaveRequests->getLeaveRequestsForApproval($authUser);

        $allLeaveRequests = $lieuLeaveRequests->concat($leaveRequests)->sortByDesc('request_date')->take(5);

        $upcomingLieuLeaves = $this->lieuLeaveRequests->getUpcomingLieuLeave();
        $upcomingLeaves = $this->leaveRequests->getUpcomingLeaves()->concat($upcomingLieuLeaves)->sortBy('start_date');

        $array = [
            'authUser' => $authUser,
            'travelRequests' => $this->travelRequests->getTravelRequestsForApproval($authUser),
            'vehicleRequests' => $this->vehicleRequests->getVehicleRequestsForApproval($authUser),
            'leaveRequests' => $this->leaveRequests->getLeaveRequestsForApproval($authUser),
            'allLeaveRequests' => $allLeaveRequests,
            'workFromHomeRequests' => $this->workFromHomes->getWorkFromHomeRequestsForApproval($authUser),
            'purchaseOrders' => $this->purchaseOrders->getPurchaseOrdersForReviewAndApproval($authUser),
            'purchaseRequests' => $this->purchaseRequests->getPurchaseRequestsForReviewAndApproval($authUser),
            'hallBookings' => $this->meetingHallBookings->getBookings(),
            'approvedLeaves' => $this->leaveRequests->getEmployeesOnLeave(),
            'upcomingLeaves' => $upcomingLeaves,
            'approvedLieuLeaves' => $this->lieuLeaveRequests->getEmployeesOnLieuLeave(),
            'upcomingLieuLeaves' => $upcomingLieuLeaves,
            'approvedWorkFromHomes' => $this->workFromHomes->getEmployeesOnWorkFromHome(),
            'upcomingWorkFromHomes' => $this->workFromHomes->getUpcomingWorkFromHomes(),
            'announcements' => $this->announcements->getActiveAnnouncements(),
            'approvedTravels' => $this->travelRequests->getEmployeesOnTravel(),
            'upcomingTravels' => $this->travelRequests->getUpcomingTravels(),
            'localTravelRequests' => $this->localTravels->getLocalTravelsForReviewAndApproval($authUser),
            'eventCompletionReports' => $this->eventCompletion->getECRForApproval($authUser),
            'pendingPerformanceReviews' => $this->performanceReviews->getPendingPerformanceReview($authUser),
            'pendingStaffClearances' => $this->staffClearances->getPendingClearances(),
            'currentWeekWorkPlans' => $currentWeekWorkPlans,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd,
            'canSeeTeamEvents' => $canSeeTeamEvents,
            'upcomingBirthdays' => $upcomingBirthdays,
            'upcomingAnniversaries' => $upcomingAnniversaries,
        ];


        return view('dashboard', $array);
    }

    public function attendanceDevice()
    {
        $zk = new ZKTeco('192.168.0.205', 4370);
        $flag = $zk->connect();
        $time = $zk->getTime();
        $users = $zk->getUser();
        $attendance = $zk->getAttendance();
    }
}
