<?php

namespace App\Http\Requests\Dms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDmsFolderRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_uuid' => ['nullable', 'string', 'exists:dms_folders,uuid'],
            'classification_id' => ['nullable', 'integer', 'exists:dms_classifications,id'],
        ];
    }
}
