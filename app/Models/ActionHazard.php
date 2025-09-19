<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActionHazard extends Model
{
    use HasFactory;

    protected $fillable = [
        'hazard_id',
        'original_date',      // atau 'original_date'
        'description',
        'status',
        'due_date',
        'actual_close_date',
        'responsible_id',
    ];

    // Relasi
    public function hazard()
    {
        return $this->belongsTo(Hazard::class);
    }
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
