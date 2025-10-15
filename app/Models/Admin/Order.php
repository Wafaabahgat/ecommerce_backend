<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'currence',
        'shipping_amount',
        'shipping_method',
        'notes',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function orderItems() : HasMany {
        return $this->hasMany(OrderItem::class);
    }

    public function addresse() : HasOne {
        return $this->hasOne(Address::class);
    }
}
