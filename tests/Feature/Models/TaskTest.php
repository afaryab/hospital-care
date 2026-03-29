<?php

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;

test('task generates task number on create', function () {
    $task = Task::factory()->create();

    expect($task->task_number)->toStartWith('TSK/');
});

test('task status enum casts correctly', function () {
    $task = Task::factory()->completed()->create();

    expect($task->status->value)->toBe(TaskStatus::Completed->value);
});

test('task priority enum casts correctly', function () {
    $task = Task::factory()->urgent()->create();

    expect($task->priority->value)->toBe(TaskPriority::Urgent->value);
});

test('task soft deletes instead of hard deletes', function () {
    $task = Task::factory()->create();
    $id = $task->id;
    $task->delete();

    expect(Task::find($id))->toBeNull()
        ->and(Task::withTrashed()->find($id))->not->toBeNull();
});

test('task has comments relationship', function () {
    $task = Task::factory()->create();
    TaskComment::factory()->create(['task_id' => $task->id]);

    expect($task->comments()->count())->toBe(1);
});

test('task comment belongs to task and user', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();
    $comment = TaskComment::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);

    expect($comment->task->id)->toBe($task->id)
        ->and($comment->user->id)->toBe($user->id);
});

test('two tasks get unique task numbers', function () {
    $t1 = Task::factory()->create();
    $t2 = Task::factory()->create();

    expect($t1->task_number)->not->toBe($t2->task_number);
});
