<?php

namespace App\Models;

use App\Models\CaseStaff;
use App\Models\CaseParticipant;
use App\Traits\HasCustomFields;
use App\Traits\Tenantable;
use Modules\Task\Entities\Task;
use Modules\Finance\Entities\Invoice;
use Illuminate\Database\Eloquent\Model;
use Modules\Finance\Entities\Transaction;

class Cases extends Model
{
    use HasCustomFields;
    use Tenantable;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            saasPlanManagement('case', 'create');
        });
        self::deleted(function ($model) {
            saasPlanManagement('case', 'delete');

        });
    }

    protected $table = 'cases';
    protected $primaryKey = 'id';
    protected $fillable = ['name', 'description','layout', 'last_action', 'serial_no', 'organization_id'];

    protected $with = ['files'];

    public function acts()
    {
        return $this->hasMany(CaseAct::class);
    }

    public function lawyers()
    {
        return $this->belongsToMany(Lawyer::class)->withPivot('created_at', 'deleted_at');
    }

    public function plaintiff_client()
    {
        return $this->belongsTo(Client::class, 'plaintiff', 'id');
    }
    public function clientLayout2()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function case_stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }

    public function client_category()
    {
        return $this->belongsTo(ClientCategory::class)->withDefault();
    }

    public function case_category()
    {
        return $this->belongsTo(CaseCategory::class)->withDefault();
    }

    public function opposite_client()
    {
        return $this->belongsTo(Client::class, 'opposite', 'id');
    }

    public function court()
    {
        return $this->belongsTo(Court::class)->withDefault();
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class)->withDefault();
    }

    public function hearing_dates()
    {
        return $this->hasMany(HearingDate::class)->orderBy('date', 'desc');
    }

    public function files()
    {
        return $this->hasMany(Upload::class, 'case_id')->whereNull('hearing_date_id');
    }

    public function allFiles()
    {
        return $this->hasMany(Upload::class, 'case_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'case_id', 'id');
    }

    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            Invoice::class,
            'case_id',
            'morphable_id',
            'id',
            'id'
        )->where(['transactions.morphable_type' => get_class(new Invoice())]);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'case_id', 'id');
    }

    public function caseParticipants()
    {
        return $this->hasMany(CaseParticipant::class, 'case_id', 'id');
    }
    public function caseStaffs()
    {
        return $this->hasMany(CaseStaff::class, 'case_id', 'id')->where('staff_id', '!=', auth()->user()->id);
    }
    public function comments()
    {
        return $this->hasMany(CaseComment::class, 'case_id', 'id')->latest();
    }
}
