<?php

namespace App\Models\HR;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
