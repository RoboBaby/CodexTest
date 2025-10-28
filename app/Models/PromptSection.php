<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptSection extends Model
{
    protected $table = 'prompt_section';

    protected $fillable = [
        'key',
        'title',
        'description',
        'order_index',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(PromptLine::class, 'section_id')
            ->orderBy('order_index');
    }
}
