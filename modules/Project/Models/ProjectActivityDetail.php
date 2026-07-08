<?php

namespace Modules\Project\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectActivityDetail extends Model
{
    use HasFactory;

    protected $table = 'project_activity_details';

    protected $fillable = [
        'project_activity_id',
        'key_accomplishment',
        'challenge',
        'lesson_learned',
        'created_by',
        'updated_by',
    ];

    public function scopeFilterByRequest($query, array $filters)
    {
        return $query
            ->when($filters['project_id'] ?? null, fn($q, $v) => $q->whereHas('projectActivity', fn($q) => $q->where('project_id', $v)))
            ->when($filters['activity_id'] ?? null, fn($q, $v) => $q->where('project_activity_id', $v))
            ->when(
                isset($filters['from_date']) || isset($filters['to_date']),
                fn($q) => $q->whereHas('projectActivity', function ($q) use ($filters) {
                    if (!empty($filters['to_date'])) {
                        $q->whereDate('start_date', '<=', $filters['to_date']);
                    }
                    if (!empty($filters['from_date'])) {
                        $q->whereDate('completion_date', '>=', $filters['from_date']);
                    }
                })
            );
    }

    public function projectActivity()
    {
        return $this->belongsTo(ProjectActivity::class, 'project_activity_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
