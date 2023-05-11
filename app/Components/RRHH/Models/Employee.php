<?php

namespace App\Components\RRHH\Models;

use App\Components\Common\Models\Agency;
use App\Components\Common\Models\Department;
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


    public function scopeSearch($query, String $search)
    {
        $query->when($search ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        });
    }

    public function getFullNameAttribute()
    {
        return trim(str_replace('  ', ' ',  "{$this->name} {$this->last_name}"));
    }



    public function getProfilePhotoUrlAttribute()
    {
        // return $this->photo_path
        //     // ? Storage::disk('s3')->put('/', $this->photo_path, 'public')
        //     ? Storage::disk('s3')->url($this->photo_path)
        //     : $this->defaultProfilePhotoUrl();
        return $this->defaultProfilePhotoUrl();
    }
    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->full_name) . '&color=7F9CF5&background=EBF4FF';
    }
}
