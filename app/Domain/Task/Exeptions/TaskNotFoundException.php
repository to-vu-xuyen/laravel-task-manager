<?php

namespace App\Domain\Task\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class TaskNotFoundException extends Exception
{
    public function __construct(int $taskId)
    {
        parent::__construct(
            sprintf('The requested task with ID %d could not be found.', $taskId),
            Response::HTTP_NOT_FOUND
        );
    }
}
