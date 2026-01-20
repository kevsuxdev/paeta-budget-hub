<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetLineItem extends Model
{
    protected $fillable = [
        'budget_id',
        'description',
        'quantity',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
