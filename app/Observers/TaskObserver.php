<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function creating(Task $task): void
    {
        if (empty($task->task_number)) {
            $task->task_number = Task::generateTaskNumber();
        }
    }
}
