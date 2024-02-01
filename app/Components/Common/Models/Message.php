<?php

namespace App\Components\Common\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $table = 'message';
    protected $guarded = ['id'];

    protected $with = ['sender'];


    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function messageable()
    {
        return $this->morphTo();
    }

    /**
     * serializes concept attribute on the fly before saving to database
     *
     * @param $body
     */
    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = serialize($body);
    }

    /**
     * unserializes body attribute before spitting out from database
     *
     * @return mixed
     */
    public function getBodyAttribute()
    {
        if (empty($this->attributes['body']) || is_null($this->attributes['body'])) {
            return [];
        }

        return unserialize($this->attributes['body']);
    }
}
