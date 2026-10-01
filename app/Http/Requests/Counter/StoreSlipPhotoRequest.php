<?php

namespace App\Http\Requests\Counter;

use App\Enum\SlipPhotoSource;
use App\Enum\SlipPhotoSubject;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSlipPhotoRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'subject' => ['required', Rule::enum(SlipPhotoSubject::class)],
            'source' => ['required', Rule::enum(SlipPhotoSource::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'No photo was captured.',
            'photo.image' => 'The photo must be an image.',
            'photo.max' => 'The photo may not be larger than 5 MB.',
            'subject.required' => 'Choose whether the photo is of the patient or the guardian.',
        ];
    }
}
