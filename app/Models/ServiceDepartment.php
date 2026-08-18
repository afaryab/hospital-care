<?php

namespace App\Models;

use App\Concerns\Cacheable;
use App\Enum\ServiceOrderTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceDepartment extends Model
{
    use Cacheable, HasFactory;

    protected $table = 'service_departments';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'have_composit_services',
        'service_order_template',
    ];

    protected $appends = [
        'image_url',
    ];

    protected $casts = [
        'have_composit_services' => 'boolean',
        'service_order_template' => ServiceOrderTemplate::class,
    ];

    public function services()
    {
        return $this->hasMany(Service::class, 'service_department_id');
    }

    /**
     * Resolve `image` into a renderable URL regardless of which format it's
     * stored in — a seeded public-folder path (`/img/emergency.png`), a full
     * URL, or a Filament FileUpload storage-disk path — so both the admin
     * table and the frontend share one resolution instead of duplicating it.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        if (Str::startsWith($this->image, '/img/')) {
            return asset($this->image);
        }

        return Storage::disk('public')->url($this->image);
    }

    /**
     * The full department list, used across navigation, filters, and
     * service-order forms. Small and rarely changes.
     */
    public static function cachedAll(): Collection
    {
        return static::rememberCache(fn () => static::query()->orderBy('name')->get());
    }
}
