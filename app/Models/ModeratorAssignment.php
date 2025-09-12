<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeratorAssignment extends Model
{
    protected $table='moderator_assignments';
    protected $fillable = ['user_id', 'department_id', 'contractor_id', 'company_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
