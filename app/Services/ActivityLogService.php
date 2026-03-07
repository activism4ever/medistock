<?php
namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public function log(
        string  $action,
        string  $description,
        ?string $modelType = null,
        ?int    $modelId   = null,
        array   $meta      = []
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'meta'        => $meta,
            'ip_address'  => Request::ip(),
        ]);
    }
}
