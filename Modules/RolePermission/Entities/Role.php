<?php

namespace Modules\RolePermission\Entities;

use App\Traits\Tenantable;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use Tenantable;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            saasPlanManagement('role', 'create');
        });
        self::deleted(function ($model) {
            saasPlanManagement('role', 'delete');

        });
    }

    protected $guarded = ['id'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'role_permission','role_id','permission_id');
    }

    public function users(){
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
