<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class Client extends Model
{
    use HasFactory;
    protected $guarded = [];

    function clientRole()
    {
        return $this->belongsTo(ClientRole::class, 'client_role');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::id();
        });
    }

    function event()
    {
        return $this->belongsTo(Event::class, 'id', 'client_id');
    }

    function user() {
        return $this->hasOne(User::class, 'id' ,'user_id');
    }
}
