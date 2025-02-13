<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventFolder extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = [
        'can_update_folder_name'
    ];

    function files()
    {
        return $this->hasMany(FolderFile::class, 'folder_id');
    }

    function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    function getCanUpdateFolderNameAttribute()
    {
        return $this->files->count() > 0 ? false : true;
    }
}
