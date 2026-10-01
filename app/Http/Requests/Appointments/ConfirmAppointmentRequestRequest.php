<?php

namespace App\Http\Requests\Appointments;

use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmAppointmentRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('appointmentRequest'));
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => ['nullable', 'integer', Rule::exists(Patient::class, 'id')],
            'service_id' => ['required', 'integer', Rule::exists(Service::class, 'id')],
            'doctor_id' => ['nullable', 'integer', Rule::exists(User::class, 'id')],
            'scheduled_at' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
