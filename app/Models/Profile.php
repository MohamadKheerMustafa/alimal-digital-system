<?php

namespace App\Models;

use App\Models\Archive\Archive;
use App\Models\HR\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'department',
        'contact_number',
        'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
