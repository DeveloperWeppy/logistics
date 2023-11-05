<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastSyncInvoices extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_register'
    ];
}
