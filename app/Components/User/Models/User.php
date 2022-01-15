<?php

namespace App\Components\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @package App\Components\User\Models
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property array $permissions
 * @property string|null $active
 */
class User extends Authenticatable
{
    use Notifiable, UserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'remember_token',
        'permissions', 'last_login', 'active', 'activation_key', 'seller_key'
    ];
    // 'agency_id', 'departments_id', 'job_title', 'seller_key'

    // protected $with = ['agency', 'department', 'profiable'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // protected $appends = ['groups', 'all_permissions'];

    /**
     * the validation rules
     *
     * @var array
     */
    public static $rules = [];

    /**
     * Get the owning profiable model.
     */
    public function profiable()
    {
        return $this->morphTo();
    }
}
