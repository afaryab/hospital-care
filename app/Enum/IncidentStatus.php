<?php

namespace App\Enum;

/**
 * PHC guideline §9.2 lifecycle: Reported → Classified → Assigned →
 * Investigated → Resolved → Closed. Strictly linear — see
 * Incident::booted() for the enforcement that a status can only ever
 * advance to the immediate next stage, never skip ahead or move backward.
 */
enum IncidentStatus: string
{
    case Reported = 'reported';
    case Classified = 'classified';
    case Assigned = 'assigned';
    case Investigated = 'investigated';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Reported => 'Reported',
            self::Classified => 'Classified',
            self::Assigned => 'Assigned',
            self::Investigated => 'Investigated',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Reported => 'danger',
            self::Classified => 'warning',
            self::Assigned => 'info',
            self::Investigated => 'primary',
            self::Resolved => 'success',
            self::Closed => 'gray',
        };
    }

    /**
     * The only status this one is allowed to advance to next. Null once
     * Closed — the lifecycle is terminal there.
     */
    public function next(): ?self
    {
        return match ($this) {
            self::Reported => self::Classified,
            self::Classified => self::Assigned,
            self::Assigned => self::Investigated,
            self::Investigated => self::Resolved,
            self::Resolved => self::Closed,
            self::Closed => null,
        };
    }
}
