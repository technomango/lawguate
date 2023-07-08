<?php

namespace App\Models;

use App\Scopes\OrganizationScope;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\AdvSaas\Entities\SaasCheckout;
use Modules\AdvSaas\Entities\SaasOrganizePlanManagement;

class Organization extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class)->withOutGlobalScope(OrganizationScope::class)->withDefault([
            'name' => ''
        ]);
    }

    public function checkout()
    {
        return $this->hasMany(SaasCheckout::class, 'organization_id')->orderBy('id', 'desc');
    }

    public function planManagement()
    {
        return $this->belongsTo(SaasOrganizePlanManagement::class, 'id', 'organization_id');
    }

}
