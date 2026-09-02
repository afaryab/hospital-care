<?php

namespace App\Http\Requests\Dms;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDmsZipUploadRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:zip', 'max:'.(200 * 1024)],
            'folder_uuid' => ['required', 'string', 'exists:dms_folders,uuid'],
        ];
    }
}
