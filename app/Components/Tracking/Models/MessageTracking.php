<?php

namespace App\Components\Tracking\Models;

use App\Components\User\Models\User;
use Illuminate\Database\Eloquent\Model;

class MessageTracking extends Model
{
    protected $table = 'message_tracking';

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
}
