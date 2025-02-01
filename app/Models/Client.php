<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Client extends Model
{
    use HasFactory;
    protected $guarded = [];

    function roleModel()
    {
        return $this->belongsTo(Role::class, 'role');
    }
}
