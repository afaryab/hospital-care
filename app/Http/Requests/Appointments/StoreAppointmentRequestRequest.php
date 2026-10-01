<?php

namespace App\Http\Requests\Appointments;

use App\Models\AppointmentRequest;
use App\Models\Service;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequestRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'contact' => ['required', 'string', 'regex:/^[\d\s+()-]{10,20}$/'],
            'gender' => ['nullable', Rule::in(['m', 'f', 't'])],
            'age_years' => ['nullable', 'integer', 'min:0', 'max:120'],
            'service_id' => ['nullable', 'integer', Rule::exists(Service::class, 'id')->where('generate_service_order', true)],
            'preferred_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(60)->toDateString()],
            'preferred_time' => ['required', Rule::in(array_keys(AppointmentRequest::PREFERRED_TIMES))],
            'notes' => ['nullable', 'string', 'max:500'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'contact.regex' => 'Enter a valid phone number (at least 10 digits).',
            'preferred_date.after_or_equal' => 'Choose today or a later date.',
            'preferred_date.before_or_equal' => 'Appointments can be requested up to 60 days ahead.',
            'website.prohibited' => 'Your request could not be submitted.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function requestData(): array
    {
        return collect($this->validated())->except('website')->all();
    }
}
