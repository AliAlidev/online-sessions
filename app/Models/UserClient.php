<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserClient extends Model
{
    use HasFactory;
    protected $table = "users_clients";
    protected $guarded = [];

    function client() {
        return $this->belongsTo(Client::class, "client_id");
    }

    function user() {
        return $this->belongsTo(User::class, "user_id");
    }
}
