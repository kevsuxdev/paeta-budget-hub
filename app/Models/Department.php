<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'budget_release',
    ];

    protected $casts = [
        'budget_release' => 'decimal:2',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
