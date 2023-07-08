<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public function setting(){
        return $this->hasOne(PaymentGatewaySetting::class, 'gateway_name', 'method');
    }
}
