<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['total_amount', 'status', 'cancelled_at'];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
