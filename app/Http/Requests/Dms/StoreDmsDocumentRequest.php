<?php

namespace App\Http\Requests\Dms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDmsDocumentRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:'.(200 * 1024)],
            'folder_uuid' => ['required', 'string', 'exists:dms_folders,uuid'],
            'classification_id' => ['nullable', 'integer', 'exists:dms_classifications,id'],
        ];
    }
}
