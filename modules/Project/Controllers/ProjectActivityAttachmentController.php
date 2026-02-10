<?php

namespace Modules\Project\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Project\Models\ProjectActivityAttachment;

class ProjectActivityAttachmentController extends Controller
{
    public function stream(ProjectActivityAttachment $attachment)
    {
        $this->ensureAttachmentContext($attachment);

        if (!Storage::exists($attachment->file_path)) {
            abort(404);
        }

        $fileName = $this->buildDownloadFileName($attachment);

        return Storage::response($attachment->file_path, $fileName, [
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    public function download(ProjectActivityAttachment $attachment)
    {
        $this->ensureAttachmentContext($attachment);

        if (!Storage::exists($attachment->file_path)) {
            abort(404);
        }

        return Storage::download($attachment->file_path, $this->buildDownloadFileName($attachment));
    }

    public function destroy(ProjectActivityAttachment $attachment)
    {
        $this->authorizeAttachmentDeletion($attachment);

        if (Storage::exists($attachment->file_path)) {
            Storage::delete($attachment->file_path);
        }

        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted successfully.',
        ]);
    }

    protected function ensureAttachmentContext(ProjectActivityAttachment $attachment): void
    {
        if (!$attachment->relationLoaded('activity')) {
            $attachment->load('activity.project');
        }

        if (!$attachment->activity || !$attachment->activity->project) {
            abort(404);
        }
    }

    protected function authorizeAttachmentDeletion(ProjectActivityAttachment $attachment): void
    {
        $this->ensureAttachmentContext($attachment);

        $project = $attachment->activity->project;
        if (Gate::denies('manage-project-activity-on-certain-time', $project)) {
            abort(403);
        }
    }

    protected function buildDownloadFileName(ProjectActivityAttachment $attachment): string
    {
        $title = $attachment->title ?: 'document';
        $extension = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
        $safeTitle = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $title);

        return $extension ? $safeTitle . '.' . $extension : $safeTitle;
    }
}
