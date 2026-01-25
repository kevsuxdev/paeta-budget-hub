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
        'e_signed',
        'approved_by',
        'date_updated',
        'dept_head_reviewed_at',
        'finance_reviewed_at',
        'final_approved_at',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'total_budget' => 'decimal:2',
        'date_updated' => 'datetime',
        'dept_head_reviewed_at' => 'datetime',
        'finance_reviewed_at' => 'datetime',
        'final_approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function logs()
    {
        return $this->hasMany(BudgetLog::class)->orderBy('created_at', 'desc');
    }

    public function lineItems()
    {
        return $this->hasMany(BudgetLineItem::class, 'budget_id');
    }
}
