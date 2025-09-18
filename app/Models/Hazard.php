<?php

namespace App\Models;

use App\Enums\HazardStatus;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Hazard extends Model
{
    use LogsActivity;
    protected $table = 'hazard_reports';
    protected $fillable = [
        'event_type_id',
        'event_sub_type_id',
        'status',
        'department_id',
        'contractor_id',
        'penanggung_jawab_id',
        'pelapor_id',
        'manualPelaporName',
        'location_id',
        'location_specific',
        'tanggal',
        'description',
        'doc_deskripsi',
        'immediate_corrective_action',
        'doc_corrective',
        'key_word',
        'kondisi_tidak_aman_id',
        'tindakan_tidak_aman_id',
        'consequence_id',
        'likelihood_id',
        'risk_level',
    ];



    protected static $logOnlyDirty = true; // hanya log kalau ada perubahan
    protected static $logName = 'hazard_report';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('hazard_report')
            ->logAll()
            ->logOnlyDirty(); // hanya field berubah yang dicatat
    }
    /**
     * Ubah ID relasi jadi name di activity log
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $map = [
            'penanggung_jawab_id' => fn($id) => \App\Models\User::find($id)?->name,
            'pelapor_id'          => fn($id) => \App\Models\User::find($id)?->name,
            'department_id'       => fn($id) => \App\Models\Department::find($id)?->department_name,
            'contractor_id'       => fn($id) => \App\Models\Contractor::find($id)?->contractor_name,
            'location_id'         => fn($id) => \App\Models\Location::find($id)?->name,
        ];

        foreach (['attributes', 'old'] as $key) {
            if (! isset($activity->properties[$key])) {
                continue;
            }

            // ambil sebagai array/collection sementara
            $props = collect($activity->properties[$key]);

            foreach ($map as $field => $resolver) {
                if (isset($props[$field])) {
                    $id = $props[$field];
                    $props[$field . '_name'] = $resolver($id);
                }
            }

            // set ulang sebagai array
            $activity->properties[$key] = $props->toArray();
        }
    }

    // relasi ke logs
    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject');
    }
    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function eventSubType()
    {
        return $this->belongsTo(EventSubType::class, 'event_sub_type_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }
    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function consequence()
    {
        return $this->belongsTo(RiskConsequence::class);
    }

    public function likelihood()
    {
        return $this->belongsTo(Likelihood::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function assignedErms()
    {
        return $this->belongsToMany(User::class, 'hazard_erm_assignments', 'hazard_id', 'erm_id');
    }
    // Scope untuk filter status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    public function resolveCompanyId()
    {
        return $this->department->company_id ?? $this->contractor->company_id ?? null;
    }
}
