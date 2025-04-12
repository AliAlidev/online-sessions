<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EventOrganizer extends Model
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

    function role() {
        return $this->belongsTo(ClientRole::class, 'role_in_event');
    }

    function client() {
        return $this->belongsTo(Client::class, 'organizer_id');
    }
}
