<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "slug",
        "disc",
        "logo",
        "cover",
        "status",
    ];

    protected $appends = ['logo_url', 'cover_url'];
    protected $hidden = [
        'logo', 'cover',  'created_at',
        'updated_at',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // scopes
    public function scopeFilter(Builder $builder, $filters)
    {
        if ($filters['name'] ?? false) {
            $builder->where('name', 'LIKE', "%{$filters['name']}%");
        }
        if ($filters['status'] ?? false) {
            $builder->where('status', '=', $filters['status']);
        }
    }
    public function scopeActive(Builder $builder)
    {
        return $builder->where('status', '=', 'active');
    }


    public function getCoverUrlAttribute()
    {
        if (!$this->cover) {
            return null;
        }
        if (Str::startsWith($this->cover, ['http://', 'https://'])) {
            return $this->cover;
        }

        return asset('storage/' . $this->cover);
    }

    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        if (Str::startsWith($this->logo, ['http://', 'https://'])) {
            return $this->logo;
        }

        return asset('storage/' . $this->logo);
    }
}
