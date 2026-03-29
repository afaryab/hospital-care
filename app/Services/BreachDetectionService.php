<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\SecurityIncidentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class BreachDetectionService
{
    public function recordFailedLogin(Request $request, ?User $user = null, ?string $identifier = null): ?Incident
    {
        $identifier = strtolower(trim((string) ($identifier ?? 'unknown')));
        $ipAddress = (string) ($request->ip() ?? 'unknown');
        $windowMinutes = (int) config('security.breach.failed_login_window_minutes', 10);
        $threshold = (int) config('security.breach.failed_login_threshold', 5);

        $counterKey = sprintf('security:failed-login:%s', sha1($identifier.'|'.$ipAddress));
        $alertKey = $counterKey.':alerted';

        Cache::add($counterKey, 0, now()->addMinutes($windowMinutes));
        $attempts = Cache::increment($counterKey);

        if ($attempts < $threshold || Cache::has($alertKey)) {
            return null;
        }

        Cache::put($alertKey, true, now()->addMinutes($windowMinutes));

        return $this->createIncident(
            type: 'failed_login_threshold',
            severity: 'high',
            user: $user,
            ipAddress: $ipAddress,
            context: [
                'identifier' => $identifier,
                'attempts' => $attempts,
                'window_minutes' => $windowMinutes,
            ],
        );
    }

    public function recordSuccessfulLogin(User $user, Request $request): ?Incident
    {
        $ipAddress = $request->ip();
        $deviceSignature = hash('sha256', (string) $request->userAgent());
        $previousDeviceSignature = Cache::get($this->deviceCacheKey($user->id));

        $isNewIp = ! empty($user->ip_address) && ! empty($ipAddress) && $user->ip_address !== $ipAddress;
        $isNewDevice = ! empty($previousDeviceSignature) && $previousDeviceSignature !== $deviceSignature;

        $incident = null;

        if ($isNewIp || $isNewDevice) {
            $incident = $this->createIncident(
                type: 'new_login_context',
                severity: 'medium',
                user: $user,
                ipAddress: $ipAddress,
                deviceSignature: $deviceSignature,
                context: [
                    'new_ip' => $isNewIp,
                    'new_device' => $isNewDevice,
                    'previous_ip' => $user->ip_address,
                ],
            );
        }

        Cache::forever($this->deviceCacheKey($user->id), $deviceSignature);

        $user->forceFill([
            'last_login' => now(),
            'last_login_attempt' => now(),
            'ip_address' => $ipAddress,
            'login_attempts' => 0,
        ])->save();

        return $incident;
    }

    public function recordPatientAccess(User $user, Patient $patient, Request $request): ?Incident
    {
        $windowMinutes = (int) config('security.breach.bulk_patient_window_minutes', 5);
        $threshold = (int) config('security.breach.bulk_patient_access_threshold', 20);

        $counterKey = sprintf('security:patient-access:%d', $user->id);
        $alertKey = $counterKey.':alerted';

        Cache::add($counterKey, 0, now()->addMinutes($windowMinutes));
        $accessCount = Cache::increment($counterKey);

        if ($accessCount < $threshold || Cache::has($alertKey)) {
            return null;
        }

        Cache::put($alertKey, true, now()->addMinutes($windowMinutes));

        return $this->createIncident(
            type: 'bulk_patient_access',
            severity: 'high',
            user: $user,
            patient: $patient,
            ipAddress: $request->ip(),
            deviceSignature: hash('sha256', (string) $request->userAgent()),
            context: [
                'access_count' => $accessCount,
                'window_minutes' => $windowMinutes,
                'patient_ps_number' => $patient->ps_number,
            ],
        );
    }

    protected function createIncident(
        string $type,
        string $severity,
        ?User $user = null,
        ?Patient $patient = null,
        ?string $ipAddress = null,
        ?string $deviceSignature = null,
        array $context = [],
    ): Incident {
        $incident = Incident::create([
            'type' => $type,
            'severity' => $severity,
            'status' => 'open',
            'user_id' => $user?->id,
            'patient_id' => $patient?->id,
            'ip_address' => $ipAddress,
            'device_signature' => $deviceSignature,
            'context' => $context,
            'occurred_at' => now(),
        ]);

        $emails = collect(config('security.breach.notification_emails', []))
            ->filter(fn ($email): bool => is_string($email) && $email !== '')
            ->unique()
            ->values();

        foreach ($emails as $email) {
            Notification::route('mail', $email)->notify(new SecurityIncidentNotification($incident));
        }

        return $incident;
    }

    protected function deviceCacheKey(int $userId): string
    {
        return sprintf('security:user-device:%d', $userId);
    }
}
