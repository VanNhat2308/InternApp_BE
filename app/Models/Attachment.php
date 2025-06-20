<?php
// app/Models/Attachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['message_id', 'file_url', 'file_type'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
