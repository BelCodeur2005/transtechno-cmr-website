<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoleProjectRequest extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'structure', 'city',
        'pole_type', 'message', 'status', 'assigned_to',
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
