<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'wc_order_id',
        'wc_status',
        'billing',
        'shipping',
        'line_items',
        'total_amount',
        'create_user_id',
        'picking_user_id',
        'packing_user_id',
        'finalized_user_id',
        'date_picking',
        'date_packing',
        'date_delivery',
        'siigo_invoice',
        'tracking_code',
        'status',
        'payment_method',
        'id_transaction_payment'
    ];
    public function creatorUser()
    {
        return $this->hasMany(User::class, 'id', 'create_user_id');
    }
    public function pickingUser()
    {
        return $this->hasMany(User::class, 'id', 'picking_user_id');
    }
    public function packingUser()
    {
        return $this->hasMany(User::class, 'id', 'packing_user_id');
    }
    public function deliveryUser()
    {
        return $this->hasMany(User::class, 'id', 'finalized_user_id');
    }
}
