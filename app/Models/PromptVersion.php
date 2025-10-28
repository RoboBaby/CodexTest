<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptVersion extends Model
{
    protected $table = 'prompt_version';

    protected $fillable = [
        'prompt_name',
        'version_label',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_ACTIVE,
            self::STATUS_ARCHIVED,
        ];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PromptLine::class, 'version_id')
            ->orderBy('order_index');
    }
}
