<div>
    <a href="{{ route('project.dashboard', $project->id) }}"
        class="btn 
              {{ request()->routeIs('project.dashboard') ? 'btn-primary' : 'btn-outline-primary' }}
              btn-sm">
        Dashboard
    </a>

    <a href="{{ route('project.show', [$project->id, 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}"
        class="btn {{ request()->routeIs('project.show') ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
        Activities
    </a>

    <a href="{{ route('project.gantt.index', ['id' => $project->id]) }}"
        class="btn 
              {{ request()->routeIs('project.gantt.index') ? 'btn-primary' : 'btn-outline-primary' }} 
              btn-sm">
        Gantt Chart
    </a>
</div>
