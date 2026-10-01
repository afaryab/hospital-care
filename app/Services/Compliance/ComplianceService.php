<?php

namespace App\Services\Compliance;

use App\Enum\ComplianceStatus;
use App\Enum\IncidentStatus;
use App\Models\BirthCertificate;
use App\Models\Closing;
use App\Models\DeathCertificate;
use App\Models\HospitalSetting;
use App\Models\Incident;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TreatmentRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Throwable;

/**
 * Evaluates the PHC MSDS and HIPAA safeguards the system can verify on its
 * own, and tracks admin attestations for the ones it cannot (training,
 * agreements, physical safeguards).
 */
class ComplianceService
{
    public const ATTESTATIONS_KEY = 'compliance_attestations';

    /**
     * @var array<string, array{title: string, framework: string, category: string, description: string}>
     */
    public const MANUAL_ITEMS = [
        'risk_assessment' => ['title' => 'Periodic risk assessment completed', 'framework' => 'HIPAA §4.1', 'category' => 'Administrative safeguards', 'description' => 'A documented risk analysis of the system and its vulnerabilities, reviewed at least yearly.'],
        'workforce_training' => ['title' => 'Workforce trained on privacy and incident reporting', 'framework' => 'HIPAA §4.2', 'category' => 'Administrative safeguards', 'description' => 'All staff with system access have been trained on data privacy, system use and how to report incidents.'],
        'business_associate_agreements' => ['title' => 'Business associate agreements signed', 'framework' => 'HIPAA §4.4', 'category' => 'Administrative safeguards', 'description' => 'Signed agreements with hosting, backup and other vendors that can access patient data.'],
        'breach_response_plan' => ['title' => 'Breach response plan defined', 'framework' => 'HIPAA §9 / PHC §9', 'category' => 'Incident management', 'description' => 'A written plan to notify affected patients and the regulator within 60 days of discovering a breach.'],
        'physical_safeguards' => ['title' => 'Physical and workstation safeguards in place', 'framework' => 'HIPAA §5', 'category' => 'Physical safeguards', 'description' => 'Restricted server access, visitor logs, workstation auto-lock and screen privacy, secure device disposal.'],
        'downtime_procedures' => ['title' => 'Downtime and manual fallback procedures', 'framework' => 'PHC §15', 'category' => 'Reliability', 'description' => 'Printable forms and a manual process for outages, with a procedure to enter delayed records afterwards.'],
    ];

    /**
     * @return array<int, array{key: string, title: string, framework: string, category: string, description: string, status: ComplianceStatus, summary: string, issues: array<int, string>, attestation: array<string, mixed>|null}>
     */
    public function checks(): array
    {
        $automated = [
            $this->accessControl(),
            $this->multiFactorAuthentication(),
            $this->auditLogging(),
            $this->phiEncryption(),
            $this->transmissionSecurity(),
            $this->sessionTimeout(),
            $this->productionHardening(),
            $this->softDeletes(),
            $this->recordFinalization(),
            $this->consentGate(),
            $this->incidentManagement(),
            $this->breachNotificationContacts(),
            $this->backups(),
        ];

        return [...$automated, ...$this->manualChecks()];
    }

    /**
     * @return array<ComplianceStatus|string, int>
     */
    public function summary(array $checks): array
    {
        return collect(ComplianceStatus::cases())
            ->mapWithKeys(fn (ComplianceStatus $status): array => [$status->value => collect($checks)->where('status', $status)->count()])
            ->all();
    }

