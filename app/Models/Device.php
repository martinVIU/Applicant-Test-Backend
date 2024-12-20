<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Device;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'model', 'device_unique_id'];

    // Relationship with User model through the Access model 
    public function users()
    {
        return $this->belongsToMany(User::class, 'access', 'device_id', 'user_id');
    }
}

