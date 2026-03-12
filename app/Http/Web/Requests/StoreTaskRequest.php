<?php

namespace App\Http\Web\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Task\Enums\TaskStatus;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignee_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::enum(TaskStatus::class)],
            'due_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            // 'user_id.required' => 'User ID is required',
            // 'user_id.exists' => 'User ID does not exist',
            'assignee_id.exists' => 'Assignee ID does not exist',
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'title.max' => 'Title must be less than 255 characters',
            'description.required' => 'Description is required',
            'description.string' => 'Description must be a string',
            'description.max' => 'Description must be less than 255 characters',
            'content.nullable' => 'Content is nullable',
            'content.string' => 'Content must be a string',
            'status.required' => 'Status is required',
            'status.string' => 'Status must be a string',
            'status.max' => 'Status must be less than 255 characters',
            'due_date.required' => 'Due date is required',
            'due_date.date' => 'Due date must be a date',
        ];
    }
}
