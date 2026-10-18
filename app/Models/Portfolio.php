<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Portfolio extends Model
{
    protected $fillable = [
        'user_id', 'full_name', 'slug', 'profile_picture', 'email',
        'contact_number', 'address', 'about_me', 'education', 'skills',
        'projects', 'experience', 'social_links', 'template', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'education' => 'array',
            'skills' => 'array',
            'projects' => 'array',
            'experience' => 'array',
            'social_links' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mediaUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $disk = config('filesystems.portfolio_disk', 'public');

        return $disk === 'public'
            ? url('/storage/'.ltrim($path, '/'))
            : Storage::disk($disk)->url($path);
    }
}