    public function attest(string $key, User $user, ?string $note = null): void
    {
        abort_unless(array_key_exists($key, self::MANUAL_ITEMS), 404);

        $attestations = $this->attestations();
        $attestations[$key] = [
            'attested_by' => $user->id,
            'attested_by_name' => $user->name,
            'attested_at' => now()->toIso8601String(),
            'note' => $note,
        ];

        HospitalSetting::set(self::ATTESTATIONS_KEY, json_encode($attestations));

        activity()->causedBy($user)->event('compliance_attested')
            ->withProperties(['item' => $key, 'note' => $note])
            ->log('Compliance item attested: '.self::MANUAL_ITEMS[$key]['title']);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function attestations(): array
    {
        return json_decode((string) HospitalSetting::get(self::ATTESTATIONS_KEY, '{}'), true) ?: [];
    }

    private function accessControl(): array
    {
        $withoutProfile = User::query()->nonSystem()->get()->reject(fn (User $user): bool => $user->hasAnyProfile());

        return $this->result('rbac', 'Role-based access control', 'HIPAA §4.3 / PHC §11', 'Access control',
            'Every account has a role profile that limits what it can see.',
            $withoutProfile->isEmpty() ? ComplianceStatus::Pass : ComplianceStatus::Warning,
            $withoutProfile->isEmpty() ? 'All active accounts have a role profile.' : "{$withoutProfile->count()} account(s) have no role profile.",
            $withoutProfile->take(10)->map(fn (User $user): string => "{$user->name} ({$user->email}) has no role profile")->values()->all(),
        );
    }

    private function multiFactorAuthentication(): array
    {
        $issues = [];

        if (! config('security.two_factor.enforced')) {
            $issues[] = 'Two-factor authentication is not enforced for the admin panel (SECURITY_ENFORCE_TWO_FACTOR).';
        }

        $privileged = User::query()->nonSystem()
            ->where(fn ($q) => $q->whereHas('adminProfiles')->orWhereHas('accountantProfiles'))
            ->whereNull('two_factor_confirmed_at')
            ->get();

        foreach ($privileged->take(10) as $user) {
            $issues[] = "{$user->name} ({$user->email}) has admin/accounts access without two-factor authentication.";
        }

        return $this->result('mfa', 'Multi-factor authentication for privileged users', 'HIPAA §6.1 / PHC §17', 'Access control',
            'Administrators and accountants sign in with a second factor.',
            empty($issues) ? ComplianceStatus::Pass : (config('security.two_factor.enforced') ? ComplianceStatus::Warning : ComplianceStatus::Fail),
            empty($issues) ? 'Enforced, and every privileged account has 2FA confirmed.' : "{$privileged->count()} privileged account(s) without 2FA.",
            $issues,
        );
    }

    private function auditLogging(): array
    {
        $issues = [];

        if (! config('activitylog.enabled')) {
            $issues[] = 'Activity logging is disabled (ACTIVITYLOG_ENABLED).';
        }

        $latest = Activity::query()->latest('id')->value('created_at');
        if ($latest === null) {
            $issues[] = 'No audit log entries have been recorded yet.';
        } elseif (Carbon::parse($latest)->lt(now()->subDays(7))) {
            $issues[] = 'No audit log entry has been written in the last 7 days.';
        }

        foreach ([Patient::class, Transaction::class, ServiceOrder::class, Closing::class] as $model) {
            if (! in_array(LogsActivity::class, class_uses_recursive($model), true)) {
                $issues[] = class_basename($model).' changes are not written to the audit log.';
            }
        }

        return $this->result('audit_log', 'Audit trail of access and changes', 'HIPAA §6.2, §8 / PHC §8', 'Audit & integrity',
            'Logins, patient record access and record changes are logged with user, time and IP.',
            empty($issues) ? ComplianceStatus::Pass : (config('activitylog.enabled') ? ComplianceStatus::Warning : ComplianceStatus::Fail),
            empty($issues) ? 'Audit logging is active; last entry '.Carbon::parse($latest)->diffForHumans().'.' : 'Audit logging has gaps.',
            $issues,
        );
    }

    private function phiEncryption(): array
    {
        $issues = [];

        foreach (['cnic', 'contact', 'address'] as $column) {
            $plain = Patient::withTrashed()
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->where($column, 'not like', 'eyJ%')
                ->count();

            if ($plain > 0) {
                $issues[] = "{$plain} patient record(s) store {$column} unencrypted. Run `php artisan patients:encrypt-fields`.";
            }
        }

        return $this->result('phi_encryption', 'Patient identifiers encrypted at rest', 'HIPAA §7.1 / PHC §4.2', 'Data protection',
            'CNIC, contact number and address are stored encrypted.',
            empty($issues) ? ComplianceStatus::Pass : ComplianceStatus::Fail,
            empty($issues) ? 'All stored CNIC, contact and address values are encrypted.' : 'Some patient identifiers are stored in plain text.',
            $issues,
        );
    }

    private function transmissionSecurity(): array
    {
        $issues = [];

        if (! str_starts_with((string) config('app.url'), 'https://')) {
            $issues[] = 'APP_URL is not served over HTTPS.';
        }
        if (! config('session.secure')) {
            $issues[] = 'Session cookies are not marked secure (SESSION_SECURE_COOKIE).';
        }
        if (! config('session.encrypt')) {
            $issues[] = 'Session data is not encrypted (SESSION_ENCRYPT).';
        }

        return $this->result('transport', 'Encrypted transmission and sessions', 'HIPAA §6.4, §7.2 / PHC §4.2', 'Data protection',
            'All traffic uses TLS and session data is protected.',
            empty($issues) ? ComplianceStatus::Pass : (str_starts_with((string) config('app.url'), 'https://') ? ComplianceStatus::Warning : ComplianceStatus::Fail),
            empty($issues) ? 'HTTPS with secure, encrypted sessions.' : count($issues).' transport setting(s) need attention.',
            $issues,
        );
    }

    private function sessionTimeout(): array
    {
        $lifetime = (int) config('session.lifetime');

        return $this->result('session_timeout', 'Automatic session timeout', 'HIPAA §6.1', 'Access control',
            'Idle sessions expire so unattended workstations do not stay signed in.',
            $lifetime <= 30 ? ComplianceStatus::Pass : ComplianceStatus::Warning,
            "Idle sessions expire after {$lifetime} minutes.",
            $lifetime <= 30 ? [] : ["SESSION_LIFETIME is {$lifetime} minutes; 15–30 minutes is recommended for clinical workstations."],
        );
    }

    private function productionHardening(): array
    {
        $issues = [];

        if (app()->isProduction() && config('app.debug')) {
            $issues[] = 'APP_DEBUG is enabled in production, which can expose patient data in error pages.';
        }

        return $this->result('hardening', 'Production hardening', 'PHC §17', 'Security',
            'Debug output is disabled in production.',
            empty($issues) ? ComplianceStatus::Pass : ComplianceStatus::Fail,
            empty($issues) ? 'Debug output is off in production.' : 'Debug output is on in production.',
            $issues,
        );
    }

    private function softDeletes(): array
    {
        $models = [Patient::class, Transaction::class, ServiceOrder::class, TreatmentRecord::class, Closing::class, DeathCertificate::class, BirthCertificate::class];
        $issues = collect($models)
            ->reject(fn (string $model): bool => in_array(SoftDeletes::class, class_uses_recursive($model), true))
            ->map(fn (string $model): string => class_basename($model).' can be permanently deleted.')
            ->values()->all();

        return $this->result('soft_deletes', 'No permanent deletion of patient or financial records', 'PHC §5 / HIPAA §12', 'Audit & integrity',
            'Deleted records are retained (soft deleted) for the retention period.',
            empty($issues) ? ComplianceStatus::Pass : ComplianceStatus::Fail,
            empty($issues) ? 'All clinical and financial records are soft-deleted only.' : 'Some records can be hard-deleted.',
            $issues,
        );
    }

    private function recordFinalization(): array
    {
        $stale = TreatmentRecord::query()->where('is_finalized', false)->where('created_at', '<', now()->subDays(7))->count();
        $unlocked = DeathCertificate::query()->where('is_locked', false)->where('created_at', '<', now()->subDays(7))->count();

        $issues = array_values(array_filter([
            $stale > 0 ? "{$stale} treatment record(s) older than 7 days are not finalized." : null,
            $unlocked > 0 ? "{$unlocked} death certificate(s) older than 7 days are not locked." : null,
        ]));

        return $this->result('record_locking', 'Medico-legal records finalized and locked', 'PHC §5.1', 'Audit & integrity',
            'Clinical records and certificates are finalized so later edits create versions.',
            empty($issues) ? ComplianceStatus::Pass : ComplianceStatus::Warning,
            empty($issues) ? 'No clinical records are left open past 7 days.' : 'Some clinical records are still editable.',
            $issues,
        );
    }

    private function consentGate(): array
    {
        $enabled = filter_var(HospitalSetting::get('require_consent_before_treatment', false), FILTER_VALIDATE_BOOL);

        return $this->result('consent', 'Consent captured before treatment', 'PHC §7', 'Clinical workflow',
            'Treatment records cannot be saved without a recorded treatment consent.',
            $enabled ? ComplianceStatus::Pass : ComplianceStatus::Warning,
            $enabled ? 'Consent is required before treatment.' : 'Treatment can be recorded without consent.',
            $enabled ? [] : ['Turn on "Require consent before treatment" in Hospital Settings.'],
        );
    }

    private function incidentManagement(): array
    {
        $open = Incident::query()->whereNotIn('status', [IncidentStatus::Resolved->value, IncidentStatus::Closed->value]);
        $overdue = (clone $open)->where('created_at', '<', now()->subDays(30))->get();
        $critical = (clone $open)->where('severity', 'critical')->count();

        $issues = $overdue->take(10)->map(fn (Incident $incident): string => "Incident #{$incident->id} ({$incident->severity?->label()}) has been open since {$incident->created_at->format('d M Y')}.")->values()->all();
        if ($critical > 0) {
            array_unshift($issues, "{$critical} critical incident(s) are open.");
        }

        return $this->result('incidents', 'Incidents handled through their lifecycle', 'PHC §9', 'Incident management',
            'Incidents are classified, assigned, investigated and closed promptly.',
            $critical > 0 ? ComplianceStatus::Fail : ($overdue->isNotEmpty() ? ComplianceStatus::Warning : ComplianceStatus::Pass),
            (clone $open)->count().' open incident(s).',
            $issues,
        );
    }

    private function breachNotificationContacts(): array
    {
        $emails = (array) config('security.breach.notification_emails');
        $placeholder = empty($emails) || collect($emails)->contains(fn ($email): bool => str_ends_with((string) $email, '@example.com'));

        return $this->result('breach_contacts', 'Breach alerts reach a real contact', 'HIPAA §9 / PHC §10', 'Incident management',
            'Automatic breach detections are emailed to the security contact.',
            $placeholder ? ComplianceStatus::Fail : ComplianceStatus::Pass,
            $placeholder ? 'Breach alerts go to a placeholder address.' : 'Breach alerts go to '.implode(', ', $emails).'.',
            $placeholder ? ['Set SECURITY_CONTACT_EMAILS to the hospital security contact.'] : [],
        );
    }

    private function backups(): array
    {
        $issues = [];

        if (blank(config('backup.backup.password'))) {
            $issues[] = 'Backup archives are not encrypted (BACKUP_ARCHIVE_PASSWORD).';
        }

        $disks = (array) config('backup.backup.destination.disks');
        if (count($disks) < 2) {
            $issues[] = 'Backups are stored on this server only; configure an off-site disk (BACKUP_OFFSITE_DISK).';
        }

        $latest = $this->latestBackupAt($disks);
        if ($latest === null) {
            $issues[] = 'No backup archive was found.';
        } elseif ($latest->lt(now()->subHours(26))) {
            $issues[] = 'The newest backup is from '.$latest->diffForHumans().'.';
        }

        return $this->result('backups', 'Encrypted, recent, off-site backups', 'HIPAA §15 / PHC §13', 'Reliability',
            'A daily encrypted backup exists on and off the server.',
            empty($issues) ? ComplianceStatus::Pass : ($latest === null ? ComplianceStatus::Fail : ComplianceStatus::Warning),
            $latest ? 'Last backup '.$latest->diffForHumans().'.' : 'No backups found.',
            $issues,
        );
    }

    /**
     * @param  array<int, string>  $disks
     */
    private function latestBackupAt(array $disks): ?Carbon
    {
        $folder = (string) config('backup.backup.name');
        $latest = null;

        foreach ($disks as $disk) {
            try {
                foreach (Storage::disk($disk)->files($folder) as $file) {
                    if (! str_ends_with($file, '.zip')) {
                        continue;
                    }
                    $modified = Carbon::createFromTimestamp(Storage::disk($disk)->lastModified($file));
                    $latest = $latest === null || $modified->gt($latest) ? $modified : $latest;
                }
            } catch (Throwable) {
                continue;
            }
        }

        return $latest;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function manualChecks(): array
    {
        $attestations = $this->attestations();

        return collect(self::MANUAL_ITEMS)->map(function (array $item, string $key) use ($attestations): array {
            $attestation = $attestations[$key] ?? null;
            $expired = $attestation && Carbon::parse($attestation['attested_at'])->lt(now()->subYear());

            $check = $this->result($key, $item['title'], $item['framework'], $item['category'], $item['description'],
                $attestation && ! $expired ? ComplianceStatus::Attested : ComplianceStatus::Pending,
                match (true) {
                    $attestation === null => 'Not yet attested by an administrator.',
                    $expired => 'Attestation expired; last attested '.Carbon::parse($attestation['attested_at'])->format('d M Y').'.',
                    default => 'Attested by '.$attestation['attested_by_name'].' on '.Carbon::parse($attestation['attested_at'])->format('d M Y').'.',
                },
                $attestation === null || $expired ? ['Confirm this is in place and attest it (renew yearly).'] : [],
            );
            $check['attestation'] = $attestation;
            $check['manual'] = true;

            return $check;
        })->values()->all();
    }

    /**
     * @param  array<int, string>  $issues
     * @return array<string, mixed>
     */
    private function result(string $key, string $title, string $framework, string $category, string $description, ComplianceStatus $status, string $summary, array $issues): array
    {
        return [
            'key' => $key,
            'title' => $title,
            'framework' => $framework,
            'category' => $category,
            'description' => $description,
            'status' => $status,
            'summary' => $summary,
            'issues' => $issues,
            'attestation' => null,
            'manual' => false,
        ];
    }
}
