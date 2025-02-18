<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $guarded = [];

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
        return Carbon::parse($this->start_date)->gte(Carbon::now()) ? true : false;
    }
}
