<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class DmsShare extends Model
{
    use HasFactory;

    public const GRANTEE_USER = 'user';

    public const GRANTEE_ROLE = 'role';

    public const GRANTEE_EMAIL = 'email';

    protected $fillable = [
        'document_id',
        'folder_id',
        'grantee_type',
        'grantee_value',
        'ability',
        'token',
        'expires_at',
        'created_by',
        'accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accessed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(DmsDocument::class, 'document_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DmsFolder::class, 'folder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function markAccessed(): void
    {
        $this->forceFill(['accessed_at' => Carbon::now()])->saveQuietly();
    }
}
