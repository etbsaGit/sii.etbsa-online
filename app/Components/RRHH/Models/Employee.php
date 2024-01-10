<?php

namespace App\Components\RRHH\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
use App\Components\Common\Models\Job;
use App\Components\Common\Models\Township;
use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Employee extends Model
{
    protected $table = 'employees';

    protected $guarded = ['id'];

    protected $appends = ['full_name', 'profile_photo_url'];
    protected $with = ['township'];

    /**
     * Get the user's profile.
     */
    public function user()
    {
        return $this->morphOne(User::class, 'profiable');
    }

    /**
     * Get the user's profile.
     */
    public function boss()
    {
        return $this->belongsTo(DirectBoss::class, 'direct_boss');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
    public function township()
    {
        return $this->belongsTo(Township::class, 'town_id');
    }


    // public function getPhotoAttribute()
    // {
    //     return $this->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']);
    // }
    // public function photoUrl(array $attributes)
    // {
    //     if ($this->photo_path) {
    //         return URL::to(App::make(Server::class)->fromPath($this->photo_path, $attributes));
    //     }
    // }


    public function scopeSearch($query, string $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        });
    }
    public function scopeFilter($query, array $filter = [])
    {
        $query->when($filter['number_employee'] ?? null, function ($query, $number_employee) {
            $query->orWhere('number_employee', 'like', "%{$number_employee}%");
        })->when($filter['name'] ?? null, function ($query, $name) {
            $query->orWhere('name', 'like', "%{$name}%");
        })->when($filter['last_name'] ?? null, function ($query, $last_name) {
            $query->orWhere('last_name', 'like', "%{$last_name}%");
        })->when($filter['job_title'] ?? null, function ($query, $job_title) {
            $query->orWhere('job_title', 'like', "%{$job_title}%");
        })->when($filter['agencies_ids'] ?? null, function ($query, $agencies_ids) {
            $query->whereHas('agency', function ($query) use ($agencies_ids) {
                return $query->whereIn('agency_id', $agencies_ids);
            });
        })->when($filter['department_ids'] ?? null, function ($query, $department_ids) {
            $query->whereHas('department', function ($query) use ($department_ids) {
                return $query->whereIn('department_id', $department_ids);
            });
        });
    }
    // last_name
    // job_title
    // agency_id

    public function getFullNameAttribute()
    {
        return trim(str_replace('  ', ' ', "{$this->name} {$this->second_name} {$this->last_name} {$this->second_last_name}"));
    }



    public function getProfilePhotoUrlAttribute()
    {

        return $this->user ? $this->user->path : $this->defaultProfilePhotoUrl();
        //     // ? Storage::disk('s3')->put('/', $this->photo_path, 'public')
        //     ? Storage::disk('s3')->url($this->photo_path)
        //     : $this->defaultProfilePhotoUrl();
        // return $this->defaultProfilePhotoUrl();
    }
    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
    }
}