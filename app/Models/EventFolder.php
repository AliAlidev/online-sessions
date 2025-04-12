<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EventFolder extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = [
        'can_update_folder_name'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });
    }

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
