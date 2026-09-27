<?php

namespace App\Enum;

enum ComplianceStatus: string
{
    case Pass = 'pass';
    case Warning = 'warning';
    case Fail = 'fail';
    case Attested = 'attested';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Pass => 'Compliant',
            self::Warning => 'Needs attention',
            self::Fail => 'Non-compliant',
            self::Attested => 'Attested',
            self::Pending => 'Attestation pending',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pass, self::Attested => 'success',
            self::Warning, self::Pending => 'warning',
            self::Fail => 'danger',
        };
    }
}
