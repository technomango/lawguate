<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use App\Traits\Tenantable;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\ClientLogin\Entities\AssignLegalContract;
use Modules\ClientLogin\Entities\LegalContract;
use Modules\Finance\Entities\Invoice;
use Modules\Finance\Entities\Transaction;

class Client extends Model
{

    use HasCustomFields;
    use Tenantable;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($course) {
            saasPlanManagement('client', 'create');
        });

        self::deleted(function ($model) {
            saasPlanManagement('client', 'delete');
        });
    }

    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $fillable = ['country_id', 'state_id', 'city_id', 'client_category_id', 'email', 'mobile', 'gender', 'address', 'name', 'description','status', 'organization_id'];

    public function country()
    {
        return $this->belongsTo(Country::class)->withDefault();
    }

    public function state()
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(ClientCategory::class, 'client_category_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->morphMany(Invoice::class, 'clientable');
    }

    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            Invoice::class,
            'clientable_id',
            'morphable_id',
            'id',
            'id'
        )->where(['invoices.clientable_type' => __CLASS__, 'transactions.morphable_type' => get_class(new Invoice())]);
    }

    public function plaintiffs()
    {
        return $this->hasMany(Cases::class, 'plaintiff', 'id');
    }

    public function clientLayout2()
    {
        return $this->hasMany(Cases::class, 'client_id', 'id');
    }

    public function opposites()
    {
        return $this->hasMany(Cases::class, 'opposite', 'id');
    }

    public function legalContracts()
    {
        return $this->hasMany(AssignLegalContract::class, 'client_id', 'id');
    }
    public function getNameTypeAttribute()
    {
        if (moduleStatusCheck('ClientLogin')) {
            return $this->name . '[' . $this->type . ']';
        }
        return $this->name;
    }
}
