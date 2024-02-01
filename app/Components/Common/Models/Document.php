<?php

namespace App\Components\Common\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';
    protected $guarded = ['id'];

    public function documentable()
    {
        return $this->morphTo();
    }

    /**
     * serializes file_info attribute on the fly before saving to database
     *
     * @param $file_info
     */
    public function setFileInfoAttribute($file_info)
    {
        $this->attributes['file_info'] = serialize($file_info);
    }

    /**
     * unserializes file_info attribute before spitting out from database
     *
     * @return mixed
     */
    public function getFileInfoAttribute()
    {
        if (empty($this->attributes['file_info']) || is_null($this->attributes['file_info'])) {
            return [];
        }

        return unserialize($this->attributes['file_info']);
    }
}
