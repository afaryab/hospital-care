<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Patient;
use App\Models\PatientManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Normalize mobile to include +country code if user typed local format
        $data = $input;
        if (!empty($data['mobile'])) {
            $mobileRaw = $data['mobile'];
            $digits = preg_replace('/\D+/', '', $mobileRaw ?? '');
            if (str_starts_with($mobileRaw ?? '', '+')) {
                $data['mobile'] = '+' . $digits;
            } elseif (preg_match('/^0\d{9,}$/', $digits)) {
                // Convert leading 0XXXXXXXXX to +92XXXXXXXXX (Pakistan default)
                $data['mobile'] = '+92' . substr($digits, 1);
            }
        }

        Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            // Email is optional but must be valid & unique if provided
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            // Mobile/contact is required and must start with +country code, 9 to 13 digits
            'mobile' => ['required', 'string', 'max:20', 'regex:/^\+\d{9,13}$/', Rule::unique(User::class, 'mobile')],
            // Additional fields
            'gender' => ['required', 'in:m,f,t'],
            'dob' => ['required', 'date'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($data) {
            // Ensure at least one of email or mobile is provided
            if (empty($data['email']) && empty($data['mobile'])) {
                $validator->errors()->add('email', 'Email or mobile is required.');
                $validator->errors()->add('mobile', 'Email or mobile is required.');
            }
        })->validate();

        return DB::transaction(function () use ($data) {
            // Determine username: whichever of email or mobile is provided first
            $username = !empty($data['email']) ? $data['email'] : $data['mobile'];

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'mobile' => $data['mobile'] ?? null,
                'username' => $username,
                'password' => $data['password'],
            ]);

            // Create patient record with provided demographic info
            $patient = Patient::create([
                'name' => $data['name'],
                'gender' => $data['gender'],
                'age_group' => null,
                'age_days' => null,
                'age_dob' => $data['dob'],
                'address' => null,
                'guardian' => null,
                'relation' => null,
                'contact' => $data['mobile'] ?? null,
                'cnic' => null,
            ]);

            // Link user to patient via PatientManager
            PatientManager::create([
                'user_id' => $user->id,
                'patient_id' => $patient->id,
            ]);

            return $user;
        });
    }
}
