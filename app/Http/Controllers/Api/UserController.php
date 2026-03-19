<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'doctor_only' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = User::query()->latest('id');

        if (! empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (! empty($filters['username'])) {
            $query->where('username', 'like', "%{$filters['username']}%");
        }

        if (! empty($filters['email'])) {
            $query->where('email', 'like', "%{$filters['email']}%");
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', $filters['is_active']);
        }

        if (! empty($filters['doctor_only'])) {
            $query->where(function ($doctorQuery) {
                $doctorQuery
                    ->whereHas('opdDoctorProfiles')
                    ->orWhereHas('indDoctorProfiles')
                    ->orWhereHas('emergencyDoctorProfiles')
                    ->orWhereHas('dentistProfiles')
                    ->orWhereHas('ultrasoundDoctorProfiles');
            });
        }

        $exact = collect();

        if (! empty($filters['name'])) {
            $exact = User::query()
                ->where('name', $filters['name'])
                ->get();

            if (! empty($filters['doctor_only'])) {
                $exact = $exact->filter(function (User $user) {
                    return $user->opdDoctorProfiles()->exists()
                        || $user->indDoctorProfiles()->exists()
                        || $user->emergencyDoctorProfiles()->exists()
                        || $user->dentistProfiles()->exists()
                        || $user->ultrasoundDoctorProfiles()->exists();
                })->values();
            }

            if ($exact->isNotEmpty()) {
                $query->whereNotIn('id', $exact->pluck('id'));
            }
        }

        return response()->json([
            'data' => [
                'exact' => $exact->values(),
                'possible' => $query->limit($filters['limit'] ?? 50)->get(['id', 'name', 'username', 'email']),
            ],
        ]);
    }
}
