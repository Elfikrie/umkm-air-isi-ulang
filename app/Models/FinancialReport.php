<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    protected $fillable=[
        'type',
        'category',
        'amount',
        'description',
        'report_date',
        'order_id',
        'created_by'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
