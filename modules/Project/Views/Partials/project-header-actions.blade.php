 <div>
     <a href="{{ route('project.dashboard', $project->id) }}" class="btn btn-primary btn-sm">
         Dashboard
     </a>
     <a href="{{ route('project.show', $project->id) }}" class="btn btn-primary btn-sm">
         Activities
     </a>
     <a href="{{ route('project.gantt.index', ['id' => $project->id]) }}" class="btn btn-primary btn-sm">Gantt
         Chart</a>
 </div>
