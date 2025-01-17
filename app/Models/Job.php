<?php

namespace App\Models;

use Akaunting\Money\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'title',
        'description',
        'location',
        'contact_email',
        'job_type',
        'quantity',
        'salary',
        'requirement',
        'benefit',
        'image',
        'status',
        'close_at',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function employerProfile()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function employeeProfiles()
    {
        return $this
            ->belongsToMany(EmployeeProfile::class)
            ->as('application')
            ->withPivot(
                'job_id',
                'status',
                'created_at',
                'updated_at',
                'cover_letter',
                'cv'
            );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getCloseAtAttribute($value)
    {
        $date = new Carbon($value);

        return $date->format('d/m/Y');
    }

    public function getSalaryAttribute($value)
    {
        if (is_int($value)) {
            return Money::USD($value, true);
        }

        return 0;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = Str::title($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Str::ucfirst($value);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = Str::title($value);
    }

    public function setRequirementAttribute($value)
    {
        $this->attributes['requirement'] = Str::ucfirst($value);
    }

    public function setBenefitAttribute($value)
    {
        $this->attributes['benefit'] = Str::ucfirst($value);
    }

    public function getCreatedAtAttribute($value)
    {
        $date = new Carbon($value);

        return $date->format('d/m/Y');
    }

    public function scopeActive($query)
    {
        return $query->where('status', config('user.job_status.active'));
    }
}
