<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });
    }

    function setting()
    {
        return $this->hasOne(EventSetting::class, 'event_id');
    }

    function type()
    {
        return $this->hasOne(EventType::class, 'id', 'event_type_id');
    }

    function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    function organizers()
    {
        return $this->hasMany(EventOrganizer::class, 'event_id');
    }

    function folders()
    {
        return $this->hasMany(EventFolder::class, 'event_id');
    }

    function canUpdateEventNameAndStartDate()
    {
        $status = eventStatus($this);
        if ($status == 'Pending') return true;
        return false;

        // return Carbon::parse($this->start_date)->gte(Carbon::now()->endOfDay()) ? true : false;
    }

    function supportAutoApprove()
    {
        return  $this->setting->auto_image_approve == 1 ? true : false;
    }

    function supportShowImageFolders()
    {
        return  $this->setting->image_folders == 1 ? true : false;
    }

    function supportShowVideoFolders()
    {
        return  $this->setting->video_playlist == 1 ? true : false;
    }

    function supportShowGuestImageFolder()
    {
        return  $this->setting->image_share_guest_book == 1 ? true : false;
    }

    function supportImageUpload()
    {
        return $this->setting->allow_upload == 1 ? true : false;
    }

    function supportImageDownload()
    {
        return $this->setting->allow_image_download == 1 ? true : false;
    }
}
