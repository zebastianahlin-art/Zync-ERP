<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Flash;
use App\Models\PayrollRepository;
use App\Models\LeaveRepository;
use App\Models\RecruitmentRepository;
use App\Models\TrainingRepository;
use App\Models\EmployeeRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HrController extends Controller
{
    private PayrollRepository $payroll;
    private LeaveRepository $leave;
    private RecruitmentRepository $recruitment;
    private TrainingRepository $training;
    private EmployeeRepository $employees;

    public function __construct()
    {
        parent::__construct();
        $this->payroll     = new PayrollRepository();
        $this->leave       = new LeaveRepository();
        $this->recruitment = new RecruitmentRepository();
        $this->training    = new TrainingRepository();
        $this->employees   = new EmployeeRepository();
    }

    // ══════════════════════════════════════════════
    //  DASHBOARD
    // ══════════════════════════════════════════════

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/index', [
            'title'            => 'HR – ZYNC ERP',
            'payrollStats'     => $this->payroll->stats(),
            'leaveStats'       => $this->leave->stats(),
            'recruitmentStats' => $this->recruitment->stats(),
            'trainingStats'    => $this->training->stats(),
        ]);
    }

    // ══════════════════════════════════════════════
    //  LÖNEHANTERING (Payroll)
    // ══════════════════════════════════════════════

    public function payrollIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $year = isset($request->getQueryParams()['year']) ? (int) $request->getQueryParams()['year'] : null;
        return $this->render($response, 'hr/payroll/index', [
            'title'   => 'Lönehantering – ZYNC ERP',
            'periods' => $this->payroll->allPeriods($year),
            'years'   => $this->payroll->availableYears(),
            'filter'  => ['year' => $year],
            'stats'   => $this->payroll->stats(),
        ]);
    }

    public function payrollCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/payroll/form', [
            'title'  => 'Ny löneperiod – ZYNC ERP',
            'period' => ['year' => (int) date('Y'), 'month' => (int) date('m')],
            'errors' => [],
            'isNew'  => true,
        ]);
    }

    public function payrollStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $year  = (int) ($data['year'] ?? date('Y'));
        $month = (int) ($data['month'] ?? date('m'));

        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate   = date('Y-m-t', strtotime($startDate));

        $periodId = $this->payroll->createPeriod([
            'year'       => $year,
            'month'      => $month,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $count = $this->payroll->generateRecords($periodId);
        Flash::set('success', "Löneperiod skapad med $count anställda.");
        return $this->redirect($response, '/hr/payroll/' . $periodId);
    }

    public function payrollShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $period = $this->payroll->findPeriod((int) $args['id']);
        if (!$period) {
            Flash::set('error', 'Löneperiod hittades inte.');
            return $this->redirect($response, '/hr/payroll');
        }

        return $this->render($response, 'hr/payroll/show', [
            'title'   => 'Löneperiod – ZYNC ERP',
            'period'  => $period,
            'records' => $this->payroll->recordsForPeriod((int) $args['id']),
        ]);
    }

    public function payrollRecordEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $record = $this->payroll->findRecord((int) $args['recordId']);
        if (!$record) {
            Flash::set('error', 'Lönepost hittades inte.');
            return $this->redirect($response, '/hr/payroll');
        }

        return $this->render($response, 'hr/payroll/record_form', [
            'title'  => 'Redigera lönepost – ZYNC ERP',
            'record' => $record,
            'period' => $this->payroll->findPeriod((int) $record['period_id']),
            'errors' => [],
        ]);
    }

    public function payrollRecordUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $record = $this->payroll->findRecord((int) $args['recordId']);
        if (!$record) {
            Flash::set('error', 'Lönepost hittades inte.');
            return $this->redirect($response, '/hr/payroll');
        }

        $data = (array) $request->getParsedBody();
        $this->payroll->updateRecord((int) $args['recordId'], [
            'base_salary'     => (float) ($data['base_salary'] ?? 0),
            'overtime_hours'  => (float) ($data['overtime_hours'] ?? 0),
            'overtime_amount' => (float) ($data['overtime_amount'] ?? 0),
            'bonus'           => (float) ($data['bonus'] ?? 0),
            'deductions'      => (float) ($data['deductions'] ?? 0),
            'tax'             => (float) ($data['tax'] ?? 0),
            'net_salary'      => (float) ($data['net_salary'] ?? 0),
            'notes'           => $data['notes'] ?? null,
        ]);

        Flash::set('success', 'Lönepost uppdaterad.');
        return $this->redirect($response, '/hr/payroll/' . $record['period_id']);
    }

    public function payrollApprove(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->payroll->updatePeriodStatus((int) $args['id'], 'approved', Auth::id());
        Flash::set('success', 'Löneperiod godkänd.');
        return $this->redirect($response, '/hr/payroll/' . $args['id']);
    }

    public function payrollMarkPaid(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->payroll->updatePeriodStatus((int) $args['id'], 'paid');
        Flash::set('success', 'Löneperiod markerad som utbetald.');
        return $this->redirect($response, '/hr/payroll/' . $args['id']);
    }

    public function payrollDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->payroll->deletePeriod((int) $args['id']);
        Flash::set('success', 'Löneperiod borttagen.');
        return $this->redirect($response, '/hr/payroll');
    }

    // ══════════════════════════════════════════════
    //  FRÅNVARO (Leave)
    // ══════════════════════════════════════════════

    public function leaveIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $status = $params['status'] ?? null;
        return $this->render($response, 'hr/leave/index', [
            'title'    => 'Frånvaro – ZYNC ERP',
            'requests' => $this->leave->allRequests($status),
            'stats'    => $this->leave->stats(),
            'filter'   => ['status' => $status],
        ]);
    }

    public function leaveCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/leave/form', [
            'title'     => 'Ny frånvaroansökan – ZYNC ERP',
            'leaveReq'  => [],
            'types'     => $this->leave->allTypes(),
            'employees' => $this->employees->all(),
            'errors'    => [],
            'isNew'     => true,
        ]);
    }

    public function leaveStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $errors = [];

        if (empty($data['employee_id'])) $errors['employee_id'] = 'Välj anställd.';
        if (empty($data['leave_type_id'])) $errors['leave_type_id'] = 'Välj typ.';
        if (empty($data['start_date'])) $errors['start_date'] = 'Startdatum krävs.';
        if (empty($data['end_date'])) $errors['end_date'] = 'Slutdatum krävs.';

        if (empty($errors) && $data['start_date'] > $data['end_date']) {
            $errors['end_date'] = 'Slutdatum måste vara efter startdatum.';
        }

        if (!empty($errors)) {
            return $this->render($response, 'hr/leave/form', [
                'title'     => 'Ny frånvaroansökan – ZYNC ERP',
                'leaveReq'  => $data,
                'types'     => $this->leave->allTypes(),
                'employees' => $this->employees->all(),
                'errors'    => $errors,
                'isNew'     => true,
            ]);
        }

        // Calculate business days
        $days = $this->calcDays($data['start_date'], $data['end_date']);

        $this->leave->createRequest([
            'employee_id'   => (int) $data['employee_id'],
            'leave_type_id' => (int) $data['leave_type_id'],
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'days'          => $days,
            'reason'        => $data['reason'] ?? null,
        ]);

        Flash::set('success', 'Frånvaroansökan skapad.');
        return $this->redirect($response, '/hr/leave');
    }

    public function leaveApprove(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->leave->approveRequest((int) $args['id'], Auth::id());
        Flash::set('success', 'Ansökan godkänd.');
        return $this->redirect($response, '/hr/leave');
    }

    public function leaveReject(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->leave->rejectRequest((int) $args['id'], Auth::id(), $data['rejection_reason'] ?? '');
        Flash::set('success', 'Ansökan avslagen.');
        return $this->redirect($response, '/hr/leave');
    }

    public function leaveDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->leave->deleteRequest((int) $args['id']);
        Flash::set('success', 'Ansökan borttagen.');
        return $this->redirect($response, '/hr/leave');
    }

    // ══════════════════════════════════════════════
    //  NÄRVARO (Attendance)
    // ══════════════════════════════════════════════

    public function attendanceIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $date = $request->getQueryParams()['date'] ?? date('Y-m-d');
        return $this->render($response, 'hr/attendance/index', [
            'title'      => 'Närvaro – ZYNC ERP',
            'date'       => $date,
            'records'    => $this->leave->attendanceForDate($date),
            'employees'  => $this->employees->all(),
        ]);
    }

    public function attendanceStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $date = $data['date'] ?? date('Y-m-d');

        if (!empty($data['attendance']) && is_array($data['attendance'])) {
            foreach ($data['attendance'] as $empId => $row) {
                $this->leave->upsertAttendance([
                    'employee_id' => (int) $empId,
                    'date'        => $date,
                    'check_in'    => $row['check_in'] ?? null,
                    'check_out'   => $row['check_out'] ?? null,
                    'status'      => $row['status'] ?? 'present',
                    'notes'       => $row['notes'] ?? null,
                ]);
            }
        }

        Flash::set('success', 'Närvaro sparad.');
        return $this->redirect($response, '/hr/attendance?date=' . $date);
    }

    // ══════════════════════════════════════════════
    //  REKRYTERING (Recruitment)
    // ══════════════════════════════════════════════

    public function recruitmentIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $status = $request->getQueryParams()['status'] ?? null;
        return $this->render($response, 'hr/recruitment/index', [
            'title'     => 'Rekrytering – ZYNC ERP',
            'positions' => $this->recruitment->allPositions($status),
            'stats'     => $this->recruitment->stats(),
            'filter'    => ['status' => $status],
        ]);
    }

    public function recruitmentCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/recruitment/form', [
            'title'       => 'Ny tjänst – ZYNC ERP',
            'position'    => [],
            'departments' => $this->employees->allDepartments(),
            'errors'      => [],
            'isNew'       => true,
        ]);
    }

    public function recruitmentStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $errors = [];
        if (empty(trim($data['title'] ?? ''))) $errors['title'] = 'Titel krävs.';

        if (!empty($errors)) {
            return $this->render($response, 'hr/recruitment/form', [
                'title'       => 'Ny tjänst – ZYNC ERP',
                'position'    => $data,
                'departments' => $this->employees->allDepartments(),
                'errors'      => $errors,
                'isNew'       => true,
            ]);
        }

        $data['created_by'] = Auth::id();
        $id = $this->recruitment->createPosition($data);
        Flash::set('success', 'Tjänst skapad.');
        return $this->redirect($response, '/hr/recruitment/' . $id);
    }

    public function recruitmentShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $position = $this->recruitment->findPosition((int) $args['id']);
        if (!$position) {
            Flash::set('error', 'Tjänst hittades inte.');
            return $this->redirect($response, '/hr/recruitment');
        }

        return $this->render($response, 'hr/recruitment/show', [
            'title'      => $position['title'] . ' – ZYNC ERP',
            'position'   => $position,
            'candidates' => $this->recruitment->candidatesForPosition((int) $args['id']),
        ]);
    }

    public function recruitmentEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $position = $this->recruitment->findPosition((int) $args['id']);
        if (!$position) {
            Flash::set('error', 'Tjänst hittades inte.');
            return $this->redirect($response, '/hr/recruitment');
        }

        return $this->render($response, 'hr/recruitment/form', [
            'title'       => 'Redigera tjänst – ZYNC ERP',
            'position'    => $position,
            'departments' => $this->employees->allDepartments(),
            'errors'      => [],
            'isNew'       => false,
        ]);
    }

    public function recruitmentUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->recruitment->updatePosition((int) $args['id'], $data);
        Flash::set('success', 'Tjänst uppdaterad.');
        return $this->redirect($response, '/hr/recruitment/' . $args['id']);
    }

    public function recruitmentDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->recruitment->deletePosition((int) $args['id']);
        Flash::set('success', 'Tjänst borttagen.');
        return $this->redirect($response, '/hr/recruitment');
    }

    // ── Candidates ───────────────────────────────

    public function candidateStore(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['position_id'] = (int) $args['id'];
        $this->recruitment->createCandidate($data);
        Flash::set('success', 'Kandidat tillagd.');
        return $this->redirect($response, '/hr/recruitment/' . $args['id']);
    }

    public function candidateUpdateStatus(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->recruitment->updateCandidateStatus((int) $args['candidateId'], $data['status'] ?? 'new');
        $candidate = $this->recruitment->findCandidate((int) $args['candidateId']);
        Flash::set('success', 'Kandidatstatus uppdaterad.');
        return $this->redirect($response, '/hr/recruitment/' . ($candidate['position_id'] ?? ''));
    }

    // ══════════════════════════════════════════════
    //  UTBILDNING (Training)
    // ══════════════════════════════════════════════

    public function trainingIndex(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/index', [
            'title'   => 'Utbildning – ZYNC ERP',
            'courses' => $this->training->allCourses(),
            'stats'   => $this->training->stats(),
            'overdue' => $this->training->overdueTraining(),
        ]);
    }

    public function trainingCreate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->render($response, 'hr/training/form', [
            'title'  => 'Ny utbildning – ZYNC ERP',
            'course' => [],
            'errors' => [],
            'isNew'  => true,
        ]);
    }

    public function trainingStore(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $errors = [];
        if (empty(trim($data['name'] ?? ''))) $errors['name'] = 'Namn krävs.';

        if (!empty($errors)) {
            return $this->render($response, 'hr/training/form', [
                'title'  => 'Ny utbildning – ZYNC ERP',
                'course' => $data,
                'errors' => $errors,
                'isNew'  => true,
            ]);
        }

        $id = $this->training->createCourse($data);
        Flash::set('success', 'Utbildning skapad.');
        return $this->redirect($response, '/hr/training/' . $id);
    }

    public function trainingShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $course = $this->training->findCourse((int) $args['id']);
        if (!$course) {
            Flash::set('error', 'Utbildning hittades inte.');
            return $this->redirect($response, '/hr/training');
        }

        return $this->render($response, 'hr/training/show', [
            'title'    => $course['name'] . ' – ZYNC ERP',
            'course'   => $course,
            'sessions' => $this->training->sessionsForCourse((int) $args['id']),
        ]);
    }

    public function trainingEdit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $course = $this->training->findCourse((int) $args['id']);
        if (!$course) {
            Flash::set('error', 'Utbildning hittades inte.');
            return $this->redirect($response, '/hr/training');
        }

        return $this->render($response, 'hr/training/form', [
            'title'  => 'Redigera utbildning – ZYNC ERP',
            'course' => $course,
            'errors' => [],
            'isNew'  => false,
        ]);
    }

    public function trainingUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->training->updateCourse((int) $args['id'], $data);
        Flash::set('success', 'Utbildning uppdaterad.');
        return $this->redirect($response, '/hr/training/' . $args['id']);
    }

    public function trainingDelete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->training->deleteCourse((int) $args['id']);
        Flash::set('success', 'Utbildning borttagen.');
        return $this->redirect($response, '/hr/training');
    }

    // ── Sessions ─────────────────────────────────

    public function sessionStore(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['course_id'] = (int) $args['id'];
        $this->training->createSession($data);
        Flash::set('success', 'Tillfälle skapat.');
        return $this->redirect($response, '/hr/training/' . $args['id']);
    }

    public function sessionShow(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $session = $this->training->findSession((int) $args['sessionId']);
        if (!$session) {
            Flash::set('error', 'Tillfälle hittades inte.');
            return $this->redirect($response, '/hr/training');
        }

        return $this->render($response, 'hr/training/session', [
            'title'        => $session['course_name'] . ' – Tillfälle – ZYNC ERP',
            'session'      => $session,
            'participants' => $this->training->participantsForSession((int) $args['sessionId']),
            'employees'    => $this->employees->all(),
        ]);
    }

    public function participantAdd(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->training->addParticipant((int) $args['sessionId'], (int) ($data['employee_id'] ?? 0));
        Flash::set('success', 'Deltagare tillagd.');
        return $this->redirect($response, '/hr/training/' . $args['id'] . '/sessions/' . $args['sessionId']);
    }

    public function participantUpdate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->training->updateParticipant((int) $args['participantId'], $data);
        Flash::set('success', 'Deltagare uppdaterad.');
        return $this->redirect($response, '/hr/training/' . $args['id'] . '/sessions/' . $args['sessionId']);
    }

    // ══════════════════════════════════════════════
    //  HELPERS
    // ══════════════════════════════════════════════

    private function calcDays(string $start, string $end): float
    {
        $s = new \DateTime($start);
        $e = new \DateTime($end);
        $days = 0;
        while ($s <= $e) {
            if ($s->format('N') < 6) $days++;
            $s->modify('+1 day');
        }
        return (float) $days;
    }
}
