<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetLog extends Model
{
    protected $fillable = [
        'budget_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'notes',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
