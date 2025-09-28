<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;

class Hazard extends Model
{
    use LogsActivity;

    protected $table = 'hazard_reports';

    protected $fillable = [
        'no_referensi',
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

    protected static function boot()
    {
        parent::boot();

        // Mendengarkan event 'deleting'
        static::deleting(function ($hazard) {
            // Hapus file dokumentasi sesudah perbaikan jika ada
            if ($hazard->doc_corrective && Storage::disk('public')->exists($hazard->doc_corrective)) {
                Storage::disk('public')->delete($hazard->doc_corrective);
            }

            // Jika ada file dokumentasi lainnya, tambahkan logika penghapusan di sini.
            // Contoh:
            if ($hazard->doc_deskripsi && Storage::disk('public')->exists($hazard->doc_deskripsi)) {
                Storage::disk('public')->delete($hazard->doc_deskripsi);
            }
        });
    }

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
    public function assignedErms()
    {
        return $this->belongsToMany(User::class, 'hazard_erm_assignments', 'hazard_id', 'erm_id');
    }
    public function actionHazards()
    {
        return $this->hasMany(ActionHazard::class, 'hazard_id');
    }

    /** SCOPES */
    public function scopeStatus($query, $status)
    {
        return $query->whereIn('status', [$status]);
    }
    public function scopeByEventType($query, $id)
    {
        return $query->where('event_type_id', $id);
    }
    public function scopeByEventSubType($query, $id)
    {
        return $query->where('event_sub_type_id', $id);
    }

    public function scopeByDepartment($query, $name)
    {
        return $query->whereHas('department', function ($q) use ($name) {
            $q->where('department_name', 'like', "%{$name}%");
        });
    }

    public function scopeByContractor($query, $name)
    {
        return $query->whereHas('contractor', function ($q) use ($name) {
            $q->where('contractor_name', 'like', "%{$name}%");
        });
    }
    public function scopeDateRange(Builder $query, string $startDate, string $endDate): void
    {
        // Jika tidak ada tanggal yang dipilih, jangan terapkan filter
        if (is_null($startDate) && is_null($endDate)) {
            return;
        }

        // Filter jika hanya tanggal awal yang ada
        if (!is_null($startDate) && is_null($endDate)) {
            $startDateFormatted = Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay();
            $query->where('tanggal', '===', $startDateFormatted);
            return;
        }

        // Filter jika hanya tanggal akhir yang ada
        if (is_null($startDate) && !is_null($endDate)) {
            $endDateFormatted = Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay();
            $query->where('tanggal', '<=', $endDateFormatted);
            return;
        }

        // Filter jika kedua tanggal ada (rentang penuh)
        $startDateFormatted = Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay();
        $endDateFormatted = Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay();

        $query->whereBetween('tanggal', [$startDateFormatted, $endDateFormatted]);
    }

    public function scopeWithHazardCounts($query)
    {
        $query->withCount([
            'actionHazards as total_due_dates' => function ($q) {
                $q->whereNotNull('due_date');
            },
            'actionHazards as pending_actual_closes' => function ($q) {
                $q->whereNull('actual_close_date');
            }
        ]);
    }

    /** HELPERS */
    public function resolveCompanyId()
    {
        return $this->department->company_id ?? $this->contractor->company_id ?? null;
    }
}
