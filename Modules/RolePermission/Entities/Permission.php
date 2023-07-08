<?php

namespace Modules\RolePermission\Entities;

use App\Traits\Tenantable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Permission extends Model
{
    protected $fillable = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permission','permission_id','role_id')->where(function($q){
            if (moduleStatusCheck('AdvSaas')){
                $organization_id = 1;
                if (getOrganization()) {
                    $organization_id = getOrganization()->id;
                } else if (Auth::check()) {
                    $organization_id = Auth()->user()->organization_id;
                }

                $q->where('organization_id', $organization_id)->orWhereNull('organization_id');
            }

        })->withPivot(['organization_id']);
    }

    public function scopeModule($query)
    {
        $query->where(function ($query){
            $query->where('route', 'LIKE', '%index%')->orWhere('route', 'LIKE', '%store%')->orWhere('route', 'LIKE', '%create%');
        });
    }

}
