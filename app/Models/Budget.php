<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'department_id',
        'title',
        'justification',
        'fiscal_year',
        'category',
        'submission_date',
        'total_budget',
        'supporting_document',
        'status',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'total_budget' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
