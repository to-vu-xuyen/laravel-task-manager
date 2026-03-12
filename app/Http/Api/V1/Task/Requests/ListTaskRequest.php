<?php

namespace App\Http\Api\V1\Task\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Domain\Task\Enums\TaskStatus;

class ListTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page'     => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status'   => ['nullable', 'string', Rule::enum(TaskStatus::class)],
            'search'   => ['nullable', 'string', 'max:50'],
        ];
    }
}
