<?php

namespace App\Models;

use App\Enums\HazardStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
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

    protected static $logOnlyDirty = true;
    protected static $logName = 'hazard_report';

    /**
     * Activity Log Options
     */
    public function getActivitylogOptions(): LogOptions
    {
        // Catat semua field, tapi log *_id diganti nama lewat accessor
        return LogOptions::defaults()
            ->useLogName($this->getTable())
            ->logAll()
            ->logOnlyDirty();
    }

    /**
     * Tap activity untuk menambahkan nama relasi
     */
    public function tapActivity(Activity $activity, string $eventName)
    {
        $map = [
            'penanggung_jawab_id' => fn($id) => $id ? $this->penanggungJawab?->name : null,
            'pelapor_id'          => fn($id) => $id ? $this->pelapor?->name : null,
            'department_id'       => fn($id) => $id ? $this->department?->department_name : null,
            'contractor_id'       => fn($id) => $id ? $this->contractor?->contractor_name : null,
            'location_id'         => fn($id) => $id ? $this->location?->name : null,
            'event_type_id'         => fn($id) => $id ? $this->eventType?->event_type_name : null,
            'event_sub_type_id'         => fn($id) => $id ? $this->eventSubType?->event_sub_type_name : null,
        ];

        foreach (['attributes', 'old'] as $key) {
            if (!isset($activity->properties[$key])) continue;

            $props = collect($activity->properties[$key]);
            foreach ($map as $field => $resolver) {
                if (isset($props[$field])) {
                    // Ganti ID dengan name agar log lebih readable
                    $props[$field . '_name'] = $resolver($props[$field]);
                }
            }
            $activity->properties[$key] = $props->toArray();
        }
    }

    /** RELATIONS */
    public function activities()          { return $this->morphMany(Activity::class, 'subject'); }
    public function eventType()           { return $this->belongsTo(EventType::class, 'event_type_id'); }
    public function eventSubType()        { return $this->belongsTo(EventSubType::class, 'event_sub_type_id'); }
    public function department()          { return $this->belongsTo(Department::class); }
    public function contractor()          { return $this->belongsTo(Contractor::class); }
    public function penanggungJawab()     { return $this->belongsTo(User::class, 'penanggung_jawab_id'); }
    public function pelapor()             { return $this->belongsTo(User::class, 'pelapor_id'); }
    public function location()            { return $this->belongsTo(Location::class); }
    public function consequence()         { return $this->belongsTo(RiskConsequence::class); }
    public function likelihood()          { return $this->belongsTo(Likelihood::class); }
    public function company()             { return $this->belongsTo(Company::class); }
    public function assignedErms()        { return $this->belongsToMany(User::class, 'hazard_erm_assignments', 'hazard_id', 'erm_id'); }

    /** SCOPES */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /** HELPERS */
    public function resolveCompanyId()
    {
        return $this->department->company_id ?? $this->contractor->company_id ?? null;
    }
}
