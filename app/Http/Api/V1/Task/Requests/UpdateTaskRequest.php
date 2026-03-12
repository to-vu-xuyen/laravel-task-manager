<?php

namespace App\Http\Api\V1\Task\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Domain\Task\Enums\TaskStatus;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content'     => ['nullable', 'string'],
            'status'      => ['sometimes', Rule::enum(TaskStatus::class)],
            'due_date'    => ['nullable', 'date'],
            'assignee_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
