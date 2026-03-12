<?php

namespace App\Http\Api\V1\Task\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Api\V1\Task\Resources\TaskResource;

class TaskCollection extends ResourceCollection
{
    public $collects = TaskResource::class;
}
