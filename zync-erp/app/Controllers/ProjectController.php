<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Models\ProjectRepository;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProjectController extends \App\Core\Controller
{
    private ProjectRepository $repo;
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->repo = new ProjectRepository();
        $this->db = \App\Core\Database::pdo();
    }

    // ─── PROJEKT ÖVERSIKT ───
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $filters = [
            'status'     => $params['status'] ?? '',
            'category'   => $params['category'] ?? '',
            'manager_id' => $params['manager_id'] ?? '',
            'search'     => $params['search'] ?? '',
        ];
        $projects = $this->repo->all($filters);
        $stats = $this->repo->stats();
        $managers = $this->db->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

        return $this->render($response, 'projects/index', [
            'title' => 'Projekt', 'projects' => $projects, 'stats' => $stats,
            'filters' => $filters, 'managers' => $managers,
        ]);
    }

    // ─── ARKIV (avslutade) ───
    public function archive(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $projects = $this->repo->all(['status' => 'completed']);
        return $this->render($response, 'projects/archive', [
            'title' => 'Projektarkiv', 'projects' => $projects,
        ]);
    }

    // ─── SKAPA ───
    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $customers = $this->db->query("SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $departments = $this->db->query("SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $users = $this->db->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
        $nextNumber = $this->repo->nextProjectNumber();

        return $this->render($response, 'projects/create', [
            'title' => 'Skapa projekt', 'customers' => $customers,
            'departments' => $departments, 'users' => $users, 'nextNumber' => $nextNumber,
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['created_by'] = Auth::id();
        $id = $this->repo->create($data);
        $this->repo->addLog($id, Auth::id(), 'created', 'Projekt skapat');
        Flash::set('success', 'Projekt skapat!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── VISA ───
    public function show(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $project = $this->repo->find($id);
        if (!$project) { Flash::set('error', 'Projektet hittades inte.'); return $response->withHeader('Location', '/projects')->withStatus(302); }

        $phases     = $this->repo->phases($project['id']);
        $milestones = $this->repo->milestones($project['id']);
        $tasks      = $this->repo->tasks($project['id']);
        $timesheets = $this->repo->timesheets($project['id']);
        $budget     = $this->repo->budgetLines($project['id']);
        $risks      = $this->repo->risks($project['id']);
        $log        = $this->repo->log($project['id']);
        $members    = $this->repo->members($project['id']);
        $users      = $this->db->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

        // Budget summering
        $budgetSummary = ['budgeted' => 0, 'actual' => 0];
        foreach ($budget as $b) { $budgetSummary['budgeted'] += $b['budgeted_amount']; $budgetSummary['actual'] += $b['actual_amount']; }
        // Lägg till tidkostnad
        $timeCost = $project['actual_hours'] * $project['hourly_rate'];
        $budgetSummary['actual'] += $timeCost;
        $budgetSummary['time_cost'] = $timeCost;

        return $this->render($response, 'projects/show', [
            'title' => $project['project_number'] . ' — ' . $project['name'],
            'project' => $project, 'phases' => $phases, 'milestones' => $milestones,
            'tasks' => $tasks, 'timesheets' => $timesheets, 'budget' => $budget,
            'budgetSummary' => $budgetSummary, 'risks' => $risks, 'log' => $log,
            'members' => $members, 'users' => $users,
        ]);
    }

    // ─── REDIGERA ───
    public function edit(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $project = $this->repo->find($id);
        if (!$project) { Flash::set('error', 'Projektet hittades inte.'); return $response->withHeader('Location', '/projects')->withStatus(302); }

        $customers = $this->db->query("SELECT id, name FROM customers WHERE is_deleted = 0 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $departments = $this->db->query("SELECT id, name FROM departments WHERE is_deleted = 0 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $users = $this->db->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);

        return $this->render($response, 'projects/edit', [
            'title' => 'Redigera: ' . $project['name'], 'project' => $project,
            'customers' => $customers, 'departments' => $departments, 'users' => $users,
        ]);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->update($id, $data);
        $this->repo->addLog($id, Auth::id(), 'updated', 'Projekt uppdaterat');
        Flash::set('success', 'Projekt uppdaterat!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $this->repo->delete($id);
        Flash::set('success', 'Projekt borttaget.');
        return $response->withHeader('Location', '/projects')->withStatus(302);
    }

    // ─── AVSLUTA PROJEKT (med utvärdering) ───
    public function complete(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->complete($id, $data);
        $this->repo->addLog($id, Auth::id(), 'completed', 'Projekt avslutat. Betyg: ' . ($data['evaluation_score'] ?? '-'));
        Flash::set('success', 'Projekt avslutat!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── FASER ───
    public function storePhase(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->createPhase($id, $data);
        $this->repo->addLog($id, Auth::id(), 'phase_added', 'Fas tillagd: ' . $data['name']);
        Flash::set('success', 'Fas tillagd!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function updatePhaseStatus(ServerRequestInterface $request, ResponseInterface $response, int $id, int $phaseId): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->updatePhaseStatus($phaseId, $data['status']);
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function deletePhase(ServerRequestInterface $request, ResponseInterface $response, int $id, int $phaseId): ResponseInterface
    {
        $this->repo->deletePhase($phaseId);
        Flash::set('success', 'Fas borttagen.');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── MILSTOLPAR ───
    public function storeMilestone(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->createMilestone($id, $data);
        $this->repo->addLog($id, Auth::id(), 'milestone_added', 'Milstolpe tillagd: ' . $data['name']);
        Flash::set('success', 'Milstolpe tillagd!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function completeMilestone(ServerRequestInterface $request, ResponseInterface $response, int $id, int $milestoneId): ResponseInterface
    {
        $this->repo->completeMilestone($milestoneId);
        $this->repo->addLog($id, Auth::id(), 'milestone_completed', 'Milstolpe slutförd');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function deleteMilestone(ServerRequestInterface $request, ResponseInterface $response, int $id, int $milestoneId): ResponseInterface
    {
        $this->repo->deleteMilestone($milestoneId);
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── UPPGIFTER ───
    public function storeTask(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->createTask($id, $data);
        $this->repo->addLog($id, Auth::id(), 'task_added', 'Uppgift tillagd: ' . $data['title']);
        Flash::set('success', 'Uppgift tillagd!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function updateTaskStatus(ServerRequestInterface $request, ResponseInterface $response, int $id, int $taskId): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->updateTaskStatus($taskId, $data['status']);
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function deleteTask(ServerRequestInterface $request, ResponseInterface $response, int $id, int $taskId): ResponseInterface
    {
        $this->repo->deleteTask($taskId);
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── TIDRAPPORTER ───
    public function timesheets(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getQueryParams();
        $filters = [
            'user_id'  => $params['user_id'] ?? '',
            'from'     => $params['from'] ?? date('Y-m-01'),
            'to'       => $params['to'] ?? date('Y-m-t'),
            'approved' => $params['approved'] ?? '',
        ];
        $entries = $this->repo->allTimesheets($filters);
        $users = $this->db->query("SELECT id, full_name FROM users WHERE is_active = 1 ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
        $totalHours = array_sum(array_column($entries, 'hours'));

        return $this->render($response, 'projects/timesheets', [
            'title' => 'Tidrapporter', 'entries' => $entries, 'users' => $users,
            'filters' => $filters, 'totalHours' => $totalHours,
        ]);
    }

    public function storeTimesheet(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $data['project_id'] = $id;
        $data['user_id'] = Auth::id();
        $this->repo->createTimesheet($data);
        Flash::set('success', 'Tid registrerad!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function approveTimesheet(ServerRequestInterface $request, ResponseInterface $response, int $timesheetId): ResponseInterface
    {
        $this->repo->approveTimesheet($timesheetId, Auth::id());
        Flash::set('success', 'Tidrapport godkänd!');
        $referer = $request->getHeaderLine('Referer') ?: '/projects/timesheets';
        return $response->withHeader('Location', $referer)->withStatus(302);
    }

    // ─── BUDGET ───
    public function storeBudgetLine(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->createBudgetLine($id, $data);
        $this->repo->addLog($id, Auth::id(), 'budget_added', 'Budgetpost tillagd: ' . $data['description']);
        Flash::set('success', 'Budgetpost tillagd!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function deleteBudgetLine(ServerRequestInterface $request, ResponseInterface $response, int $id, int $budgetId): ResponseInterface
    {
        $this->repo->deleteBudgetLine($budgetId);
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── RISKER ───
    public function storeRisk(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->createRisk($id, $data);
        $this->repo->addLog($id, Auth::id(), 'risk_added', 'Risk identifierad: ' . $data['title']);
        Flash::set('success', 'Risk tillagd!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function updateRiskStatus(ServerRequestInterface $request, ResponseInterface $response, int $id, int $riskId): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->updateRiskStatus($riskId, $data['status']);
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── LOGG ───
    public function storeComment(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->addLog($id, Auth::id(), 'comment', $data['message']);
        Flash::set('success', 'Kommentar tillagd!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    // ─── MEDLEMMAR ───
    public function addMember(ServerRequestInterface $request, ResponseInterface $response, int $id): ResponseInterface
    {
        $data = (array) $request->getParsedBody();
        $this->repo->addMember($id, (int) $data['user_id'], $data['role'] ?? 'member');
        Flash::set('success', 'Medlem tillagd!');
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }

    public function removeMember(ServerRequestInterface $request, ResponseInterface $response, int $id, int $userId): ResponseInterface
    {
        $this->repo->removeMember($id, $userId);
        return $response->withHeader('Location', '/projects/' . $id)->withStatus(302);
    }
}
