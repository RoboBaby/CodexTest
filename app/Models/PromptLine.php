<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptLine extends Model
{
    protected $table = 'prompt_line';

    protected $fillable = [
        'version_id',
        'section_id',
        'order_index',
        'enabled',
        'content',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(PromptVersion::class, 'version_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PromptSection::class, 'section_id');
    }
}
