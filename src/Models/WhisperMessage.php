<?php

namespace Zareismail\Whisper\Models;


class WhisperMessage extends Model 
{  
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    	'read_at' => 'datetime',
    	'sent_at' => 'datetime',
    ]; 

    /**
     * Query the related message.
     * 
     * @return \Illuminate\Database\Eloquent\Query
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'message_id');
    }

    /**
     * Query the related message.
     * 
     * @return \Illuminate\Database\Eloquent\Query
     */
    public function replies()
    {
        return $this->hasMany(static::class, 'message_id');
    } 

    /**
     * Query the related user.
     * 
     * @return \Illuminate\Database\Eloquent\Query
     */
    public function recipient()
    {
        return $this->authenticatable('recipient_id');
    }

    public function registerMediaCollections(): void
    { 
        $this->addMediaCollection('attachments');
    }
}
