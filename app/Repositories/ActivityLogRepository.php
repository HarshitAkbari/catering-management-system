<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ActivityLog;

class ActivityLogRepository extends BaseRepository
{
    public function __construct(ActivityLog $model)
    {
        parent::__construct($model);
    }
}

