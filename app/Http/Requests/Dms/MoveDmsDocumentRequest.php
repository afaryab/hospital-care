<?php

namespace App\Http\Requests\Dms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MoveDmsDocumentRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'target_uuid' => ['required', 'string', 'exists:dms_folders,uuid'],
        ];
    }
}
