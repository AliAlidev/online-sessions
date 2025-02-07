<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFolder extends Model
{
    use HasFactory;
    protected $guarded = [];

    function files()
    {
        return $this->hasMany(FolderFile::class, 'folder_id');
    }

    function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
